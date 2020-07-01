<?php

use Illuminate\Database\Seeder;

class PriceListProductTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('price_list_product')->delete();
        
        \DB::table('price_list_product')->insert(array (
            0 => 
            array (
                'id' => 28,
                'product_id' => 19,
                'price_list_id' => 11,
                'price' => 11.0,
                'created_at' => '2019-07-29 14:23:16',
                'updated_at' => '2019-07-29 14:23:16',
            ),
            1 => 
            array (
                'id' => 29,
                'product_id' => 20,
                'price_list_id' => 11,
                'price' => 11.0,
                'created_at' => '2019-07-29 14:23:17',
                'updated_at' => '2019-07-29 14:23:17',
            ),
            2 => 
            array (
                'id' => 30,
                'product_id' => 23,
                'price_list_id' => 11,
                'price' => 11.0,
                'created_at' => '2019-07-29 14:23:22',
                'updated_at' => '2019-07-29 14:23:22',
            ),
            3 => 
            array (
                'id' => 31,
                'product_id' => 21,
                'price_list_id' => 11,
                'price' => 11.0,
                'created_at' => '2019-07-29 14:23:26',
                'updated_at' => '2019-07-29 14:23:26',
            ),
            4 => 
            array (
                'id' => 32,
                'product_id' => 25,
                'price_list_id' => 11,
                'price' => 11.0,
                'created_at' => '2019-07-29 14:23:34',
                'updated_at' => '2019-07-29 14:23:34',
            ),
            5 => 
            array (
                'id' => 33,
                'product_id' => 16,
                'price_list_id' => 11,
                'price' => 11.0,
                'created_at' => '2019-07-29 14:23:43',
                'updated_at' => '2019-07-29 14:23:43',
            ),
            6 => 
            array (
                'id' => 34,
                'product_id' => 22,
                'price_list_id' => 11,
                'price' => 11.0,
                'created_at' => '2019-07-29 14:23:51',
                'updated_at' => '2019-07-29 14:23:51',
            ),
            7 => 
            array (
                'id' => 35,
                'product_id' => 19,
                'price_list_id' => 13,
                'price' => 0.08,
                'created_at' => '2019-07-29 14:28:51',
                'updated_at' => '2019-07-29 14:28:51',
            ),
            8 => 
            array (
                'id' => 36,
                'product_id' => 20,
                'price_list_id' => 13,
                'price' => 0.09,
                'created_at' => '2019-07-29 14:28:55',
                'updated_at' => '2019-07-29 14:28:55',
            ),
            9 => 
            array (
                'id' => 37,
                'product_id' => 23,
                'price_list_id' => 13,
                'price' => 0.1,
                'created_at' => '2019-07-29 14:29:07',
                'updated_at' => '2019-07-29 14:29:07',
            ),
            10 => 
            array (
                'id' => 38,
                'product_id' => 21,
                'price_list_id' => 13,
                'price' => 0.11,
                'created_at' => '2019-07-29 14:29:21',
                'updated_at' => '2019-07-29 14:29:21',
            ),
            11 => 
            array (
                'id' => 39,
                'product_id' => 25,
                'price_list_id' => 13,
                'price' => 0.12,
                'created_at' => '2019-07-29 14:29:47',
                'updated_at' => '2019-07-29 14:29:47',
            ),
            12 => 
            array (
                'id' => 40,
                'product_id' => 16,
                'price_list_id' => 13,
                'price' => 0.13,
                'created_at' => '2019-07-29 14:29:54',
                'updated_at' => '2019-07-29 14:29:54',
            ),
            13 => 
            array (
                'id' => 41,
                'product_id' => 22,
                'price_list_id' => 13,
                'price' => 0.14,
                'created_at' => '2019-07-29 14:30:25',
                'updated_at' => '2019-07-29 14:30:25',
            ),
            14 => 
            array (
                'id' => 42,
                'product_id' => 33,
                'price_list_id' => 11,
                'price' => 10.0,
                'created_at' => '2019-08-02 14:01:09',
                'updated_at' => '2019-08-02 14:01:09',
            ),
        ));
        
        
    }
}