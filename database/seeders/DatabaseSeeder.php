<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use Database\Seeders\GroupSeeder;
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

        //$this->call(GroupSeeder::class);

        $admin_group = Group::select('id')->where('name', 'Administrator')->limit(1)->first();
        if(!empty($admin_group)) {
            User::factory()->count(1)->sequence(
                [
                    'username' => 'admin',
                    'password' => '$2y$10$RJpJITMf4UvpztKmtEX1uOCl0sRAgBy7s9zKpL1Hz3pPF9gD6D6mW',//123456
                    'fullname' => 'Administrator',
                    'email' => 'maivanduong081294@gmail.com',
                    'remember_token' => md5('admin.123456'),
                    'group_id' =>  $admin_group->id,
                ]
            )->create();
        }
    }
}
