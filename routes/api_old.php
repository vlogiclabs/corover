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
Route::post('get_groupmessage',['as' => 'get_groupmessage','uses' => 'ApiController@get_groupmessage']);
Route::post('search_group_users',['as' => 'search_group_users','uses' => 'ApiController@search_group_users']);
Route::post('qrcode_scan',['as' => 'qrcode_scan','uses' => 'ApiController@qrcode_scan']);








   //==================================================
 

    Route::post('facebook_login',['as' => 'facebook_login','uses' => 'ApiController@facebook_login']);  
    Route::post('update_profile',['as' => 'update_profile','uses' => 'ApiController@update_profile']);
    Route::post('profile',['as' => 'profile','uses' => 'ApiController@profile']);
 
});


INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12103', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12104', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12105', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12106', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12107', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12108', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12109', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12110', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12111', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12112', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12113', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12114', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12115', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12116', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12117', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12118', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12119', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12120', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12121', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12122', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12123', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12124', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12125', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12126', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12127', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12128', 'U', '2017-12-27 00:00:00');

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12129', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12130', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12131', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12132', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12133', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12134', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12135', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12136', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12137', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12138', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12139', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12140', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12141', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12142', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12143', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12144', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12145', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12146', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12147', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12148', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12149', 'U', '2017-12-27 00:00:00');







INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12150', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12151', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12152', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12153', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12154', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12155', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12156', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12157', 'U', '2017-12-27 00:00:00');







INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12158', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12159', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12160', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12161', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12162', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12163', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12164', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12165', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12166', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12167', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12168', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12169', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12170', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12172', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12173', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12174', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12175', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12176', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12177', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12178', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12179', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12181', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12182', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12183', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12184', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12185', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12186', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12187', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12188', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12189', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12190', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12191', 'U', '2017-12-27 00:00:00');




INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12192', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12193', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12194', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12195', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12196', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12197', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12198', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12199', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12200', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12201', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12202', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12203', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12204', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12205', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12206', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12207', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12208', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12209', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12210', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12211', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12212', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12213', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12214', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12215', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12216', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12217', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12218', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12219', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12220', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12221', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12222', 'U', '2017-12-27 00:00:00');









INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12223', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12224', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12225', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12226', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12227', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12228', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12229', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12230', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12231', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12232', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12233', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12234', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12235', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12236', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12237', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12238', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12239', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12240', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12241', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12242', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12243', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12244', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12245', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12246', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12247', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12248', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12249', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12250', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12251', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12252', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12253', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12254', 'U', '2017-12-27 00:00:00');











INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12255', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12256', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12257', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12258', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12259', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12260', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12261', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12262', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12263', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12264', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12265', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12266', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12267', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12268', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12269', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12270', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12271', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12272', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12273', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12274', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12275', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12276', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12277', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12278', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12279', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12280', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12281', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12282', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12283', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12284', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12285', 'U', '2017-12-27 00:00:00');







INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12286', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12287', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12288', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12289', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12290', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12291', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12292', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12293', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12294', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12295', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12296', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12297', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12298', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12299', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12300', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12301', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12302', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12303', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12304', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12305', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12306', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12307', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12308', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12309', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12310', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12311', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12312', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12313', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12314', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12315', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12316', 'U', '2017-12-27 00:00:00');







INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12317', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12318', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12319', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12320', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12321', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12322', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12323', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12324', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12325', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12326', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12327', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12328', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12329', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12330', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12331', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12331', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12332', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12333', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12334', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12335', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12336', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12337', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12338', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12339', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12340', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12341', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12342', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12343', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12344', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12345', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12346', 'U', '2017-12-27 00:00:00');





INSERT INTO `jktourismnew`.`group_members` (`id`, `group_id`, `user_id`, `type`, `created`) VALUES 
(NULL, '41', '12347', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12348', 'U', '2017-12-27 00:00:00'), 
(NULL, '41', '12349', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12350', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12351', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12352', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12353', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12354', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12355', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12356', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12357', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12358', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12359', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12360', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12361', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12362', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12363', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12364', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12365', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12366', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12367', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12368', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12369', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12370', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12371', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12372', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12373', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12374', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12375', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12376', 'U', '2017-12-27 00:00:00'),
(NULL, '41', '12377', 'U', '2017-12-27 00:00:00');







285



sudo nano /etc/php/7.0/apache2/php.ini


ALTER TABLE `users` ADD `role` VARCHAR(255) NOT NULL AFTER `modified`, ADD `token` VARCHAR(255) NOT NULL AFTER `role`, ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `token`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `users` ADD `timezone` VARCHAR(255) NOT NULL AFTER `token`;


comapnt_id,admin_id,username

LTER TABLE `users` CHANGE `unique_id_F` `unique_id_F` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` CHANGE `access_token_F` `access_token_F` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` CHANGE `new_countrycode` `new_countrycode` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


ALTER TABLE `users` CHANGE `new_mobile` `new_mobile` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` CHANGE `large_image` `large_image` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` CHANGE `logo_image` `logo_image` VARCHAR(2000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


ALTER TABLE `users` CHANGE `group_id` `group_id` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `unique_id_L` `unique_id_L` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `access_token_L` `access_token_L` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


ALTER TABLE `users` CHANGE `age` `age` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` CHANGE `latitude` `latitude` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `longitude` `longitude` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


ALTER TABLE `users` CHANGE `city` `city` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `state` `state` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `designation` `designation` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;


ALTER TABLE `users` CHANGE `device_id` `device_id` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` CHANGE `group` `group` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` CHANGE `privatestatus_offtime` `privatestatus_offtime` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `groupstatus_offtime` `groupstatus_offtime` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `bluetooth_mac` `bluetooth_mac` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL, CHANGE `type` `type` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `modified` `modified` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;






============== important links 
https://askubuntu.com/questions/44394/apt-get-update-error-http-extras-ubuntu-com-public-key-unavailable

https://askubuntu.com/questions/821550/how-to-fix-the-error-while-updating-my-package-list-in-ubuntu-16-04


https://askubuntu.com/questions/307/how-can-ppas-be-removed