<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('main', [
    'uses' => function () {
        return response()->view('main');
    },
])->name('main');

/**
 * Dashboard routes
 */
Route::group([
    'prefix' => 'dashboard',
    'as' => 'dashboard::',
    'middleware' => 'auth',
    'namespace' => 'Dashboard',
], function () {

    Route::get('/', [
        'as' => 'index',
        'uses' => function () {
            if (Gate::allows('admin')) {
                return app()->make(\Webmagic\Dashboard\Dashboard::class)->render();
            } else {
                return redirect(route('dashboard::users.show', Auth::user()->id));
            }
        },
    ]);

    /*
     * Users
     */
    Route::resources([
        'users' => 'UserDashboardController',
    ]);
    Route::group([
        'prefix' => 'users',
        'as' => 'users.',
    ], function () {
        Route::put('password/{user}', 'UserDashboardController@updatePassword')->name('update.password');
        Route::put('status/{user}', 'UserDashboardController@updateStatus')->name('update.status');
        Route::get('users/credentials/{user}', 'UserDashboardController@sendCredentials')->name('send.credentials');
        Route::get('profile/show', 'UserDashboardController@show')->name('profile');
        Route::get('rules/download', 'UserDashboardController@downloadRules')->name('download.rules');
    });

    /*
    * Unprocessed photos
    */
    Route::group([
        'prefix' => 'unprocessed-photos',
        'as' => 'unprocessed-photos.',
    ], function () {
        Route::get('', 'UnprocessedPhotosDashboardController@index')->name('index');
        Route::delete('{gallery}/{user_id}', 'UnprocessedPhotosDashboardController@destroy')->name('destroy');
        Route::post('convert', 'UnprocessedPhotosDashboardController@convert')->name('convert');

        Route::post('get-popup-for-convert/{directory_name}/{user_id}', 'UnprocessedPhotosDashboardController@getPopupForStartingConvertingProcess')->name('get-popup-for-convert');
        Route::post('check-directory-structure/{directory_name}/{user_id}', 'UnprocessedPhotosDashboardController@checkDirectoryStructure')->name('check-directory-structure');
    });

    /*
    * Gallery
    */

    Route::group([
        'prefix' => 'gallery',
        'as' => 'gallery.',
    ], function () {
        Route::post('/', 'GalleryDashboardController@index')->name('index');

        Route::get('converting-statuses/{gallery}', 'GalleryDashboardController@getConvertingStatuses')->name('get-converting-statuses');
        Route::get('group-photo-converting-statuses/{gallery}', 'GalleryDashboardController@getGroupPhotosConvertingStatuses')->name('get-group-photo-converting-statuses');
        Route::get('converting-status/{gallery}', 'GalleryDashboardController@getConvertingStatus')->name('get-converting-status');

        Route::get('group-photo-generation/status-btn/{gallery}', 'GalleryDashboardController@getStatusBtn')->name('get-status-button');
        Route::get('group-photo-generation/start/{gallery}', 'GalleryDashboardController@groupPhotoGenerationStart')->name('group-photo-generation-start');


        Route::get('create/{gallery_name}/{user_id}', 'GalleryDashboardController@create')->name('create');
        Route::get('passwords/{gallery}', 'GalleryDashboardController@downloadPasswords')->name('passwords');

        Route::get('download/proofs/{gallery}', 'GalleryDashboardController@downloadProofingPhotos')->name('download.proofing.photos');
        Route::post('download/proofs/{gallery}/start', 'GalleryDashboardController@proofPhotosExportZipPreparingStart')->name('download.proofing.photos.start');
        Route::get('download/proofs/{gallery}/status', 'GalleryDashboardController@proofPhotosExportZipPreparingStatus')->name('download.proofing.photos.status');

        Route::get('download/clients/export/{gallery}', 'GalleryDashboardController@exportClients')->name('export.clients');
        Route::get('download/clients/import/{gallery}', 'GalleryDashboardController@getImportFile')->name('import.clients.get');
        Route::post('download/clients/import/{gallery}', 'GalleryDashboardController@importClients')->name('import.clients');

        Route::get('class-photo/generation-test', 'GalleryDashboardController@generationTestPage')->name('generation-test-page');
        Route::post('class-photo/generation-test', 'GalleryDashboardController@generationTest')->name('generation-test');

        Route::get('continue-initial-processing/{gallery_id}', 'GalleryDashboardController@manualContinueInitialGalleryProcessingScenario')->name('continue-initial-processing');
        Route::get('continue-group-processing/{gallery_id}', 'GalleryDashboardController@manualContinueInitialGalleryProcessingScenario')->name('continue-group-processing');

        /*
         * Subgallery
         */
        Route::group([
            'prefix' => 'subgallery',
            'as' => 'subgallery.',
        ], function () {
            Route::get('filter/{gallery}/{return_type?}', 'SubgalleryDashboardController@filter')->name('filter');
            Route::delete('delete/{subgallery}', 'SubgalleryDashboardController@delete')->name('delete');
            Route::get('show/{subgallery}', 'SubgalleryDashboardController@show')->name('show');
            Route::get('move/{subgallery}', 'SubgalleryDashboardController@getPopupForMovingSubgallery')->name('get-move-popup');
            Route::post('move/{subgallery}', 'SubgalleryDashboardController@move')->name('move');
            Route::get('change-cropped-info/{subgallery}', 'SubgalleryDashboardController@getPopupForChangingCroppedFaceInfo')->name('change-cropped-info');
            Route::post('change-cropped-info/{subgallery}', 'SubgalleryDashboardController@updateCroppedFaceInfo')->name('update-cropped-info');
            Route::delete('delete-cropped-photo/{subgallery}', 'SubgalleryDashboardController@deleteCroppedPhoto')->name('delete-cropped-photo');
            Route::get('delete/photo/{photo_id}', 'SubgalleryDashboardController@deletePhotoFromSubgallery')->name('delete.photo');

            Route::get('change-proof-photo/{subgallery}', 'SubgalleryDashboardController@getPopupForChangingProofPhoto')->name('change-proof-photo');
            Route::post('change-proof-photo/{subgallery}', 'SubgalleryDashboardController@updateProofPhoto')->name('update-proof-photo');
            Route::post('check-proof-photo-status/{subgallery}', 'SubgalleryDashboardController@proofPhotoUpdatingStatus')->name('check-proof-photo-status');

            Route::get('download/proofs/{subgallery}', 'SubgalleryDashboardController@downloadProofingPhotos')->name('download.proofing.photos');
            Route::get('client/{client}', 'SubgalleryDashboardController@editClient')->name('client.edit');
            Route::put('client/{client}', 'SubgalleryDashboardController@updateClient')->name('client.update');
            Route::put('client/{client}/all-class-photo', 'SubgalleryDashboardController@updateAllClassPhotosForClient')->name('change-all-class-photos');
            Route::put('client/{client}/update-position', 'SubgalleryDashboardController@updatePositionForTeacher')->name('update-position');

            Route::put('{subgallery_id}/group-photo', 'SubgalleryDashboardController@updateGroupPhotoAvailability')->name('change-availability-group-photo');

            Route::get('create/{gallery_id}', 'SubgalleryDashboardController@create')->name('create');
            Route::post('store/{gallery_id}', 'SubgalleryDashboardController@store')->name('store');
            Route::get('add-photo/{sub_gallery_id}', 'SubgalleryDashboardController@addPhoto')->name('add.photo');
            Route::post('store-photo/{gallery_id}', 'SubgalleryDashboardController@storePhoto')->name('store.photo');
            Route::get('photo-converting-statuses/{sub_gallery_id}', 'SubgalleryDashboardController@getSubgalleryPhotosConvertingStatuses')->name('get-photo-converting-statuses');
        });

    });
    Route::resource('gallery', 'GalleryDashboardController', ['except' => ['create', 'store']]);

    /*
        * Schools
        */
    Route::resource('schools', 'SchoolDashboardController');

    /*
     * Seasons
     */
    Route::group([
        'prefix' => 'schools/{school}',
        'as' => 'schools.',
    ], function () {
        Route::resource('seasons', 'SeasonDashboardController');
    });
    Route::group([
        'prefix' => 'season-export',
        'as' => 'season-export.',
    ], function () {
        Route::post('{season}/zip/preparing-start', 'SeasonDashboardController@zipPreparingStart')->name('zip.preparing-start');
        Route::get('{season}/zip/preparing-status', 'SeasonDashboardController@zipPreparingStatus')->name('zip.preparing-status');
        Route::get('{season}/zip/preparing-choice', 'SeasonDashboardController@zipPreparingChoice')->name('zip.preparing-choice');
    });

    Route::group([
        'middleware' => 'admin',
    ], function () {

        /*
        * Settings
        */
        Route::group([
            'prefix' => 'settings',
            'as' => 'settings.',
        ], function () {
            Route::get('main', 'SettingsDashboardController@settingsPage')->name('main');
            Route::put('main', 'SettingsDashboardController@updateSettings')->name('main.update');
            Route::put('watermark', 'SettingsDashboardController@watermarkUpdate')->name('watermark.update');
            Route::get('watermark', 'SettingsDashboardController@watermark')->name('watermark');
            Route::get('specification', 'SettingsDashboardController@specification')->name('specification');
            Route::put('specification', 'SettingsDashboardController@specificationUpdate')->name('specification.update');
            Route::get('text', 'SettingsDashboardController@text')->name('text');
            Route::put('text', 'SettingsDashboardController@textUpdate')->name('text.update');
            Route::get('tax', 'SettingsDashboardController@tax')->name('tax');
            Route::put('tax', 'SettingsDashboardController@taxUpdate')->name('tax.update');
            Route::get('group-photos', 'SettingsDashboardController@groupPhotos')->name('group-photos');
            Route::put('group-photos', 'SettingsDashboardController@groupPhotosUpdate')->name('group-photos.update');
        });

        /*
         * Products
         */
        Route::resources([
            'products' => 'ProductDashboardController',
        ]);
        Route::group([
            'prefix' => 'products',
            'as' => 'products.',
        ], function () {
            Route::get('{product}/copy', 'ProductDashboardController@copy')->name('copy');
            Route::get('create/{type}', 'ProductDashboardController@createByType')->name('create.by-type');
        });

        /*
        * Sizes
        */
        Route::resource('sizes', 'SizeDashboardController', ['except' => ['show']]);
        Route::resource('combinations', 'SizeCombinationDashboardController');
        Route::group([
            'prefix' => 'combinations',
            'as' => 'combinations.',
        ], function () {
            Route::group([
                'prefix' => 'sizes',
                'as' => 'sizes.',
            ], function () {
                Route::get('add/{combination_id}', [
                    'as' => 'add',
                    'uses' => 'SizeCombinationDashboardController@getPopupForAddingSizes',
                ]);
                Route::post('save/{combination_id}/{size_id}', [
                    'as' => 'save',
                    'uses' => 'SizeCombinationDashboardController@addSize',
                ]);
                Route::delete('remove/{combination_id}/{size_id}', [
                    'as' => 'remove',
                    'uses' => 'SizeCombinationDashboardController@removeSize',
                ]);
                Route::get('edit/{combination_id}/{size_id}', [
                    'as' => 'edit',
                    'uses' => 'SizeCombinationDashboardController@getPopupFotEditSize',
                ]);
                Route::put('update/{combination_id}/{size_id}', [
                    'as' => 'update',
                    'uses' => 'SizeCombinationDashboardController@updateSize',
                ]);
            });
        });

        /*
       * Price lists
       */
        Route::resources([
            'price-lists' => 'PriceListDashboardController',
        ]);
        Route::group([
            'prefix' => 'price-lists',
            'as' => 'price-lists.',
        ], function () {
            Route::post('{id}/copy', 'PriceListDashboardController@copy')->name('copy');

            /*
             * Add, remove and edit products in price list
             */
            Route::group([
                'prefix' => 'addon',
                'as' => 'addon.',
            ], function () {
                Route::get('list/{price_list_id}', [
                    'as' => 'list',
                    'uses' => 'PriceListProductsDashboardController@getAddonsTable',
                ]);
                Route::get('add/{price_list_id}', [
                    'as' => 'add',
                    'uses' => 'PriceListProductsDashboardController@getPopupForAddingAddons',
                ]);
                Route::post('save/{price_list_id}/{product_id}', [
                    'as' => 'save',
                    'uses' => 'PriceListProductsDashboardController@addAddon',
                ]);
                Route::delete('remove/{price_list_id}/{product_id}', [
                    'as' => 'remove',
                    'uses' => 'PriceListProductsDashboardController@removeAddon',
                ]);
                Route::get('edit/{price_list_id}/{product_id}', [
                    'as' => 'edit',
                    'uses' => 'PriceListProductsDashboardController@getPopupFotEditAddon',
                ]);
                Route::put('update/addon/{price_list_id}/{product_id}', [
                    'as' => 'update',
                    'uses' => 'PriceListProductsDashboardController@updateAddon',
                ]);
            });

            /*
            * Add, remove and edit packages in price list
            */
            Route::group([
                'prefix' => 'package',
                'as' => 'package.',
            ], function () {
                Route::get('list/{price_list_id}', [
                    'as' => 'list',
                    'uses' => 'PriceListPackagesDashboardController@getPackagesTable',
                ]);
                Route::get('add/{price_list_id}', [
                    'as' => 'add',
                    'uses' => 'PriceListPackagesDashboardController@getPopupForAddingPackages',
                ]);
                Route::post('save/{price_list_id}/{package_id}', [
                    'as' => 'save',
                    'uses' => 'PriceListPackagesDashboardController@addPackage',
                ]);
                Route::delete('remove/{price_list_id}/{package_id}', [
                    'as' => 'remove',
                    'uses' => 'PriceListPackagesDashboardController@removePackage',
                ]);
                Route::get('edit/{price_list_id}/{package_id}', [
                    'as' => 'edit',
                    'uses' => 'PriceListPackagesDashboardController@getPopupFotEditPackage',
                ]);
                Route::put('update/{price_list_id}/{package_id}', [
                    'as' => 'update',
                    'uses' => 'PriceListPackagesDashboardController@updatePackage',
                ]);
            });
        });

        /*
        * Packages
        */
        Route::resources([
            'packages' => 'PackageDashboardController',
        ]);
        /*
         * Add, remove and edit products in packages
         */
        Route::group([
            'prefix' => 'packages',
            'as' => 'packages.',
        ], function () {
            Route::get('products/{package_id}', [
                'as' => 'products',
                'uses' => 'PackageDashboardController@getProductsTable',
            ]);
            Route::post('products/{package_id}', [
                'as' => 'products-post',
                'uses' => 'PackageDashboardController@getProductsTable',
            ]);
            Route::get('{packages}/copy', [
                'as' => 'copy',
                'uses' => 'PackageDashboardController@copy',
            ]);
            Route::get('add/product/{id}', [
                'as' => 'add-product',
                'uses' => 'PackageDashboardController@getPopupForAddingProducts',
            ]);
            Route::post('save/product/{package_id}/{product_id}', [
                'as' => 'save-product',
                'uses' => 'PackageDashboardController@addProduct',
            ]);
            Route::delete('remove/product/{package_id}/{product_id}', [
                'as' => 'remove-product',
                'uses' => 'PackageDashboardController@removeProduct',
            ]);
        });

        /*
        * Promo codes
        */
        Route::resource('promo-codes', 'PromoCodeDashboardController', ['except' => ['show']]);

        /*
         * Orders
         */
        Route::resource('orders', 'OrderDashboardController', ['except' => ['create', 'store', 'edit']]);
        Route::group([
            'prefix' => 'orders',
            'as' => 'orders.',
        ], function () {
            Route::post('/', 'OrderDashboardController@index')->name('index');
            Route::get('all-photos-for-print/download', 'OrderDashboardController@downloadPhotosForPrintFromAllOrders')->name('all-photos-for-print.download');
            Route::get('{order}/send-order-details', 'OrderDashboardController@sendOrderDetails')->name('send-order-details');
            Route::get('{order}/payment-status/edit', 'OrderDashboardController@editPaymentStatus')->name('payment-status.edit');
            Route::put('{order}/payment-status', 'OrderDashboardController@updatePaymentStatus')->name('payment-status.update');
            Route::get('{order}/status/edit', 'OrderDashboardController@editStatus')->name('status.edit');
            Route::put('{order}/status', 'OrderDashboardController@updateStatus')->name('status.update');
            Route::get('{order}/customer/edit', 'OrderDashboardController@editCustomerDetails')->name('customer.edit');
            Route::put('{order}/customer', 'OrderDashboardController@updateCustomerDetails')->name('customer.update');
            Route::get('{order}/details/download', 'OrderDashboardController@downloadOrderDetails')->name('details.download');

            Route::get('{order}/photos-for-print/download', 'OrderDashboardController@downloadPhotosForPrintByOrder')->name('photos-for-print.download');

            Route::post('{order}/zip/preparing-start', 'OrderDashboardController@zipPreparingStart')->name('zip.preparing-start');
            Route::get('{order}/zip/preparing-status', 'OrderDashboardController@zipPreparingStatus')->name('zip.preparing-status');
            Route::get('{order}/zip/digital/preparing-status', 'OrderDashboardController@zipDigitalPreparingStatus')->name('zip.digital.preparing-status');


            Route::get('{order}/promo-code/edit', 'OrderDashboardController@editPromoCode')->name('promo-code.edit');
            Route::put('{order}/promo-code', 'OrderDashboardController@updatePromoCode')->name('promo-code.update');
            Route::get('{order}/addon', 'OrderDashboardController@addAddon')->name('addon.add');
            Route::post('{order}/addon', 'OrderDashboardController@saveAddon')->name('addon.save');
            Route::get('{order}/package', 'OrderDashboardController@addPackage')->name('package.add');
            Route::post('{order}/package', 'OrderDashboardController@savePackage')->name('package.save');
            Route::post('{order}/item/{item_id}/delete', 'OrderDashboardController@deleteItem')->name('item.delete');
            Route::get('{order}/item/{item_id}/count', 'OrderDashboardController@editQuantity')->name('item.count.edit');
            Route::put('{order}/item/{item_id}/count', 'OrderDashboardController@updateQuantity')->name('item.count.update');
            Route::get('{order}/item/{item_id}/size', 'OrderDashboardController@editSize')->name('item.size.edit');
            Route::put('{order}/item/{item_id}/size', 'OrderDashboardController@updateSize')->name('item.size.update');
            Route::get('{order}/item/{item_id}/image', 'OrderDashboardController@editImage')->name('item.image.edit');
            Route::put('{order}/item/{item_id}/image', 'OrderDashboardController@updateImage')->name('item.image.update');
        });

        /*
        * Carts
        */
        Route::resource('carts', 'CartDashboardController', ['only' => ['index', 'show', 'destroy']]);
        Route::post('carts', 'CartDashboardController@index')->name('carts.index');
    });
});

/*
 * Auth
 */
Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

/**
 * All routes for Angular app
 */
Route::get('/', 'OrderController@mainPage')->name('main-page');

Route::post('check-code', [
    'as' => 'check_code',
    'uses' => 'MainController@index',
]);
Route::get('recode/{code}', 'MainController@recode');

Route::view('app/{id}', 'app.index')->name('main');
Route::view('app/{id}/categories', 'app.index')->name('gallery');
Route::view('app/{id}/gallery', 'app.index')->name('subgallery');
Route::get('data/gallery/{code}', 'MainController@gallery')->name('get-gallery');
Route::get('data/subgallery/{code}', 'MainController@subGallery')->name('get-subgallery');
Route::get('data/get/price-list/{sub_gallery_id}', 'MainController@priceList')->name('get-price_list');

/*
 * Cart
 */
Route::group([
    'prefix' => 'cart',
], function () {
    Route::post('add', 'CartController@addItem');
    Route::get('get', 'CartController@getCart');
    Route::get('clear', 'CartController@cartClear');
    Route::get('image-crop/{cart_item_id}', 'CartController@updateCartItemCropInfo');
    Route::get('remove/{cart_item_id}', 'CartController@removeItem');
//    Route::post('update/{cart_item_id}', 'CartController@updateItem');
    Route::post('update-quantity/{cart_item_id}', 'CartController@updateQty');
    Route::post('update-image-info/{cart_item_id}', 'CartController@updateImageOrSize');
    Route::post('update-crop-info/{cart_item_id}', 'CartController@updateCropInfo');
    Route::get('get-free-gift-info', 'CartController@getFreeGiftInfo');
    Route::post('update-free-gift-info', 'CartController@updateFreeGiftInfo');
});

/*
 * Order
 */
Route::group([
    'prefix' => 'order',
], function () {
    Route::post('create', 'OrderController@makeOrder');
    Route::get('get/{order_id}', 'OrderController@getOrderPage')->name('order_page');
    Route::get('get_email/{order_id}', 'OrderController@getOrderEmailPage')->name('order_email');
    Route::post('promocode', 'OrderController@checkIsPromoCodeValid');
    Route::get('get/{order_id}/photos', 'OrderController@getDownloadablePhotos')->name('downloadable-photos-page');
    Route::get('get/{photo}/photo', 'OrderController@getDownloadablePhoto')->name('downloadable-photo-page');

    Route::post('set-gift', 'OrderController@setGiftToOrder')->name('set-free-gift');

    Route::get('get-free-gift/{order_id}', 'OrderController@getFormForGettingGift')->name('get-free-gift');
});
    // used for payments
    Route::get('order/card', 'OrderController@getPayForm');
    Route::post('order/pay', 'OrderController@sendPaymentRequest')->name('pay');
    Route::get('order/card/{order_hash}', 'OrderController@getPayFormForUnpaidOrder')->name('payment-page');

/*
* Customer
*/
Route::group([
    'prefix' => 'customer',
], function () {
    Route::post('create', 'MainController@createPotentialCustomer');
});
