<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Role;

class session_mw {


	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next ){

		if(!Session()->has('loggedin') ){
				/*If Nothing Found in Session Person will be redirected to login page*/
				//return redirect()->route('');			
				return new RedirectResponse(url('/'));
			}
			return $next($request);
		}
}
