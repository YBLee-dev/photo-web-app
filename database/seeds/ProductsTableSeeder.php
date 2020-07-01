<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('products')->delete();
        
        \DB::table('products')->insert(array (
            0 => 
            array (
                'id' => 16,
                'type' => 'Printable',
                'name' => '1 - 5x7 and 4 - Wallets',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => '<p>1 - 5x7 and 4 - Wallets</p>',
                'created_at' => '2019-07-04 13:45:42',
                'updated_at' => '2019-07-29 13:54:14',
            ),
            1 => 
            array (
                'id' => 17,
                'type' => 'Printable',
                'name' => '2 - 5x7, 2 - 3.5, and 4 - Wallets',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => '<p>2 - 5x7, 2 - 3.5, and 4 - Wallets</p>',
                'created_at' => '2019-07-04 13:46:28',
                'updated_at' => '2019-07-29 13:57:11',
            ),
            2 => 
            array (
                'id' => 19,
                'type' => 'Printable',
                'name' => '1 - 8x10',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => '<p>1 - 8x10</p>',
                'created_at' => '2019-07-04 13:48:11',
                'updated_at' => '2019-07-29 13:55:44',
            ),
            3 => 
            array (
                'id' => 20,
                'type' => 'Printable',
                'name' => '2 - 5x7',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => '<p>2 - 5x7</p>',
                'created_at' => '2019-07-04 13:48:31',
                'updated_at' => '2019-07-29 13:53:10',
            ),
            4 => 
            array (
                'id' => 21,
                'type' => 'Printable',
                'name' => 'Set of 8 wallets',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => '<p>Set of 8 wallets</p>',
                'created_at' => '2019-07-04 13:48:57',
                'updated_at' => '2019-07-29 13:56:34',
            ),
            5 => 
            array (
                'id' => 22,
                'type' => 'Printable',
                'name' => '2 - 3.5x5 and 4 - Wallets',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => '<p>2 - 3.5x5 and 4 - Wallets</p>',
                'created_at' => '2019-07-04 13:49:16',
                'updated_at' => '2019-07-29 13:57:00',
            ),
            6 => 
            array (
                'id' => 23,
                'type' => 'Printable',
                'name' => '4 - 3.5x5',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => '<p>4 - 3.5x5</p>',
                'created_at' => '2019-07-04 13:49:29',
                'updated_at' => '2019-07-29 13:57:28',
            ),
            7 => 
            array (
                'id' => 25,
                'type' => 'Printable',
                'name' => '1 - 5x7 and 2 - 3.5x5',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => '<p>1 - 5x7 and 2 - 3.5x5</p>',
                'created_at' => '2019-07-04 13:50:03',
                'updated_at' => '2019-07-29 13:57:59',
            ),
            8 => 
            array (
                'id' => 26,
                'type' => 'Printable',
                'name' => 'Custom choice 1',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-07-29 14:02:32',
                'updated_at' => '2019-07-29 14:02:32',
            ),
            9 => 
            array (
                'id' => 27,
                'type' => 'Printable',
                'name' => 'Custom choice 2',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-07-29 14:02:59',
                'updated_at' => '2019-07-29 14:02:59',
            ),
            10 => 
            array (
                'id' => 28,
                'type' => 'Printable',
                'name' => 'Custom choice 3',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-07-29 14:03:23',
                'updated_at' => '2019-07-29 14:03:23',
            ),
            11 => 
            array (
                'id' => 29,
                'type' => 'Printable',
                'name' => 'Custom choice 4',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-07-29 14:03:46',
                'updated_at' => '2019-07-29 14:03:46',
            ),
            12 => 
            array (
                'id' => 31,
                'type' => 'Printable',
                'name' => 'Custom choice 5',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-07-30 08:24:52',
                'updated_at' => '2019-07-30 08:24:52',
            ),
            13 => 
            array (
                'id' => 32,
                'type' => 'Printable',
                'name' => 'Custom choice 6',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-07-30 08:25:20',
                'updated_at' => '2019-07-30 08:25:20',
            ),
            14 => 
            array (
                'id' => 33,
                'type' => 'Retouch',
                'name' => 'Retouching',
                'reference' => NULL,
                'default_price' => 10.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-08-02 13:56:06',
                'updated_at' => '2019-08-02 14:13:27',
            ),
            15 => 
            array (
                'id' => 34,
                'type' => 'Digital',
                'name' => 'Digital Download',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-08-02 13:56:20',
                'updated_at' => '2019-08-02 14:12:40',
            ),
            16 => 
            array (
                'id' => 35,
                'type' => 'Digital Full',
                'name' => 'Digital Download Full',
                'reference' => NULL,
                'default_price' => 11.0,
                'taxable' => 1,
                'description' => NULL,
                'created_at' => '2019-08-02 13:56:27',
                'updated_at' => '2019-08-02 14:10:34',
            ),
        ));
        
        
    }
}