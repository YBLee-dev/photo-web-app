<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'admin_email' => 'admin@gmail.com',
                'email_signature' => '<p>Signature</p>',
                'created_at' => '2019-08-08 07:09:59',
                'updated_at' => '2019-08-08 07:09:59',
            ),
        ));
        
        
    }
}