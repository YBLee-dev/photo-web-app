<?php

use Illuminate\Database\Seeder;

class EmailNotificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('email_notifications')->delete();
        
        \DB::table('email_notifications')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'Send payment success to customer',
                'address' => '{{customer_email}}',
                'subject' => 'Payment Confirmation at Playful Portraits - #{{ order_id }}',
                'subject_type' => 'view',
                'body' => '<p>Dir {{customer_first_name}} {{customer_last_name}}<br />
Your payment is successful!</p>

<p>{{order_data}}</p>

<p>Once your order has been processed and shipped, we will send you an email with the shipping details.</p>

<p>{{ order_sub_data }}</p>

<p>{{&nbsp;signature }}</p>',
                'template' => 'notifier::mail.template',
                'created_at' => '2019-07-15 10:11:33',
                'updated_at' => '2019-08-02 12:37:39',
                'files' => NULL,
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'New order pay',
                'address' => '{{admin_email}}',
                'subject' => 'New payment',
                'subject_type' => 'view',
                'body' => '<p>{{order_data}}</p>

<p>{{order_sub_data}}</p>',
                'template' => 'notifier::mail.template',
                'created_at' => '2019-07-15 10:28:49',
                'updated_at' => '2019-08-08 06:39:18',
                'files' => NULL,
            ),
        ));
        
        
    }
}