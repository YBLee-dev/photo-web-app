<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoAbleForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photo_able', function (Blueprint $table) {
            $table->integer('photo_id')->unsigned()->change();

            $table->foreign('photo_id')->references('id')->on('photos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photo_able', function (Blueprint $table) {
            $table->dropForeign('photo_able_photo_id_foreign');
        });
    }
}
