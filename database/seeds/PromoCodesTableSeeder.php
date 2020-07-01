<?php

use Illuminate\Database\Seeder;

class PromoCodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('promo_codes')->delete();
        
        \DB::table('promo_codes')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'Test',
                'redeem_code' => 'qQFTkGyX',
                'type' => '%',
                'discount_amount' => 10.0,
                'active_from' => NULL,
                'expires_at' => NULL,
                'may_be_used' => 'Unlimited',
                'cart_total_from' => 0.0,
                'cart_total_to' => 999999.0,
                'description' => NULL,
                'status' => 'Active',
                'created_at' => '2019-06-22 06:26:52',
                'updated_at' => '2019-07-08 13:22:06',
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'New test coupon',
                'redeem_code' => 'yevhen_promo',
                'type' => '%',
                'discount_amount' => 10.0,
                'active_from' => '2019-06-23',
                'expires_at' => '2019-06-28',
                'may_be_used' => 'Once per person',
                'cart_total_from' => 0.04,
                'cart_total_to' => 999999.0,
                'description' => NULL,
                'status' => 'Expired',
                'created_at' => '2019-06-22 06:36:01',
                'updated_at' => '2019-07-08 13:22:06',
            ),
            2 => 
            array (
                'id' => 4,
                'name' => 'FREE',
                'redeem_code' => '100',
                'type' => '%',
                'discount_amount' => 100.0,
                'active_from' => NULL,
                'expires_at' => NULL,
                'may_be_used' => 'Unlimited',
                'cart_total_from' => 0.0,
                'cart_total_to' => 999999.0,
                'description' => NULL,
                'status' => 'Active',
                'created_at' => '2019-07-04 14:56:05',
                'updated_at' => '2019-07-08 13:22:06',
            ),
        ));
        
        
    }
}