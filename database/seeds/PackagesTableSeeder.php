<?php

use Illuminate\Database\Seeder;

class PackagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('packages')->delete();
        
        \DB::table('packages')->insert(array (
            0 => 
            array (
                'id' => 11,
                'image' => '5d3ef943eeb78th_21d_Basic_Package_Final.jpg',
                'name' => 'Basic Package',
                'reference_name' => NULL,
                'price' => 27.0,
                'taxable' => 1,
                'limit_poses' => 1,
                'description' => '<p>Package Includes:<br />
1 - Pose<br />
2 - 5x7<br />
2 - 3.5x5 and 4 - Wallets<br />
1 - Personalized Class Photo<br />
1 - **FREE Portrait Calendar<br />
(**Leave Google Review for Playful Portraits)&nbsp;</p>',
                'created_at' => '2019-07-29 13:48:51',
                'updated_at' => '2019-07-29 13:48:51',
                'available_after_deadline' => 0,
            ),
            1 => 
            array (
                'id' => 12,
                'image' => '5d3efd730c67eth_720_Custom_Package_Final.jpg',
                'name' => 'Custom Package',
                'reference_name' => NULL,
                'price' => 59.0,
                'taxable' => 1,
                'limit_poses' => 5,
                'description' => '<p>Package Includes:<br />
4 - poses<br />
4 - Choose Your Own Size for Each Selection<br />
1 - *Digital Download<br />
1 - Personalized Class Photo<br />
1 - **FREE Portrait Calendar<br />
&nbsp;(**Leave Google Review for Playful Portraits)</p>

<p>* Digital Image available for download after checkout *</p>',
                'created_at' => '2019-07-29 14:06:43',
                'updated_at' => '2019-07-29 14:06:43',
                'available_after_deadline' => 0,
            ),
            2 => 
            array (
                'id' => 13,
                'image' => '5d3efe9c25000th_0fe_Midsize_Package_Final.jpg',
                'name' => 'Midsize Package',
                'reference_name' => NULL,
                'price' => 40.0,
                'taxable' => 1,
                'limit_poses' => 2,
                'description' => '<p>Package Includes:<br />
2 - Poses&nbsp;<br />
1 - 8x10<br />
1 - 5x7 and 2 - 3.5x5<br />
Set of 8 Wallets<br />
1 - Personalized Class Photo<br />
1 - **FREE Portrait Calendar<br />
(**Leave Google Review for Playful Portraits)<br />
&nbsp;</p>',
                'created_at' => '2019-07-29 14:11:40',
                'updated_at' => '2019-07-29 14:11:40',
                'available_after_deadline' => 0,
            ),
            3 => 
            array (
                'id' => 14,
                'image' => NULL,
                'name' => 'Staff Basic Package',
                'reference_name' => NULL,
                'price' => 27.0,
                'taxable' => 1,
                'limit_poses' => 1,
                'description' => '<p>Package Includes:<br />
1 - Pose<br />
2 - 5x7<br />
2 - 3.5x5 and 4 - Wallets<br />
Free Class Photo&nbsp;</p>',
                'created_at' => '2019-07-29 14:25:45',
                'updated_at' => '2019-07-29 14:25:45',
                'available_after_deadline' => 0,
            ),
            4 => 
            array (
                'id' => 15,
                'image' => '5d3fff8b07060th_d66_Digital_Package_Official.jpg',
            'name' => 'Digital Package (#1 Best Seller)',
                'reference_name' => NULL,
                'price' => 65.0,
                'taxable' => 1,
                'limit_poses' => 0,
                'description' => '<p>Package Includes:<br />
All Digital Images (High Resolution)<br />
1 - Personalized Class Photo</p>

<p>** All Individual Portraits are Immediately Available for Download After Checkout **<br />
** After the school&#39;s ordering deadline, only the Digital Package will be available**</p>',
                'created_at' => '2019-07-30 08:27:55',
                'updated_at' => '2019-07-30 08:27:55',
                'available_after_deadline' => 0,
            ),
            5 => 
            array (
                'id' => 17,
                'image' => '5d4441ea33cfath_d66_Digital_Package_Official.jpg',
            'name' => 'Digital Package Full (#1 Best Seller)',
                'reference_name' => NULL,
                'price' => 65.0,
                'taxable' => 1,
                'limit_poses' => 0,
                'description' => '<p data-original-title="" title="">Package Includes:<br data-original-title="" title="" />
All Digital Images (High Resolution)<br data-original-title="" title="" />
1 - Personalized Class Photo</p>

<p data-original-title="" title="">** All Individual Portraits are Immediately Available for Download After Checkout **<br data-original-title="" title="" />
** After the school\'s ordering deadline, only the Digital Package will be available**</p>',
                'created_at' => '2019-08-02 13:58:20',
                'updated_at' => '2019-08-02 14:00:10',
                'available_after_deadline' => 1,
            ),
            6 => 
            array (
                'id' => 18,
                'image' => '5d444219e59b2th_a2b_Gold_Package_Final.jpg',
                'name' => 'Gold Package',
                'reference_name' => NULL,
                'price' => 85.0,
                'taxable' => 1,
                'limit_poses' => 6,
                'description' => '<p>Package Includes:<br />
All Digital Images (High Resolution)<br />
6 - Choose Your Own Size for Each Selection<br />
1 - Personalized Class Photo<br />
1 - **FREE Portrait Calendar<br />
(**Leave Google Review for Playful Portraits)</p>

<p>** All Individual Portraits are Immediately Available for Download After Checkout**<br />
**Personalized Class Photo and Portrait Calendar are prints, not downloads**</p>',
                'created_at' => '2019-08-02 14:00:57',
                'updated_at' => '2019-08-02 14:00:57',
                'available_after_deadline' => 0,
            ),
        ));
        
        
    }
}