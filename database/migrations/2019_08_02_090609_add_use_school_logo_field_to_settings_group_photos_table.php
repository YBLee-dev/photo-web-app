<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUseSchoolLogoFieldToSettingsGroupPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_group_photos', function (Blueprint $table) {
            $table->boolean('use_school_logo')->nullable();
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
            $table->dropColumn('use_school_logo');
        });
    }
}
