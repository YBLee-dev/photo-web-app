<?php

use Illuminate\Database\Seeder;

class PriceListsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('price_lists')->delete();
        
        \DB::table('price_lists')->insert(array (
            0 => 
            array (
                'id' => 11,
                'name' => 'Regular Price List - Official',
                'created_at' => '2019-07-29 14:18:38',
                'updated_at' => '2019-07-29 14:18:38',
            ),
            1 => 
            array (
                'id' => 12,
                'name' => 'Staff Pricelist',
                'created_at' => '2019-07-29 14:26:32',
                'updated_at' => '2019-07-29 14:26:32',
            ),
            2 => 
            array (
                'id' => 13,
                'name' => 'Penny Price List',
                'created_at' => '2019-07-29 14:27:43',
                'updated_at' => '2019-07-29 14:27:43',
            ),
        ));
        
        
    }
}