<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Route::factory()->count(6)->sequence(
            [
                'title' => 'Định tuyến',
                'uri' => '/routes',
                'controller' => 'Routes',
                'function' => 'index',
                'method' => 'GET',
                'super_admin' => 1
            ],
            [
                'title' => 'Thêm định tuyến',
                'uri' => '/add',
                'controller' => 'Routes',
                'function' => 'add',
                'method' => 'POST,GET',
                'parent_id' => 4,
                'super_admin' => 1
            ],
            [
                'title' => 'Sửa định tuyến',
                'uri' => '/edit/{id}',
                'controller' => 'Routes',
                'function' => 'edit',
                'method' => 'POST,GET',
                'parent_id' => 4,
                'super_admin' => 1
            ],
            [
                'title' => 'Phân quyền',
                'uri' => '/permissions',
                'controller' => 'Permissions',
                'function' => 'index',
                'method' => 'GET',
            ],
            [
                'title' => 'Thêm phân quyền',
                'uri' => '/add',
                'controller' => 'Permissions',
                'function' => 'add',
                'method' => 'POST,GET',
                'parent_id' => 1,
            ],
            [
                'title' => 'Sửa phân quyền',
                'uri' => '/edit/{id}',
                'controller' => 'Permissions',
                'function' => 'edit',
                'method' => 'POST,GET',
                'parent_id' => 1,
            ],
        )->create();
    }
}
