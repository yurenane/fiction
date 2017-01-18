<?php

namespace App\Http\Middleware;

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
		if (!$request->session()->has('user') && !in_array($request->path(), array('login', 'register'))) {
			return redirect('login');
		}
		return $next($request);
	}

}
