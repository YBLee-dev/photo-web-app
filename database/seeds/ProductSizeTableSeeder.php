<?php

use Illuminate\Database\Seeder;

class ProductSizeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('product_size')->delete();
        
        \DB::table('product_size')->insert(array (
            0 => 
            array (
                'id' => 23,
                'product_id' => 16,
                'size_id' => 6,
            ),
            1 => 
            array (
                'id' => 25,
                'product_id' => 17,
                'size_id' => 4,
            ),
            2 => 
            array (
                'id' => 26,
                'product_id' => 19,
                'size_id' => 1,
            ),
            3 => 
            array (
                'id' => 27,
                'product_id' => 20,
                'size_id' => 2,
            ),
            4 => 
            array (
                'id' => 28,
                'product_id' => 21,
                'size_id' => 3,
            ),
            5 => 
            array (
                'id' => 29,
                'product_id' => 22,
                'size_id' => 4,
            ),
            6 => 
            array (
                'id' => 30,
                'product_id' => 23,
                'size_id' => 5,
            ),
            7 => 
            array (
                'id' => 32,
                'product_id' => 25,
                'size_id' => 7,
            ),
            8 => 
            array (
                'id' => 35,
                'product_id' => 26,
                'size_id' => 1,
            ),
            9 => 
            array (
                'id' => 36,
                'product_id' => 26,
                'size_id' => 2,
            ),
            10 => 
            array (
                'id' => 37,
                'product_id' => 26,
                'size_id' => 3,
            ),
            11 => 
            array (
                'id' => 38,
                'product_id' => 26,
                'size_id' => 4,
            ),
            12 => 
            array (
                'id' => 39,
                'product_id' => 26,
                'size_id' => 5,
            ),
            13 => 
            array (
                'id' => 40,
                'product_id' => 26,
                'size_id' => 6,
            ),
            14 => 
            array (
                'id' => 41,
                'product_id' => 26,
                'size_id' => 7,
            ),
            15 => 
            array (
                'id' => 42,
                'product_id' => 27,
                'size_id' => 1,
            ),
            16 => 
            array (
                'id' => 43,
                'product_id' => 27,
                'size_id' => 2,
            ),
            17 => 
            array (
                'id' => 44,
                'product_id' => 27,
                'size_id' => 3,
            ),
            18 => 
            array (
                'id' => 45,
                'product_id' => 27,
                'size_id' => 4,
            ),
            19 => 
            array (
                'id' => 46,
                'product_id' => 27,
                'size_id' => 5,
            ),
            20 => 
            array (
                'id' => 47,
                'product_id' => 27,
                'size_id' => 6,
            ),
            21 => 
            array (
                'id' => 48,
                'product_id' => 28,
                'size_id' => 1,
            ),
            22 => 
            array (
                'id' => 49,
                'product_id' => 28,
                'size_id' => 2,
            ),
            23 => 
            array (
                'id' => 50,
                'product_id' => 28,
                'size_id' => 3,
            ),
            24 => 
            array (
                'id' => 51,
                'product_id' => 28,
                'size_id' => 4,
            ),
            25 => 
            array (
                'id' => 52,
                'product_id' => 28,
                'size_id' => 5,
            ),
            26 => 
            array (
                'id' => 53,
                'product_id' => 28,
                'size_id' => 6,
            ),
            27 => 
            array (
                'id' => 54,
                'product_id' => 29,
                'size_id' => 1,
            ),
            28 => 
            array (
                'id' => 55,
                'product_id' => 29,
                'size_id' => 2,
            ),
            29 => 
            array (
                'id' => 56,
                'product_id' => 29,
                'size_id' => 3,
            ),
            30 => 
            array (
                'id' => 57,
                'product_id' => 29,
                'size_id' => 4,
            ),
            31 => 
            array (
                'id' => 58,
                'product_id' => 29,
                'size_id' => 5,
            ),
            32 => 
            array (
                'id' => 59,
                'product_id' => 29,
                'size_id' => 6,
            ),
            33 => 
            array (
                'id' => 60,
                'product_id' => 31,
                'size_id' => 1,
            ),
            34 => 
            array (
                'id' => 61,
                'product_id' => 31,
                'size_id' => 2,
            ),
            35 => 
            array (
                'id' => 62,
                'product_id' => 31,
                'size_id' => 3,
            ),
            36 => 
            array (
                'id' => 63,
                'product_id' => 31,
                'size_id' => 4,
            ),
            37 => 
            array (
                'id' => 64,
                'product_id' => 31,
                'size_id' => 5,
            ),
            38 => 
            array (
                'id' => 65,
                'product_id' => 31,
                'size_id' => 6,
            ),
            39 => 
            array (
                'id' => 66,
                'product_id' => 32,
                'size_id' => 1,
            ),
            40 => 
            array (
                'id' => 67,
                'product_id' => 32,
                'size_id' => 2,
            ),
            41 => 
            array (
                'id' => 68,
                'product_id' => 32,
                'size_id' => 3,
            ),
            42 => 
            array (
                'id' => 69,
                'product_id' => 32,
                'size_id' => 4,
            ),
            43 => 
            array (
                'id' => 70,
                'product_id' => 32,
                'size_id' => 5,
            ),
            44 => 
            array (
                'id' => 71,
                'product_id' => 32,
                'size_id' => 6,
            ),
        ));
        
        
    }
}