<?php

use Illuminate\Database\Seeder;

class PackagePriceListTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('package_price_list')->delete();
        
        \DB::table('package_price_list')->insert(array (
            0 => 
            array (
                'id' => 29,
                'package_id' => 11,
                'price_list_id' => 11,
                'price' => 27.0,
                'created_at' => '2019-07-29 14:22:06',
                'updated_at' => '2019-07-29 14:22:06',
            ),
            1 => 
            array (
                'id' => 30,
                'package_id' => 12,
                'price_list_id' => 11,
                'price' => 59.0,
                'created_at' => '2019-07-29 14:22:14',
                'updated_at' => '2019-07-29 14:22:14',
            ),
            2 => 
            array (
                'id' => 31,
                'package_id' => 13,
                'price_list_id' => 11,
                'price' => 40.0,
                'created_at' => '2019-07-29 14:22:19',
                'updated_at' => '2019-07-29 14:22:19',
            ),
            3 => 
            array (
                'id' => 32,
                'package_id' => 14,
                'price_list_id' => 12,
                'price' => 0.0,
                'created_at' => '2019-07-29 14:26:46',
                'updated_at' => '2019-07-29 14:26:46',
            ),
            4 => 
            array (
                'id' => 33,
                'package_id' => 11,
                'price_list_id' => 13,
                'price' => 0.01,
                'created_at' => '2019-07-29 14:28:10',
                'updated_at' => '2019-07-29 14:28:10',
            ),
            5 => 
            array (
                'id' => 34,
                'package_id' => 13,
                'price_list_id' => 13,
                'price' => 0.02,
                'created_at' => '2019-07-29 14:28:16',
                'updated_at' => '2019-07-29 14:28:16',
            ),
            6 => 
            array (
                'id' => 35,
                'package_id' => 12,
                'price_list_id' => 13,
                'price' => 0.03,
                'created_at' => '2019-07-29 14:28:19',
                'updated_at' => '2019-07-29 14:28:19',
            ),
            7 => 
            array (
                'id' => 36,
                'package_id' => 15,
                'price_list_id' => 11,
                'price' => 65.0,
                'created_at' => '2019-07-30 19:54:39',
                'updated_at' => '2019-07-30 19:54:39',
            ),
            8 => 
            array (
                'id' => 40,
                'package_id' => 18,
                'price_list_id' => 11,
                'price' => 85.0,
                'created_at' => '2019-08-02 14:01:17',
                'updated_at' => '2019-08-02 14:01:17',
            ),
            9 => 
            array (
                'id' => 41,
                'package_id' => 17,
                'price_list_id' => 11,
                'price' => 65.0,
                'created_at' => '2019-08-02 14:01:18',
                'updated_at' => '2019-08-02 14:01:18',
            ),
        ));
        
        
    }
}