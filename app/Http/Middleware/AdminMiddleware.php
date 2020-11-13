<?php

namespace App\Http\Middleware;

use App\Services\LockScreenService;
use App\Services\LoginService;
use Closure;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle ($request, Closure $next)
    {
        define ('AUTH_SYSTEM_TYPE','admin');
        if (!get_admin_id()) {
            return redirect (route ('admin-login'));
        }

        //检查是否已锁屏
        $LockScreenService = new LockScreenService();
        $lock              = $LockScreenService->setType ('admin')->checkIsLock ();
        if ($lock === true) {
            return redirect (url ('admin-lockscreen'));
        }

        return $next($request);
    }
}
