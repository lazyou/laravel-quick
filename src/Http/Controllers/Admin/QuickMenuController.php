<?php


namespace Lazyou\Quick\Http\Controllers\Admin;

use Lazyou\Quick\Http\Requests\MenuDeleteRequest;
use Lazyou\Quick\Http\Requests\MenuEditRequest;
use Lazyou\Quick\Models\QuickPermission;
use Illuminate\Http\Request;

class QuickMenuController extends QuickBaseController
{
    public function index(Request $request)
    {
        if (! $request->wantsJson()) {
            $this->setHeadTitle('菜单管理');
            return $this->viewPackage();
        }

        return [
        ];
    }

    /**
     * 创建or编辑数据处理.
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(MenuEditRequest $request)
    {
        $input = $request->only([
            'user_id',
            'parent_id',
            'name',
            'url',
            'icon',
            'sort',
            'status',
        ]);
        $input['type'] = QuickPermission::TYPE_MENU;
        $input['deep'] = $input['parent_id'] ? 2 : 1;

        $id = $request->get('id');
        if ($id) {
            $menu = QuickPermission::query()->findOrFail($id);
            $menu->update($input);
        } else {
            QuickPermission::query()->create($input);
        }

        return $this->apiOk();
    }

    public function tree(): array
    {
        $fields = [
            'id',
            'parent_id',
            'name',
            'name as label',
            'type',
            'url',
            'icon',
            'sort',
            'status',
        ];

        $menus = QuickPermission::query()
            ->orderBy('sort')
            ->orderBy('id', 'DESC')
            ->get($fields)
            ->toArray();

        return buildTree($menus);
    }

    public function treeMenus()
    {
        $fields = [
            'id',
            'parent_id',
            'name',
            'name as label',
            'type',
            'url',
            'icon',
            'sort',
            'status',
        ];

        $menus = QuickPermission::query()
            ->where('type', QuickPermission::TYPE_MENU)
            ->orderBy('sort')
            ->get($fields)
            ->toArray();

        return buildTree($menus);
    }

    public function topOptions()
    {
        $fields = [
            'id',
            'name',
        ];

        return QuickPermission::query()
            ->where('type', QuickPermission::TYPE_MENU)
            ->where('parent_id', 0)
            ->get($fields);
    }

    /**
     * 删除数据.
     * @param MenuDeleteRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(MenuDeleteRequest $request, $id)
    {
        $menu = QuickPermission::query()->findOrFail($id);
        // TODO: 判断数据是否被使用 决定能否被删除
        $menu->forceDelete();

        return $this->apiOk();
    }
}
