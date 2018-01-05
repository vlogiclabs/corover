<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('admin', function () {
    return view('admin_template');
});

Route::get('show', function () {
    return view('show');
});

Route::get('/', 'UsersController@login');


Route::get('/logout', function () {
	Session()->flush();
	return redirect('/');
   // return view('login');
});

Route::get('users', 'home@index');

//=======================START API  CODE =============

Route::post('testing', 'ServicesController@testing');

//======================END API CODE =================


Route::post('loginuser','Auth\LoginController@login');

//Route::post('loginuser',['uses' => 'UsersController@login']);

Route::get('register', function () {
    return view('register');
});

Route::get('te', 'UsersController@testing');

Route::post('reguser',['uses' => 'UsersController@store']);
/*------------------------/
| Login Protected Routes
| Created on : 8-09-2017
| Middleware session_check used to check whether logged in or not :
|
\*------------------------*/
Route::group(['middleware' => 'session_check'], function () {
	Route::get('home',['uses' => 'UsersController@home']);
	
	/*Super Admin Routes*/
	Route::get('admins',['uses' => 'UsersController@admins']);
	Route::get('add-admin',['uses' => 'UsersController@addadmin']);
	Route::post('add-admin',['uses' => 'UsersController@addadmin']);
	Route::get('edit-admin/{id}',['as'=>'edit-admin','uses' => 'UsersController@editadmin']);
	Route::post('edit-admin',['as'=>'edit-admin','uses' => 'UsersController@editadmin']);
	Route::post('delete-admin',['as'=>'deleteadmin','uses' => 'UsersController@deleteadmin']);
	Route::get('profile',['as'=>'profile','uses' => 'UsersController@profile']);
	Route::post('profile',['as'=>'profile','uses' => 'UsersController@profile']);

	//add super admin routes
	Route::get('superadmins',['uses' => 'UsersController@superadmins']);
	Route::get('add-super-admin',['uses' => 'UsersController@addsuperadmin']);
	Route::post('add-super-admin',['uses' => 'UsersController@addsuperadmin']);
	Route::get('edit-super-admin/{id}',['as'=>'edit-admin','uses' => 'UsersController@editsuperadmin']);
	Route::post('edit-super-admin',['as'=>'edit-admin','uses' => 'UsersController@editsuperadmin']);
	Route::post('delete-super-admin',['as'=>'deleteadmin','uses' => 'UsersController@deletesuperadmin']);
	//Taxi-Companies Routes
	Route::get('taxi-companies',['uses' => 'TaxiCompaniesController@taxicompanies']);
	Route::get('add-taxi-company',['uses' => 'TaxiCompaniesController@addtaxicompany']);
	Route::post('add-taxi-company',['uses' => 'TaxiCompaniesController@addtaxicompany']);
	Route::get('edit-taxi-company/{id}',['as'=>'edit-taxi-company','uses' => 'TaxiCompaniesController@edittaxicompany']);
	Route::post('edit-taxi-company',['as'=>'edit-taxi-company','uses' => 'TaxiCompaniesController@edittaxicompany']);
	Route::post('delete-taxi-company',['uses' => 'TaxiCompaniesController@deletetaxicompany']);
	//Dirivers
	Route::get('drivers',['uses' => 'UsersController@drivers']);
	Route::get('add-driver',['uses' => 'UsersController@adddriver']);
	Route::post('add-driver',['uses' => 'UsersController@adddriver']);
	Route::get('edit-driver/{id}',['as'=>'edit-admin','uses' => 'UsersController@editdriver']);
	Route::post('edit-driver',['as'=>'edit-admin','uses' => 'UsersController@editdriver']);
	Route::post('delete-driver',['as'=>'deleteadmin','uses' => 'UsersController@deletesuperadmin']);
	//Countries 
	Route::get('countries',['uses' => 'UsersController@countries']);
	Route::get('add-country',['uses' => 'UsersController@addcountry']);
	Route::post('add-country',['uses' => 'UsersController@addcountry']);
	Route::get('edit-country/{id}',['as'=>'edit-admin','uses' => 'UsersController@editcountry']);
	Route::post('edit-country',['as'=>'edit-admin','uses' => 'UsersController@editcountry']);
	Route::post('delete-country',['as'=>'deleteadmin','uses' => 'UsersController@deletecountry']);
	//Cities
	Route::get('cities',['uses' => 'UsersController@cities']);
	Route::get('add-city',['uses' => 'UsersController@addcity']);
	Route::post('add-city',['uses' => 'UsersController@addcity']);
	Route::get('edit-city/{id}',['as'=>'edit-admin','uses' => 'UsersController@editcity']);
	Route::post('edit-city',['as'=>'edit-admin','uses' => 'UsersController@editcity']);
	Route::post('delete-city',['as'=>'deleteadmin','uses' => 'UsersController@deletecity']);
	Route::post('get-cities',['as'=>'edit-admin','uses' => 'UsersController@getcities']);
	
	/*Super Admin Routes*/

});
/*Login Protected Routes*/