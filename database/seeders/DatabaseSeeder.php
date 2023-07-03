<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use Database\Seeders\GroupSeeder;
use Database\Seeders\RouteSeeder;
use Hash;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(GroupSeeder::class);

        $admin_group = Group::select('id')->where('name', 'Administrator')->limit(1)->first();
        if(!empty($admin_group)) {
            User::factory()->count(1)->sequence(
                [
                    'username' => 'admin',
                    'password' => Hash::make('123456'),
                    'fullname' => 'Administrator',
                    'email' => 'maivanduong081294@gmail.com',
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'remember_token' => md5('admin.123456'),
                    'group_id' =>  $admin_group->id,
                    'status' =>  1,
                ]
            )->create();
        }

        $this->call(RouteSeeder::class);
    }
}
