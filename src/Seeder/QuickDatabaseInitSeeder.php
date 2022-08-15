<?php

namespace Lazyou\Quick\Seeder;

use Illuminate\Database\Seeder;
use Lazyou\Quick\Models\QuickPermission;
use Lazyou\Quick\Models\QuickUser;

class QuickDatabaseInitSeeder extends Seeder
{
    public function run()
    {
        $this->seedQuickUser();
        $this->seedPermissions();
    }

    protected function seedQuickUser()
    {
        $adminUser = QuickUser::query()->where('name', 'admin')->first();
        if (is_null($adminUser)) {
            QuickUser::query()
                ->create([
                    'name' => 'admin',
                    'email' => 'admin@qq.com',
                    'password' => bcrypt('admin123'),
                    'status' => 1,
                    'is_admin' => 1,
                ]);
        }
    }

    protected function seedPermissions()
    {
        $permissions = [
            ["id" => 1, "user_id" => 0, "parent_id" => 0, "name" => "首页", "url" => "/admin/home", "icon" => null, "as" => null, "controller" => null, "type" => 1, "deep" => 1, "sort" => 1, "status" => 1 ],
            ["id" => 2, "user_id" => 0, "parent_id" => 0, "name" => "系统管理", "url" => "/admin/system", "icon" => null, "as" => null, "controller" => null, "type" => 1, "deep" => 1, "sort" => 2, "status" => 1 ],
            ["id" => 3, "user_id" => 0, "parent_id" => 2, "name" => "用户管理", "url" => "/admin/user", "icon" => null, "as" => null, "controller" => null, "type" => 1, "deep" => 2, "sort" => 1, "status" => 1 ],
            ["id" => 4, "user_id" => 0, "parent_id" => 2, "name" => "菜单管理", "url" => "/admin/menu", "icon" => null, "as" => null, "controller" => null, "type" => 1, "deep" => 2, "sort" => 2, "status" => 1 ],
            ["id" => 5, "user_id" => 0, "parent_id" => 2, "name" => "权限管理", "url" => "/admin/permission", "icon" => null, "as" => null, "controller" => null, "type" => 1, "deep" => 2, "sort" => 3, "status" => 1 ],
            ["id" => 6, "user_id" => 0, "parent_id" => 2, "name" => "角色管理", "url" => "/admin/role", "icon" => null, "as" => null, "controller" => null, "type" => 1, "deep" => 2, "sort" => 4, "status" => 1 ],
            ["id" => 7, "user_id" => 0, "parent_id" => 2, "name" => "操作日志", "url" => "/admin/log", "icon" => null, "as" => null, "controller" => null, "type" => 1, "deep" => 2, "sort" => 5, "status" => 1 ],
            ["id" => 10, "user_id" => 0, "parent_id" => 1, "name" => "首页", "url" => "/admin/home", "icon" => null, "as" => "admin.home.index", "controller" => "App\Http\Controllers\HomeController@index", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 11, "user_id" => 0, "parent_id" => 3, "name" => "用户列表", "url" => "/admin/user", "icon" => null, "as" => "admin.user.index", "controller" => "App\Http\Controllers\UserController@index", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 12, "user_id" => 0, "parent_id" => 3, "name" => "用户编辑", "url" => "/admin/user", "icon" => null, "as" => "admin.user.edit", "controller" => "App\Http\Controllers\UserController@edit", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 13, "user_id" => 0, "parent_id" => 3, "name" => "用户删除", "url" => "/admin/user/{id}", "icon" => null, "as" => "admin.user.delete", "controller" => "App\Http\Controllers\UserController@delete", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 14, "user_id" => 0, "parent_id" => 4, "name" => "菜单列表", "url" => "/admin/menu", "icon" => null, "as" => "admin.menu.index", "controller" => "App\Http\Controllers\MenuController@index", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 15, "user_id" => 0, "parent_id" => 4, "name" => "菜单编辑", "url" => "/admin/menu", "icon" => null, "as" => "admin.menu.edit", "controller" => "App\Http\Controllers\MenuController@edit", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 16, "user_id" => 0, "parent_id" => 4, "name" => "菜单删除", "url" => "/admin/menu/{id}", "icon" => null, "as" => "admin.menu.delete", "controller" => "App\Http\Controllers\MenuController@delete", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 17, "user_id" => 0, "parent_id" => 5, "name" => "权限列表", "url" => "/admin/permission", "icon" => null, "as" => "admin.permission.index", "controller" => "App\Http\Controllers\PermissionController@index", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 18, "user_id" => 0, "parent_id" => 5, "name" => "权限编辑", "url" => "/admin/permission", "icon" => null, "as" => "admin.permission.edit", "controller" => "App\Http\Controllers\PermissionController@edit", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 19, "user_id" => 0, "parent_id" => 6, "name" => "角色列表", "url" => "/admin/role", "icon" => null, "as" => "admin.role.index", "controller" => "App\Http\Controllers\RoleController@index", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 25, "user_id" => 0, "parent_id" => 6, "name" => "角色编辑", "url" => "/admin/role", "icon" => null, "as" => "admin.role.edit", "controller" => "App\Http\Controllers\RoleController@edit", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 26, "user_id" => 0, "parent_id" => 6, "name" => "角色删除", "url" => "/admin/role/{id}", "icon" => null, "as" => "admin.role.delete", "controller" => "App\Http\Controllers\RoleController@delete", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1 ],
            ["id" => 27, "user_id" => 0, "parent_id" => 7, "name" => "日志列表", "url" => "/admin/log", "icon" => null, "as" => "admin.log.index", "controller" => "App\Http\Controllers\OperationLogController@index", "type" => 2, "deep" => 0, "sort" => 0, "status" => 1],
        ];

        foreach ($permissions as $permission) {
            QuickPermission::query()->firstOrCreate(['id' => $permission['id']], $permission);
        }
    }
}
