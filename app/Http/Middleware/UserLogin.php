<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Closure;

class UserLogin {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if (!$request->session()->has('user') && !in_array($request->path(), array('login'))) {
			if(isset($_COOKIE['user'])){
				$info = DB::table('user')
					->where('id', $_COOKIE['user'])
					->first();
				if($info){
					setcookie('user',$info->id,time()+60*60*24);
					$request->session()->set('user', $info);
				}
				return $next($request);
			}
			return redirect('login');
		}
		return $next($request);
	}

}
