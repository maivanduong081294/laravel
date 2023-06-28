<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Group::factory()->count(5)->sequence(
            ['name' => 'Administrator'],
            ['name' => 'Manager'],
            ['name' => 'Saler'],
            ['name' => 'Staff'],
            ['name' => 'Subscriber'],
        )->create();
    }
}
