<?php

use App\Ecommerce\Orders\OrderPaymentStatusEnum;
use App\Ecommerce\Orders\OrderStatusEnum;
use App\Ecommerce\Products\ProductTypesEnum;
use App\Ecommerce\PromoCodes\PromoCodeStatusTypesEnum;
use App\Ecommerce\PromoCodes\PromoCodeTypesEnum;
use App\Ecommerce\PromoCodes\PromoCodeUsedTypesEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upRoles();
        $this->upUsers();
        $this->upSchools();
        $this->upPriceLists();
        $this->upProducts();
        $this->upPriceListToProduct();
        $this->upPackages();
        $this->upPackageToProduct();
        $this->upPackageToPriceList();
        $this->upGalleries();
        $this->upSubGalleries();
        $this->upPeople();
        $this->upPromoCodes();
        $this->upOrderPagesText();
        $this->upCustomers();
        $this->upCarts();
        $this->upSizes();
        $this->upSizeCombinations();
        $this->upSizeToSizeCombination();
        $this->upCartItems();
        $this->upJobs();
        $this->upFailedJobs();
        $this->upOrders();
        $this->upCustomerToPromoCode();
        $this->upOrderItems();
        $this->upProductToSize();
        $this->upSeasons();
        $this->upSettingsGroupPhotos();
        $this->upSettings();
        $this->upProcessRecords();
        $this->upPhotos();
        $this->upPhotoAble();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::drop('process_records');
        Schema::drop('settings');
        Schema::drop('settings_group_photos');
        Schema::drop('seasons');
        Schema::drop('product_size');
        Schema::drop('order_items');
        Schema::drop('customer_promo_code');
        Schema::drop('orders');
        Schema::drop('failed_jobs');
        Schema::drop('jobs');
        Schema::drop('cart_items');
        Schema::drop('size_size_combination');
        Schema::drop('size_combinations');
        Schema::drop('sizes');
        Schema::drop('carts');
        Schema::drop('customers');
        Schema::drop('order_pages_text');
        Schema::drop('promo_codes');
        Schema::drop('people');
        Schema::drop('sub_galleries');
        Schema::drop('galleries');
        Schema::drop('package_price_list');
        Schema::drop('package_product');
        Schema::drop('packages');
        Schema::drop('price_list_product');
        Schema::drop('products');
        Schema::drop('price_lists');
        Schema::drop('schools');
        Schema::drop('users');
        Schema::drop('roles');
        Schema::drop('photos');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Create roles table
     */
    protected function upRoles()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Create users table
     */
    protected function upUsers()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('credential_password');
            $table->boolean('status')->nullable();

            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles');

            $table->string('ftp_login');
            $table->string('ftp_password');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Crate galleries table
     */
    protected function upGalleries()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->string('password');
            $table->date('deadline');

            $table->integer('school_id')->unsigned()->nullable();
            $table->integer('season_id')->unsigned()->nullable();

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('price_list_id')->unsigned()->nullable();
            $table->foreign('price_list_id')->references('id')->on('price_lists');

            $table->timestamps();
        });
    }

    /**
     * Create jobs table
     */
    protected function upJobs()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    /**
     * Create filed jobs table
     */
    protected function upFailedJobs()
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Create sub galleries table
     */
    protected function upSubGalleries()
    {
        Schema::create('sub_galleries', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('password');

            $table->boolean('available_on_class_photo')->default(true);
            $table->boolean('available_on_general_photo')->default(true);

            $table->integer('gallery_id')->unsigned();
            $table->foreign('gallery_id')->references('id')->on('galleries')->onDelete('cascade');

            $table->integer('main_photo_id');
            $table->integer('preview_image_id');

            $table->timestamps();
        });
    }

    /**
     * Create people table
     */
    protected function upPeople()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('classroom')->nullable();
            $table->boolean('graduate')->nullable();
            $table->boolean('teacher')->nullable();
            $table->string('title')->nullable();

            $table->string('school_name')->nulalble();
            $table->string('contact_email')->nulalble();

            $table->string('proof_photo_id')->nulalble();
            $table->string('cropped_face_photo_id')->nulalble();

            $table->integer('sub_gallery_id')->unsigned();
            $table->foreign('sub_gallery_id')->references('id')->on('sub_galleries')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Creat sizes table
     */
    protected function upSizes()
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->float('width');
            $table->float('height');

            $table->timestamps();
        });
    }

    /**
     * Create products table
     */
    protected function upProducts()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('type', ProductTypesEnum::values());
            $table->string('name');
            $table->string('reference')->nullable();
            $table->float('default_price');
            $table->boolean('taxable');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Create price lists table
     */
    protected function upPriceLists()
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Create packages table
     */
    protected function upPackages()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');

            $table->string('image')->nullable();
            $table->string('name');
            $table->string('reference_name')->nullable();
            $table->float('price', 8, 2);
            $table->boolean('taxable');
            $table->integer('limit_poses')->default(0);
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Create price list product table
     */
    protected function upPriceListToProduct()
    {
        Schema::create('price_list_product', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->integer('price_list_id')->unsigned()->nullable();
            $table->foreign('price_list_id')->references('id')->on('price_lists')->onDelete('cascade');

            $table->float('price');

            $table->timestamps();
        });
    }

    /**
     * Create package product table
     */
    protected function upPackageToProduct()
    {
        Schema::create('package_product', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->integer('package_id')->unsigned()->nullable();
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Create package price list table
     */
    protected function upPackageToPriceList()
    {
        Schema::create('package_price_list', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('package_id')->unsigned()->nullable();
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');

            $table->integer('price_list_id')->unsigned()->nullable();
            $table->foreign('price_list_id')->references('id')->on('price_lists')->onDelete('cascade');

            $table->float('price');

            $table->timestamps();
        });
    }

    /**
     * Create promo codes table
     */
    protected function upPromoCodes()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('redeem_code');
            $table->enum('type', PromoCodeTypesEnum::values());
            $table->float('discount_amount');
            $table->date('active_from')->nullable();
            $table->date('expires_at')->nullable();
            $table->enum('may_be_used', PromoCodeUsedTypesEnum::values());
            $table->float('cart_total_from')->nullable();
            $table->float('cart_total_to')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', PromoCodeStatusTypesEnum::values());

            $table->timestamps();
        });
    }

    /**
     * Create order pages text table
     * //todo check if this is really needed
     */
    protected function upOrderPagesText()
    {
        Schema::create('order_pages_text', function (Blueprint $table) {
            $table->increments('id');

            $table->text('delivery')->nullable();
            $table->text('successful_order')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Create customers table
     */
    protected function upCustomers()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    /**
     * Create carts table
     */
    protected function upCarts()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('total');
            $table->integer('items_count');
            $table->boolean('abandoned')->nullable();
            $table->string('session_key');

            $table->integer('sub_gallery_id')->unsigned()->nullable();
            $table->foreign('sub_gallery_id')->references('id')->on('sub_galleries');

            $table->integer('gallery_id')->unsigned()->nullable();
            $table->foreign('gallery_id')->references('id')->on('galleries');

            $table->integer('price_list_id')->unsigned()->nullable();
            $table->foreign('price_list_id')->references('id')->on('price_lists');


            $table->timestamps();
        });
    }

    /**
     * Create orders table
     */
    protected function upOrders()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('status', OrderStatusEnum::values());
            $table->enum('payment_status', OrderPaymentStatusEnum::values());

            $table->string('total');
            $table->string('subtotal');
            $table->string('discount')->nullable();
            $table->integer('items_count');
            $table->string('discount_type')->nullable();
            $table->string('discount_name')->nullable();
            $table->string('total_coupon')->nullable();

            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal');
            $table->string('country');
            $table->string('message')->nullable();
            $table->boolean('receive_promotions_by_email');
            $table->boolean('free_gift')->default(false);

            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->integer('sub_gallery_id')->unsigned()->nullable();
            $table->foreign('sub_gallery_id')->references('id')->on('sub_galleries');

            $table->integer('gallery_id')->unsigned()->nullable();
            $table->foreign('gallery_id')->references('id')->on('galleries');

            $table->integer('price_list_id')->unsigned()->nullable();
            $table->foreign('price_list_id')->references('id')->on('price_lists');

            $table->string('transaction_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Create cart items table
     */
    protected function upCartItems()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products');

            $table->string('name');
            $table->string('price');
            $table->integer('quantity');
            $table->string('sum');
            $table->string('image');

            $table->integer('size_combination_id')->unsigned()->nullable();
            $table->foreign('size_combination_id')->references('id')->on('size_combinations');

            $table->integer('cart_id')->unsigned()->nullable();
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');

            $table->integer('package_id')->unsigned()->nullable();
            $table->foreign('package_id')->references('id')->on('packages');
            $table->string('package_name')->nullable();

            $table->string('cart_item_id'); //todo check if really needed

            $table->timestamps();
        });
    }

    /**
     * Create customer promo code table
     */
    protected function upCustomerToPromoCode()
    {
        Schema::create('customer_promo_code', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->integer('promo_code_id')->unsigned()->nullable();
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Create order items table
     */
    protected function upOrderItems()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products');

            $table->string('item_id');

            $table->string('name');
            $table->string('price');
            $table->integer('quantity');
            $table->string('sum');
            $table->string('image');

            $table->integer('size_combination_id')->unsigned()->nullable();
            $table->foreign('size_combination_id')->references('id')->on('size_combinations');

            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->integer('package_id')->unsigned()->nullable();
            $table->foreign('package_id')->references('id')->on('packages');
            $table->string('package_name')->nullable();

            $table->string('crop_info_width')->nullable();
            $table->string('crop_info_height')->nullable();
            $table->string('crop_info_x')->nullable();
            $table->string('crop_info_y')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Create sizes combinations
     */
    protected function upSizeCombinations()
    {
        Schema::create('size_combinations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Create size to size combination table
     */
    protected function upSizeToSizeCombination()
    {
        Schema::create('size_size_combination', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('size_id')->unsigned()->nullable();
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');

            $table->integer('size_combination_id')->unsigned()->nullable();
            $table->foreign('size_combination_id')->references('id')->on('size_combinations')->onDelete('cascade');

            $table->integer('quantity');

            $table->timestamps();
        });
    }

    /**
     * Create product to size
     */
    protected function upProductToSize()
    {
        Schema::create('product_size', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->integer('size_id')->unsigned()->nullable();
            $table->foreign('size_id')->references('id')->on('size_combinations')->onDelete('cascade');
        });
    }

    /**
     * Create schools table
     */
    protected function upSchools()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Create seasons table
     */
    protected function upSeasons()
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');

            $table->timestamps();
        });
    }

    /**
     * Create group photos settings
     * //todo check name and if really needed nullable
     */
    protected function upSettingsGroupPhotos()
    {
        Schema::create('settings_group_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_name')->nullable();
            $table->string('year')->nullable();
            $table->string('class_name')->nullable();
            $table->string('school_logo')->nullable();
            $table->string('naming_structure')->nullable();
            $table->boolean('use_teacher_prefix')->nullable();
            $table->string('font_file')->nullable();
            $table->integer('school_name_font_size')->nullable();
            $table->integer('class_name_font_size')->nullable();
            $table->integer('year_font_size')->nullable();
            $table->integer('name_font_size')->nullable();
            $table->string('school_background')->nullable();
            $table->string('class_background')->nullable();
            $table->string('id_cards_background_portrait')->nullable();
            $table->string('id_cards_background_landscape')->nullable();
            $table->integer('id_cards_portrait_name_size')->nullable();
            $table->integer('id_cards_portrait_title_size')->nullable();
            $table->integer('id_cards_portrait_year_size')->nullable();
            $table->integer('id_cards_landscape_name_size')->nullable();
            $table->integer('id_cards_landscape_title_size')->nullable();
            $table->integer('id_cards_landscape_year_size')->nullable();

            $table->integer('season_id')->unsigned();
            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Create settings table
     */
    protected function upSettings()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');

            $table->string('admin_email')->nullable();
            $table->longText('email_signature')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Create process records table
     */
    protected function upProcessRecords()
    {
        Schema::create('process_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->integer('job_id')->nullable();
            $table->string('process');
            $table->string('scenario')->nullable();
            $table->integer('processable_id');
            $table->string('processable_type');

            $table->timestamps();
        });
    }

    /**
     * Create photos table
     */
    protected function upPhotos()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');

            // Meta data
            $table->string('original_filename')->nullable();
            $table->string('extension')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->integer('size')->nullable();

            // Technical data
            $table->string('status');
            $table->boolean('local_copy')->default(false);
            $table->boolean('remote_copy')->default(false);

            // For has one morph relations
            $table->integer('photo_able_id')->nullable();
            $table->string('photo_able_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Create table for morph many relations
     */
    protected function upPhotoAble()
    {
        Schema::create('photo_able', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('photo_id');
            $table->integer('photo_able_id');
            $table->string('photo_able_type');

            $table->timestamps();
        });
    }
}
