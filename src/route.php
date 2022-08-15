<?php

use Illuminate\Support\Facades\Route;
use Lazyou\Quick\Http\Controllers\Admin\QuickAuthController;
use Lazyou\Quick\Http\Controllers\Admin\QuickHomeController;
use Lazyou\Quick\Http\Controllers\Admin\QuickUserController;
use Lazyou\Quick\Http\Controllers\Admin\QuickMenuController;
use Lazyou\Quick\Http\Controllers\Admin\QuickOperationLogController;
use Lazyou\Quick\Http\Controllers\Admin\QuickPermissionController;
use Lazyou\Quick\Http\Controllers\Admin\QuickRoleController;
use Lazyou\Quick\Http\Controllers\Admin\QuickBaseController;

// TIP: 视图路由必须设置 name, 用作视图渲染和权限管理
$adminPath = config('quick.admin_path', 'admin');

Route::middleware(['web'])->prefix("/{$adminPath}")->group(function () {
    Route::get('/auth/login', [QuickAuthController::class, 'login']);
    Route::post('/auth/login', [QuickAuthController::class, 'loginPost'])->middleware('quick.log');
    Route::get('/auth/logout', [QuickAuthController::class, 'logout']);
});

Route::middleware(['web', 'quick.auth', 'quick.log'])->prefix("/$adminPath")->group(function () {
    Route::get('/home', [QuickHomeController::class, 'index'])->name('admin.home.index');
    Route::post('/qiniu/token', [QuickBaseController::class, 'qiniuToken']);

    // 操作日志
    Route::get('/log', [QuickOperationLogController::class, 'index'])->name('admin.log.index');

    // 用户管理
    Route::get('/user', [QuickUserController::class, 'index'])->name('admin.user.index');
    Route::post('/user', [QuickUserController::class, 'edit'])->name('admin.user.edit');
    Route::delete('/user/{id}', [QuickUserController::class, 'delete'])->name('admin.user.delete');
    Route::get('/user/roles', [QuickUserController::class, 'roles']);

    // 菜单管理
    Route::get('/menu', [QuickMenuController::class, 'index'])->name('admin.menu.index');
    Route::post('/menu', [QuickMenuController::class, 'edit'])->name('admin.menu.edit');
    Route::delete('/menu/{id}', [QuickMenuController::class, 'delete'])->name('admin.menu.delete');
    Route::get('/menu/tree', [QuickMenuController::class, 'tree']);
    Route::get('/menu/tree_menus', [QuickMenuController::class, 'treeMenus']);
    Route::get('/menu/top_options', [QuickMenuController::class, 'topOptions']);

    // 权限管理
    Route::get('/permission', [QuickPermissionController::class, 'index'])->name('admin.permission.index');
    Route::post('/permission', [QuickPermissionController::class, 'edit'])->name('admin.permission.edit');
    Route::get('/permission/routes', [QuickPermissionController::class, 'routes']);
    Route::get('/permission/menus', [QuickPermissionController::class, 'menus']);

    // 角色管理
    Route::get('/role', [QuickRoleController::class, 'index'])->name('admin.role.index');
    Route::post('/role', [QuickRoleController::class, 'edit'])->name('admin.role.edit');
    Route::delete('/role/{id}', [QuickRoleController::class, 'delete'])->name('admin.role.delete');
});
