<?php

use App\Users\Roles\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_employee = new Role();
        $role_employee->name = 'Admin';
        $role_employee->description = 'Super admin';
        $role_employee->save();

        $role_manager = new Role();
        $role_manager->name = 'Photographer';
        $role_manager->description = 'Photographer user';
        $role_manager->save();
    }
}
