<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api'], function () {


  //==============
	Route::post('login',['as' => 'login','uses' => 'ApiController@login']);
	Route::post('register',['as' => 'register','uses' => 'ApiController@register']);
	Route::post('testingtime',['as' => 'testingtime','uses' => 'ApiController@testingtime']); 
	Route::post('verification',['as' => 'verification','uses' => 'ApiController@verification']);
	Route::post('resend_code',['as' => 'resend_code','uses' => 'ApiController@resend_code']);
	Route::post('forgot_password',['as' => 'forgot_password','uses' => 'ApiController@forgot_password']);
 	Route::post('fetch_profile',['as' => 'fetch_profile','uses' => 'ApiController@fetch_profile']);
  	Route::post('get_groups',['as' => 'get_groups','uses' => 'ApiController@get_groups']);
  	Route::post('get_groupstaff',['as' => 'get_groupstaff','uses' => 'ApiController@get_groupstaff']);
    Route::post('get_groupusers',['as' => 'get_groupusers','uses' => 'ApiController@get_groupusers']);
    Route::get('no',['as' => 'no','uses' => 'ApiController@no']);
	Route::post('get_groupdetails',['as' => 'get_groupdetails','uses' => 'ApiController@get_groupdetails']);
	Route::post('send_message',['as' => 'send_message','uses' => 'ApiController@send_message']);
	Route::post('get_chat',['as' => 'get_chat','uses' => 'ApiController@get_chat']);
Route::post('qrcode_scan',['as' => 'qrcode_scan','uses' => 'ApiController@qrcode_scan']);
Route::post('Facebook_Login',['as' => 'Facebook_Login','uses' => 'ApiController@Facebook_Login']);
Route::post('search_group_users',['as' => 'search_group_users','uses' => 'ApiController@search_group_users']);

// simple grroup 
Route::post('send_privatemessage',['as' => 'send_privatemessage','uses' => 'ApiController@send_privatemessage']);


// that is in chat controller chats 
Route::post('helpdeskChat',['as' => 'helpdeskChat','uses' => 'ApiController@helpdeskChat']);
Route::post('closechat',['as' => 'closechat','uses' => 'ApiController@closechat']);
Route::post('getreceiver',['as' => 'getreceiver','uses' => 'ApiController@getreceiver']);
Route::post('help_api',['as' => 'help_api','uses' => 'ApiController@help_api']);


//============================================
 

    Route::post('update_profile',['as' => 'update_profile','uses' => 'ApiController@update_profile']);
    Route::post('profile',['as' => 'profile','uses' => 'ApiController@profile']);

    
	//===========================********==================================================
	Route::post('get_services',['as' => 'get_services','uses' => 'ApiController@get_services']);
	Route::post('get_public_group_list',['as' => 'get_public_group_list','uses' => 'ApiController@get_public_group_list']);
	Route::post('delete_group',['as' => 'delete_group','uses' => 'ApiController@delete_group']);
	Route::post('chat_on_off',['as' => 'chat_on_off','uses' => 'ApiController@chat_on_off']);
	Route::post('block',['as' => 'block','uses' => 'ApiController@block']);
	Route::post('get_refrence',['as' => 'get_refrence','uses' => 'ApiController@get_refrence']);
	//==============================================================================
	Route::post('change_password',['as' => 'change_password','uses' => 'ApiController@change_password']);
	Route::post('update_deviceid',['as' => 'update_deviceid','uses' => 'ApiController@update_deviceid']);
	Route::post('edit_profile',['as' => 'edit_profile','uses' => 'ApiController@edit_profile']);
	Route::post('chat_feedback',['as' => 'chat_feedback','uses' => 'ApiController@chat_feedback']);
	Route::post('feedback',['as' => 'feedback','uses'=>'ApiController@feedback']); 
});

