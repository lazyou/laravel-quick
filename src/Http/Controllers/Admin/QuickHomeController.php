<?php


namespace Lazyou\Quick\Http\Controllers\Admin;

use Illuminate\Http\Request;

class QuickHomeController extends QuickBaseController
{
    public function index(Request $request)
    {
        if (! $request->wantsJson()) {
            $this->setHeadTitle('首页');
            return $this->viewPackage();
        }

        return [
        ];
    }
}
