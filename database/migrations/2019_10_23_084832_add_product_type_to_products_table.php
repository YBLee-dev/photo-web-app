<?php

use App\Ecommerce\Products\ProductTypesEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductTypeToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->enum('type', ProductTypesEnum::values());
        });
    }
}
