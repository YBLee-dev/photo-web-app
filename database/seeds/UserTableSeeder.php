<?php

use App\Users\Roles\Role;
use App\Users\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = Role::where('name', 'admin')->first();
        $role_photographer  = Role::where('name', 'photographer')->first();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
            'status' => true,
            'role_id' => $role_admin->id
        ]);
        $photographer = User::create([
            'name' => 'photographer',
            'email' => 'photographer@gmail.com',
            'password' => bcrypt('photographer'),
            'status' => true,
            'role_id' => $role_photographer->id
        ]);
    }
}
