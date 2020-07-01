<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchoolPhotoSettingsToSettingsGroupPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_group_photos', function (Blueprint $table) {
            $table->integer('school_name_font_size_school_photo');
            $table->integer('year_font_size_school_photo');
            $table->integer('name_font_size_school_photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings_group_photos', function (Blueprint $table) {
            $table->dropColumn('school_name_font_size_school_photo');
            $table->dropColumn('year_font_size_school_photo');
            $table->dropColumn('name_font_size_school_photo');
        });
    }
}
