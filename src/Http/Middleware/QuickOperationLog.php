<?php


namespace Lazyou\Quick\Http\Middleware;

use Lazyou\Quick\Jobs\CreateQuickOperationLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 操作日志中间件
 * Class QuickOperationLog.
 */
class QuickOperationLog
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->isLogging($request)) {
            $log = [
                'user_id' => $this->getUserId(),
                'as' => $request->route()->getName() ?? '',
                'ip' => $request->ip(),
                'method' => $request->getMethod(),
                // TODO: 考虑是否对太长的传入参数做处理
                'body' => $request->getContent(),
                'url' => $this->getUrl($request),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            CreateQuickOperationLog::dispatch($log);
        }

        return $response; // 后置中间件
    }

    /**
     * 是否记录操作日志.
     * @param Request $request
     * @return bool
     */
    protected function isLogging(Request $request): bool
    {
        return $request->wantsJson() && ! $this->blockListUrl($request) && ! $this->blockListAs($request);
    }

    /**
     * 路由path 黑名单 -- 不记录。
     * @param Request $request
     * @return bool
     */
    protected function blockListUrl(Request $request): bool
    {
        return in_array('/' . $request->path(), config('quick.block_path_list', []));
    }

    /**
     * 路由别名 黑名单 -- 不记录。
     * @param Request $request
     * @return bool
     */
    protected function blockListAs(Request $request): bool
    {
        return in_array($request->route()->getName(), config('quick.block_as_list', []));
    }

    protected function getUserId(): int
    {
        $userId = 0;
        $auth = Auth::user();
        if ($auth) {
            $userId = $auth->getAuthIdentifier();
        }

        return $userId;
    }

    /**
     * 当前请求的 uri + query.
     * @param Request $request
     * @return string
     */
    protected function getUrl(Request $request): string
    {
        return '/' . $request->path() . urldecode(str_replace($request->url(), '', $request->fullUrl()));
    }
}
