<?php

use Illuminate\Database\Seeder;

class SizesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sizes')->delete();
        
        \DB::table('sizes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Wallet',
                'width' => 2.5,
                'height' => 3.5,
                'created_at' => '2019-05-17 12:35:13',
                'updated_at' => '2019-07-04 13:40:47',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '3R',
                'width' => 3.5,
                'height' => 5.0,
                'created_at' => '2019-05-17 12:35:23',
                'updated_at' => '2019-06-24 12:37:01',
            ),
            2 => 
            array (
                'id' => 4,
                'name' => '5R',
                'width' => 5.0,
                'height' => 7.0,
                'created_at' => '2019-05-29 15:39:54',
                'updated_at' => '2019-06-24 12:37:27',
            ),
            3 => 
            array (
                'id' => 9,
                'name' => '8R',
                'width' => 8.0,
                'height' => 10.0,
                'created_at' => '2019-05-29 15:40:11',
                'updated_at' => '2019-06-24 12:37:56',
            ),
        ));
        
        
    }
}