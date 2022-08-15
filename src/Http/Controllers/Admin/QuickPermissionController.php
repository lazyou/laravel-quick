<?php


namespace Lazyou\Quick\Http\Controllers\Admin;

use Lazyou\Quick\Http\Requests\PermissionEditRequest;
use Lazyou\Quick\Models\QuickPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class QuickPermissionController extends QuickBaseController
{
    public function index(Request $request)
    {
        if (! $request->wantsJson()) {
            $this->setHeadTitle('权限管理');
            return $this->viewPackage();
        }

        $routes = $this->getRoutes();

        $permissions = QuickPermission::query()
            ->where('type', QuickPermission::TYPE_PERMISSION)
            ->get()
            ->toArray();

        $permissionsMap = [];
        foreach ($permissions as $permission) {
            $permissionsMap[$permission['as']] = $permission;
        }

        // 对命名路由已存在数据库的 name parent_id 进行不全
        foreach ($routes as &$route) {
            if (isset($permissionsMap[$route['as']])) {
                $permission = $permissionsMap[$route['as']];
                $route['name'] = $permission['name'];
                $route['parent_id'] = $permission['parent_id'];
            }
        }

        return $routes;
    }

    public function routes(Request $request)
    {
        $prefix = $request->get('prefix', 'admin');

        return $this->getRoutes($prefix);
    }

    // 获取无子级菜单，用于关联权限
    public function menus()
    {
        $list = [];
        $menus = QuickPermission::query()
            ->where('type', QuickPermission::TYPE_MENU)
            ->get(['id', 'parent_id', 'name'])
            ->toArray();

        $parentIds = array_unique(array_column($menus, 'parent_id'));
        foreach ($menus as $menu) {
            if (! in_array($menu['id'], $parentIds)) {
                $list[] = $menu;
            }
        }

        return $list;
    }

    /**
     * 创建or编辑数据处理.
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(PermissionEditRequest $request)
    {
        $parentId = $request->get('parent_id');
        if ($parentId) {
            $isParentMenu = QuickPermission::query()
                ->where('type', QuickPermission::TYPE_MENU)
                ->where('parent_id', $parentId)
                ->first();

            if ($isParentMenu) {
                return $this->apiBad('权限不允许设置在父级菜单下');
            }
        }

        $input = $request->only([
            'user_id',
            'parent_id',
            'name',
            'as',
            'url',
            'controller',
        ]);
        $input['type'] = QuickPermission::TYPE_PERMISSION;

        $permission = QuickPermission::query()->where('as', $input['as'])->first();
        if ($permission) {
            $permission->update($input);
        } else {
            QuickPermission::create($input);
        }
        return $this->apiOk();
    }

    /**
     * 获取所有设置name的路由.
     * @param string $prefix
     * @return array
     */
    protected function getRoutes($prefix = 'admin')
    {
        $list = [];
        $routes = Route::getRoutes()->getRoutesByName();

        foreach ($routes as $key => $route) {
            if (Str::startsWith($key, $prefix)) {
                $list[] = [
                    'parent_id' => null,
                    'name' => '',
                    'url' => '/' . $route->uri(),
                    'as' => $key,
                    'controller' => $route->getAction()['controller'],
                    'sort' => 1,
                ];
            }
        }

        return $list;
    }
}
