<?php


namespace Lazyou\Quick\Http\Controllers\Admin;

use Lazyou\Quick\Models\QuickOperationLog;
use Illuminate\Http\Request;

class QuickOperationLogController extends QuickBaseController
{
    public function index(Request $request)
    {
        if (! $request->wantsJson()) {
            $this->setHeadTitle('操作日志');
            return $this->viewPackage();
        }

        $map = [
            'id',
            'as',
            'method',
            'url' => 'like',
        ];

        return vuePaginate(QuickOperationLog::query()->with([
            'user',
            'permission',
            'permission.parent',
        ]), $map);
    }
}
