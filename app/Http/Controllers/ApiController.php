<?php

namespace App\Http\Controllers;
use MongoDB\BSON\ObjectID as obj;
use Illuminate\Http\Request;
use DB;
use App\ApiModel as Api;
use Response;
use Hash;
use DateTime;
use DateTimeZone;
//use Mail;
use App\Helpers; // include the helper 
use App\Register;
use App\Supportchat;
use Validator;
use Carbon\Carbon;
use App\Group;
use App\GroupMember;
use App\Newbroadcast;
use App\HelpdeskBadgesForApp;
/*use App\Chatbadge;
*/
use App\Unreadgroupbadge;
use App\Groupinformation;
use App\Block;
use App\Grouptype;
use App\CheckSetting;
use Intervention\Image\Facades\Image as Image;
use App\GroupModel as Groups;
use App\ChatModel as MongoChat;
use App\GroupChatModel as GroupMongoChat;
use App\GroupBadge as GroupBadge;
use App\ChatBadge as ChatBadge;
use App\ServiceGroup;
use App\User;
use App\Chats;
use App\Service; 
use App\Feedback;
use Illuminate\Support\Facades\Mail;

// Socket add
/*use Fabiang\Xmpp\Options;
use Fabiang\Xmpp\Client;
use Fabiang\Xmpp\Protocol\Roster;
use Fabiang\Xmpp\Protocol\Presence;
use Fabiang\Xmpp\Protocol\Message;


use Xmpp\Xep\Xep0045 as xmpp;
use Psr\Log\LoggerInterface;*/

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Fabiang\Xmpp\Options;
use Fabiang\Xmpp\Client;
use Fabiang\Xmpp\Protocol\Roster;
use Fabiang\Xmpp\Protocol\Presence;
use Fabiang\Xmpp\Protocol\Message;
class ApiController extends Controller
{


public function no(Request $request){

}
   #_________________________________________________________________________#

    /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/
// comment :  we check if record exist
     // then check login type - Guest or User
     // if G means guest then update all data 
     // if N same update data 
     // else register the new user 

public function register(Request $request)
{
        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         $result = array();
         if($status=="true")
		 {
          if(!empty($request->file('image')))
		  {
                $image =  $helper->upload($request->file('image'),'users','none');
          }
		  else
		  {
               $image =  url('/').'/images/users/no_image.png';
          } 
            // after check auth key 
            // check the fields 
             $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'mobile' => 'required',
                    //'email' => 'required',
                    'password' => 'required',
                   // 'address' => 'required',
                   // 'designation' => 'required',
                   // 'gender' => 'required',
                    'timezone'=>'required',
                   // 'country_code'=>'required', 
					'image'=>'required',
                ]);
              if ($validator->fails()) 
			  {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
              }
			  else
			  {
                   // start code from here after all validation 
                   // file upload by common function  we get the file name in $imagename  variable
                    $CheckExit = Register::where(['mobile' => $saveArray['mobile'],'status'=>'1','deleted' => '0' ])->first();
                          if(count($CheckExit)>0)
							{
                            // if find check login type 
                               if($CheckExit->login_type=="G")
							   {
                                // if login by guest login then send Otp and update all data
                                   $Get= $helper->CallCoomin($saveArray,$image,$CheckExit->id,'I');
                                     $result = array('status'=>'1','message'=>'Register Successfully','id'=>$Get,'is_verified'=>'not verified');                              
                               }
							   else if($CheckExit->is_verified=="Y" || $CheckExit->is_verified=="C")
							   {
                                    // already exist hai
                                     $result = array('status'=>'0','message'=>'Already Registered','is_verified'=>'verified');
                               }
							   else
							   {
                                  // not verified need to be verified
                                  $Get= $helper->CallCoomin2($saveArray,$image,$CheckExit->id);
                                   $result = array('status'=>'0','message'=>'Already Registered but need to verified','is_verified'=>'not verified','id'=>$Get); 
                                }
                           }
						   else
						   {
                                 // start else part
                                // if not exist then save the data 
                                // save in database 
                              $Get= $helper->SaveRegister($saveArray,$image);
                              $result = array('status'=>1,'message' => "Register Successfully",'id'=>$Get,'is_verified'=>'not verified');
						   } // end else part
                  }// validation falils or not else
            }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
		 
		$data['result'] = $result;
		return Response::json($data,200);
      die;
}


 /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

  public function login(Request $request){
       $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         $result = array();
         if($status=="true"){
             $validator = Validator::make($request->all(), [
                    'country_code' => 'required',
                    'mobile' => 'required',
                    'password' => 'required',
                ]);
              if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                    $CheckExit = Register::where(['mobile' => $saveArray['mobile'],'status'=>'1','deleted' => '0','original_password'=>$saveArray['password']])->first();
                      if(count($CheckExit)>0){
                         if($CheckExit->user_type=="S" && $CheckExit->is_verified == "Y"){
                            $result = array('status'=>'1','message'=>'Login Successfully','id'=>$CheckExit->id,'user_type' => $CheckExit->user_type,'user_name' => $CheckExit->name);
                         }else if($CheckExit->user_type=="A" && $CheckExit->is_verified == "Y"){
                            $result = array('status'=>'1','message'=>'Login Successfully','id'=>$CheckExit->id,'user_type' => $CheckExit->user_type,'user_name' => $CheckExit->name);
                         }else{
                              if(!empty($CheckExit->new_mobile) || !empty($CheckExit->new_countrycode)){
                                    if($CheckExit->is_verified=="Y" || $CheckExit->is_verified =="C"){
                                        $result = array('status'=>'1','message'=>'Login Successfully','id'=>$CheckExit->id,'user_type' => $CheckExit->user_type,'user_name' => $CheckExit->name);
                                     }else if($CheckExitis_verified=="I"){
                                        $Get= $helper->loginChecker($CheckExit);
                                        $result=array('status'=>'1','message'=>'Need to verify','id'=>$CheckExit->id,'user_type' => $CheckExit->user_type,'user_name' => $CheckExit->name);
                                    }
                              }else{
                                  if($CheckExit->is_verified == "Y"){
                                     $result = array('status'=>'1','message'=>'Login Successfully','id'=>$CheckExit->id,'user_type' => $CheckExit->user_type,'user_name' => $CheckExit->name);
                                  }else if($CheckExit->is_verified == "I"){
                                     $result=array('status'=>'1','message'=>'Need to verify','id'=>$CheckExit->id,'user_type' => $CheckExit->user_type,'user_name' => $CheckExit->name);
                                  }
                              }
                         }
                       }else{
                       $result=array('status'=>'0','message'=>'Password or mobile number is incorrect');
                      } // end 
                  }// validation
            }else{
            $result = array('status'=>0,'message' => "Auth Key not matched");
         }
		$data['result'] = $result;
		return Response::json($data,200);
      die;
  } 

 /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

public function verification(Request $request){
  $saveArray =  $request->all();
  $helper = new Helpers();
  $status= $helper->CheckAuthKey($saveArray);
  if($status=="true"){ 
   $validator = Validator::make($request->all(), [
        'user_id' => 'required',
        'verification_code' => 'required',
    ]);
   if ($validator->fails()) {
      $result = array('status'=>0,'message' =>"Please fill all fields");
    }else{
       // start code after validating
       $CheckExit = Register::where(['id' => $saveArray['user_id'],'verification_code'=>$saveArray['verification_code']])->first();
       if(count($CheckExit)>0){
             // if exist then check the new_mobile key 
             if(!empty($user_exist['User']['new_mobile']) OR !empty($user_exist['User']['new_countrycode'])){
                    // call the common function and pass paramertes userddata or passing data with post
                    $helper->VerificationCommon($CheckExit,$saveArray);
                    $result = array('status' => '1', 'message' => "Verified Successfully", 'user_id' => $CheckExit->id);
             }else{
                //update the user records
                // call the common function and pass paramertes userddata or passing data with post
                $helper->VerificationCommon($CheckExit,$saveArray);
                $result = array('status' => '1', 'message' => "Verified Successfully", 'user_id' => $CheckExit->id);
             } // else part end
        }else{
           $result = array('status' => '0', 'message' => "Wrong Information");
       }// checkexist else end 
   } // validating wala else
  }else{
  $result = array('status'=>0,'message' => "Auth Key not matched");
  }
   $data['result'] = $result;
   return Response::json($data,200);
   die;
}

 /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

  public function resend_code(Request $request){
      $saveArray =  $request->all();
       $helper = new Helpers();
       $status= $helper->CheckAuthKey($saveArray);
       if($status=="true"){ 
             $validator = Validator::make($request->all(), [
                  'user_id' => 'required',
              ]);
             if ($validator->fails()) {
                $result = array('status'=>0,'message' =>"Please fill all fields");
              }else{
               $CheckExit = Register::where(['id' => $saveArray['user_id']])->first();
               if(count($CheckExit)>0){
                  // add the plus at the user number check its new number or mobile number    
                  if(!empty($CheckExit->new_mobile) && !empty($CheckExit->new_countrycode)){
                     $number = '+'. $CheckExit->new_countrycode . $CheckExit->new_mobile;
                  }else{
                     $number = '+'. $CheckExit->country_code . $CheckExit->mobile;
                  }
                  // first get the user number then send otp of this nuber
                  // check user verirfird status or logintype
                  // call common function to send otp and update user data  
                  // Resend_Checker is a common function in it
                  // check again next condition
                  if ($CheckExit->is_verified == "C" OR  $CheckExit->is_verified == "I" ) { 
                      $Get_Code= $helper->Resend_Checker($number,$CheckExit);
                      $result = array('status' => '1', 'message' => "Code sent to registered mobile number",'Code'=>$Get_Code);
                   }else if($CheckExit->is_verified == "Y" && $CheckExit->login_type == "G"){
                       $Get_Code= $helper->Resend_Checker($number,$CheckExit);
                       $result = array('status' => '1', 'message' => "Code sent to registered mobile number",'Code'=>$Get_Code);
                   }else if($CheckExit->is_verified == "Y" && $CheckExit->login_type == "U"){
                      $Get_Code= $helper->Resend_Checker($number,$CheckExit);
                      $result = array('status' => '1', 'message' => "Code sent to registered mobile number",'Code'=>$Get_Code);
                   }
               }else{
                  $result = array('status' => '0', 'message' => "You are not Register. Firstly Sign Up.");
                } 
              }
       }else{
           $result = array('status'=>0,'message' => "Auth Key not matched");
       }
		$data['result'] = $result;
		return Response::json($data,200);
  die;
  }





public function forgot_password(Request $request){
        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'email' => 'required_without_all:mobile',
                    'mobile' => 'required_without_all:email',
                    'country_code'=> 'required_without_all:mobile,email',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                       // First Check not Empty Email 
                       if(!empty($saveArray['email'])){
                          $CheckExit = Register::where(['email' => $saveArray['email'],'status'=>'1',
                            'deleted'=>'0'])->first();
                           if(count($CheckExit)>0){
                                //call common function for save  data 
                                $Get=$helper->Forgot_Checker($CheckExit);
                               $result=array('status'=>'1','message'=>'Password changed,check you mobile inbox');
                            }else{
                             $result=array('status'=>'0','message'=>'This user not exist');
                           }
                       }
                     // Check Next Condition if both are not empty
                     if(!empty($saveArray['mobile']) And !empty($saveArray['country_code'])){
                         $CheckExit = Register::where(['mobile' => $saveArray['mobile'],'status'=>'1',
                        'deleted'=>'0','country_code'=>$saveArray['country_code']])->first();
                        if(count($CheckExit)>0){
                          // common function in helper file to send otp and get code
                            $Get= $helper->Forgot_Checker_Mobile($CheckExit);
                            $result=array('status'=>'1','message'=>'Password changed,check you mobile inbox');
                        }else{
                           $result=array('status'=>'0','message'=>'This user not exist');
                        }

                     } // Next con end

                } //else part end
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
		$data['result'] = $result;
		return Response::json($data,200);
    die;
}


/**/

public function fetch_profile(Request $request){
        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                    $CheckExit = Register::where(['id' => $saveArray['user_id'],'status'=>'1',
                        'deleted'=>'0'])->first();
                     if(count($CheckExit)>0){
                        if(!empty($CheckExit->image)){
                           $image =  url('/').'/images/users/small'.$CheckExit->image;
                           }else{
                           $image =  url('/').'/images/users/no_image.png';
                          } 
                         //Taken Blank Array to put the result in it. i mean response
                          $record=array();
                          $record['user_id'] = $CheckExit->id;
                          $record['name'] = $CheckExit->name;
                          $record['email'] = $CheckExit->email;
                          $record['country_code'] = $CheckExit->country_code;
                          $record['mobile'] = $CheckExit->mobile;
                          $record['new_countrycode'] = $CheckExit->new_countrycode;
                          $record['new_mobile'] = $CheckExit->new_mobile;
                          $record['image'] = $image;
                          $record['gender'] = $CheckExit->gender;
                          $record['age'] = $CheckExit->age;
                          $record['designation'] = $CheckExit->designation;
                          $record['address'] = $CheckExit->address;
                          $record['user_type'] = $CheckExit->user_type;
                          $result=array('status'=>'1','result'=>$record);
                     }else{
                       $result=array('status'=>'0','message'=>'This user not exist');
                     }
                } // else end
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
		$data['result'] = $result;
		return Response::json($data,200);
    die;
}

public function get_groups(Request $request)
{
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
          if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    //'page_no'=>'required',
                ]);
                if($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                  $CheckExit = Register::where(['id' => $saveArray['user_id'],'status'=>'1',
                        'deleted'=>'0'])->first();
                   if(count($CheckExit)>0){
                         //===============FIRST CHECK WHEN USER TYPE U =========================//
                      if($CheckExit->user_type == "U"  ||  $CheckExit->user_type == "S"){
                          
                          $All_Groups = Group::whereRaw('FIND_IN_SET('.$saveArray['user_id'].',user_id)')
                          ->where(['status'=>'1','deleted'=>'0'])
                          ->orderby('updated_at','desc')
                          //->with('BroadCast')
                          ->get();
                           if(count($All_Groups)>0){
                                 foreach($All_Groups as $v){
                                   // get the last broad cast messge with common function 
                                   $Get=$helper->Group_BroadCast_Message($v->id,$saveArray['user_id']);
                                     // get the badges count
                                     // pass the three param
                                     // 1 ) user_id,2) group_id, group has helpdesk or not 
                                  $Bagdes=$helper->Group_Badges($v->id,$saveArray['user_id'],$v->has_helpdesk);
                                  $Final_Array = $helper->Get_FinaelArray($v,$Bagdes,$Get);
                                   $data1[] = $Final_Array;
                                 }
                                  if (!empty($data1)) {
                                     $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>"0");
                                  } else {
                                     $result = array('status' => '0', 'message' => 'Groups not found.');
                                  }
                           }else{
                             $result = array('status' => '0', 'message' => 'Groups not found.');
                           }

                      }else if($CheckExit->user_type == "A"){
             //===============Second CHECK WHEN USER TYPE Admin=========================//
                         $All_Groups = Group::where(['status'=>'1','deleted'=>'0','created_id'=>$saveArray['user_id']])
                          ->orderby('updated_at','desc')
                           ->get();
                           if(count($All_Groups)>0){
                                 foreach($All_Groups as $v){
                                  // get the last broad cast messge with common function 
                                  $Get=$helper->Group_BroadCast_Message($v->id,$saveArray['user_id']);
                                     // get the badges count
                                     // pass the three param
                                     // 1 ) user_id,2) group_id, group has helpdesk or not 
                                  $Bagdes=$helper->Group_Badges($v->id,$saveArray['user_id'],$v->has_helpdesk);
                                  $Final_Array = $helper->Get_FinaelArray($v,$Bagdes,$Get);
                                   $data1[] = $Final_Array;
                                 }
                                  if (!empty($data1)) {
                                     $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>"0");
                                  } else {
                                     $result = array('status' => '0', 'message' => 'Groups not found.');
                                  }
                           }else{
                             $result = array('status' => '0', 'message' => 'Groups not found.');
                           }

                      }
                  //===============Second CHECK WHEN USER TYPE Admin= END CODE ========================//

                   }else{
                           $result = array('status' => '0', 'message' => 'User not Found.');
                   }
                    
                } // else end
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
		$data['result'] = $result;
		return Response::json($data,200);
    die;
}
 



public function get_groupstaff(Request $request){
        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'group_id' => 'required',
                    'page_no' => 'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                  // Get all Staff Member of this Group only usertype S 
                   $offset = $saveArray['page_no']*10;
                   $limit = 10;
                   $Get_Group_Member =  GroupMember::offset($offset)->where(['group_id'=> $saveArray['group_id'],'type'=>'S'])->
                    where('user_id','!=', $saveArray['user_id'])
                     ->limit($limit)->with('GetUSer')->get();
                      if(count($Get_Group_Member)>0){
                        // for get the total page
                         $page_count = count($Get_Group_Member) / 10;
                         $page_count = ceil($page_count);
                          /* end get total page code*/
                        foreach($Get_Group_Member as $v){
                            if(!empty($v->GetUSer->image)){
                                  if($v->GetUSer->register_type=="F"){
                                    $image = $v->GetUSer->image;
                                   }  
                                  if($v->GetUSer->register_type=="N"){
                                      $image = url('/'). "images/users/small" . $v->GetUSer->image;
                                    }  
                               }else{
                                  $image= url('/')."/images/users/no_image.png";
                              }
                               $record[]=array(
                                      'user_id'=>$v->GetUSer->id,
                                      'name'=>$v->GetUSer->name,
                                      'image'=>$image,
                                      'designation'=>$v->GetUSer->designation,
                                      'mobile'=>$v->GetUSer->mobile,
                                  );
                           }

                        $result = array('status' => '1', 'message' => 'Successfully.','data' => $record,'totalPages'=>$page_count);
                      }else{
                        $result = array('status' => '0', 'message' => 'No Staff added to this group.');
                      }
                      
                 }
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
		$data['result'] = $result;
		return Response::json($data,200);
    die;
}



public function get_groupusers(Request $request){
        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_no' => 'required',
                    'group_id' => 'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                    /* check current user is block or not  */
                 $Get_Block_Member =  Block::where(['friend_id'=> $saveArray['user_id'],'group_id'=>$saveArray['group_id']])
                   ->get();
                 if(count($Get_Block_Member)>0){
                        $result = array('status' => '0', 'message' => 'This User not exist.');
                 }else{
                  // get the group type and get the type name from group type table with join
                   $Group_info =Group::where(['id' => $saveArray['group_id'],
                        'deleted'=>'0'])->with('GroupType')->first();
                   // get the Total number of staff in it 
                   $staff =  GroupMember::where(['group_id'=> $saveArray['group_id'],'type'=>'S'])->
                    where('user_id','!=', $saveArray['user_id'])
                     ->get();
                  /* if not block then further process */
                  // Get all Staff Member of this Group only usertype S 
                   $offset = $saveArray['page_no']*10;
                   $limit = 10;
                   $Get_Group_Member =  GroupMember::offset($offset)->where(['group_id'=> $saveArray['group_id'],'type'=>'U'])->
                    where('user_id','!=', $saveArray['user_id'])
                     ->limit($limit)->with('GetUSer')
                     ->with('GetGroupData')
                     ->orderby('updated_at','desc')
                     ->get();
                      if(count($Get_Group_Member)>0){
                        // for get the total page
                         $page_count = count($Get_Group_Member) / 10;
                         $page_count = ceil($page_count);
                          /* end get total page code*/
                        foreach($Get_Group_Member as $v){
                         //  dd($v->GetGroupData->id->GroupType());
                          $check_group_helpdeadkornot=  $v->GetGroupData->has_helpdesk;
                          // common function call and get the member group information
                          $member_information = $helper->Group_Member_Information($v->group_id,$v->user_id);
                          $getBlock= $helper->Group_Member_Block($v->group_id,$v->user_id);
                          $get_Badegs= $helper->Group_MEMber_Badges($v->group_id,$v->user_id,$check_group_helpdeadkornot);
                           $data= $helper->Get_Meber_FInalArray($v->GetUSer,$member_information,$getBlock,$get_Badegs,$Group_info->GroupType);
                            $data1[] = $data;
                           } // foreach
                      $result = array('status' => '1', 'message' => 'Successfully.','data' => $data1,'staff_count'=>count($staff), 'user_count'=>count($Get_Group_Member),'totalPages'=>$page_count);
                      }else{
                        $result = array('status' => '0', 'message' => 'No Staff added to this group.');
                      }

                 }

                }
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
		$data['result'] = $result;
		return Response::json($data,200);
    die;
}

  public function get_groupdetails(Request $request){
        $saveArray =  $request->all();
           $helper = new Helpers();
           $status= $helper->CheckAuthKey($saveArray);
           if($status=="true"){ 
                 $validator = Validator::make($request->all(), [
                      'user_id' => 'required',
                      'group_id' => 'required',
                  ]);
                 if ($validator->fails()) {
                    $result = array('status'=>0,'message' =>"Please fill all fields");
                  }else{
                   $CheckExit = Register::where(['id' => $saveArray['user_id'] ])->first();
                    if(count($CheckExit)>0){
                    $Group_info =Group::where(['id' => $saveArray['group_id'],
                    'deleted'=>'0'])->with('GroupType')->first();
                     $member_information = $helper->Group_Member_Information($saveArray['user_id'],$saveArray['user_id']);
                     $Group_Settings= $helper->Group_Setting($saveArray['group_id'],$saveArray['user_id']);
                      if(count($Group_info)>0){
                          $data['group_id'] = $Group_info->id;
                          $data['group_name'] = $Group_info->name;
                          $data['compnay_name'] = $Group_info->company_name;
                          $data['description'] = $Group_info->description;
                          $data['address'] =$Group_info->address;
                          $defaulturl = url('/')."images/common/dummy.jpg";
                          if(!empty($Group_info->icon)){

                          $data['logo'] = url('/') . "/img/group_logo/" . $Group_info->icon;
                          }else{
                          $data['logo'] = $defaulturl;
                          }
                          $data['qr_code'] = url('/') . "/img/" . $Group_info->image;
                          $data['type'] = $Group_info->type;
                          $data['allow_chat'] = $Group_info->allow_chat;
                          $data['private_chat'] = $Group_Settings['private_cht'];
                          $data['group_chat'] = $Group_Settings['group_cht'];
                          $data['admin_id'] = $Group_info->created_id;
                          $data['unique_id'] = $Group_info->qr_code;
                          $data['seat_no'] = $member_information['seat_no'];
                         
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data);

                      }else{
                        $result = array('status' => '0', 'message' => 'Groups not found.');
                      }
                    }else{
                        $result = array('status' => '0', 'message' => 'User id not found.');
                    }
                  } //else end
           }else{
               $result = array('status'=>0,'message' => "Auth Key not matched");
           }
        $data['result'] = $result;
		return Response::json($data,200);
  }
  



public function send_message(Request $request){
        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'group_id' => 'required',
                    'type' => 'required',
                    'lat' => 'required',
                    'long' => 'required',
                    'image' => 'required_without_all:message',
                    'message' => 'required_without_all:image',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                    $Group_data = Group::where(['id' => $saveArray['group_id'] ])->first();
                    $Get_Group_Member =  GroupMember::where(['group_id'=> $saveArray['group_id'],'type'=>'U'])->
                    where('user_id','!=', $saveArray['user_id'])
                    ->with('GetUSer')->paginate('1');
                    $User_data = Register::where(['id' => $saveArray['user_id'] ])->first();
                    if(count($User_data)>0){
                        // send notification to its group member
                        // $send_group_badges = $helper->Group_Notification_BAdge($Get_Group_Member,$saveArray['user_id']);
                       $date= $helper->Get_time($saveArray['timezone']);
                       $saveArray['submit_time'] = $date;
                       $saveData = $helper->Save_Data($saveArray);
                       $send_group_notiifcation = $helper->Group_Notification($Get_Group_Member,$saveArray['user_id'],$saveArray['group_id'],$User_data);
                      $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $saveData['groupimd']);
                    }else{
                       $result = array('status' => '0', 'message' => 'User id  or trip id does not exist');
                   }
                } // else 
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
     echo json_encode($result);
    die;
}



	public function get_services(Request $request) 
    {
        $saveArray = $request->all();  
		$helper = new Helpers();
        $status= $helper->CheckAuthKey($saveArray);
		if($status=="true")
		{ 
        if(!empty($saveArray['group_id']))
        {
            $result = array('status'=>'1');
            $service_group_data = ServiceGroup::where(['group_id'=>$saveArray['group_id']])->get();
            if(count($service_group_data) > '0')
            {
                foreach($service_group_data as $service_groups_data)
                {
                    echo $service_id = $service_groups_data->service_id;
                    
                    $service_tb_data = Service::where(['deleted'=>'0','id'=>$service_id])->get();
                   if(count($service_tb_data) > '0')
                   {
                       $record1 = array();
                        foreach ($service_tb_data as $service_tb1_data) 
                        {
                            $record1 =array('service_name'=>$service_tb1_data->name,
                                            'service_link'=>$service_tb1_data->link,
                                            'phone'=>$service_tb1_data->phone,
                                            'service_icon'=>url('/')  . "img/profile_images/" .$service_tb1_data->service_icon,);
                        }
                   } 
                   else 
                   {
                        $record1 = "";
                   }	
                }
                if(count($record1) > '0')
                {
                    $result=array('status'=>'1','message'=>'Success','services'=> $record1);
                }
                else
                {
                     $result = array('status' => '0', 'message' => 'No data');
                }
               
            }
            else
            {
               $result = array('status' => '0', 'message' => 'No data');
            }
        }
		
        else
        {
            $result = array('status'=>'0','message'=>'Please Fill All Fields');
        }
		}
		else{
			$result = array('status'=>0,'message' => "Auth Key not matched");
		}
		$data['result'] = $result;
		return Response::json($data,200);
        //return Response::json($result,200);
		die;
    }
	
	public function get_public_group_list(Request $request)
    {
        $saveArray = $request->all();
		$helper = new Helpers();
        $status= $helper->CheckAuthKey($saveArray);
		if($status=="true")
		{ 
        $group_type = "Pb";
        if(!empty($saveArray['user_id']))
        {
            $result = array('status'=>'1'); 
            $group_type = Group::where(['group_type'=>$group_type])->get();
            if($group_type)
            { 
                foreach ($group_type as $group_type_data) 
                {
                    $group_type_data->id;
                    $is_added = 'No';
                    if(isset($saveArray['user_id']))
                    {
                       $exists = GroupMember::where(['user_id'=>$saveArray['user_id'],'group_id'=>$group_type_data->id])->first();              
                       if(count($exists) > 0 )
                       {
                           $is_added = 'Yes';
                       }
                    }
                    $record[] = array(
                        'group_id'=> $group_type_data->id,
                        'group_name'=> $group_type_data->name,
                        'qrcode'=>$group_type_data->qr_code,
                        'group_icon'=>url('/'). "img/group_logo/" .$group_type_data->icon,
                        'location'=>$group_type_data->address,
                        'has_joined'=>$is_added,
                        'has_helpdesk'=> $group_type_data->has_helpdesk,
                        'country_code'=> $group_type_data->country_code,
                        'help_mobile'=> $group_type_data->mobile,
                        );

                }
               
                $result = array('status'=>'1', 'message' => 'Succesfully','data'=>$record);
            }
            else
			{
              
				$result = array('status' => '0', 'message' => 'Public Group Not Found');
			}
               
        }
        else
        {
            $result = array('status'=>'0','message'=>'Please Fill All Fields'); 
        }
		}
		else{
			$result = array('status'=>0,'message' => "Auth Key not matched");
		}
        //return Response::json($result,200);
		$data['result'] = $result;
		return Response::json($data,200);
		die;
    }
	
	public function delete_group(Request $request)
    {
        $saveArray = $request->all();
		$helper = new Helpers();
        $status= $helper->CheckAuthKey($saveArray);
		if($status=="true")
		{ 
        if(!empty($saveArray['user_id'] AND $saveArray['group_id']))
        {
            $result = array('status'=>'1','message'=>'YES');
            $group_data = Group::where(['id'=>$saveArray['group_id']])->first();
            if($group_data)
            {
                if($group_data->default_group == '1')
                {
                    $result = array('Status'=>'0','message' => 'You cannot delete this group,as it is default group.');
                }
                else
                {
                    $del_user_id = $saveArray['user_id'];
                    $user_id = $group_data->user_id;
                    $userarr = explode(',',$user_id);
                    
                    $exists = GroupMember::where(['user_id'=>$saveArray['user_id'],'group_id'=>$group_data->id])->first();
                    if($exists)
                    {
                        print_r($exists);
                       $exists = GroupMember::where(['user_id'=>$saveArray['user_id'],'group_id'=>$group_data->id])->delete();
                    }
                    else
                    {
                        $result = array('status' => '1', 'message' => 'This Record Does Not Exist In Group Member Table.');
                    }
                    $userarr1 = array();
                    foreach ($userarr as $value) 
                    {
                        if($value == $del_user_id)
                        {
                            continue;
                        }
                        else
                        {
                            $userarr1[] = $value;
                        }
                    }
                    $imp_ids = implode(',',$userarr1);
                    $update = Group::where(['id'=>$group_data->id])->update(['user_id'=>$imp_ids]);
                    if($update)
                    {
                        $result = array('status'=>'1','message'=>'Deleted Successfully.');
                    }
                    else
					{
						 $result = array('status' => '0', 'message' => 'Failed.');
					}
                }
            }
            else
            {
                $result = array('status' => '0', 'message' => 'Group not found.');
            }
        }
        else
        {
            $result = array('status'=>'0','message'=>'Please Fill All Fields');
        }
		}
		else{
			$result = array('status'=>0,'message' => "Auth Key not matched");
		}
        //return Response::json($result,200);
		$data['result'] = $result;
		return Response::json($data,200);
		die;
    }
	
	
	public function chat_on_off(Request $request)
    {
        $saveArray = $request->all();
		$helper = new Helpers();
        $status= $helper->CheckAuthKey($saveArray);
		if($status=="true")
		{ 
       if(!empty($saveArray['user_id'] AND $saveArray['group_id'] AND $saveArray['group_chat'] AND $saveArray['private_chat']))
       {
           $user_exist = User::where(['id'=>$saveArray['user_id']])->first();
           if($user_exist)
           {
                //old code 
                //$on="0";
                //$off="1"; 
                //========
                //but we will follow this.
                //$on="1";
                //$off="0"; 
                //========
                //CheckSetting
                $submit_date_time  = Carbon::now($saveArray['timezone']); 
                $submit_timezone = $saveArray['timezone'];

                
//========================== start code For Private =========================================================
                if($saveArray['private_chat'] == 'on')
                {
                   
                   $record_exist = CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->first();
                   if($record_exist)
                   {
                       $update_data = array('private_chat'=>'0','time_zone'=>$submit_timezone,'privatestatus_offtime'=>$submit_date_time);
                       CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->update($update_data);
                   
                   }
                   else
                   {
                        $save_data = array('user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'privatestatus_offtime'=>$submit_date_time,'private_chat'=>'0','time_zone'=>$submit_timezone);
                        CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->insert($save_data);
                        
                   }
                   $result = array('status' => '1', 'message' => 'successfully');
                   
                }
                if($saveArray['private_chat'] == 'off')
                {
                   
                    $record_exist = CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->first();
                    if($record_exist)
                    {
                        $update_data = array('private_chat'=>'1','time_zone'=>$submit_timezone,'privatestatus_offtime'=>$submit_date_time);
                        CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->update($update_data);
                    
                    }
                    else
                    {
                         $save_data = array('user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'privatestatus_offtime'=>$submit_date_time,'private_chat'=>'1','time_zone'=>$submit_timezone);
                         CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->insert($save_data);
                        
                    }
                    $result = array('status' => '1', 'message' => 'successfully');            
                }
	//======================End code for Private chat=============================

    //======================start code for Group chat=============================
                if($saveArray['group_chat'] == 'on')
                {
                    $record_exist = CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->first();
                    if($record_exist)
                    {
                        $update_data = array('group_chat'=>'0','time_zone'=>$submit_timezone,'groupstatus_offtime'=>$submit_date_time);
                        CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->update($update_data);
       
                    }
                    else
                    {
                        $save_data = array('user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'groupstatus_offtime'=>$submit_date_time,'group_chat'=>'0','time_zone'=>$submit_timezone);
                        CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->insert($save_data);
                       
                    }
                        $result = array('status' => '1', 'message' => 'successfully');
       
                }

                if($saveArray['group_chat'] == 'off')
                {
                   
                    $record_exist = CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->first();
                    if($record_exist)
                    {
                        $update_data = array('group_chat'=>'1','time_zone'=>$submit_timezone,'groupstatus_offtime'=>$submit_date_time);
                        CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->update($update_data);
        
                    }
                    else
                    {
                        $save_data = array('user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'groupstatus_offtime'=>$submit_date_time,'group_chat'=>'1','time_zone'=>$submit_timezone);
                        CheckSetting::where(['user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']])->insert($save_data);
                        
                    }
                    $result = array('status' => '1', 'message' => 'successfully');            
                }
    //======================End code for Group chat====================================================================
               

           }
           else
           {
                $result = array('status'=>'0','message'=>'User Does Not Exist');
           }
            
       }
       else
       {
            $result = array('status'=>'0','message'=>'Please Fill All Fields');
       }
		}
		else{
			$result = array('status'=>0,'message' => "Auth Key not matched");
		}
		
		$data['result'] = $result;
		return Response::json($data,200);
		die;
      

    }


public function get_chat(Request $request){

        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'sender_id' => 'required',
                    'group_id' => 'required',
                    'reciever_id' => 'required',
                    'timezone' => 'required',
                    'page_no' =>'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                  $CheckExit = Register::where(['id' => $saveArray['sender_id'] ])->first();
                  if(count($CheckExit)>0){
                      // check in setting table
                     $Settings = CheckSetting::where(['user_id' => $saveArray['sender_id'],'group_id'=>$saveArray['group_id'] ])->first();
                     if($Settings>0){
                        // find the record in setting table 
                     }else{
                       // not find record 
                      $offset = $saveArray['page_no']*10;
                      $limit = 10;
                  /* $Get_chat =  Chats::offset($offset)->
                      where(['sender_id'=>$saveArray['sender_id'],'receiver_id' =>$saveArray['reciever_id'],'group_id'=>$saveArray['group_id']])
                     ->orwhere(['sender_id'=>$saveArray['reciever_id'],'receiver_id' =>$saveArray['sender_id'],'group_id'=>$saveArray['group_id']])
                     ->limit($limit)->with('Chatusers')->get();
*/
          $Get_chat = MongoChat::offset($offset)->where(['sender_id'=>$saveArray['sender_id'],'receiver_id' =>$saveArray['reciever_id'],'group_id'=>$saveArray['group_id']])->orWhere(function($q) use ($request){
                               $saveArray = $request->all();
                              return $q->where(['sender_id'=>$saveArray['reciever_id'],'receiver_id' =>$saveArray['sender_id'],'group_id'=>$saveArray['group_id']]);
                          })->limit($limit)->with('Chatusers')->get();

                   if(count($Get_chat)>0){
                         $page_count = count($Get_chat) / 10;
                         $page_count = ceil($page_count);
                          foreach($Get_chat as $v){
                             $data['msg_id'] = $v->id;
                             $data['senderid'] = $v->sender_id; 
                                 if(!empty($v->Chatusers->image)){
                                   if($v->Chatusers->register_type=="F"){
                                     $data['senderimage'] = $v->Chatusers->image;
                                   }
                                   if($v->Chatusers->register_type=="N"){
                                    $data['senderimage'] = url('/') . "/img/profile_images/" .$v->Chatusers->image;
                                   }
                                 }
                            if(empty($v->Chatusers->image)){
                               $defaulturl = url('/')."/images/common/user_img_placeholder.png";
                                $data['senderimage'] = $defaulturl;
                             }
                           $data['sendername'] = $v->Chatusers->name;
                             $data['msg'] = $v->message;
                            $data['type'] = $v->type;
                            if($v->type=="I"){
                              $data['image'] = url('/') . "/img/groupchatimg/" . $v->image;  
                            }else{
                             $data['image'] = ''; 
                            }
                             if($v->type=="L"){
                              $data['lat'] = $v->lat; 
                              $data['lng'] =$v->lng;
                            }else{
                            $data['lat'] = "";  
                                $data['lng'] ="";
                            }
                      /**/ $currentDateTime = $v->submit_time;
                      /**/ $submited_time_zone = $v->time_zone;
                      /**/ $user_time_zone = $saveArray['timezone'];
                           $converted_data =   $helper->timezone_test_get_chat($currentDateTime,$submited_time_zone,$user_time_zone);
                           $day = explode(" ",$converted_data);
                           $data['date'] = $day[0];
                           $data['time'] = $day[1]." ".$day[2];
                           $data1[] = ($data);
                      }
                       

                         if (!empty($data1)) {
                              $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count);
                          } else {

                              $result = array('status' => '0', 'message' => 'Groups not found.');
                      }

                   }else{
                      $result = array('status' => '0', 'message' => 'Data Not found');
                   }


                     }
                  }else{
                    // sender not exist
                  }
                } // else 
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
    $data['result'] = $result;
    return Response::json($data,200);
    die;
}

 public function block(Request $request)
    {
		//echo "hello"; die;
        $saveArray = $request->all();
		$helper = new Helpers();
        $status= $helper->CheckAuthKey($saveArray);
        if($status=="true"){ 
       
        if(!empty($saveArray['user_id'] AND $saveArray['group_id'] AND $saveArray['friend_id'] AND $saveArray['reason']))
        {
            // in old code , please confirm code in if block ($saveArray['group_id'] != 1)

            $result = array('status'=>'1','message' => "YES");
            $user_exist = User::where(['id'=>$saveArray['user_id'],'user_type'=>'U','is_verified'=>'1','register_type'=>'N'])->orWhere(['register_type'=>'F'])->first();
            if($user_exist)
            {
                
                $reecord_exist = Block::where(['user_id'=>$saveArray['user_id'],'friend_id'=>$saveArray['friend_id'],'group_id'=>$saveArray['group_id']])->first();
                if(empty($reecord_exist)) //check record exist or not
                {
                    $save_data = array('user_id'=>$saveArray['user_id'],'friend_id'=>$saveArray['friend_id'],'group_id'=>$saveArray['group_id'],'reason'=>$saveArray['reason']);
                    Block::insert($save_data);
                    GroupInformation::where(['user_id'=>$saveArray['friend_id'],'group_id'=>$saveArray['group_id']])->increment('count');
                   //start from here
                   $check_already_block = Block::where(['group_id'=>$saveArray['group_id'],'friend_id'=>$saveArray['friend_id']])->first();
                   
                   if($check_already_block)
                   {
                    if($check_already_block->friend_id == $saveArray['friend_id'] AND $check_already_block->group_id == $saveArray['group_id'])
                    {
                        User::where(['id'=>$saveArray['friend_id']])->increment('count');
                    }
                    else
                    {
                        User::where(['id'=>$saveArray['friend_id']])->update(['count'=>'1']);
                    }
                    $result = array('status'=>'1','message' => "check_already_block");
                        
                   }
                   $result = array('status' => '1', 'message' => 'successfully.');
                  
                }
                else
                {
                    $result = array('status' => '0', 'message' => 'You have already submitted the block request for this user As per our terms, user will be removed from the group if there are atleast two block requests.');
                }
            }
            else
            {
                $result = array('status' => '0', 'message' => 'user id or friend_id id or group_id id not found.');
            }


        }
        else
        {
            $result = array('status'=>'0','message' => "Please Fill All Fields");
        }
		 }
		 else
		 {
			$result = array('status'=>0,'message' => "Auth Key not matched");
		 }
		$data['result'] = $result;
		return Response::json($data,200);
		die;
        
    }
	
	public function get_refrence(Request $request)
    {
        $saveArray = $request->all();
		$helper = new Helpers();
        $status= $helper->CheckAuthKey($saveArray);
        if($status=="true")
		{
			$data=array();
			if(!empty($saveArray['user_id'] AND $saveArray['qr_code']))
			{
				$groupdetail = Group::where(['qr_code'=>$saveArray['qr_code'],'status'=>'1','deleted'=>'0'])->first();
				if($groupdetail)
				{
					$grouptypedetail = GroupType::where(['type_name'=>$groupdetail->type,'deleted'=>'0'])->select('id','type_name','refrence')->first();
					if($grouptypedetail)
					{
						$data = $grouptypedetail;
					}
					else
					{
						$data=array();
					}
					$result = array('status'=>'1','message'=>'Login Successfully','data'=>$data);
				}
				else
				{
					$result=array('status'=>'0','message'=>'QR code does not match');
				}  
			}
			else
			{
				$result=array('status'=>'0','message'=>'Please fill qrcode');
			}
		}
		else
		{
			$result = array('status'=>0,'message' => "Auth Key not matched");
		}
		$data_1['result'] = $result;
		return Response::json($data_1,200);
    }
	
	public function change_password(Request $request)
    {
  
      $saveArray = $request->all();
      $helper = new Helpers();
      $status= $helper->CheckAuthKey($saveArray);
      if($status=="true")
      {
       if(!empty($saveArray['user_id']) && !empty($saveArray['old_password']) && !empty($saveArray['password']) ) 
       {
          $user = User::where(['id'=>$saveArray['user_id']])->first();
          $userid = $saveArray['user_id'];
          if($user->original_password == $saveArray['old_password'])
          {
            $new_password = $saveArray['password'];
            $update = User::where('id', $userid)->update(['original_password' => $new_password,'password'=>md5($new_password)]);
            if($update)
            {
              $result = array('status' => '1','message'=> 'Password Change Successfully');
            }
            else
            {
                $result = array('status' => '0','message' =>'Password Not changed' );
            }
          }
          else
          {
            $result = array('status' => '0','Message' =>'Old Password Not Matched' );
          }      
       }
       else
       {
          $result=array('status'=>'0','message'=>'Fill all fields');
       }
     }
     else
     {
        $result = array('status'=>'0','message' => "Auth Key not matched");
     }
		$data['result'] = $result;
		return Response::json($data,200);
		die;
	}
	
	public function update_deviceid(Request $request)
	{
      $saveArray = $request->all();
      $record1 = array();
	  
	   if(!empty($saveArray['user_id']) && !empty($saveArray['device_id']) && !empty($saveArray['device_type']))
	   {
		$userExist = User::where(['id'=>$saveArray['user_id']])->first();
			if($userExist)
			{
			  if(!empty($saveArray['bluetooth_mac']))
			  {
				$bluetooth_mac= $saveArray['bluetooth_mac'];
			  }
			  if(empty($saveArray['bluetooth_mac']))
			  {
				 $bluetooth_mac= "";
			  }
			  $update_data = array('device_id' => $saveArray['device_id'],'bluetooth_mac'=>$saveArray['bluetooth_mac'],'device_type'=>$saveArray['device_type']);
               $data_1 = User::where(['id'=>$saveArray['user_id']])->update($update_data);
               
				$services = Service::where(['deleted'=>'0','group_id'=>'0','service_status'=>'Yes'])->get();
				   if($services)
				   {
						foreach($services as $services)
						{
							echo "1367";
						  $record1 = array('service_name'=>$services->name,'service_link'=>$services->link,
							'phone'=>$services->phone,
							'service_icon'=>url('/'). "/img/profile_images/" .$services->image,
						  );
						}
				   }
				   else
				   {
					   echo "1376";
					  $record1;
				   }
					$result=array('status'=>'1','message'=>'Success','User Id'=> $userExist->id,'activate_status'=>$userExist->activate_status,'services'=>$record1);
			}
			else
			{
			  $result=array('status'=>'0','message'=>'User not found.');
			}
	   }
	   else
	   {
		  $result=array('status'=>'0','message'=>'Enter all field.');
	   }

		$data['result'] = $result;
		return Response::json($data,200);
		die;
	}
	
	public function edit_profile(Request $request)
    {
      $saveArray = $request->all();
       $helper = new Helpers();
      if(!empty($saveArray['user_id']))
      {
        $user_exist = User::where(['id'=>$saveArray['user_id']])->first();
             if(!empty($user_exist))
             {
				if(!empty($saveArray['name']))
				{
					$fname = $saveArray['name'];
				}
				else if(!empty($user_exist->name))
				{
					$fname = $user_exist->name;
				}
				else
				{
				   $fname = '';
				}
				   
				if(!empty($saveArray['email']))
				{
					$email = $saveArray['email']; 
				} 
				else if(!empty($user_exist->email))
				{
					$email = $user_exist->email;
				}
				else
				{
					$email = '';
				}
			   if(!empty($saveArray['age']))
			   {
					$age = $saveArray['age'];
			   }
			   else if(!empty($user_exist->age))
			   {
					$age = $user_exist->age;
			   }
			   else
			   {
					$age = ''; 
			   }
			   if(!empty($saveArray['gender']))
			   {
					$gender = $saveArray['gender'];
			   }
			   else if(!empty($user_exist->gender))
			   {
					$gender = $user_exist->gender;
			   }
			   else
			   {
					$gender = '';
			   }
			   if(!empty($saveArray['country_code']))
			   {
					$country_code = $saveArray['country_code'];
			   }
			   else if(!empty($user_exist->country_code))
			   {
					$country_code = $user_exist->country_code;
			   }
			   else if(empty($saveArray['country_code']))
			   {
					$country_code ="91";
			   }
			   if(!empty($saveArray['address']))
			   {
					$address = $saveArray['address'];
			   }
			   else if(!empty($user_exist->address))
			   {
					$address = $user_exist->address;
			   }
			   else
			   {
					$address = '';
			   }
			   if(!empty($saveArray['mobile']) && $saveArray['mobile'] != $user_exist->mobile OR !empty($saveArray['country_code']) && $saveArray['country_code'] != $user_exist->country_code)
			   {
				 $mobile = $saveArray['mobile'];
				 $r_code = rand(0000, 9999);
				 $verification_code = $r_code;
				 $sms = 'CoRover Connect Verification code is ' . $r_code;
				 $pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
				 $number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
				  $otpdata = [
						'number' => $number,
						'password' => $r_code
					  ];
					  $Get=$helper->send_otp($otpdata,'verification');
					  $is_verified = 'C';
					  $data= User::where('id', $saveArray['user_id'])->update(['verification_code' => $verification_code,'is_verified'=>$is_verified,'new_mobile'=>$mobile,'new_countrycode'=>$country_code]);
					  $verified='not verified'; 
	  
			   }
			   else
			   {
					$verified='verified';
			   }
			   if(!empty($saveArray['designation']))
			   {
					$designation = $saveArray['designation']; 
			   }
			   else if(!empty($user_exist->designation))
			   {
					$designation = $user_exist->designation;
			   }
			   else
			   {
					$designation = '';
			   }
			   
			   $user_chk = User::where(['id'=>$saveArray['user_id']])->first();
			   if(!empty($request->file('image')))
			   {
					$destination=url('/')."/img/profile_images/";
					$filename =   $helper->upload($request->file('image'),'img/profile_images','none');  
					$eventImage=$filename ;
					$saveArray['image'] = $eventImage;
					if(!empty($filename))
					{  
						if($user_chk->register_type=='F')
						{
							$image = url('/')."/img/profile_images/".$saveArray['image'];
						}
						else
						{
							$image = $saveArray['image'];
						}
					}
			   }
			   else if(!empty($user_exist->image))
				{
					$image = $user_exist->image;
				}
				else
				{
					$image = '';
				}
					$id = $user_exist->id;
					$data = User::where('id', $saveArray['user_id'])->update(['name' => $fname,'age'=>$age,'gender'=>$gender,'address'=>$address,'designation'=>$designation,'image'=>$image,'email'=>$email]);
					$result=array('status'=>'1','message'=>'Updated successfully','is_verified'=>$verified);
             }
			 else
			 {
				$result=array('status'=>'0','message'=>'User does not exist');   
			 }
      }
	  else
	  {
         $result=array('status'=>'0','message'=>'User Id is mendetory');   
      }
  
  
		$data_1['result'] = $result;
		return Response::json($data_1,200);
		die;
    }
	
	public function feedback(Request $request)
    {
      $saveArray = $request->all();
      $helper = new Helpers();
      $status= $helper->CheckAuthKey($saveArray);
       if($status=="true")
       { 
         if(!empty($saveArray['user_id']) && !empty($saveArray['group_id']) && !empty($saveArray['comment']) && !empty($saveArray['rating']))
            {
                $user_exist = User::where(['id'=>$saveArray['user_id']])->first();
                $group_exist  = Group::where(['id'=>$saveArray['group_id']])->first();
                $admin_id  = User::where(['id'=>$group_exist->created_id])->first();
                if (!empty($user_exist) && !empty($group_exist))
                    {
                        $save_data = array('user_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'comment'=>$saveArray['comment'],'rating'=>$saveArray['rating']);
                        $user = Feedback::insert($save_data);
                        $subject = "A New Feedback For ".$group_exist->name;
                        $group_logo = "https://corover.co.in/JKtourismdev/img/group_logo/".$group_exist->icon;
                        $rating_img_src ="https://corover.co.in/JKtourismdev/img/emails/ratting.png";
                        $no_rating_img_src = "https://corover.co.in/JKtourismdev/img/emails/ratting3.png";
                        $corover_logo = "https://corover.co.in/JKtourismdev/img/emails/2.png";
                        $facebook_url = "https://corover.co.in/JKtourismdev/img/emails/facebook_90.png";
                        $twitter_url = "https://corover.co.in/JKtourismdev/img/emails/twitter_99.png";
                        $youtube_url = "https://corover.co.in/JKtourismdev/img/emails/youtube_987.png";

                        $params = array(
                            'name' => $user_exist->name, //user's data
                            'sender_email' => $user_exist->email,
                            'sender_phone' => $user_exist->mobile,
                            'phone' => "phone",
                            'data' => "data",
                            'subject'=>$subject,
                            'group_name'=>$group_exist->name,
                            'group_logo'=>$group_logo,
                            'feedback_comment'=>$saveArray['comment'],
                            'rating_img_src'=>$rating_img_src,
                            'rating'=>$saveArray['rating'],
                            'no_rating_img_src'=>$no_rating_img_src,
                            'corover_logo'=>$corover_logo,
                            'facebook_url'=>$facebook_url,
                            'twitter_url'=>$twitter_url,
                            'youtube_url'=>$youtube_url,
                            //other stuffs in the form 
                        ); 
						
                        $user_emailid = "shaminders80@gmail.com";
                        $mailsend=Mail::send('emails.welcome_email',['data'=>$params],function($message) use($params) 
                        {
                            $message->to('shaminders80@gmail.com', 'Drawtopic.in');
                            $message->subject($params['subject']);
                            $message->from('shaminders80@gmail.com');
                        });

                        if($user)
                        {
                            $result = array('status' => '1','message'=> 'Feedback Submitted  Successfully','user_name' => $user_exist->name,'group_name' => $group_exist->name,'comment'=>$saveArray['comment'],'rating'=>$saveArray['rating']);
                        }
                        else
                        {
                            $result = array('status' => '0','message' =>'not submit' );
                        }
                    }
                    else 
                    {
                        $result = array('status' => '0', 'message' => "Wrong Information");
                    }
  
            }
            else
            {
                $result=array('status'=>'0','message'=>'Please fill all fields');
            } 
        }
        else
        {
                $result = array('status'=>0,'message' => "Auth Key not matched");
        }
        
		$data_1['result'] = $result;
		return Response::json($data_1,200);
		die;
    }
	

	public function chat_feedback(Request $request)
      {
        $saveArray=$request->all();
        if(!empty($saveArray['user_id']) && !empty($saveArray['group_id'])  && !empty($saveArray['rating'])  && !empty($saveArray['session_id']) )
         {
          $user_exist = User::where(['id'=>$saveArray['user_id']])->first();
          $staff_exist = User::where(['id' => $saveArray['staff_id']])->first();
          $group_exist =  Group::where(['id' => $saveArray['group_id']])->first();
          $admin_id = User::where(['id'=>$group_exist->created_id])->first();

          if (!empty($user_exist) && !empty($group_exist))
          {
			$resetPassword = rand(100000,999999);
			$groupname = $group_exist->name;
            $username= (isset($user_exist->name)) ? $user_exist->name : 'Guest';
            $phone =  (isset($user_exist->mobile)) ? $user_exist->mobile : 'Not Provided';
            $email =  (isset($user_exist->email)) ? $user_exist->email : 'Not Provided';
            $subject = "A New Feedback For ".$groupname." ";
            $name    = $admin_id->name;
            $senderemail = trim($admin_id->email);
            $group_logo = $group_exist->icon;
            $comment = $saveArray['comment'];
            $rating = $saveArray['rating'];
			$mes  ="User has submitted feedback and rating.";
     $group_logo = "https://corover.co.in/JKtourismdev/img/group_logo/".$group_exist->icon;
     $rating_img_src = "https://corover.co.in/JKtourismdev/img/emails/ratting.png";
     $no_rating_img_src = "https://corover.co.in/JKtourismdev/img/emails/ratting3.png";
     $corover_logo = "https://corover.co.in/JKtourismdev/img/emails/2.png";
     $facebook_url = "https://corover.co.in/JKtourismdev/img/emails/facebook_90.png";
     $twitter_url = "https://corover.co.in/JKtourismdev/img/emails/twitter_99.png";
     $youtube_url = "https://corover.co.in/JKtourismdev/img/emails/youtube_987.png";
			
			
			$params = array(
                            'name' => $name,
							'subtitle' => $mes,
							'username' => $username,
							'group_name' => $groupname,
							'comment' => $comment,
							'rating' => $rating,
							'group_logo' => $group_logo,
							'phone' => $phone,
							'email' => $email,
							
                            'sender_email' => $user_exist->email,
                            'sender_phone' => $user_exist->mobile,
                            'phone' => "phone",
                            'data' => "data",
                            'subject'=>$subject,
                            'group_name'=>$group_exist->name,
                            //'group_logo'=>$group_logo,
                            'feedback_comment'=>$saveArray['comment'],
                            'rating_img_src'=>$rating_img_src,
                            'rating'=>$saveArray['rating'],
                            'no_rating_img_src'=>$no_rating_img_src,
                            'corover_logo'=>$corover_logo,
                            'facebook_url'=>$facebook_url,
                            'twitter_url'=>$twitter_url,
                            'youtube_url'=>$youtube_url,
                            //other stuffs in the form 
                        ); 
			
			

            $mailsend=Mail::send('emails.chat_feedback',['data'=>$params],function($message) use($params) 
                        {
                            $message->to('shaminders80@gmail.com', 'Drawtopic.in');
                            $message->subject($params['subject']);
                            $message->from('shaminders80@gmail.com');
                        });
            $result = array('status' => '1', 'message' => "Feedback Submitted  Successfully", 'user_name' => $user_exist->name,'group_name' => $group_exist->name,'comment'=>$saveArray['comment'],'rating'=>$saveArray['rating']);

          }else {
                $result = array('status' => '0', 'message' => "Wrong Information");
            }


         }else{
                 $result=array('status'=>'0','message'=>'Please fill all fields');
              } 

              echo json_encode($result);
              die;

      }
	  
	  public function get_groupmessage(Request $request){

        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'group_id' => 'required',
                    'timezone' => 'required',
                    'page_no' =>'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                  $CheckExit = Register::where(['id' => $saveArray['user_id'] ])->first();
                  if(count($CheckExit)>0){
                      // check in setting table
                     $Settings = CheckSetting::where(['user_id' => $saveArray['user_id'],'group_id'=>$saveArray['group_id'] ])->first();
                
                       // not find record 
                      $offset = $saveArray['page_no']*10;
                      $limit = 10;
                      // /Chatusers
                    $Get_chat =  Groupchat::offset($offset)->
                      where(['group_id'=>$saveArray['group_id']])
                     ->limit($limit)->with('GroupChatusers')->get();
                     if(count($Get_chat)>0){
                         $page_count = count($Get_chat) / 10;
                         $page_count = ceil($page_count);
                          foreach($Get_chat as $v){
                             $data['msg_id'] = $v->id;
                             $data['user_id'] = $v->user_id; 
                                 if(!empty($v->GroupChatusers->image)){
                                   if($v->GroupChatusers->register_type=="F"){
                                     $data['senderimage'] = $v->GroupChatusers->image;
                                   }
                                   if($v->GroupChatusers->register_type=="N"){
                                    $data['senderimage'] = url('/') . "img/profile_images/" .$v->GroupChatusers->image;
                                   }
                                 }
                            if(empty($v->GroupChatusers->image)){
                               $defaulturl =  url('/') ."images/common/user_img_placeholder.png";
                                $data['senderimage'] = $defaulturl;
                             }
                           $data['sendername'] = $v->GroupChatusers->name;
                             $data['msg'] = $v->message;
                            $data['type'] = $v->type;
                            if($v->type=="I"){
                              $data['image'] =  url('/')  . "img/groupchatimg/" . $v->image;  
                            }else{
                             $data['image'] = ''; 
                            }
                             if($v->type=="L"){
                              $data['lat'] = $v->lat; 
                              $data['lng'] =$v->lng;
                            }else{
                            $data['lat'] = "";  
                                $data['lng'] ="";
                            }
                        /**/$currentDateTime = $v->submit_time;
                        // we havr to save in groupchat table of timexone key
                        /**/ //$submited_time_zone = $v->time_zone;
                        /**/ $submited_time_zone = 'Asia/kolkata';
                        /**/ $user_time_zone = $saveArray['timezone'];
                           $converted_data =   $helper->timezone_test_get_chat($currentDateTime,$submited_time_zone,$user_time_zone);
                            //dd($converted_data);

                           $day = explode(" ",$converted_data);
                           $data['date'] = $day[0];
                           $data['time'] = $day[1]." ".$day[2];
                           $data1[] = ($data);
                      }
                      
                    if (!empty($data1)) {
                              $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count);
                          } else {

                              $result = array('status' => '0', 'message' => 'Groups not found.');
                      }

                   }else{
                      $result = array('status' => '0', 'message' => 'Data Not found');
                   }
                  }else{
                    // sender not exist
                  }
                } // else 
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
     echo json_encode($result);
    die;
}





public function search_group_users(Request $request){
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'group_id' => 'required',
                    'page_no' =>'required',
                ]);
                 if ($validator->fails()) {
                   $result = array('status'=>0,'message' =>"Please fill all fields");
                 }else{
                   // now start the code to find
                   $offset = $saveArray['page_no']*10;
                   $limit = 10;
                   $name= $saveArray['name'];
                     $Group_info =Group::where(['id' => $saveArray['group_id'],
                        'deleted'=>'0'])->with('GroupType')->first();
                   $Get_Group_Member = GroupMember::offset($offset)->where(['group_id'=> $saveArray['group_id'],'type'=>'U'])
                     ->limit($limit)
                     // this is the code for when we have join with user model and we have to search like in that 
                     ->with('GetUSer')->whereHas('GetUSer', function($q) use ($name)
                      {
                          $q->where('name', 'like', '%'.$name.'%');

                      })
                     // end the search code 
                     ->with('GetGroupData')
                     ->orderby('updated_at','desc')
                     ->get();
                      if(count($Get_Group_Member)>0){
                         //for get the total page
                         $page_count = count($Get_Group_Member) / 10;
                         $page_count = ceil($page_count);
                          /* end get total page code*/
                        foreach($Get_Group_Member as $v){
                         //  dd($v->GetGroupData->id->GroupType());
                          $check_group_helpdeadkornot=  $v->GetGroupData->has_helpdesk;
                          // common function call and get the member group information
                          $member_information = $helper->Group_Member_Information($v->group_id,$v->user_id);
                          $getBlock= $helper->Group_Member_Block($v->group_id,$v->user_id);
                          $get_Badegs= $helper->Group_MEMber_Badges($v->group_id,$v->user_id,$check_group_helpdeadkornot);
                           $data= $helper->Get_Meber_FInalArray($v->GetUSer,$member_information,$getBlock,$get_Badegs,$Group_info->GroupType);
                            $data1[] = $data;
                           } // foreach
                      $result = array('status' => '1', 'message' => 'Successfully.','data' => $data1,'staff_count'=>0, 'user_count'=>count($Get_Group_Member),'totalPages'=>$page_count);
                      }else{
                        $result = array('status' => '0', 'message' => 'No Staff added to this group.');
                      }
                } // else 
          }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
     echo json_encode($result);
    die;
}





public function qrcode_scan(Request $request){
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'qr_code' => 'required',
                   // 'country_code' =>'required',
                   // 'mobile' =>'required',
                   // 'seat_no' =>'required',
                 ]);
                 if ($validator->fails()) {
                    $result = array('status'=>0,'message' =>"Please fill all fields");
                 }else{
                   // now start the code to find
                  $getQRcode=  $helper->GetQRcode($saveArray['qr_code']);
                  // get seatc number 
                  $getSeat = $helper->GetSeatNumber($saveArray['seat_no']);
                 // dd("dfsd");
                 $result = $helper->firstCheck($saveArray,$getQRcode,$getSeat);
         
                } // else 
          }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
       $data_1['result'] = $result;
       return Response::json($data_1,200);
       die;

}//

public function Facebook_Login(Request $request){
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
                $validator = Validator::make($request->all(), [
                    'unique_id' => 'required',
                    'timezone'=>'required',
                 ]);
                 if ($validator->fails()) {
                    $result = array('status'=>0,'message' =>"Please fill all fields");
                 }else{
                     $CheckExit = Register::where(['unique_id_F' => $saveArray['unique_id']])->first();
                     if(count($CheckExit)>0){
                       // already exist
                           $data = [
                            'image' => $saveArray['image'],
                            'name' => $saveArray['name'],
                            ];
                            Register::where('id',$CheckExit->id)->update($data);
                           $result=array('status'=>'1','message'=>'success','id'=> $CheckExit->id,'register_type' => 'F');
                       }else{
                           //insert recoed 
                           // create default group 
                            $data = new Register;
                            $data->name = $saveArray['name'];
                            if(!empty($saveArray['email'])){
                               $data->email= $saveArray['email'];
                            }else{
                               $data->email= "";
                            }
                           if(!empty($saveArray['gender'])){
                               $data->gender= $saveArray['gender'];
                            }
                            $data->password= "";
                            $data->original_password= "";
                            $data->token= "";
                            $data->role= 3;
                            $data->mobile=$saveArray['mobile_no'];
                            $data->address= "";  
                            $data->designation= "";  
                            $data->timezone=$saveArray['timezone'];
                            $data->register_type="F";
                            $data->image=$saveArray['image'];
                            $data->verification_code="";
                            $data->country_code="";
                            $data->unique_id_F = $saveArray['unique_id'];
                            $data->is_verified = "Y";
                            $data->created_at=date('Y-m-d H:i:s');
                            $data->updated_at=date('Y-m-d H:i:s');
                            $data->save();
                            $insertedid = $data->id;
                            // common function for add the default group in that 
                            $getData = $helper->AddDefaultGroup($insertedid);
                            $result=array('status'=>'1','message'=>'success','id'=> $insertedid,'register_type' => 'F');
                       }// check exits else
                   } // else 
          }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
       $data_1['result'] = $result;
       return Response::json($data_1,200);
       die;

}//


/*public function helpdeskChat(Request $request){
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
                $validator = Validator::make($request->all(), [
                    'sender_id' => 'required',
                    'group_id'=>'required',
                 ]);
                 if ($validator->fails()) {
                    $result = array('status'=>0,'message' =>"Please fill all fields");
                 }else{
                  // first get the group 
                     $get_group= Group::where('id',$saveArray['group_id'])->first(); 
                     // get the register data 
                     $Sender_data= Register::where('id',$saveArray['sender_id'])->first(); 
                     //
                    }
            }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
          }
       $data_1['result'] = $result;
       return Response::json($data_1,200);
       die;

}//
*/

//https://corover.co.in/JKtourismdev/Chats/get_chat.json

public function helpdeskChat(Request $request){
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'sender_id' => 'required',
                    'group_id' => 'required',
                    //'reciever_id' => 'required',
                    'timezone' => 'required',
                    'page_no' =>'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                  $CheckExit = Register::where(['id' => $saveArray['sender_id'] ])->first();
                  if(count($CheckExit)>0){
                      // check in setting table 
                     $Settings = CheckSetting::where(['user_id' => $saveArray['sender_id'],'group_id'=>$saveArray['group_id'] ])->first();
                      if(count($Settings)>0){
                          //First Case if private chat == 0 then ren other wise display blank reocrd 
                          // if private chat is 1 
                          // then check the private offtime and get the reocrd less that this time 
                         if($Settings->private_chat=="0"){
                            $result =  $helper->Support_PRivate_On($saveArray);
                          }else{
                               $privateofftime= $Settings->privatestatus_offtime;
                               $result =  $helper->Support_PrivateOfftime_On($saveArray,$privateofftime);
                          }
                         // find the record in setting table 
                     }else{
                       // not find record 
                      $offset = $saveArray['page_no']*10;
                      $limit = 10;
                    /*  $Get_chat =  Supportchat::offset($offset)->
                      where(['sender_id'=>$saveArray['sender_id'],'group_id'=>$saveArray['group_id']])
                     ->orwhere(['receiver_id' =>$saveArray['sender_id'],'group_id'=>$saveArray['group_id']])
                     ->limit($limit)->with('SupportChatusers')->get();*/

                     $Get_chat = Supportchat::offset($offset)->where(['sender_id'=>$saveArray['sender_id'],'group_id'=>$saveArray['group_id']])->orWhere(function($q) use ($request){
                               $input = $request->all();
                              return $q->where(['sender_id'=>$input['sender_id'],'group_id'=>$input['group_id']]);
                          })->limit($limit)->with('SupportChatusers')->get();
                     if(count($Get_chat)>0){
                      // call the common function for this 
                        $result= $helper->SupportComman($Get_chat,$saveArray);
                     }else{
                        $result = array('status' => '0', 'message' => 'Data Not found');
                     }

                     }
                  }else{
                    $result = array('status' => '0', 'message' => 'User Not found');
                  }
                } // else 
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
    $data['result'] = $result;
    return Response::json($data,200);
    die;
}



public function closechat(Request $request){
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'group_id' => 'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                    $Get_chat = Supportchat::where(['is_closed'=>'No','group_id'=>$saveArray['group_id'],'sender_id'=>$saveArray['user_id']])->orWhere(function($q) use ($request){
                               $input = $request->all();
                              return $q->where('is_closed','No')->where('group_id',$input['group_id'])
                              ->where('receiver_id' ,$input['user_id']);
                          })->first(); // getting all results
                      if(count($Get_chat)>0){
                               $result = array('status' => '1', 'message' => 'Chat  Already Closed!','session_id' => "");
                      }else{
                        $uniqueid = date('YmdHis').$saveArray['group_id'];
                        $total = count($Get_chat);
                        $cc = 1;
                        $date1 = new \DateTime();
                        $date1->setTimezone(new DateTimeZone($saveArray['timezone']));
                      foreach($Get_chat as $chat){
                            if($cc < $total){
                                $savedata =['is_closed' => 'Yes','session_id' => $uniqueid , 'close_time' => $date1->format('Y-m-d H:i:s') ];
                                  $cc++;  
                                } else {
                                   $savedata =['is_closed' => 'Yes','session_id' => $uniqueid , 'close_time' => $date1->format('Y-m-d H:i:s') ,'is_last_message' => 'Yes'];
                                }
                            $update = Supportchat::where(['id'=>$chat->id])->update($savedata);
                          }
                   $result = array('status' => '1', 'message' => 'Chat Closed!','session_id' => $uniqueid);
                      }
                } // else 
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
    $data['result'] = $result;
    return Response::json($data,200);
    die;
}



public function getreceiver(Request $request){
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'group_id' => 'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                 $Get_chat = Supportchat::where(['is_closed'=>'No','group_id'=>$saveArray['group_id'],'sender_id'=>$saveArray['user_id']])->orWhere(function($q) use ($request){
                               $input = $request->all();
                              return $q->where('is_closed','No')->where('group_id',$input['group_id'])
                              ->where('receiver_id' ,$input['user_id']);
                          })->first(); // getting all results
                  if(count($Get_chat) > 0){
                    $receiver_id = ($Get_chat->sender_id == $saveArray['user_id']) ? $Get_chat->receiver_id :  $Get_chat->sender_id;
                    $result = ['status'=>'1','receiver_id' =>  $receiver_id ];
                  } else {
                    $result = ['status'=>'1','receiver_id' =>  '0' ];
                  }
                } // else 
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
    $data['result'] = $result;
    return Response::json($data,200);
    die;
}






public function help_api(Request $request){
         $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    //'receiver_id' => 'required',
                    'group_id' => 'required',
                   // 'sender_id' => 'required',
                   // 'lat' => 'required',
                   //  'lng' => 'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                    // first get the group 
                     $get_group= Group::where('id',$saveArray['group_id'])->first(); 
                     dd($get_group);

                } // else 
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
    $data['result'] = $result;
    return Response::json($data,200);
    die;
}



public function send_privatemessage(Request $request){
              $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'sender_id' => 'required',
                    'group_id' => 'required',
                    'receiver_id'=>'required',
                    'type' => 'required',
                    'lat' => 'required',
                    'long' => 'required',
                   // 'image' => 'required_without_all:message',
                   // 'message' => 'required_without_all:image',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{
                    $Group_data = Group::where(['id' => $saveArray['group_id'] ])->first();
                    $Sender = Register::where(['id' => $saveArray['sender_id'] ])->first();
                     $Reciver = Register::where(['id' => $saveArray['receiver_id'] ])->first();
                    if(count($Sender)>0){
                  // first check the user is block or not 
                     $Get_Block_Member =  Block::where(['friend_id'=> $saveArray['sender_id'],'group_id'=>$saveArray['group_id'],'user_id'=>$saveArray['receiver_id']])->first();
                      if(count($Get_Block_Member)>0){
                           $result = array('status' => '0', 'message' => 'Block user not send message', 'groupmsg_img' => '');
                        }else{

                       $Settings = CheckSetting::where(['user_id' => $saveArray['sender_id'],'group_id'=>$saveArray['group_id'] ])->first();
                      if(count($Settings)>0){
                          // check the private filed 
                          // if this is zero then send the notification else no
                          if($Settings->private_chat=="0"){
                              // send notification
                               $date= $helper->Get_time($saveArray['timezone']);
                               $saveArray['submit_time'] = $date;
                               $saveData = $helper->Save_DataPrivate($saveArray,$request);
                               // send notification to memeber 
                                $sendnotification = $helper->PrivateChat_Notification($saveArray,$Sender,$Group_data,$date,$saveData['groupimd'][0],$Reciver);
                               $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $saveData['groupimd'][0]);
                             // save record 
                           }else{
                                $result = array('status' => '0', 'message' => 'Cannot send the message', 'groupmsg_img' => '');
                           }
                      }else{
                        // send notification 
                        // save record 
                        // send notification to its group member
                        // $send_group_badges = $helper->Group_Notification_BAdge($Get_Group_Member,$saveArray['user_id']);
                       $date= $helper->Get_time($saveArray['timezone']);
                       $saveArray['submit_time'] = $date;
                       $saveData = $helper->Save_DataPrivate($saveArray,$request);
                       // send notification to memeber 
                        if(!empty($saveData['groupimd'])){
                            $uploadedimage= $saveData['groupimd'][0];
                        }else{
                            $uploadedimage='';
                        }
                       $sendnotification = $helper->PrivateChat_Notification($saveArray,$Sender,$Group_data,$date,$uploadedimage,$Reciver);
                       $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $uploadedimage);


                        } // eles part 
                     }
                    }else{
                       $result = array('status' => '0', 'message' => 'Sender id  does not exist');
                   }

                } // else 
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }

       $data['result'] = $result;
      return Response::json($data,200);
     die;
}





} //last
