<?php


namespace Lazyou\Quick\Http\Controllers\Admin;

use Lazyou\Quick\Models\QuickUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickAuthController extends QuickBaseController
{
    public function login()
    {
        if (Auth::check()) {
            $adminPath = config('quick.admin_path', 'admin');
            return redirect("/$adminPath/home");
        }

        return $this->view([], 'quick::admin.auth.login');
    }

    public function loginPost(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|captcha',
        ], [
            'email' => '账号错误',
            'password' => '密码错误',
            'captcha' => '验证码错误',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $adminPath = config('quick.admin_path', 'admin');
            return $this->apiData(['url' => "/$adminPath/home"]);
        }

        return $this->apiBad('账号或密码错误');
    }

    public function logout()
    {
        Auth::logout();
        $adminPath = config('quick.admin_path', 'admin');
        return redirect("/$adminPath/auth/login");
    }
}
