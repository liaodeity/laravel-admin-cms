<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Services\LoginService;
use Closure;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle ($request, Closure $next)
    {
        //session ()->put ('LOGIN_USER_ID', 1);
        //session ()->put ('LOGIN_ADMIN', 'admin');

        $user_id      = get_login_user_id ();
        //dd($user_id);
        $LoginService = new LoginService();
        $check        = $LoginService->checkIsLogin ();
        if (empty($user_id) || empty($check)) {
            if ($request->ajax ()) {
                return ajax_error_result ('登录已过期');
            } else {
                return redirect (route ('admin.login'));
            }
        }

        return $next($request);
    }
}
