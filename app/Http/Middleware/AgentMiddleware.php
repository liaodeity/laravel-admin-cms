<?php

namespace App\Http\Middleware;

use App\Services\LockScreenService;
use Closure;
use Illuminate\Support\Facades\Session;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        define ('AUTH_SYSTEM_TYPE','agent');
        if (!get_agent_id()) {
            return redirect (route ('agent-login'));
        }
//        dd(session()->all());
        $auth = check_agent_permission('show console');
        if (!$auth) {
            //非代理商角色
            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => trans('非代理商禁止访问，无权限'),
                ]);
            }
            abort(403, trans('非代理商禁止访问，无权限'));
        }

        //检查是否已锁屏
        $LockScreenService = new LockScreenService();
        $lock              = $LockScreenService->setType ('agent')->checkIsLock ();
        if ($lock === true) {
            return redirect (url ('agent-lockscreen'));
        }


        return $next($request);
    }
}
