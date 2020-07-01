<?php

use Illuminate\Database\Seeder;

class PackageProductTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('package_product')->delete();
        
        \DB::table('package_product')->insert(array (
            0 => 
            array (
                'id' => 59,
                'product_id' => 20,
                'package_id' => 11,
                'created_at' => '2019-07-29 13:49:09',
                'updated_at' => '2019-07-29 13:49:09',
            ),
            1 => 
            array (
                'id' => 60,
                'product_id' => 22,
                'package_id' => 11,
                'created_at' => '2019-07-29 13:49:22',
                'updated_at' => '2019-07-29 13:49:22',
            ),
            2 => 
            array (
                'id' => 61,
                'product_id' => 26,
                'package_id' => 12,
                'created_at' => '2019-07-29 14:06:59',
                'updated_at' => '2019-07-29 14:06:59',
            ),
            3 => 
            array (
                'id' => 62,
                'product_id' => 27,
                'package_id' => 12,
                'created_at' => '2019-07-29 14:07:07',
                'updated_at' => '2019-07-29 14:07:07',
            ),
            4 => 
            array (
                'id' => 63,
                'product_id' => 28,
                'package_id' => 12,
                'created_at' => '2019-07-29 14:07:14',
                'updated_at' => '2019-07-29 14:07:14',
            ),
            5 => 
            array (
                'id' => 64,
                'product_id' => 29,
                'package_id' => 12,
                'created_at' => '2019-07-29 14:07:23',
                'updated_at' => '2019-07-29 14:07:23',
            ),
            6 => 
            array (
                'id' => 66,
                'product_id' => 19,
                'package_id' => 13,
                'created_at' => '2019-07-29 14:12:07',
                'updated_at' => '2019-07-29 14:12:07',
            ),
            7 => 
            array (
                'id' => 67,
                'product_id' => 25,
                'package_id' => 13,
                'created_at' => '2019-07-29 14:12:21',
                'updated_at' => '2019-07-29 14:12:21',
            ),
            8 => 
            array (
                'id' => 68,
                'product_id' => 21,
                'package_id' => 13,
                'created_at' => '2019-07-29 14:12:29',
                'updated_at' => '2019-07-29 14:12:29',
            ),
            9 => 
            array (
                'id' => 77,
                'product_id' => 34,
                'package_id' => 15,
                'created_at' => '2019-08-02 14:01:38',
                'updated_at' => '2019-08-02 14:01:38',
            ),
            10 => 
            array (
                'id' => 79,
                'product_id' => 26,
                'package_id' => 18,
                'created_at' => '2019-08-02 14:03:49',
                'updated_at' => '2019-08-02 14:03:49',
            ),
            11 => 
            array (
                'id' => 80,
                'product_id' => 27,
                'package_id' => 18,
                'created_at' => '2019-08-02 14:03:50',
                'updated_at' => '2019-08-02 14:03:50',
            ),
            12 => 
            array (
                'id' => 81,
                'product_id' => 28,
                'package_id' => 18,
                'created_at' => '2019-08-02 14:03:51',
                'updated_at' => '2019-08-02 14:03:51',
            ),
            13 => 
            array (
                'id' => 82,
                'product_id' => 29,
                'package_id' => 18,
                'created_at' => '2019-08-02 14:03:51',
                'updated_at' => '2019-08-02 14:03:51',
            ),
            14 => 
            array (
                'id' => 83,
                'product_id' => 31,
                'package_id' => 18,
                'created_at' => '2019-08-02 14:03:53',
                'updated_at' => '2019-08-02 14:03:53',
            ),
            15 => 
            array (
                'id' => 84,
                'product_id' => 32,
                'package_id' => 18,
                'created_at' => '2019-08-02 14:03:55',
                'updated_at' => '2019-08-02 14:03:55',
            ),
            16 => 
            array (
                'id' => 85,
                'product_id' => 34,
                'package_id' => 18,
                'created_at' => '2019-08-02 14:04:07',
                'updated_at' => '2019-08-02 14:04:07',
            ),
            17 => 
            array (
                'id' => 86,
                'product_id' => 35,
                'package_id' => 17,
                'created_at' => '2019-08-02 14:53:23',
                'updated_at' => '2019-08-02 14:53:23',
            ),
        ));
        
        
    }
}