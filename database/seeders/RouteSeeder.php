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
                'title' => 'Phân quyền',
                'uri' => '/',
                'controller' => 'Permissions',
                'function' => 'index',
                'super_admin' => 1
            ],
            [
                'title' => 'Thêm phân quyền',
                'uri' => '/add',
                'controller' => 'Permissions',
                'function' => 'add',
                'super_admin' => 1
            ],
            [
                'title' => 'Sửa phân quyền',
                'uri' => '/edit/{id}',
                'controller' => 'Permissions',
                'function' => 'edit',
                'method' => 'POST,GET',
                'super_admin' => 1
            ],
            [
                'title' => 'Định tuyến',
                'uri' => '/',
                'controller' => 'Routes',
                'function' => 'index',
                'super_admin' => 1
            ],
            [
                'title' => 'Thêm định tuyến',
                'uri' => '/add',
                'controller' => 'Routes',
                'function' => 'add',
                'super_admin' => 1
            ],
            [
                'title' => 'Sửa định tuyến',
                'uri' => '/edit/{id}',
                'controller' => 'Routes',
                'function' => 'edit',
                'method' => 'POST,GET',
                'super_admin' => 1
            ],
        )->create();
    }
}
