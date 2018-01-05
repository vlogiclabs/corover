<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use DB;
use App\UsersModel as User;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Register;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){
       // dd($request->all());
        $intended = (!empty($request->input('intention')) || $request->input('intention') !== null  ) ? $request->input('intention') : 'home';
           $v = Validator::make($request->all(), [
                  'email' => 'required',
                  'password' => 'required'
                ]);
        if ($v->fails()){
                 //return  redirect()->back()->withInput()->withErrors($v->errors());
                 return redirect('/')->withErrors($v)->withInput();
        }else{
                $remember = $request->input('remember');
                $user = Register::where(['email' =>$request->input('email')])->first();

               if(count($user) > 0){
                //dd($request->input('password'));
                     if(Hash::check($request->input('password'), $user->password)){
                            //Auth::login($user);
                           // Auth::loginUsingId($user->_id);
                           // $request->session()->push('user.teams', 'developers');
                            Session::put('loggedin',['email' => $user->email , 'token' => $user->token ,'role' => $user->role ]);   // put that id in session
                            //$arr = array(array( "activitiy" => "log in"));
                            //$user=Auth::user(); 
                            return redirect()->intended('home');
                        } else {
                             return redirect('/')->with('status', 'Wrong Email And Password!');
                        }
               } else {
                    return redirect('/')->with('status', 'Wrong Email And Password!');
                    return redirect()->back()->withInput();
               }
       
        }
    }    
}
