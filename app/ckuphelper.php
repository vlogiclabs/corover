<?php 
namespace App;
use App\Register;
use App\Newbroadcast;
use Illuminate\Http\Request;
use DB;
use Response;
use Hash;
use DateTime;
use DateTimeZone;
use App\HelpdeskBadgesForApp;
use App\Chatbadge;
use App\Unreadgroupbadge;
use Intervention\Image\Facades\Image as Image;
use App\GroupModel as Groups;
use Storage;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
use League\Flysystem\Filesystem;
	class Helpers {
	  
	  //public function upload($file,$path,$old_image){
	  /*		if($file !== null){
                       $photoName = md5(rand(9999,99999999) ).time().'.'.$file->getClientOriginalExtension();
                       $oldimageurl =   getcwd().'/'.$path.'/'.$old_image;
                     if (is_file($oldimageurl)){ if there is file found in the folder
                          unlink(str_replace("\\","/",$oldimageurl)); /*deleting older image*/
                     // }
                   // $file->move($path, $photoName);
                  //  return $photoName; 

                //}


	  //}


function upload($file,$path,$old_image) {
 

   if($path=="users"){

     $small_thumbnail = Image::make($file)->resize(50,50);
     $medium_thumbnail = Image::make($file)->resize(250,180);
     $large_thumbnail = Image::make($file)->resize(1000,600);

  
/*
  $path_to_small = 'images/'.$path.'/small/';
  $path_to_medium = 'images/'.$path.'/medium/';
  $path_to_large = 'images/'.$path.'/large/';


    $trimmeds = str_replace(' ','_' ,$file->getClientOriginalName());
    $trimmed = str_replace('/','_' ,$trimmeds);
    if(strlen($trimmed) > 35){
      $Datafile= md5(rand(1,100000)).date('dmYhis').'_'.substr($trimmed,0,30).'.'.$file->getClientOriginalExtension();
    } else {
      $Datafile= md5(rand(1,100000)).date('dmYhis').'_'.$trimmed;
    }*/



//dd($small_thumbnail);

 //$s3 = Storage::disk('s3');

  //$s3 =Storage::disk('s3');
  //$s3->put($Datafile, file_get_contents($file));
  //$s3->put( $small_thumbnail->save($path."/medium/".$Datafile), file_get_contents($file));



  // https://s3.console.aws.amazon.com/s3/object/vllmohali/users/small/fd3266c37c03e1b82ad0b1b19c534bfd12122017113838_tabs.png?region=us-east-1&tab=overview

   }elseif($path=="img/groupchatimg"){
     $small_thumbnail = Image::make($file)->resize(50,50);
     $medium_thumbnail = Image::make($file)->resize(280,150);
     $large_thumbnail = Image::make($file)->resize(650,650);
   }elseif ($path=="courses") {
     $small_thumbnail = Image::make($file)->resize(50,50);
     $medium_thumbnail = Image::make($file)->resize(280,150);
     $large_thumbnail = Image::make($file)->resize(800,600);
   }elseif($path=="group"){
    $small_thumbnail = Image::make($file)->resize(50,50);
    $medium_thumbnail = Image::make($file)->resize(640,350);
    $large_thumbnail = Image::make($file)->resize(800,500);
  }

  $old_small = 'images/'.$path.'/small/'.$old_image;
  $old_medium = 'images/'.$path.'/medium/'.$old_image;
  $old_large = 'images/'.$path.'/large/'.$old_image;

  $path_to_small = 'images/'.$path.'/small/';
  $path_to_medium = 'images/'.$path.'/medium/';
  $path_to_large = 'images/'.$path.'/large/';
  $url = getcwd()."/".$old_small ;
  $urls = str_replace("\\","/",$url);
  if (is_file($urls)){
    unlink(str_replace("\\","/",getcwd().'/'.$old_small));
    unlink(str_replace("\\","/",getcwd().'/'.$old_medium));
    unlink(str_replace("\\","/",getcwd().'/'.$old_large));
    $trimmeds = str_replace(' ','_' ,$file->getClientOriginalName());
    $trimmed = str_replace('/','_' ,$trimmeds);
    if(strlen($trimmed) > 35){
      $Datafile= md5(rand(1,100000)).date('dmYhis').'_'.substr($trimmed,0,30).'.'.$file->getClientOriginalExtension();
    } else {
      $Datafile= md5(rand(1,100000)).date('dmYhis').'_'.$trimmed;
    }
  } else{
    $trimmeds = str_replace(' ','_' ,$file->getClientOriginalName());
    $trimmed = str_replace('/','_' ,$trimmeds);
    if(strlen($trimmed) > 35){
      $Datafile= md5(rand(1,100000)).md5('xyz').date('dmYhis').'_'.substr($trimmed,0,30).'.'.$file->getClientOriginalExtension();
    } else {
      $Datafile= md5(rand(1,100000)).md5('xyz').date('dmYhis').'_'.$trimmed;
    }
  }

  $small_thumbnail->save($path_to_small.$Datafile);
  $medium_thumbnail->save($path_to_medium.$Datafile);
  $large_thumbnail->save($path_to_large.$Datafile);
  return $Datafile;
  
}

 
    /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to authcheck user
    * @Param: none
    * @Return: none 
    **/

    public function CheckAuthKey($post){

         if($post['auth_key'] != 123){
           $code= "false";
         }else{
           $code="true";
         }
       return  $code;
    }

 
     /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to send otp user
    * @Param: none
    * @Return: none 
    **/

 public function send_otp($data,$type){
        
         if(isset($data)) {
            if($type == 'forgot_password'){
                 $template = 'forgot_otp';
             } else if($type == 'verification'){
                $template = 'connect_verification  ';
             }
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "http://2factor.in/API/V1/70480142-9c67-11e7-94da-0200cd936042/SMS/".$data['number']."/".$data['password']."/".$template."",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "GET",
                      CURLOPT_POSTFIELDS => "",
                      CURLOPT_HTTPHEADER => array(
                        "content-type: application/x-www-form-urlencoded"
                      ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                    if ($err) {
                      echo "cURL Error #:" . $err;
                    } else {
                     $x = json_decode($response);
                    return $x->Details;
                    }
            //OTP Code Ends Here
            }
    }


    /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

 public function  CallCoomin($saveArray,$image,$id,$verified){
	 
        $r_code = rand(0000, 9999);
           $saveArray['verification_code'] = $r_code;
                $sms = 'CoRover Connect Verification code is ' . $r_code;
          $pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
          $number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
          $otpdata = [
                'number' => $number,
                'password' => $r_code
              ];
               $helper = new Helpers();
         $otpstatus= $helper->send_otp($otpdata,'verification');
         $token = md5($saveArray['email']).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
          $data = [
            'name'  => $saveArray['name'],
            //'email'  => $saveArray['email'],
            'password'  => Hash::make($saveArray['password']),
            'token' => $token,
            'role' => 3, 
            'mobile' => $saveArray['mobile'],
            //'address' => $saveArray['address'],
            //'designation' =>$saveArray['designation'],
            //'gender' => $saveArray['gender'],
            'timezone' => $saveArray['timezone'],
            'country_code' => $saveArray['country_code'],
            'image' => $image,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'is_verified'=>$verified,
            'verification_code'=>$r_code,
          ];
		  if(!empty($saveArray['gender']))
		  {
			  $data['gender'] = $saveArray['gender'];
		  }
		  if(!empty($saveArray['email']))
		  {
			  $data['email'] = $saveArray['email'];
		  }
		  if(!empty($saveArray['address']))
		  {
			  $data['address'] = $saveArray['address'];
		  }
		  if(!empty($saveArray['designation']))
		  {
			  $data['designation'] = $saveArray['designation'];
		  }
		  
        Register::where('id',$id)->update($data);
        return $id;
 }



 public function  CallCoomin2($saveArray,$image,$id){
	
        $r_code = rand(0000, 9999);
           $saveArray['verification_code'] = $r_code;
                $sms = 'CoRover Connect Verification code is ' . $r_code;
          $pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
          $number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
          $otpdata = [
                'number' => $number,
                'password' => $r_code
              ];
               $helper = new Helpers();
         $otpstatus= $helper->send_otp($otpdata,'verification');
         $token = md5($saveArray['email']).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
          $data = [
            'name'  => $saveArray['name'],
            //'email'  => $saveArray['email'],
            'password'  => Hash::make($saveArray['password']),
            'token' => $token,
            'role' => 3, 
            'mobile' => $saveArray['mobile'],
            //'address' => $saveArray['address'],
            //'designation' =>$saveArray['designation'],
            'timezone' => $saveArray['timezone'],
            'country_code' => $saveArray['country_code'],
            'image' => $image,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'verification_code'=>$r_code,
          ];
		  //pic, name, mobile no. , password
		  if(!empty($saveArray['gender']))
		  {
			  $data['gender'] = $saveArray['gender'];
		  }
		  if(!empty($saveArray['email']))
		  {
			  $data['email'] = $saveArray['email'];
		  }
		  if(!empty($saveArray['address']))
		  {
			  $data['address'] = $saveArray['address'];
		  }
		  if(!empty($saveArray['designation']))
		  {
			  $data['designation'] = $saveArray['designation'];
		  }
		  
        Register::where('id',$id)->update($data);
        return $id;
 }

    /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

 public function  SaveRegister($saveArray,$image){
	 
        $r_code = rand(0000, 9999);
           $saveArray['verification_code'] = $r_code;
                $sms = 'CoRover Connect Verification code is ' . $r_code;
          $pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
          $number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
          $otpdata = [
                'number' => $number,
                'password' => $r_code
              ];
               $helper = new Helpers();
         $otpstatus= $helper->send_otp($otpdata,'verification');
         $token = md5($saveArray['email']).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
          $data = new Register;
          $data->name = $saveArray['name'];
          $data->email= $saveArray['email'];
          $data->password= Hash::make($saveArray['password']);
          $data->token= $token;
          $data->role= 3;
          $data->original_password=$saveArray['password'];
          $data->mobile=$saveArray['mobile'];
          //$data->address=$saveArray['address'];
          //$data->designation = $saveArray['designation'];
		  
          if(!empty($saveArray['gender']))
		  {
			$data->gender= $saveArray['gender'];  
		  }
		  if(!empty($saveArray['email']))
		  {
			$data->email= $saveArray['email'];  
		  }
		  if(!empty($saveArray['address']))
		  {
			$data->address= $saveArray['address'];  
		  }
		  if(!empty($saveArray['designation']))
		  {
			$data->designation= $saveArray['designation'];  
		  }
		  
		  
          $data->timezone=$saveArray['timezone'];
          $data->login_type="U";
          $data->image=$image;
          $data->verification_code=$r_code;
          $data->country_code=$saveArray['country_code'];
          $data->created_at=date('Y-m-d H:i:s');
          $data->updated_at=date('Y-m-d H:i:s');
          $data->save();
		  return $data->id;
 }





    /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

 public function  loginChecker($saveArray){
        $r_code = rand(0000, 9999);
           $saveArray['verification_code'] = $r_code;
                $sms = 'CoRover Connect Verification code is ' . $r_code;
          $pos = strpos($saveArray->country_code.$saveArray->mobile , '+');
          $number =  ($pos === false) ? '+'.$saveArray->country_code.$saveArray->mobile : $saveArray->country_code.$saveArray->mobile;
          $otpdata = [
                'number' => $number,
                'password' => $r_code
              ];
               $helper = new Helpers();
         $otpstatus= $helper->send_otp($otpdata,'verification');
         $token = md5($saveArray->email).'-'.date('YmdHis').'-'.md5(rand(99999,999999999999));
          
          $data = [
            'token' => $token,
            'verification_code'=>$r_code,
          ];
        Register::where('id',$saveArray->id)->update($data);
        return $saveArray->id;
 }

   /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/


  function timezone_test_get_chat($submited_time,$submited_time_zone,$user_time_zone)
    {
      $submited_time_zone = $submited_time_zone;
      $time= $submited_time;
      $dt = new DateTime($time, new DateTimeZone($submited_time_zone));
      $dt->setTimezone(new DateTimeZone($user_time_zone));
      $timestamp = $dt->format("Y-m-d h:i");
      $a = $timestamp ." " .$dt->format("A");
      return $a;
    }


// for verification time 

 public function VerificationCommon($CheckExit,$saveArray){
           $default_group="1"; // set value 1 for default groups 
              $data = [
                  'mobile' => $CheckExit->mobile,
                  'new_mobile' => $CheckExit->new_mobile,
                  'new_countrycode' => $CheckExit->new_countrycode,
                  'updated_at' => date('Y-m-d H:i:s'),
                  'is_verified'=>'Y',
                ];
                Register::where('id',$saveArray['user_id'])->update($data);
                // find the default Group 
                $GroupDetails = Group::where(['default_group' => $default_group])->first();
                   if(count($GroupDetails)>0){
                    // exlode the user_id of group then find the current person exist or not 
                    // if not exist then concat his id
                    $get_group_users=explode(',',$GroupDetails->user_id);
                    if(!(in_array($saveArray['user_id'],$get_group_users))){
                        if(count($get_group_users) > 0){
                        $Groups_ids=$GroupDetails->user_id.','.$saveArray['user_id'];
                        }else{
                        $Groups_ids=$saveArray['user_id'];
                        }
                    }else{
                       $Groups_ids=$GroupDetails->user_id;
                    }
                   // update the groups 
                    $data = [
                    'user_id' => $Groups_ids,
                    ];
                  Group::where('id',$GroupDetails->id)->update($data);
                  // find in group members table 
                  $Group_Members= GroupMember::where(['user_id' => $saveArray['user_id'],'group_id'=>$GroupDetails->id])->first();
                  if(count($Group_Members)>0){
                      //update
                      $data = [
                       'user_id' => $saveArray['user_id'],
                       'group_id' => $GroupDetails->id,
                      ];
                     GroupMember::where('id',$Group_Members->id)->update($data);
                  }else{
                    // insert in group member table
                    $data = new GroupMember;
                    $data->user_id = $saveArray['user_id'];
                    $data->group_id= $GroupDetails->id;
                    $data->created = date('Y-m-d H:i:s');
                    //$data->created_at=date('Y-m-d H:i:s');
                    $data->updated_at=date('Y-m-d H:i:s');
                    $data->save();
                    // after this update the members count in groups table regarding this user
                      $total_user_count= $GroupDetails->user_count +1;
                      $data = [
                       'user_count' => $total_user_count,
                     ];
                    Group::where('id',$GroupDetails->id)->update($data);

                  }
                 }// group exist end
         return  "true";         
    }



    /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

 public function  Resend_Checker($number,$saveArray){
           $r_code = rand(0000, 9999);
           $sms = 'CoRover Connect Verification code is ' . $r_code;
           $otpdata = [
                'number' => $number,
                'password' => $r_code
              ];
               $helper = new Helpers();
         $otpstatus= $helper->send_otp($otpdata,'verification');
          $data = [
            'verification_code'=>$r_code,
          ];
        Register::where('id',$saveArray->id)->update($data);
        return $r_code;
 }



  /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

 public function  Forgot_Checker($saveArray){
                $name    = $saveArray->name;
                $resetPassword = rand(100000,999999);
                $pos = strpos($saveArray->country_code.$saveArray->mobile  , '+');
                $number =  ($pos === false) ? '+'.$saveArray->country_code.$saveArray->mobile : $saveArray->country_code.$saveArray->mobile;
                $otpdata = [
                  'number' => $number,
                  'password' => $resetPassword
                ];
                 $helper = new Helpers();
             $otpstatus= $helper->send_otp($otpdata,'forgot_password');
          $data = [
              'password'=>Hash::make($resetPassword),
              'original_password'=>$resetPassword,
            ];
        Register::where('id',$saveArray->id)->update($data);
        return "true";
 }




  /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/

 public function  Forgot_Checker_Mobile($saveArray){
                $name    = $saveArray->name;
                $resetPassword = rand(100000,999999);
                $pos = strpos($saveArray->country_code.$saveArray->mobile  , '+');
                $number =  ($pos === false) ? '+'.$saveArray->country_code.$saveArray->mobile : $saveArray->country_code.$saveArray->mobile;
                $otpdata = [
                  'number' => $number,
                  'password' => $resetPassword
                ];
                 $helper = new Helpers();
             $otpstatus= $helper->send_otp($otpdata,'verification');
          $data = [
              'password'=>Hash::make($resetPassword),
              'original_password'=>$resetPassword,
            ];
        Register::where('id',$saveArray->id)->update($data);
        return "true";
 }



 public function  Group_BroadCast_Message($groupId,$user_id){
    $CheckExit = Newbroadcast::where(['user_id' => $user_id,'group_id'=>$groupId])
     ->orderby('id','desc')->first();

               if(!empty($CheckExit))
              {
                  if($CheckExit->image== ""){
                            $img="";
                 }else{
                  $img= url('/')."/img/groupchatimg/".$CheckExit->image;
                 }

                 if($CheckExit->message == ""){
                            $msg="";
                 }else{
                  $msg = $CheckExit->message;
                 }
              }else{
                $msg="";
                $img="";
              }

            $finaldata= array('img'=>$img,'msg'=>$msg);
              return $finaldata;

 }





 public function  Group_Badges($groupId,$user_id,$helpdesk){

        if($helpdesk == "Yes"){
          // check the total helpdesk badges  because helpdek
            $Badgesforhelp = HelpdeskBadgesForApp::where(['receiver_id' => $user_id,'group_id'=>$groupId,'for_check'=>'home'])->get();
                  if(count($Badgesforhelp)>0){
                      $message = count($Badgesforhelp);
                  }else{
                    $message = '0';
                  }
                  $pmessage=0;
         }else if($helpdesk == "No"){
                   // Check the total chat badges
                  $Badgesforhelp = Chatbadge::where(['receiver_id' => $user_id,'group_id'=>$groupId,'for_check'=>'homepage'])->get();
                     if(count($Badgesforhelp)>0){
                         $pmessage = count($Badgesforhelp);
                        }else{
                          $pmessage = '0';
                     }
                      // Check the total Group badges
                     $UnreadBAdge = Unreadgroupbadge::where(['receiver_id' => $user_id,'group_id'=>$groupId,'status'=>'unread'])->get();
                     if(count($UnreadBAdge)>0){
                         $message = count($UnreadBAdge);
                        }else{
                          $message = '0';
                     }

            }
              $finaldata= $message + $pmessage ;
              return $finaldata;

 }

 public function  Get_FinaelArray($groupdata,$Totalbades,$BroadCastData){
              $data['total_badges'] = $Totalbades;
              $data['group_id'] = $groupdata->id;
              $data['fb_link'] = $groupdata->fb_link;
              $data['group_name'] =$groupdata->name;
              $data['company_name'] = $groupdata->company_name;
              $data['address'] = $groupdata->address;
              $data['logo'] = url('/') . "img/group_logo/" . $groupdata->icon;
              $data['image'] = url('/') . "img/group_images/" . $groupdata->image;
              $data['type'] = $groupdata->type;
              $data['allow_chat'] = $groupdata->allow_chat;
              $data['allow_chat_private'] =$groupdata->allow_chat_private;
              $data['internet_less_chat'] = $groupdata->internet_less_chat;
              $data['admin_id'] = $groupdata->created_id;
              $data['welcome_message'] = $groupdata->welcome_message;
              $data['has_helpdesk'] = $groupdata->has_helpdesk;
              $data['country_code'] = $groupdata->country_code;
              $data['help_mobile'] = $groupdata->mobile;
              $data['broadcast_message'] = $BroadCastData['msg'];
              $data['broadcast_image'] = $BroadCastData['img'];
              $data['user_type']='U';
              //$data['welcome_message'] = '';
              if($groupdata->staff_group=="Y"){
              $data['staff_group']='Y';
              }else{
              $data['staff_group']='N';
              }

      return $data;

 }





//=====================GROUP MEMBER GET ========================

 public function  Group_Member_Information($groupId,$user_id){

 $Check_GroupMember_Info =Groupinformation::where(['group_id' => $groupId,'status'=>'1','deleted'=>'0','user_id'=>$user_id])->orderby('created_at','desc')->first();
            if(!empty($Check_GroupMember_Info))
            {
              $dt = explode("-",$Check_GroupMember_Info->check_date);
              $date_format =$dt[2]."-".$dt[1]."-".$dt[0];
              $seat_no =$Check_GroupMember_Info->seat_no;
              $date = $date_format; 
            }else{
              $seat_no ="";
              $date = "";
            }

              $finaldata= array('seat_no'=>$seat_no,'date'=>$date);
              return $finaldata;

 }


 public function  Group_Member_Block($groupId,$user_id){

  $Get_Block_Member =Block::where(['friend_id'=> $user_id,'group_id'=>$groupId])
                             ->get();
            if(count($Get_Block_Member)>2)
            {
                $block ="block";
            }else{
                $Get_Block_Member =Block::where(['friend_id'=> $user_id,'group_id'=>$groupId])
                             ->get();
                 if(count($Get_Block_Member)>0){
                      $block ="block";
                 }else{
                     $block ="unblock";
                 }
            }
              return $block;

 }




 public function  Group_MEMber_Badges($groupId,$user_id,$helpdesk){
        if($helpdesk == "Yes"){
          // check the total helpdesk badges  because helpdek
            $Badgesforhelp = HelpdeskBadgesForApp::where(['receiver_id' => $user_id,'group_id'=>$groupId,'for_check'=>'private'])->get();
                  if(count($Badgesforhelp)>0){
                      $message = count($Badgesforhelp);
                  }else{
                    $message = '0';
                  }
         }else if($helpdesk == "No"){
                   // Check the total chat badges
                  $Badgesforhelp = Chatbadge::where(['receiver_id' => $user_id,'group_id'=>$groupId,'for_check'=>'homepage'])->get();
                     if(count($Badgesforhelp)>0){
                         $message = count($Badgesforhelp);
                        }else{
                          $message = '0';
                     }

            }
            $finaldata= $message ;
            return $finaldata;

 }



 public function  Get_Meber_FInalArray($memberInfo,$MEmberGroupInformation,$block,$badges,$GroupInformation){
                if(!empty($memberInfo->image)){
                      if($memberInfo->register_type=="F"){
                        $image = $memberInfo->image;
                       }  
                      if($memberInfo->register_type=="N"){
                          $image = url('/'). "images/users/" . $memberInfo->image;
                        }  
                   }else{
                      $image= url('/')."/images/users/no_image.png";
                  }
                $data['user_id']=$memberInfo->id;
                $data['name']=$memberInfo->name;
                $data['address']=$memberInfo->address;
                $data['designation']=$memberInfo->designation;
                $data['roomno']=$MEmberGroupInformation['seat_no'];
                $data['date']=$MEmberGroupInformation['date'];
                $data['refrence']=$GroupInformation->refrence;
                $data['image']=$image;
                $data['type']=$memberInfo->user_type;
                $data['block_status']=$block;
                $data['bluetooth_mac']=$memberInfo->bluetooth_mac;
                $data['gender']=$memberInfo->gender;
                $data['age']=$memberInfo->age;
                $data['total_badges'] =$badges;
              
     return $data;

 }

//===============END GROUP EMEBER CODE ===========================


 public function  Group_Setting($groupid,$user_id){

         $Settings =CheckSetting::where(['group_id' => $groupid,'user_id'=>$user_id])->first();
                      if(count($Settings)>0){
                          $private_chat =$Settings->private_chat;
                          $group_chat = $Settings->group_chat;
                          if($private_chat =="1"){
                             $private_cht = "off";
                          }else{
                            $private_cht = "on";
                          }
                          if($group_chat =="1"){
                            $group_cht = "off";
                          }else{
                             $group_cht = "on";
                          }
                      }else{
                        $private_cht = "on";
                        $group_cht = "on";
                      }

      $finaldata= array('private_cht'=>$private_cht,'group_cht'=>$group_cht);
              return $finaldata;


 }

//=======START SEND GROUP MESSGAGE ======================
 //======get time ==================

 public function  Get_time($timezone){

   $new_time_zone = date_default_timezone_set($timezone);
   $own = date('Y-m-d H:i:s');
 return $own;

  }



 public function  Save_Data($saveArray){
 $BAC= [];

      $SaveArr = new Groups;
   if ($saveArray['type'] == "T" AND !empty($saveArray['message'])) 
          {
            $SaveArr['image'] = "";
            $SaveArr['lat'] ="";
            $SaveArr['lng'] ="";
            $SaveArr['message'] = $saveArray['message'];
            $SaveArr['time_zone'] = $saveArray['timezone'];
            $SaveArr['submit_time'] = $saveArray['submit_time'];
              $SaveArr['user_id'] =$saveArray['user_id'];
              $SaveArr['group_id'] =$saveArray['group_id'];
          }

          if ($saveArray['type'] == "L" AND !empty($saveArray['lat'])  AND !empty($saveArray['lng'])) {
              $saveArr = new Groups;
              $saveArr['message'] = $saveArray['message'];
              $saveArr['image'] = "";
              $saveArr['submit_time'] = $saveArray['submit_time'];
              $saveArr['user_id'] =$saveArray['user_id'];
              $saveArr['group_id'] =$saveArray['group_id'];
              $saveArr['type'] =$saveArray['type'];
              $saveArr['lat'] =$saveArray['lat'];
              $saveArr['lng'] =$saveArray['lng'];
              $saveArr->save();
              $insertedId[] = $saveArr->id;
               }

          if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
                          $imagename = $helper->upload($_FILES['image'],'img/groupchatimg','none');
                            $SaveArr['submit_time'] = $saveArray['submit_time'];
                            $SaveArr['time_zone'] = $saveArray['timezone'];
                            $SaveArr['lat'] ="";
                            $SaveArr['lng'] ="";
                            $SaveArr['message'] ="";
                            $SaveArr['user_id'] =$saveArray['user_id'];
                            $SaveArr['group_id'] =$saveArray['group_id'];
                            $SaveArr['image'] = $imagename;
                            $BAC[]=$imagename;
                        
                    }
     $m= $SaveArr->save();
     $insertedId[] = $SaveArr->id;
      $Array = array('groupimd'=>$BAC,'insertedid'=>$insertedId[0]);
         return $Array;

  }


    public function  Group_Notification_BAdge($group_Data,$currentperson){
            foreach($group_Data as $v){
               if($v->user_id != $currentperson)
               {
                  $SaveArr = new Unreadgroupbadge;
                  $SaveArr['sender_id'] = $currentperson;
                  $SaveArr['receiver_id'] = $v->user_id;
                  $SaveArr['group_id'] = $v->group_id;
                  $SaveArr->save();
               }
            }
        return "true";

    }


  public function  Group_Notification($group_Data,$currentperson,$groupid,$currentPErsonDetails){
                   $Type = $currentPErsonDetails->user_type;
                 if($type == "G"){
                   $name = "Guest User";
                 }else{
                   $name = $currentPErsonDetails->name;
                 }
               foreach($group_Data as $v){
                     if($v->GetUSer->id != $currentperson)
                     {
                       $Get_Block_Member =  Block::where(['friend_id'=> $currentperson,'group_id'=>$groupid,'user_id'=>$v->GetUSer->id])
                           ->first();
                      if(count($Get_Block_Member)>0){
                       // i dont know what to do
                        // this is getting the latsi nsertd id   
                        // now i am seding the last insetd id in it 
                      }else{
                         //$helper = new Helpers();
                         //$helper->Group_Setting_send($groupid,$v->GetUSer->id);
              
                        $message = array('message' => "You have a new Group Message from  (".$name.")", 'sender_id' => $currentPErsonDetails->id, 'noti_for' => 'group',
                         'date' => $date,  'group_id' => $saveArray['group_id'], 'sender_name' => $sender['User']['name'], 'name' => $sender['User']['name'],
                        'sender_image' => $sender_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'], 'group_name' =>$users['Group']['name']);
                  
  

                      }

                     } // if part end inside the foreach
                  }

        return "true";

    }



 public function  Group_Setting_send($groupid,$user_id){

         $Settings =CheckSetting::where(['group_id' => $groupid,'user_id'=>$user_id])->first();
                      if(count($Settings)>0){

                          if($data_exist4['CheckSetting']['group_chat']=='0'){  
                          }
                        
                         
                      }else{
                        
                      }

      $finaldata= array('private_cht'=>$private_cht,'group_cht'=>$group_cht);
              return $finaldata;


 }

//========= END GROUP MESSGAE CODE =====================



//=============== START FOR QR CODE SACN COMMON FUNCTION ======================

 public function  GetQRcode($qrcode){
          if(!empty($qrcode)){
                $code = $qrcode;
            }else{
              $code = "";
            }

     /* if(empty($qrcode)){
        $CheckGroup = Group::where(['default_group' => '1' ])->first();
        if(!empty($CheckGroup)){
           $code = $CheckGroup->qr_code;
        }else{
           $code = "000000";
        }
      }*/
     return $code;
 }



public function  GetSeatNumber($seat){
          $seat_n = rand(00, 99);

      if(!empty($seat)){
           $seatNumber = $seat;
      }

      if(empty($seat)){
         $seatNumber = "";
      }

    return $seatNumber;
 }

public function  firstCheck($saveArray,$getQRcode,$getSeat){
  // if we have save user cred 
         $CheckGroup = Group::where(['qr_code' => $getQRcode,'status' => '1' , 'deleted' => '0'])->first();
          if(count($CheckGroup)>0){
            // find the user exist or not
           $CheckUSer = Register::where(['id' => $saveArray['user_id'],'status' => '1' , 'deleted' => '0'])->first();
            if(count($CheckUSer)>0){
              // get the group member 
              // gte user id 
              // check al;ready exist or not 
              // if not then add that memeber otherwise  
               $user_id=$CheckGroup->user_id;
               $id= $CheckUSer->id;
               $userarr=explode(',',$user_id);
                if(!(in_array($id,$userarr))){
                  if(count($userarr) > 0){
                    $userid=$user_id.','.$id;
                  }else{
                    $userid=$id;
                  }
                }else{
                  $userid=$user_id;
                }
                // UPdate the GRoup table with current user id
                $data = [
                'user_id'  => $userid,
                 ];
               Group::where('id',$CheckGroup->id)->update($data);
               // add data in member table 
               $GroupMemberData=  GroupMember::where('group_id',$CheckGroup->id,'user_id'=>$id)->first();
                if(count($GroupMember)>0){
                  // if we updaet here 
                   $datamember = [
                  'user_id'  => $userid,
                  'group_id'=>$CheckGroup->id,
                   ];
                 GroupMember::where('id',$GroupMemberData->id)->update($datamember);
                }else{
                  //save the record as member 
                 $data = new GroupMember;
                 $data->group_id = $CheckGroup->id;
                 $data->user_id = $id;
                 $data->save();
                }
               $GroupInformtion=  Groupinformation::where('group_id',$CheckGroup->id,'user_id'=>$id)->first();
                $date_time = date('Y-m-d H:i: A');
                $dat_tne =explode(" ",$date_time);
                $date =$dat_tne[0];
                $check_date =$date;
                if(count($GroupInformtion)>0){
                  // update
                  $datamember = [
                  'user_id'  => $userid,
                  'group_id'=>$CheckGroup->id,
                  'check_date'=>$check_date,
                   ];
                 GroupInformtion::where('id',$GroupInformtion->id)->update($datamember);
                }else{
                  // insert in db
                 $data = new GroupInformtion;
                 $data->group_id = $CheckGroup->id;
                 $data->user_id = $id;
                 $data->seat_no = $getSeat;
                 $data->check_date = $check_date;
                 $data->save();
                }
           
                // data for notoifcation
                $data = array('message' => $CheckGroup->welcome_message, 'noti_for' => 'new_group');
                if ($CheckUSer->device_type == "I") {
                  $deviceIds = $CheckUSer->device_id;
                  $c = strlen($deviceIds);
                  $main = 64;
                  if ($c == $main && $deviceIds != ""){
                    $a = $helper->iphone_send_notification($deviceIds, $data);
                  }
                }elseif ($CheckUSer->device_type == "A"){
                  $registatoin_id = $CheckUSer->device_id;
                  if ($registatoin_id != "" || $registatoin_id != "(null)"){
                     $helper->android_send_notification(array($registatoin_id), $data);
                  }
                }

              $result=array('status'=>'1','message'=>"Group Added Successfully"); 

            }else{
             $result=array('status'=>'0','message'=>"User not Found"); 
            }
          }else{
           $result=array('status'=>'0','message'=>"Qr code not Found");
         }// check user end 
     
    return $result;
 }

// ========== END THE ARCODE ===============================\

// ========================= SEND NOTIFICATION CODE ============================
  public function android_send_notification($registatoin_ids, $message) {
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
        if (!defined('GOOGLE_API_KEY')) {
           // $GOOGLE_API_KEY = 'AIzaSyDZUO4vQOCA_YhSVr0NR6qHUaNaYFH8XoU';
            $GOOGLE_API_KEY = 'AIzaSyADhZzCBdLH8m647vgSfH41Nawjjo5OqJA';
        }
        $headers = array(
            'Authorization: key=' . $GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        //pr($headers);die;
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        return $result;
    }

public function iphone_send_notification($deviceIds, $msg, $badge_count) {
//pr($deviceIds);
        $result = '';
        $message = $this->tr_to_utf($msg['message']);

        if ($msg['noti_for'] == 'private') {
            $payload = '{"aps":{
         "alert":"' . $message . '",
         "sender_id":"' . $msg['sender_id'] . '",
         "user_id":"' . $msg['friend_id'] . '",
         "name":"' . $msg['name'] . '",
         "noti_for":"' . $msg['noti_for'] . '",
         
         "group_id":"' . $msg['group_id'] . '",
         "date":"' . $msg['date'] . '",
                           "sender_name":"' . $msg['sender_name'] . '",
                           "sender_image":"' . $msg['sender_image'] . '",
                           "message_msg":"' . $msg['message_msg'] . '",
                           "message_img":"' . $msg['message_img'] . '", 
               "friend_type":"' . $msg['friend_type'] . '",
         "sound":"default",
         "badge": "' . $badge_count . '",
         }}';
        } else if ($msg['noti_for'] == 'group') {
            $payload = '{"aps":{
         "alert":"' . $message . '",
         "sender_id":"' . $msg['sender_id'] . '",
       
         "noti_for":"' . $msg['noti_for'] . '",
       
         "group_id":"' . $msg['group_id'] . '",
        
          "sender_name":"' . $msg['sender_name'] . '",
                           "sender_image":"' . $msg['sender_image'] . '",
                           "message_msg":"' . $msg['message_msg'] . '",
                           "message_img":"' . $msg['message_img'] . '",  
         "sound":"default",
         "badge": "' . $badge_count . '",
         }}';
        } else if ($msg['noti_for'] == 'new_group') {
            $payload = '{"aps":{
         "alert":"' . $message . '",
         
       
         "noti_for":"' . $msg['noti_for'] . '",
       
           
         "sound":"default",
         "badge": "' . $badge_count . '",
         }}';
        }else {
            $payload = '{"aps":{
         "alert":"' . $message . '",
         "sender_id":"' . $msg['sender_id'] . '",
         "user_id":"' . $msg['user_id'] . '",
         "name":"' . $msg['name'] . '",
         "noti_for":"' . $msg['noti_for'] . '",
         "currentcity":"' . $msg['currentcity'] . '",
         "vehiclenumber":"' . $msg['vehiclenumber'] . '",
         "trip_id":"' . $msg['trip_id'] . '",
         "date":"' . $msg['date'] . '",
         "sound":"default",
         "badge": "' . $badge_count . '",
         }}';
        }
        $ctx = stream_context_create();
        $passphrase = '';

     //   stream_context_set_option($ctx, 'ssl', 'local_cert', 'certs/JANDK_Dev_Push.pem');
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'certs/push_production.pem');
        //stream_context_set_option($ctx, 'ssl', 'local_cert', 'certs/Certificates.pem');
       // stream_context_set_option($ctx, 'ssl', 'local_cert', 'certs/NewDevPush.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

         // $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 4, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
      $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 4, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        if ($fp) {
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceIds) . pack('n', strlen($payload)) . $payload;
            $result = fwrite($fp, $msg, strlen($msg));
            fclose($fp);
        }
 

  
        return $result;
    }



} // class helper 



;?>ba