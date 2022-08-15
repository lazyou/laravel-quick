<?php


namespace Lazyou\Quick\Http\Controllers\Admin;

use Lazyou\Quick\Http\Requests\UserDeleteRequest;
use Lazyou\Quick\Http\Requests\UserEditRequest;
use Lazyou\Quick\Models\QuickRole;
use Lazyou\Quick\Models\QuickUser;
use Illuminate\Http\Request;

class QuickUserController extends QuickBaseController
{
    public function index(Request $request)
    {
        if (! $request->wantsJson()) {
            $this->setHeadTitle('用户管理');
            return $this->viewPackage();
        }

        $map = [
            'id',
            'name' => 'like',
        ];

        return vuePaginate(QuickUser::class, $map);
    }

    /**
     * 创建or编辑数据处理.
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(UserEditRequest $request)
    {
        $id = $request->get('id');
        $input = $request->only([
            'name',
            'role_id',
            'email',
            'status',
            'is_admin',
        ]);

        $password = $request->get('password');
        if ($password) {
            $input['password'] = bcrypt($password);
        }

        if ($id) {
            $user = QuickUser::query()->findOrFail($id);
            $user->update($input);
        } else {
            QuickUser::query()->create($input);
        }

        return $this->apiOk();
    }

    /**
     * 删除数据.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(UserDeleteRequest $request, $id)
    {
        $user = QuickUser::query()->findOrFail($id);
        // TODO: 判断数据是否被使用 决定能否被删除
        $user->delete();

        return $this->apiOk();
    }

    public function roles()
    {
        return QuickRole::query()->get(['id', 'name']);
    }
}
