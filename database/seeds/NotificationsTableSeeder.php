<?php

use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('notifications')->delete();
        
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 2,
                'event' => 'App\\Events\\ConfirmationPaymentEvent',
                'type' => 'mail',
                'data_id' => '2',
                'created_at' => '2019-07-15 10:11:33',
                'updated_at' => '2019-07-15 10:11:33',
            ),
            1 => 
            array (
                'id' => 3,
                'event' => 'App\\Events\\NewPaymentEvent',
                'type' => 'mail',
                'data_id' => '3',
                'created_at' => '2019-07-15 10:28:49',
                'updated_at' => '2019-07-15 10:28:49',
            ),
        ));
        
        
    }
}