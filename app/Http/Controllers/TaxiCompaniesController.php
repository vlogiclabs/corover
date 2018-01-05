<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\UsersModel as User;
use App\TaxiCompany as TaxiCompany;
use Validator;
use Illuminate\Support\Facades\Hash;
use Response;

class TaxiCompaniesController extends Controller
{
     /*
    |--------------------------------------------------------------------------
    | Function : taxicompanies
    |--------------------------------------------------------------------------
    | This will be used to display the list of all taxi companies to the super admin
    */
     public function taxicompanies(){
        if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Taxi Companies Listing';
            $subtitle = '';
         $userdata = User::where(['token' => $user['token'], 'email' => $user['email'] ])->first();
         $admins = TaxiCompany::get();
         return view('superadmin.taxicompanies')->with('userdata',$userdata)->with('title',$title)->with('admins',$admins);
        }
    }

     /*
    |--------------------------------------------------------------------------
    | Function : addtaxicompany
    |--------------------------------------------------------------------------
    | This will be used to Add new Taxi Company
    */
     public function addtaxicompany(Request $request){
           if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Add Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
                  $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'address' => 'required',
                    'email' => 'required|unique:taxi_companies',
                    'owner_id' => 'required',
                ]);
                   if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }

                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                //$admin = User::where(['_id' =>  $request->input('owner_id') ])->first();
                 $data = [
                   'name'  => $request->input('name'),
                   'address'  => $request->input('address'),
                   'email'  => $request->input('email'),
                   'phone'  => $request->input('phone'),
                   'owner_id' => $request->input('owner_id'),
               ];

              //$request->input('password') = ;    //setting 
            $insert =   TaxiCompany::insertgetId($data);
                if(!empty($insert)){
                        return redirect('/taxi-companies')->with('success', 'Company Added Successfully!');
                }

            }
            $allusers = User::pluck('first_name','_id')->all();
            //dd($allusers);
            $userdata = User::where(['token' => $user['token'], 'email' => $user['email'] ])->first();
             return view('superadmin.addtaxicompany')->with('userdata',$userdata)->with('title',$title)
                    ->with('allusers',$allusers);
        }

    }

   
    /*
    |--------------------------------------------------------------------------
    | Function : edittaxicompany
    |--------------------------------------------------------------------------
    | This will be used to Edit Record in Taxi Companies  Listing
    */
     
     public function edittaxicompany(Request $request,$id = null){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Taxi Company';
            $subtitle = '';         
            $admin = TaxiCompany::where(['_id' =>  $id ])->first();
            if(count($request->all()) > 0){
                      $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255',
                    'address' => 'required',
                    'email' => 'required|unique:taxi_companies',
                    'owner_id' => 'required',
                ]);
                      //dd($request->all());
                   if ($validator->fails()) {
                     return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                }
                /*ROLES :
                    1. Super-Admin
                    2. Admin
                    3. User
                    4. Driver
                  */
                //$admin = User::where(['_id' =>  $request->input('owner_id') ])->first();
                 $data = [
                   'name'  => $request->input('name'),
                   'address'  => $request->input('address'),
                   'email'  => $request->input('email'),
                   'phone'  => $request->input('phone'),
                   'owner_id' => $request->input('owner_id'),
               ];

              //$request->input('password') = ;    //setting 
            $insert =   TaxiCompany::where('_id',$request->input('id'))->update($data);
                if(!empty($insert)){
                        return redirect('/taxi-companies')->with('success', 'Company Updated Successfully!');
                } 
            }
         $allusers = User::pluck('first_name','_id')->all();   
         $userdata = User::where(['token' => $user['token'], 'email' => $user['email'] ])->first();
         return view('superadmin.edittaxicompany')->with('userdata',$userdata)->with('title',$title)->with('admin',$admin)->with('allusers',$allusers);
        }
     }

    /*
    |--------------------------------------------------------------------------
    | Function : deletetaxicompany
    |--------------------------------------------------------------------------
    | This will be used to Delete Record in Admin Listing
    */
     public function deletetaxicompany(Request $request){
         if (session()->has('loggedin')) {
            $user = session()->get('loggedin');
            $title = 'Edit Admin';
            $subtitle = '';
            if(count($request->all()) > 0){
            $insert =   TaxiCompany::where('_id',  $request->input('origin'))->delete();
                if($insert){
                    if($request->ajax()){
                        return Response::json(['status' => 'success'],200);
                    }
                    return redirect('/admins')->with('success', 'Registered Successfully!');
                }   
            }
         $userdata = User::where(['token' => $user['token'], 'email' => $user['email'] ])->first();
         return view('superadmin.editadmin')->with('userdata',$userdata)->with('title',$title)->with('admin',$admin);
        }

     }
    

   
}