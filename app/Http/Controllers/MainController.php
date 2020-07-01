<?php

namespace App\Http\Controllers;

use App\Ecommerce\Customers\CustomerRepo;
use App\Ecommerce\Products\Product;
use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\SubGalleries\SubGallery;
use App\Photos\SubGalleries\SubGalleryRepo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    /**
     * Validate gallery code and redirect to gallery page
     *
     * @param Request        $request
     * @param GalleryRepo    $galleryRepo
     * @param SubGalleryRepo $subGalleryRepo
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request, GalleryRepo $galleryRepo, SubGalleryRepo $subGalleryRepo)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);
        
        if($gallery = $galleryRepo->getByCode($request->get('code'))){
            $route = url("app/$gallery->password/categories");
        } else {
            $subGallery = $subGalleryRepo->getByCode($request->get('code'));
            $route = url("app/$subGallery->password/gallery");
        }

        return response()->json(['redirect' => $route], 200);
    }

    /**
     * Retry in package page, Validate gallery code and redirect to gallery page
     *
     * @param                $code
     * @param GalleryRepo    $galleryRepo
     * @param SubGalleryRepo $subGalleryRepo
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function recode($code, GalleryRepo $galleryRepo, SubGalleryRepo $subGalleryRepo)
    {
        
        if($gallery = $galleryRepo->getByCode($code)){
            $route = "$gallery->password/categories";
        } else {
            $subGallery = $subGalleryRepo->getByCode($code);
            $route = "$subGallery->password/gallery";
        }

        return response()->json(['redirect' => $route, 'status'=>'success'], 200);
    }

    /**
     * Return gallery info
     *
     * @param             $code
     * @param GalleryRepo $galleryRepo
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function gallery($code, GalleryRepo $galleryRepo)
    {
        $gallery = $galleryRepo->getByCode($code);

        if (! is_null($gallery)) {
            $type = 'categories';

            foreach ($gallery->subGalleries as $key => $subGallery) {
                $prepared_data[$key] = [
                    'sub_gallery_pass' => $subGallery->password,
                    'name' => $subGallery->name,
                    'preview' => $subGallery->mainPhoto()->present()->previewUrl(),
                    'sub_gallery_route' => route('get-gallery', $subGallery->password),
                ];
            }
        }

        return response()->json([
            'status' => isset($prepared_data) ? true : false,
            'data' => $prepared_data ?? [],
            'type' => $type ?? '',
            'static' => $additional_data ?? ['gallery_id' => $gallery->id],
        ], 200);
    }

    /**
     * Return sub gallery info
     *
     * @param                   $code
     * @param SubGalleryRepo    $subGalleryRepo
     * @return JsonResponse
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function subGallery($code, SubGalleryRepo $subGalleryRepo)
    {
        /** @var SubGallery $subGallery */
        $subGallery = $subGalleryRepo->getByCode($code);

        if (! is_null($subGallery)) {
            $type = 'subGallery';

            foreach ($subGallery->photos as $key => $photo) {
                $prepared_data[$key] = [
                    'image' => $photo->present()->previewUrl(),
                    'width' => $photo->width,
                    'height' => $photo->height,
                    'id' => $photo->id
                ];
            }

            $additionalData = [
                'sub_gallery_id' => $subGallery->id,
                'sub_gallery_pass' => $subGallery->password,
                'name' => $subGallery->name,
                'school' => $subGallery->person->school_name,
                'gallery_id' => $subGallery->gallery_id
            ];
        }

        return response()->json([
            'status' => isset($prepared_data) ? true : false,
            'data' => $prepared_data ?? [],
            'type' => $type ?? '',
            'static' => $additionalData ?? [],
        ], 200);
    }

    /**
     * Prepare price list information for gallery products and packages
     *
     * @param $subGalleryId
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function priceList($subGalleryId)
    {
        /** @var SubGallery $subGallery */
        $subGallery = (new SubGalleryRepo())->getByID($subGalleryId);
        if(!$subGallery){
            abort(404, 'Sub gallery not found');
        }

        /** @var Gallery $gallery */
        $gallery = $subGallery->gallery;

        $prepared_data['products'] = [];
        $prepared_data['packages'] = [];

        // Get special price list for staff or regular for other
        $priceList = $subGallery->getPriceList();

        // Prepare Add-ons
        //
        // Addons available ONLY before deadline
        if(! $gallery->isDeadlineCame()){
            foreach ($priceList->products as $key => $product) {
                $prepared_data['products'][] = $this->prepareProductData($product);
            }
        }

        // Prepare Packages
        //
        // ONLY packages with mark 'available_after_deadline' will be able after deadline
        // Products will be able with type DIGITAL_FULL
        //
        // Before deadline, all available
        foreach ($priceList->packages as $key => $package) {
            if (! count($package->products)) {
                continue;
            }
            if($gallery->isDeadlineCame() && $package->available_after_deadline) {
                $prepared_data['packages'][] = $this->preparedPackage($package, $gallery->isDeadlineCame());
            }elseif (! $gallery->isDeadlineCame() && !$package->available_after_deadline){
                $prepared_data['packages'][] = $this->preparedPackage($package, $gallery->isDeadlineCame());
            }
        }

        $tax = DB::table('settings_tax')->find(1);
        $prepared_data['tax'] = $tax ? $tax->value : 0;

        return response()->json($prepared_data);
    }

    /**
     * @param $package
     * @param bool $deadlineCame
     * @return array
     */
    protected function preparedPackage($package, bool $deadlineCame = false) : array
    {
        $data = [
            'id' => $package->id,
            'image' => $package->present()->image,
            'name' => $package->name,
            'reference' => $package->reference_name,
            'taxable' => (bool) $package->taxable,
            'description' => $package->description,
            'price' => number_format($package->pivot->price, 2, $dec_point = ".", $thousands_sep = " "),
            'limit_poses' => $package->limit_poses,
        ];

        /**
         * @var  $p_key
         * @var Product $product
         */
        foreach ($package->products as $p_key => $product) {
            if(! $deadlineCame) {
                $data['products'][$p_key] = $this->prepareProductData($product);
            }
            elseif ($deadlineCame && $product->isDownloadable()){
                $data['products'][$p_key] = $this->prepareProductData($product);
            }
        }

        return $data;
    }

    /**
     * Prepare product
     *
     * @param $product
     * @return array
     */
    protected function prepareProductData($product) : array
    {
        $preparedData = [
            'id' => $product->id,
            'type' => $product->type,
            'name' => $product->name,
            'reference' => $product->reference,
            'taxable' => (bool)$product->taxable,
            'description' => $product->description,
            'image' => $product->image ? $product->present()->image() : null,
            'price' => number_format($product->pivot->price, 2, $dec_point = ".", $thousands_sep = " "),
            'retouch' => '',
        ];

        foreach ($product->sizes as $size_combination) {
            $square = 0;
            $width = 0;
            $height = 0;
            foreach ($size_combination->sizes as $size) {
                $current_square = $size->width * $size->height;
                if ($current_square > $square) {
                    $square = $current_square;
                    $width = $size->width;
                    $height = $size->height;
                }
            }

            $preparedData['sizes'][] = [
                'name' => $size_combination->name,
                'id' => $size_combination->id,
                'width' => $width,
                'height' => $height,
            ];
        }

        return $preparedData;
    }

    /**
     * Create customer by email from popup
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Ecommerce\Customers\CustomerRepo $customerRepo
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createPotentialCustomer(Request $request, CustomerRepo $customerRepo)
    {
        $request->validate([
            'email' => 'required|email',
            'subgallery_id' => 'required',
        ]);

        $customer = $customerRepo->findOrCreateByEmail($request->get('email'));
        $customer->subGalleries()->syncWithoutDetaching($request->get('subgallery_id'));

        return response()->json([
            'status' => true,
        ], 200);
    }
}

