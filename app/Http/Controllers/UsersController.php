<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\UsersModel as User;
use App\Country;
use App\City;
use Validator;
use Illuminate\Support\Facades\Hash;
use Response;
use App\Helpers;
use App\Test;
use App\Register;

class UsersController extends Controller
{
    


    public function testing(){

          //$chat = Test::get();
            $chat = User::get();
            dd($chat);
    }



    public function login(Request $request){
       if (session()->has('loggedin')) {
            return redirect('/home');
        } else {
            return view('login');
        }

    }


    public function store(Request $request) {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|unique:users',
                'password' => 'required|unique:users',
            ]);
    
            $validator->after(function ($validator) {
            });
            
            if ($validator->fails()) {
                return redirect('register')
                            ->withErrors($validator)
                            ->withInput();
            } else {
                  $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                 $data = [
                   'first_name'  => $request->input('first_name'),
                   'last_name'  => $request->input('last_name'),
                   'email'  => $request->input('email'),
                   'password'  => Hash::make($request->input('password')),
                   'token' => $token,
                   'role' => 3,
                   'original_password'=>$request->input('password'),
               ];

              //$request->input('password') = ;    //setting 
            $insert =   Register::insertgetId($data);
                if(!empty($insert)){
                        return redirect('/')->with('success', 'Registered Successfully!');
                }
            }
    }


    public function home(){
        if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $userdata = Register::where(['email' => $user['email'] ])->first();
            if($user['role'] == 3){
                return view('user.home')->with('userdata',$userdata);    
            } else if($user['role'] == 1){
                $usercount = Register::count();
                return view('superadmin.home')->with('userdata',$userdata)->with('usercount',$usercount);
            }else if($user['role'] == 2){
                return view('admin.home')->with('userdata',$userdata);
            }
        }
    }
   /*
    |--------------------------------------------------------------------------
    | Function : admins
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function admins(){
        if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Admin Listing';
            $subtitle = '';
         $userdata = Register::where(['email' => $user['email'] ])->first();
         $admins = Register::where(['role' => 2])->get();
         return view('superadmin.admins')->with('userdata',$userdata)->with('title',$title)->with('admins',$admins);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Function : superadmins
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function superadmins(){
        if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Super Admin Listing';
            $subtitle = '';
         $userdata = Register::where(['email' => $user['email'] ])->first();
         $admins = Register::where(['role' => 1])->get();
         return view('superadmin.superadmins')->with('userdata',$userdata)->with('title',$title)->with('admins',$admins);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Function : drivers
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function drivers(){
        if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Drivers Listing';
            $subtitle = '';
         $userdata = User::where(['email' => $user['email'] ])->first();
         $admins = User::where(['role' => 4])->get();
         return view('superadmin.drivers')->with('userdata',$userdata)->with('title',$title)->with('admins',$admins);
        }
    }

        /*
    |--------------------------------------------------------------------------
    | Function : addadmin
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function addadmin(Request $request){
           if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Add Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
                 $validator = Validator::make($request->all(), [
                    'first_name' => 'required|max:255',
                    'last_name' => 'required|max:255',
                    'email' => 'required|unique:users',
                    'password' => 'required|unique:users',
                ]);
                
                 if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }    


                    $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                 $data = [
                   'first_name'  => $request->input('first_name'),
                   'last_name'  => $request->input('last_name'),
                   'email'  => $request->input('email'),
                   'password'  => Hash::make($request->input('password')),
                   'token' => $token,
                   'role' => 2, 
               ];

              //$request->input('password') = ;    //setting 
            $insert =   Register::insertgetId($data);
                if(!empty($insert)){
                        return redirect('/admins')->with('success', 'Admin Registered Successfully!');
                }

            }
            $userdata = Register::where([ 'email' => $user['email'] ])->first();
             return view('superadmin.addadmin')->with('userdata',$userdata)->with('title',$title);
        }

    }

     /*
    |--------------------------------------------------------------------------
    | Function : adddriver
    |--------------------------------------------------------------------------
    | This will be used to save driver data into DB 
    */
     public function adddriver(Request $request){
           if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Add Admin';
            $subtitle = '';
            $userdata = User::where([ 'email' => $user['email'] ])->first();
            if(count($request->all()) > 0){
                 $validator = Validator::make($request->all(), [
                    'first_name' => 'required|max:255',
                    'last_name' => 'required|max:255',
                    'email' => 'required|unique:users',
                    'dob' => 'required',
                    'phone' => 'required|max:16',
                    'address' => 'required|max:500',
                    'country_id' => 'required|max:500',
                    'city_id' => 'required|max:500',
                    'licence_no' => 'required|max:500',
                    'licence_expiry_date' => 'required|max:500',
                    'pvc' => 'file|max:2000',
                    'insurance' => 'file|max:2000',
                    'driverid' => 'file|max:2000',
                    'car_photo' => 'file|max:2000',
                    'log_book' => 'file|max:2000',
                    'profile' => 'file|max:2000',
                    'bank_account_name' => 'required|max:500',
                    'bank_name' => 'required|max:500',
                    'bank_branch_name' => 'required|max:500',
                    'bank_branch_code' => 'required|max:500',
                    'bank_account_no' => 'required|max:500',
                ]);
                
                 if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }    


            $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*  ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                   
                    $helper = new Helpers();
                  $pvc =  $helper->upload($request->file('pvc'),'images/driverdocs','none');
                  $insurance =  $helper->upload($request->file('insurance'),'images/driverdocs','none');
                  $car_photo =  $helper->upload($request->file('car_photo'),'images/driverdocs','none');
                  $driverid =  $helper->upload($request->file('driverid'),'images/driverdocs','none');
                  $log_book =  $helper->upload($request->file('log_book'),'images/driverdocs','none');
                  $profile = ($request->file('profile') !== null) ? $helper->upload($request->file('profile'),'images/driverdocs','none') : '' ;

                 $data = [
                   'first_name'  => $request->input('first_name'),
                   'last_name'  => $request->input('last_name'),
                   'email'  => $request->input('email'),
                   'password'  => Hash::make($request->input('password')),
                   'token' => $token,
                   'role' => 4, 
                   'is_active' => 'No',
                   'is_verified' => 'No',
                    'dob' => $request->input('dob'),
                    'phone' => $request->input('phone'),
                    'address' => $request->input('address'),
                    'country' => $request->input('country'),
                    'licence_no' => $request->input('licence_no'),
                    'licence_expiry_date' => $request->input('licence_expiry_date'),
                    'pvc' => $pvc,
                    'insurance' => $insurance,
                    'driverid' => $driverid,
                    'car_photo' => $car_photo,
                    'log_book' => $log_book,
                    'image' => $profile,
                    'bank_account_name' => $request->input('bank_account_name'),
                    'bank_name' => $request->input('bank_name'),
                    'bank_branch_name' => $request->input('bank_branch_name'),
                    'bank_branch_code' => $request->input('bank_branch_code'),
                    'bank_account_no' => $request->input('bank_account_no'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'admin_id' => $userdata->_id
               ];

              //$request->input('password') = ;    //setting 
            $insert =   User::insertgetId($data);
                if(!empty($insert)){
                        return redirect('/drivers')->with('success', 'Driver Registered Successfully!');
                }

            }
             $countries = Country::pluck('name','_id')->all();   
             return view('superadmin.adddriver')->with('userdata',$userdata)->with('title',$title)
             ->with('countries',$countries);
        }

    }
    /*
    |--------------------------------------------------------------------------
    | Function : editdriver
    |--------------------------------------------------------------------------
    | This will be used to save driver data into DB 
    */
     public function editdriver(Request $request,$id = null){
           if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Driver';
            $subtitle = '';
            $userdata = User::where([ 'email' => $user['email'] ])->first();

            if(count($request->all()) > 0){
                $driver = User::where([ '_id' => $request->input('_id') ])->first();
                 $validator = Validator::make($request->all(), [
                    'first_name' => 'required|max:255',
                    'last_name' => 'required|max:255',
                    'email' => 'required|unique:users,email,',
                    'dob' => 'required',
                    'phone' => 'required|max:16',
                    'address' => 'required|max:500',
                    'country' => 'required|max:500',
                    'licence_no' => 'required|max:500',
                    'licence_expiry_date' => 'required|max:500',
                    /*'pvc' => 'file|max:2000',
                    'insurance' => 'file|max:2000',
                    'driverid' => 'file|max:2000',
                    'car_photo' => 'file|max:2000',
                    'log_book' => 'file|max:2000',*/
                    'bank_account_name' => 'required|max:500',
                    'bank_name' => 'required|max:500',
                    'bank_branch_name' => 'required|max:500',
                    'bank_branch_code' => 'required|max:500',
                    'bank_account_no' => 'required|max:500',
                ]);
                
                 if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }    


            $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*  ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                   
                    $helper = new Helpers();
                  $pvc =  ($request->file('pvc') !== null) ? $helper->upload($request->file('pvc'),'images/driverdocs',$driver->pvc) : $driver->pvc ;
                  $insurance = ($request->file('insurance') !== null) ? $helper->upload($request->file('insurance'),'images/driverdocs',$driver->insurance) : $driver->insurance ;
                  $car_photo = ($request->file('car_photo') !== null) ? $helper->upload($request->file('car_photo'),'images/driverdocs',$driver->car_photo) : $driver->car_photo ;
                  $driverid =  ($request->file('driverid') !== null) ? $helper->upload($request->file('driverid'),'images/driverdocs',$driver->driverid) : $driver->driverid ;
                  $log_book = ($request->file('log_book') !== null) ? $helper->upload($request->file('log_book'),'images/driverdocs',$driver->log_book) : $driver->log_book ;
                  $profile = ($request->file('profile') !== null) ? $helper->upload($request->file('profile'),'images/driverdocs',$driver->profile) : $driver->profile ;

                 $data = [
                   'first_name'  => $request->input('first_name'),
                   'last_name'  => $request->input('last_name'),
                   'email'  => $request->input('email'),
                   'password'  => Hash::make($request->input('password')),
                   'token' => $token,
                   'role' => 4, 
                   'is_active' => 'No',
                   'is_verified' => 'No',
                    'dob' => $request->input('dob'),
                    'phone' => $request->input('phone'),
                    'address' => $request->input('address'),
                    'country' => $request->input('country'),
                    'licence_no' => $request->input('licence_no'),
                    'licence_expiry_date' => $request->input('licence_expiry_date'),
                    'pvc' => $pvc,
                    'insurance' => $insurance,
                    'driverid' => $driverid,
                    'car_photo' => $car_photo,
                    'log_book' => $log_book,
                    'image' => $profile,
                    'bank_account_name' => $request->input('bank_account_name'),
                    'bank_name' => $request->input('bank_name'),
                    'bank_branch_name' => $request->input('bank_branch_name'),
                    'bank_branch_code' => $request->input('bank_branch_code'),
                    'bank_account_no' => $request->input('bank_account_no'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'admin_id' => $userdata->_id
               ];

              //$request->input('password') = ;    //setting 
            $insert =   User::where('_id',$request->input('_id'))->update($data);
                //if(!empty($insert)){
                        return redirect('/drivers')->with('success', 'Driver Registered Successfully!');
                //}

            }
             $countries = Country::pluck('name','_id')->all();   
            $driver = User::where([ '_id' => $id])->first();
             return view('superadmin.editdriver')->with('userdata',$userdata)->with('title',$title)
             ->with('driver',$driver)->with('countries',$countries);
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Function :  addsuperadmin
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function addsuperadmin(Request $request){
           if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Add Super-Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
                 $validator = Validator::make($request->all(), [
                    'first_name' => 'required|max:255',
                    'last_name' => 'required|max:255',
                    'email' => 'required|unique:users',
                    'password' => 'required|unique:users',
                ]);
                
                 if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }    


                    $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                 $data = [
                   'first_name'  => $request->input('first_name'),
                   'last_name'  => $request->input('last_name'),
                   'email'  => $request->input('email'),
                   'password'  => Hash::make($request->input('password')),
                   'token' => $token,
                   'role' => 1, 
               ];

              //$request->input('password') = ;    //setting 
            $insert =   User::insertgetId($data);
                if(!empty($insert)){
                        return redirect('/superadmins')->with('success', 'Super Admin Registered Successfully!');
                }

            }
            $userdata = User::where([ 'email' => $user['email'] ])->first();
             return view('superadmin.addsuperadmin')->with('userdata',$userdata)->with('title',$title);
        }

    }
    /*
    |--------------------------------------------------------------------------
    | Function : deleteadmin
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Admin Listing
    */
     
     public function editadmin(Request $request,$id = null){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Admin';
            $subtitle = '';         
            $admin = User::where(['role' => 2 ,'_id' =>  $id ])->first();
            if(count($request->all()) > 0){
                $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                 $data = [
                   'first_name'  => $request->input('first_name'),
                   'last_name'  => $request->input('last_name'),
                   'email'  => $request->input('email'),
                   'token' => $token,
                   'role' => intval($request->input('role')), 
               ];
            $insert =   User::where('_id',  $request->input('_id'))->update($data);
                if($insert){
                    return redirect('/admins')->with('success', 'Registered Successfully!');
                }   
            }
         $userdata = User::where(['email' => $user['email'] ])->first();
         return view('superadmin.editadmin')->with('userdata',$userdata)->with('title',$title)->with('admin',$admin);
        }
     }

         /*
    |--------------------------------------------------------------------------
    | Function : editsuperadmin
    |--------------------------------------------------------------------------
    | This will be used to edit Record in Super Admin Listing
    */
     
     public function editsuperadmin(Request $request,$id = null){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Super Admin';
            $subtitle = '';         
            $admin = User::where(['role' => 1 ,'_id' =>  $id ])->first();
            if(count($request->all()) > 0){
                $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                 $data = [
                   'first_name'  => $request->input('first_name'),
                   'last_name'  => $request->input('last_name'),
                   'email'  => $request->input('email'),
                   'token' => $token,
                   'role' => intval($request->input('role')), 
               ];
            $insert =   User::where('_id',  $request->input('_id'))->update($data);
                if($insert){
                    return redirect('/superadmins')->with('success', 'Registered Successfully!');
                }   
            }
         $userdata = User::where(['email' => $user['email'] ])->first();
         return view('superadmin.editsuperadmin')->with('userdata',$userdata)->with('title',$title)->with('admin',$admin);
        }
     }


    /*
    |--------------------------------------------------------------------------
    | Function : deleteadmin
    |--------------------------------------------------------------------------
    | This will be used to Edit Record in Admin Listing
    */
     public function deleteadmin(Request $request){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
            $insert =   User::where('_id',  $request->input('origin'))->delete();
                if($insert){
                    if($request->ajax()){
                        return Response::json(['status' => 'success'],200);
                    }
                    return redirect('/admins')->with('success', 'Registered Successfully!');
                }   
            }
        
        }

     }

    /*
    |--------------------------------------------------------------------------
    | Function : deletesuperadmin
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Admin Listing
    */
     public function deletesuperadmin(Request $request){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
            $insert =   User::where('_id',  $request->input('origin'))->delete();
                if($insert){
                    if($request->ajax()){
                        return Response::json(['status' => 'success'],200);
                    }
                    return redirect('/superadmins')->with('success', 'Registered Successfully!');
                }   
            }
         
        }

     }

      /*
    |--------------------------------------------------------------------------
    | Function : admins
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function countries(){
        if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Countries Listing';
            $subtitle = '';
         $userdata = User::where(['email' => $user['email'] ])->first();
         $admins = Country::get();
         return view('superadmin.countries')->with('userdata',$userdata)->with('title',$title)->with('admins',$admins);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Function : addcountry
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function addcountry(Request $request){
           if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Add Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
                 $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'iso' => 'required|max:4',
                ]);
                
                 if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }    


                    $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                 $data = [
                   'name'  => $request->input('name'),
                   'iso'  => $request->input('iso'),
               ];

              //$request->input('password') = ;    //setting 
            $insert =   Country::insertgetId($data);
                if(!empty($insert)){
                        return redirect('/countries')->with('success', 'Country Added Successfully!');
                }

            }
            $userdata = User::where([ 'email' => $user['email'] ])->first();
             return view('superadmin.addcountry')->with('userdata',$userdata)->with('title',$title);
        }

    }
        /*
    |--------------------------------------------------------------------------
    | Function : deleteadmin
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Admin Listing
    */
     
     public function editcountry(Request $request,$id = null){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Admin';
            $subtitle = '';         
            $admin = Country::where(['_id' =>  $id ])->first();
            if(count($request->all()) > 0){
                $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'iso' => 'required|max:4',
                ]);

                 if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }

                $data = [
                   'name'  => $request->input('name'),
                   'iso'  => $request->input('iso'),
               ];

            $insert =   Country::where('_id',  $request->input('_id'))->update($data);
                if($insert){
                    return redirect('/countires')->with('success', 'Country Updated Successfully!');
                }   
            }

         $userdata = User::where(['email' => $user['email'] ])->first();
         return view('superadmin.editcountry')->with('userdata',$userdata)->with('title',$title)->with('admin',$admin);
        }
     }

      /*
    |--------------------------------------------------------------------------
    | Function : deletesuperadmin
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Admin Listing
    */
     public function deletecountry(Request $request){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
            $insert =   Country::where('_id',  $request->input('origin'))->delete();
                if($insert){
                    if($request->ajax()){
                        return Response::json(['status' => 'success'],200);
                    }
                    return redirect('/countries')->with('success', 'Deleted Successfully!');
                }   
            }
         
        }

     }

    /*
    |--------------------------------------------------------------------------
    | Function : cities
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function cities(){
        if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Cities Listing';
            $subtitle = '';
         $userdata = User::where(['email' => $user['email'] ])->first();
         $admins = City::get();
         return view('superadmin.cities')->with('userdata',$userdata)->with('title',$title)->with('admins',$admins);
        }
    }

        /*
    |--------------------------------------------------------------------------
    | Function : addcountry
    |--------------------------------------------------------------------------
    | This will be used to display the list of all admins to the super admin
    */
     public function addcity(Request $request){
           if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Add City';
            $subtitle = '';
            if(count($request->all()) > 0){
                 $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'code' => 'required|max:50',
                    'country_id' => 'required',
                ]);
                
                 if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }    


                    $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                 $data = [
                   'name'  => $request->input('name'),
                   'code'  => $request->input('code'),
                   'country_id'  => $request->input('country_id'),
               ];

              //$request->input('password') = ;    //setting 
            $insert =   City::insertgetId($data);
                if(!empty($insert)){
                        return redirect('/cities')->with('success', 'City Added Successfully!');
                }

            }
            $userdata = User::where([ 'email' => $user['email'] ])->first();
            $countries = Country::pluck('name','_id')->all();
             return view('superadmin.addcity')->with('userdata',$userdata)->with('title',$title)
             ->with('countries',$countries);
        }

    }

            /*
    |--------------------------------------------------------------------------
    | Function : deleteadmin
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Admin Listing
    */
     
     public function editcity(Request $request,$id = null){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Admin';
            $subtitle = '';         
            $admin = City::where(['_id' =>  $id ])->first();
            if(count($request->all()) > 0){
                $token = md5($request->input('email')).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                   $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'code' => 'required|max:50',
                    'country_id' => 'required',
                ]);

                 if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }

               $data = [
                   'name'  => $request->input('name'),
                   'code'  => $request->input('code'),
                   'country_id'  => $request->input('country_id'),
               ];

            $insert =   City::where('_id',  $request->input('_id'))->update($data);
                if($insert){
                    return redirect('/cities')->with('success', 'City Updated Successfully!');
                }   
            }
         $userdata = User::where(['email' => $user['email'] ])->first();
            $countries = Country::pluck('name','_id')->all();
         return view('superadmin.editcity')->with('userdata',$userdata)->with('title',$title)->with('admin',$admin)
          ->with('countries',$countries);
        }
     }

        /*
    |--------------------------------------------------------------------------
    | Function : deletecity
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Cities Listing
    */
     public function deletecity(Request $request){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
            $insert =   City::where('_id',  $request->input('origin'))->delete();
                if($insert){
                    if($request->ajax()){
                        return Response::json(['status' => 'success'],200);
                    }
                    return redirect('/countries')->with('success', 'Deleted Successfully!');
                }   
            }
         
        }

     }

     /*
    |--------------------------------------------------------------------------
    | Function : deletesuperadmin
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Admin Listing
    */
     public function profile(Request $request){
        if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Profile';
            $subtitle = '';
            $userdata = User::where(['email' => $user['email'] ])->first();

            if(count($request->all())){
                if($request->file('image') !== null){
                     $old_image = (isset($userdata->image)) ? $userdata->image : '';
                       $photoName = md5($userdata->_id).time().'.'.$request->file('image')->getClientOriginalExtension();
                     $oldimageurl =   getcwd().'/images/users/'.$old_image;
                     if (is_file($oldimageurl)){ /*if there is file found in the folder*/
                          unlink(str_replace("\\","/",$oldimageurl)); /*deleting older image*/
                      }
                     /*
                    talk the select file and move it public directory and make avatars
                    folder if doesn't exsit then give it that unique name.
                    */
                    $request->file('image')->move('images/users', $photoName);
                     $data = [
                           'image'  => $photoName
                       ];
                    $insert =   User::where('_id',  $userdata->_id)->update($data);  
                    return redirect('/profile')->with('success', 'Profile Updated Successfully!'); 

                }
                  

            }
         
         return view('profile')->with('userdata',$userdata)->with('title',$title);
        }

     }
      /*
    |--------------------------------------------------------------------------
    | Function : deletesuperadmin
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Admin Listing
    */
    public function getcities(Request $request){
         if(count($request->all()) > 0){
            $cities = City::where('country_id', $request->input('country_id'))->get();
               // if($insert){
                    if($request->ajax()){
                        return Response::json(['status' => 'success','cities' => $cities ],200);
                    }
                    return redirect('/countries')->with('success', 'Deleted Successfully!');
                //}   
            }
    }
    

   
}