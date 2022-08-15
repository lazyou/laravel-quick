<?php


namespace Lazyou\Quick\Http\Controllers\Admin;

use Lazyou\Quick\Http\Controllers\QuickControllerTrait;
use Lazyou\Quick\Models\QuickPermission;
use Lazyou\Quick\Models\QuickRolePermission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

class QuickBaseController extends Controller
{
    use AuthorizesRequests;
    use QuickControllerTrait;
    use DispatchesJobs;
    use ValidatesRequests;

    protected $auth;

    // 视图数据
    protected array $_data = [
        '_debug' => false,
        '_title' => '',
        '_auth' => null,
        '_menus' => [],
        '_menu_active' => '',
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->auth = Auth::user();
            if ($this->auth) {
                // 全局请求设置当前用户id (TODO: 会覆盖前端传入的 user_id)
                request()->offsetSet('user_id', $this->auth->getAuthIdentifier());
            }

            // debug 时引入 dev 版本的 vue
            $this->_data['_debug'] = config('app.debug');
            $this->_data['_title'] = config('quick.admin_title');
            $this->_data['_auth'] = $this->auth;
            $this->_data['_menus'] = $this->getMenus();
            $this->_data['_menu_active'] = $this->getActiveMenu();

            return $next($request);
        });
    }

    // TODO: 利用 get_class($this) 或 get_called_class() App::make() 实现自动的 curd 方法
    // 例如: get_controller, get_view, get_model, get_repository
    // 和 curd 常见的: index, find, create, update, delete, delete_many

    protected function setHeadTitle($title = '后台管理')
    {
        $this->_data['_head']['title'] = $title;
    }

    /**
     * 包内部专用 view 渲染方法
     */
    public function view(array $data = [], string $path = '')
    {
        return $this->viewHandle(false, $data, $path);
    }

    /**
     * 包内部专用 view 渲染方法
     */
    public function viewPackage(array $data = [], string $path = '')
    {
        return $this->viewHandle(true, $data, $path);
    }


    /**
     * 默认视图渲染
     * @param false $isPackagePath 是否内部路径（开发扩展包时用自动补充 quick:: 前缀）
     * @param array $data
     * @param string $path
     */
    protected function viewHandle(bool $isPackagePath = false, array $data = [], string $path = '')
    {
        if (empty($path)) {
            $path = Route::currentRouteName();
            if (empty($path)) {
                throw new \Exception('请设置 Route 的 name 方法');
            }
        }

        if ($isPackagePath) {
            $path = "quick::$path";
        }

        return view($path, $this->_data, $data);
    }

    /**
     * 接口失败统一响应.
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function apiBad(string $message = '操作失败', int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }

    /**
     * 接口操作成功统一响应.
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function apiOk(string $message = '操作成功', int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }

    /**
     * 接口数据响应.
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    protected function apiData(array $data = [], int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $code);
    }

    // 接口单条表单验证错误响应
    protected function validateError($key, $errMsg)
    {
        return response(['errors' => new MessageBag([$key => $errMsg])], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function getActiveMenu(): string
    {
        return '/' . request()->path();
    }

    protected function getMenus(): array
    {
        $menus = [];
        if (is_null($this->auth)) {
            return $menus;
        }

        $fields = [
            'id',
            'parent_id',
            'name',
            'url',
            'icon',
        ];

        if ($this->auth->isAdmin()) {
            $menus = QuickPermission::query()
                ->where('type', QuickPermission::TYPE_MENU)
                ->where('status', QuickPermission::STATUS_ENABLE)
                ->get($fields)
                ->toArray();
        } else {
            // TODO: 根据角色查询菜单
            $permissionIds = QuickRolePermission::query()
                ->where('role_id', $this->auth->role_id)
                ->pluck('permission_id')
                ->toArray();

            if (empty($permissionIds)) {
                throw new \Exception('该用户角色未分配权限，无法使用');
            }

            $menus = QuickPermission::query()
                ->where('type', QuickPermission::TYPE_MENU)
                ->whereIn('id', $permissionIds)
                ->get($fields)
                ->toArray();
        }

        return buildTree($menus);
    }
}
