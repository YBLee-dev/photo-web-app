<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(UserTableSeeder::class);

        $this->call(ProductsTableSeeder::class);
        $this->call(PackagesTableSeeder::class);
        $this->call(PackageProductTableSeeder::class);

        $this->call(PriceListsTableSeeder::class);
        $this->call(PackagePriceListTableSeeder::class);
        $this->call(PriceListProductTableSeeder::class);

        $this->call(SizesTableSeeder::class);
        $this->call(SizeCombinationsTableSeeder::class);
        $this->call(SizeSizeCombinationTableSeeder::class);

        $this->call(ProductSizeTableSeeder::class);

        $this->call(PromoCodesTableSeeder::class);

        $this->call(EmailNotificationsTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
    }
}
