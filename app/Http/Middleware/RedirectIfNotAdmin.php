<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admin')
    {
        if (!Auth::guard($guard)->check()) {
            return to_route('admin.login');
        }



        // dd(Auth::guard($guard)->user(), $current_route);
        // dd($current_route);

        if ($request->method() == 'GET') {

            $admin_user_info = Auth::guard($guard)->user();
            $current_route = $request->route()->getName();

            if ($admin_user_info->user_role == 'CONTENT_MANAGER') {
                if (!in_array($current_route, [
                    'admin.profile',
                    'admin.password',
                    'admin.frontend.sections',
                    'admin.frontend.sections.element',
                ])) {

                    //$notify[] = ['error', 'Invalid access to the page'];
                    // return back()->withNotify($notify);
                    //return to_route('admin.frontend.sections', 'blog')->withNotify($notify);
                    return to_route('admin.frontend.sections', 'blog');
                }
            }
        }


        return $next($request);
    }
}
