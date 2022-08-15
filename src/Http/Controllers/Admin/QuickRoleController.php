<?php


namespace Lazyou\Quick\Http\Controllers\Admin;

use Lazyou\Quick\Http\Requests\RoleDeleteRequest;
use Lazyou\Quick\Http\Requests\RoleEditRequest;
use Lazyou\Quick\Models\QuickRole;
use Lazyou\Quick\Models\QuickRolePermission;
use Illuminate\Http\Request;

class QuickRoleController extends QuickBaseController
{
    public function index(Request $request)
    {
        if (! $request->wantsJson()) {
            $this->setHeadTitle('角色管理');
            return $this->viewPackage();
        }

        return vuePaginate(QuickRole::query()->with(['permissions']));
    }

    public function edit(RoleEditRequest $request)
    {
        $id = $request->get('id');
        $input = $request->only([
            'user_id',
            'name',
            'flag',
            'remark',
        ]);

        $permissionIds = $request->get('permission_ids', []);
        if (empty($permissionIds)) {
            return $this->apiBad('请勾选角色权限');
        }

        if ($id) {
            $role = QuickRole::query()->findOrFail($id);
            $role->update($input);
        } else {
            $role = QuickRole::query()->create($input);
        }

        $relationArr = [];
        foreach ($permissionIds as $permissionId) {
            $relationArr[] = [
                'role_id' => $role->id,
                'permission_id' => $permissionId,
                'created_at' => nowAt(),
                'updated_at' => nowAt(),
            ];
        }

        QuickRolePermission::query()->where('role_id', $role->id)->forceDelete();
        QuickRolePermission::query()->insert($relationArr);

        return $this->apiOk();
    }

    public function delete(RoleDeleteRequest $request, $id)
    {
        $role = QuickRole::query()->findOrFail($id);
        $role->delete();

        QuickRolePermission::query()->where('role_id', $id)->delete();

        return $this->apiOk();
    }
}
