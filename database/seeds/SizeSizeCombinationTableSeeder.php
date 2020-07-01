<?php

use Illuminate\Database\Seeder;

class SizeSizeCombinationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('size_size_combination')->delete();
        
        \DB::table('size_size_combination')->insert(array (
            0 => 
            array (
                'id' => 3,
                'size_id' => 9,
                'size_combination_id' => 1,
                'quantity' => 1,
                'created_at' => '2019-06-25 12:06:35',
                'updated_at' => '2019-06-25 12:06:35',
            ),
            1 => 
            array (
                'id' => 10,
                'size_id' => 4,
                'size_combination_id' => 2,
                'quantity' => 2,
                'created_at' => '2019-06-25 12:17:25',
                'updated_at' => '2019-06-25 12:17:25',
            ),
            2 => 
            array (
                'id' => 11,
                'size_id' => 4,
                'size_combination_id' => 7,
                'quantity' => 1,
                'created_at' => '2019-06-25 12:17:48',
                'updated_at' => '2019-06-25 12:17:48',
            ),
            3 => 
            array (
                'id' => 12,
                'size_id' => 2,
                'size_combination_id' => 7,
                'quantity' => 2,
                'created_at' => '2019-06-25 12:17:52',
                'updated_at' => '2019-06-25 12:17:52',
            ),
            4 => 
            array (
                'id' => 13,
                'size_id' => 2,
                'size_combination_id' => 5,
                'quantity' => 4,
                'created_at' => '2019-06-25 12:18:07',
                'updated_at' => '2019-06-25 12:18:07',
            ),
            5 => 
            array (
                'id' => 14,
                'size_id' => 1,
                'size_combination_id' => 3,
                'quantity' => 8,
                'created_at' => '2019-06-25 16:19:22',
                'updated_at' => '2019-06-25 16:19:22',
            ),
            6 => 
            array (
                'id' => 15,
                'size_id' => 2,
                'size_combination_id' => 4,
                'quantity' => 2,
                'created_at' => '2019-06-25 16:19:40',
                'updated_at' => '2019-06-25 16:19:40',
            ),
            7 => 
            array (
                'id' => 16,
                'size_id' => 1,
                'size_combination_id' => 4,
                'quantity' => 4,
                'created_at' => '2019-06-25 16:19:52',
                'updated_at' => '2019-06-25 16:19:52',
            ),
            8 => 
            array (
                'id' => 17,
                'size_id' => 4,
                'size_combination_id' => 6,
                'quantity' => 2,
                'created_at' => '2019-06-25 16:20:08',
                'updated_at' => '2019-06-25 16:20:08',
            ),
            9 => 
            array (
                'id' => 18,
                'size_id' => 1,
                'size_combination_id' => 6,
                'quantity' => 4,
                'created_at' => '2019-06-25 16:20:09',
                'updated_at' => '2019-06-25 16:20:09',
            ),
        ));
        
        
    }
}