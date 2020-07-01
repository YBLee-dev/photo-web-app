<?php

use Illuminate\Database\Seeder;

class SizeCombinationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('size_combinations')->delete();
        
        \DB::table('size_combinations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '1 - 8x10',
                'created_at' => '2019-06-25 09:46:29',
                'updated_at' => '2019-06-25 09:46:29',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '2 - 5x7',
                'created_at' => '2019-06-25 09:46:44',
                'updated_at' => '2019-06-25 09:46:44',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Set of 8 wallets',
                'created_at' => '2019-06-25 09:47:13',
                'updated_at' => '2019-06-25 09:47:13',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '2 - 3.5x5 and 4 - Wallets',
                'created_at' => '2019-06-25 09:47:47',
                'updated_at' => '2019-06-25 09:49:56',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '4 - 3.5x5',
                'created_at' => '2019-06-25 09:48:02',
                'updated_at' => '2019-06-25 09:48:02',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => '1 - 5x7 and 4 - Wallets',
                'created_at' => '2019-06-25 09:48:26',
                'updated_at' => '2019-06-25 09:48:26',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => '1 - 5x7 and 2 - 3.5x5',
                'created_at' => '2019-06-25 09:48:52',
                'updated_at' => '2019-06-25 09:48:52',
            ),
        ));
        
        
    }
}