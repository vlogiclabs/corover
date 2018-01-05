<?php
	/**
	* User Controller class
	* PHP versions 5.1.4
	* @date 17-jan-1016
	* @Purpose:This controller handles all the functionalities regarding user management.
	* @filesource
	* @revision
	* @version 0.0.1
	**/
	App::uses('Sanitize', 'Utility');
	//App::uses('CakeEmail', 'Network/Email');
class UserServicesController extends AppController
	{
    var $name        	=  "Users";

   /*
    *
	* Specifies helpers classes used in the view pages
	* @access public

	*/
    
    public $helpers     	=  array();

    /**
	* Specifies components classes used
	* @access public
    */

    var $components 	=  array('RequestHandler','Email','Common','Upload','Paginator');
    var $paginate		  =  array();
    var $uses       	=  array('Grouptype','Groupchat','Group','GroupMember','ServiceGroup','User','Image','Like','Comment','Notification','Rating','Connection','Chat','Post','ProfileImage','Groupinformation','CheckSetting','Feedback','Block','PushNotification','Service','Unreadgroupbadge','Chatbadge','Newbroadcast','HelpdeskBadgesForApp','SupportChat','ReportAbuse'); 
	// For Default Model

	#_________________________________________________________________________#

    /**
    * @Date: 17-jan-1016
    * @Method : beforeFilter
    * @Purpose: This function is called before any other function.
    * @Param: none
    * @Return: none 
    **/
	
	    //$full_path = BASE_URL."img/post_images/";
	

    function beforeFilter()
	{
		App::uses('CakeTime', 'Utility');
		if (!empty($this->data) && trim($this->data['auth_key']) != 123) {
            $result = array('status' => '0', 'message' => "Authenticated key not matched");
            echo json_encode($result);
            die;
        }
    }

	function index(){
	
	}
    #_________________________________________________________________________#

    /**
    * @Date: 31-may-1016
    * @Method : update location
    * @Purpose: This function is used to update location
    * @Param: none
    * @Return: none 
    **/
	function update_location(){
		$saveArray=$this->data;
		if(!empty($saveArray['user_id']) && !empty($saveArray['latitude']) && !empty($saveArray['longitude'])){
			$condition="User.id='".$saveArray['user_id']."' ";
			$userExist=$this->User->find('first',array('conditions'=> $condition));
			if($userExist){ 
				$this->User->updateAll(array('User.latitude' => $saveArray['latitude'],'User.longitude' => $saveArray['longitude']),array('id'=>$userExist['User']['id']));
				$result=array('status'=>'1','message'=>'success','User Id'=> $userExist['User']['id']);
					
			}else{
			$result=array('status'=>'0','message'=>'User not found');
			}
		}else{
		$result=array('status'=>'0','message'=>'Please fill all fields');
		} 
		echo json_encode($result);
		die;
	}
	 #_________________________________________________________________________#

    /**
    * @Date: 17-may-1016
    * @Method : register
    * @Purpose: This function is used to register user
    * @Param: none
    * @Return: none 
    **/
	function register(){
		$saveArray = $this->data;
		$this->User->set($saveArray);
		if(!empty($saveArray['mobile']) && !empty($saveArray['password']) && !empty($saveArray['name'])
			 && !empty($saveArray['country_code']) && !empty($saveArray['user_type'])){
				 if(!empty($saveArray['address'])){
					$saveArray['address']=$saveArray['address'];
				 }
				  if(empty($saveArray['email'])){
					$saveArray['email']="";
				 }
				  if(!empty($saveArray['email'])){
					$saveArray['email']=$saveArray['email'];
				 }
				  if(empty($saveArray['address'])){
					$saveArray['address']="";
				 }
				if(!empty($saveArray['designation'])){
					$saveArray['designation']= $saveArray['designation'];
				}
				if(empty($saveArray['designation'])){
					$saveArray['designation']="";
				}
				if(!empty($saveArray['gender'])){
					$saveArray['gender']= $saveArray['gender'];
				}
				if(empty($saveArray['gender'])){
					$saveArray['gender']="O";
				}
		/* $this->User->validator()->remove('mobile', 'ruleName4');
		$isValidated=$this->User->validates();
		
		if($isValidated){ */
		if(!empty($_FILES['image']['name'])){
			$destination=realpath('../../app/webroot/img/profile_images'). DS;
			$gen_id = rand(000, 999);
			$filename = $this->uploadPic($gen_id,$destination,$_FILES['image']);
			$saveArray['image'] =$filename;
		}
		 if(empty($_FILES['image']['name'])){
							 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
								$saveArray['image'] = $defaulturl;
						 }
			$saveArray['password'] = md5($saveArray['password']);
			//$saveArray['user_type'] = 'U';
			$condition="User.mobile='".$saveArray['mobile']."' AND User.country_code='".$saveArray['country_code']."' AND status = '1' AND deleted = '0'";
			$userdetail = $this->User->find('first',array('conditions'=> $condition,'fields'=>array('id','mobile','user_type','login_type','is_verified','country_code','image')));
			if(empty($userdetail)){
				// if(empty($saveArray['designation'])){
				// $this->User->validator()->remove('designation','ruleName');
					// $this->User->validator()->remove('designation','between');
				// }
				
				
				
		$isValidated=$this->User->validates();
		
		if($isValidated){
			 $r_code = rand(0000, 9999);
			 $saveArray['verification_code'] = $r_code;
			
            $sms = 'CoRover Connect Verification code is ' . $r_code;
           // $number = '+'.$saveArray['country_code'].$saveArray['mobile'];

			$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
			$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
			$otpdata = [
						'number' => $number,
						'password' => $r_code
					];
			$this->Common->send_otp($otpdata,'verification');

				/*
					 
					 require_once("../Vendor/twilio-php/Services/Twilio.php");
					 
                        // set your AccountSid and AuthToken from www.twilio.com/user/account
                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
                            $result = array('status' => '0', 'message' => $e->getMessage());
                            
                        }*/
						unset($saveArray['roomno']);
						/* $query= "INSERT INTO users (name, email,country_code,mobile)
                               VALUES ('".$saveArray['name']."', '".$saveArray['email']."', '".$saveArray['country_code']."','".$saveArray['mobile']."')"; */
							   
		$res= $this->User->save($saveArray,array('validate'=>false));
		//$id=$this->User->id;
		//print_r($res);die;
		//$this->User->set($saveArray);
               //$res=$this->User->query($query);	
             				
				$result = array('status'=>'1','message'=>'Register Successfully','id'=>$res['User']['id'],'is_verified'=>'not verified');
		  }else{
		$erros=$this->errorValidation('User');
		$result=array('status'=>'0','message'=>$erros);
		}
			}else if(!empty($userdetail) && $userdetail['User']['login_type']=='G'){
				$this->User->validator()->remove('mobile', 'isUnique');
		$isValidated=$this->User->validates();
		$login_type='N';
		if($isValidated){
				
				$r_code = rand(0000, 9999);
			 $saveArray['verification_code'] = $r_code;
			
            $sms = 'CoRover Connect Verification code is ' . $r_code;
            //$number = '+'.$userdetail['User']['country_code'].$userdetail['User']['mobile'];
			
			$pos = strpos($userdetail['User']['country_code'].$userdetail['User']['mobile'] , '+');
			$number =  ($pos === false) ? '+'.$userdetail['User']['country_code'].$userdetail['User']['mobile'] : $userdetail['User']['country_code'].$userdetail['User']['mobile'];
			$otpdata = [
						'number' => $number,
						'password' => $r_code
					];
			$this->Common->send_otp($otpdata,'verification');

					 
					/* require_once("../Vendor/twilio-php/Services/Twilio.php");
					 
                        // set your AccountSid and AuthToken from www.twilio.com/user/account
                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
                            $result = array('status' => '0', 'message' => $e->getMessage());
                            
                        }*/
						$saveArray['is_verified'] = 'I';
						$this->User->updateAll(array('email'=>"'".$saveArray['email']."'",'address'=>"'".$saveArray['address']."'",'mobile'=>"'".$saveArray['mobile']."'",'designation'=>"'".$saveArray['designation']."'",'gender'=>"'".$saveArray['gender']."'",'name'=>"'".$saveArray['name']."'",'user_type'=>"'".$saveArray['user_type']."'",'country_code'=>"'".$saveArray['country_code']."'",'password'=>"'".$saveArray['password']."'",'login_type'=>"'".$login_type."'",'verification_code'=>"'".$saveArray['verification_code']."'",'is_verified'=>"'".$saveArray['is_verified']."'"),array('id'=>$userdetail['User']['id']));
			 $result = array('status'=>'1','message'=>'Register Successfully','id'=>$userdetail['User']['id'],'is_verified'=>'not verified');
		 }else{
		$erros=$this->errorValidation('User');
		$result=array('status'=>'0','message'=>$erros);
		}}else if(!empty($userdetail) && $userdetail['User']['is_verified']=='Y' || $userdetail['User']['is_verified']=='C'){
			  $result = array('status'=>'0','message'=>'Already Registered','is_verified'=>'verified');
		 }else{
			 
			  $r_code = rand(0000, 9999);
			 $saveArray['verification_code'] = $r_code;
			
            $sms = 'CoRover Connect Verification code is ' . $r_code;
          //  $number = '+'.$userdetail['User']['country_code'].$userdetail['User']['mobile'];
				$pos = strpos($userdetail['User']['country_code'].$userdetail['User']['mobile'] , '+');
			$number =  ($pos === false) ? '+'.$userdetail['User']['country_code'].$userdetail['User']['mobile'] : $userdetail['User']['country_code'].$userdetail['User']['mobile'];
			$otpdata = [
						'number' => $number,
						'password' => $r_code
					];
			$this->Common->send_otp($otpdata,'verification');
					 
					/* require_once("../Vendor/twilio-php/Services/Twilio.php");
					 
                        // set your AccountSid and AuthToken from www.twilio.com/user/account
                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
                            $result = array('status' => '0', 'message' => $e->getMessage());
                            
                        }*/
						$this->User->updateAll(array('verification_code' => '"'.$saveArray['verification_code'].'"'), array('id' => $userdetail['User']['id']));
						$result = array('status'=>'0','message'=>'Already Registered but need to verified','is_verified'=>'not verified','id'=>$userdetail['User']['id']); 
		 }
			
		/* }else{
		$erros=$this->errorValidation('User');
		$result=array('status'=>'0','message'=>$erros);
		} */
	}else{
		$result=array('status'=>'0','message'=>'Plesae Fill all fields');
		}
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}

    #_________________________________________________________________________#
	
	
	function temp(){
		echo 'done';					
		die;
		
		
        ini_set('memory_limit', '256M');
	    set_time_limit(0);
   
		echo 'Start';
		$groups = $this->Group->find('all');
		foreach($groups as $k=>$v){
			$u=$v['Group']['user_id'];
			$usr_array= explode(',',$u);
			$staff= $this->User->find('count', array('conditions' => array('User.id' => $usr_array,'User.user_type =' =>'S')));
			$user= $this->User->find('count', array('conditions' => array('User.id' => $usr_array,'User.user_type !=' =>'S')));
		 
			foreach($usr_array as $k1=>$v1){
                if(!$v1){
				   continue;
				}					
				$GrpMmbr = ['group_id'=>$v['Group']['id'],'user_id'=>$v1];
				
				
				if(!$this->GroupMember->find('first',['conditions'=>['GroupMember.group_id'=>$v['Group']['id'],'GroupMember.user_id'=>$v1]])){
	
					if($this->GroupMember->saveAll($GrpMmbr,array('validate'=>false))){
						//echo 'Succes :- g_id='.$v['Group']['id'].'//u_id='.$v1;
						//echo '<br/>';
					}else{
						//echo 'Fail :- g_id='.$v['Group']['id'].'//u_id='.$v1;
						//echo '<br/>';
						
					}
				}
				
				$this->Group->updateAll(array('staff_count' =>$staff,'user_count'=>$user), array('id' => $v['Group']['id']));
			}	


             echo 'done';			
			
			
		}
		die;
    }

    /**
    * @Date: 17-may-1016
    * @Method : login
    * @Purpose: This function is used to login user
    * @Param: none
    * @Return: none 
    **/
	function login()
	{
        $saveArray=$this->data;
		if(!empty($saveArray['country_code']) && !empty($saveArray['mobile']) && !empty($saveArray['password']))
		{
			$password=md5($saveArray['password']);
			$condition="User.country_code='".$saveArray['country_code']."' AND User.mobile='".$saveArray['mobile']."' AND User.password='".$password."' AND status = '1' AND deleted = '0' AND activate_status = 'Activate'";
			$userdetail = $this->User->find('first',array('conditions'=> $condition,'fields'=>array('id','mobile','new_mobile','user_type','name', 'is_verified','country_code','new_countrycode')));
			if(!empty($userdetail)){
					$user = $userdetail['User'];
					if($userdetail['User']['user_type']=='S' && $userdetail['User']['is_verified']=='Y' ){
							$result = array('status'=>'1','message'=>'Login Successfully','id'=>$user['id'],'user_type' => $user['user_type'],'user_name' => $user['name']);
					}
					if($userdetail['User']['user_type']=='A' && $userdetail['User']['is_verified']=='Y'){
							$result = array('status'=>'1','message'=>'Login Successfully','id'=>$user['id'],'user_type' => $user['user_type'],'user_name' => $user['name']);
					}
					
				
				if(!empty($userdetail['User']['new_mobile']) || !empty($userdetail['User']['new_countrycode'])){
				if($userdetail['User']['is_verified']=='Y' || $userdetail['User']['is_verified']=='C'){
					
					$result = array('status'=>'1','message'=>'Login Successfully','id'=>$user['id'],'user_type' => $user['user_type'],'user_name' => $user['name']);
				}else if(!empty($userdetail) && $userdetail['User']['is_verified']=='I'){
					$user = $userdetail['User'];
					$r_code = rand(0000, 9999);
				 $saveArray['verification_code'] = $r_code;
				
	            $sms = 'CoRover Connect Verification code is ' . $r_code;
	            $pos = strpos($userdetail['User']['country_code'].$userdetail['User']['mobile'] , '+');
	            $number =  ($pos === false) ? '+'.$userdetail['User']['country_code'].$userdetail['User']['mobile'] : $userdetail['User']['country_code'].$userdetail['User']['mobile'];
				$otpdata = [
							'number' => $number,
							'password' => $r_code
						];
				$this->Common->send_otp($otpdata,'verification');
						 
						/* require_once("../Vendor/twilio-php/Services/Twilio.php");
						 
	                        // set your AccountSid and AuthToken from www.twilio.com/user/account
	                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
	                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
	                        $client = new Services_Twilio($AccountSid, $AuthToken);  
	                  
	                        try {
	                            $message = $client->account->messages->create(array(
	                                "From" => "(857) 267-6837",
	                                "To" => $number,
	                                "Body" => $sms,
	                            ));
	                        } catch (Services_Twilio_RestException $e) {
	                            $result = array('status' => '0', 'message' => $e->getMessage());
	                            
	                        }*/
				 $this->User->updateAll(array('verification_code' => '"'.$saveArray['verification_code'].'"'), array('id' => $userdetail['User']['id']));
				 $result=array('status'=>'1','message'=>'Need to verify','id'=>$user['id'],'user_type' => $user['user_type'],'user_name' => $user['name']);
				}
				}else{
					if(!empty($userdetail) && $userdetail['User']['is_verified']=='Y'){
					$user = $userdetail['User'];
					$result = array('status'=>'1','message'=>'Login Successfully','id'=>$user['id'],'user_type' => $user['user_type'],'user_name' => $user['name']);
				}else if(!empty($userdetail) && $userdetail['User']['is_verified']=='I'){
					$user = $userdetail['User'];
				
				 $result=array('status'=>'1','message'=>'Need to verify','id'=>$user['id'],'user_type' => $user['user_type'],'user_name' => $user['name']);
				}
					
				}
			}
			
			else{
				 $result=array('status'=>'0','message'=>'Password or mobile number is incorrect');
			}
		}else{
		$result=array('status'=>'0','message'=>'Please fill all fields');
		} 
		/* echo json_encode($result);
		die; */
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}
	#_________________________________________________________________________#
	function verification() {
        $saveArray = $this->data;
        $this->User->set($saveArray);
        if(!empty($saveArray['user_id']) && !empty($saveArray['verification_code'])){
			$isValidated = $this->User->validates();
			if($isValidated){
				$default_group="1";
				$user_exist = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'], 'User.verification_code' => $saveArray['verification_code'])));
				if(!empty($user_exist)){
					if(!empty($user_exist['User']['new_mobile']) OR !empty($user_exist['User']['new_countrycode'])){
						$a='';
						$this->User->updateAll(array('is_verified' => '"Y"','mobile' => '"'.$user_exist['User']['new_mobile'].'"',
						'country_code' => '"'.$user_exist['User']['new_countrycode'].'"','new_mobile'=>'"'.$a.'"','new_countrycode'=>'"'.$a.'"'), array('id' => $user_exist['User']['id']));
								$condition33="Group.default_group='".$default_group."'";
						$groupdetail = $this->Group->find('first',array('conditions'=> $condition33,'fields'=>array('id','user_id','name','staff_count','user_count')));
							if(!empty($groupdetail)){
							
							
								$userarr=explode(',',$user_id);
								// if(!(in_array($id,$userarr))){
									// $userid=$user_id.','.$id;
								// }else{
									// $userid=$user_id;
								// }
								$id = $saveArray['user_id'];
								if(!(in_array($id,$userarr))){
									if(count($userarr) > 0){
										$userid=$user_id.','.$id;
									}else{
										$userid=$id;
									}
								}else{
									$userid=$user_id;
								}
								$user_type = $user_exist['User']['user_type'];
								$gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
								$condition37="GroupMember.user_id='".$saveArray['user_id']."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
								$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
								if(!empty($groupmem)){
									$this->GroupMember->updateAll(array('user_id'=>"'".$saveArray['user_id']."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
								}else{
									$GrpMmbr['user_id']=$saveArray['user_id'];
									$GrpMmbr['group_id']=$groupdetail['Group']['id'];
									$this->GroupMember->save($GrpMmbr,array('validate'=>false));
									$user_count = $groupdetail['Group']['user_count']+1;
									$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
								}
								
							}
						$result = array('status' => '1', 'message' => "Verified Successfully", 'user_id' => $user_exist['User']['id']);
					}else{
						$this->User->updateAll(array('is_verified' => '"Y"'), array('id' => $user_exist['User']['id']));
								$condition33="Group.default_group='".$default_group."'";
						$groupdetail = $this->Group->find('first',array('conditions'=> $condition33,'fields'=>array('id','user_id','name','staff_count','user_count')));
							if(!empty($groupdetail)){
							
								$user_id=$groupdetail['Group']['user_id'];
								$userarr=explode(',',$user_id);
								// if(!(in_array($id,$userarr))){
									// $userid=$user_id.','.$id;
								// }else{
									// $userid=$user_id;
								// }
								$id = $saveArray['user_id'];
								if(!(in_array($id,$userarr))){
									if(count($userarr) > 0){
										$userid=$user_id.','.$id;
									}else{
										$userid=$id;
									}
								}else{
									$userid=$user_id;
								}
								$user_type = $user_exist['User']['user_type'];
								$gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
								$condition37="GroupMember.user_id='".$saveArray['user_id']."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
								$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
								if(!empty($groupmem)){
									$this->GroupMember->updateAll(array('user_id'=>"'".$saveArray['user_id']."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
								}else{
									$GrpMmbr['user_id']=$saveArray['user_id'];
									$GrpMmbr['group_id']=$groupdetail['Group']['id'];
									$this->GroupMember->save($GrpMmbr,array('validate'=>false));
									$user_count = $groupdetail['Group']['user_count']+1;
									$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
								}
							}
						$result = array('status' => '1', 'message' => "Verified Successfully", 'user_id' => $user_exist['User']['id']);
					} 
				}else{
					$result = array('status' => '0', 'message' => "Wrong Information");
				}
			}else{
				$errors = $this->errorValidation('User');
				$result = array('status' => '0', 'message' => $errors);
			}
		}else{
			$result=array('status'=>'0','message'=>'Please fill all fields');
		} 
     
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
#________________________________________________________________________________________________#
function resend_code() {
        $saveArray = $this->data;
        $this->User->set($saveArray);
       // $this->User->validator()->remove('email', 'isUnique');
        //$this->User->validator()->remove('mobile', 'ruleName4');
        $isValidated = $this->User->validates();
        if ($isValidated) {
          

            $r_code = rand(1000, 9999);

            $user_details = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
            if (!empty($user_details)) {
				if(!empty($user_details['User']['new_mobile']) && !empty($user_details['User']['new_countrycode'])){
					 $number = '+'. $user_details['User']['new_countrycode'] . $user_details['User']['new_mobile'];
				}else{
					 $number = '+'. $user_details['User']['country_code'] . $user_details['User']['mobile'];
				}
                if ($user_details['User']['is_verified'] == "C" OR  $user_details['User']['is_verified'] == "I" ) { 
				//print_r("ji");die;
				// If User is not verified, send code again
                    //$sms = 'CoRover Connect Verification code is ' . $r_code;
					$pos = strpos($user_details['User']['country_code'] . $user_details['User']['mobile'] , '+');
					$number =  ($pos === false) ? '+'.$user_details['User']['country_code'] . $user_details['User']['mobile'] : $user_details['User']['country_code'] . $user_details['User']['mobile'];
					$otpdata = [
								'number' => $number,
								'password' => $r_code
							];
					$this->Common->send_otp($otpdata,'verification');

					//. $user_details['User']['phone']
                   // $number = '+'. $user_details['User']['country_code'] . $user_details['User']['mobile'];
                      /*  require_once "../Vendor/Nexmo/src/NexmoMessage.php";
        // Declare new NexmoMessage.
                     $nexmo_sms = new NexmoMessage('89895268', 'c5c500b009847b92');
                     $nexmo_sms->sendText($number, 'freemium', $sms); */
					 /* require_once("../Vendor/twilio-php/Services/Twilio.php");
                        // set your AccountSid and AuthToken from www.twilio.com/user/account
                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
                            $result = array('status' => '0', 'message' => $e->getMessage());
                              
                        }*/
                 
                        $this->User->updateAll(array('verification_code' => '"' . $r_code . '"'), array('id' => $user_details['User']['id']));
                        $result = array('status' => '1', 'message' => "Code sent to registered mobile number",'Code'=>$r_code);
                      /*   echo json_encode($result);
                        die; */ 
						 
                   
                } else if ($user_details['User']['is_verified'] == "Y" && $user_details['User']['login_type'] == "G") {
					$r_code1 = rand(1000, 9999);
					//$sms = 'CoRover Connect Verification code is ' . $r_code1;
					$pos = strpos($user_details['User']['country_code'] . $user_details['User']['mobile'] , '+');
					$number =  ($pos === false) ? '+'.$user_details['User']['country_code'] . $user_details['User']['mobile'] : $user_details['User']['country_code'] . $user_details['User']['mobile'];
					$otpdata = [
								'number' => $number,
								'password' => $r_code1
							];
					$this->Common->send_otp($otpdata,'verification');
				/*	require_once("../Vendor/twilio-php/Services/Twilio.php");
                      $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
                            $result = array('status' => '0', 'message' => $e->getMessage());
                              
                        }*/
                 $this->User->updateAll(array('verification_code' => '"' . $r_code1 . '"'), array('id' => $user_details['User']['id']));
                        $result = array('status' => '1', 'message' => "Code sent to registered mobile number",'Code'=>$r_code1);
                    //$result = array('status' => '0', 'message' => "You are already Verified. Please Login.");
                }
            } else {
                $result = array('status' => '0', 'message' => "You are not Register. Firstly Sign Up.");
            }
        } else {
            $errors = $this->errorValidation('User');
            $result = array('status' => '0', 'message' => $errors);
        }
        /* echo json_encode($result);
        die; */
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }

	
	 /**

    * @Date: 17-may-1016

    * @Method : forgot_password

    * @Purpose: This function is to reset user's password.

    * @Param: none

    * @Return: none 

    **/



    function forgot_password(){
			if($this->data){
				$this->User->set($this->data);
				$this->User->validator()->remove('email','isUnique');
				$this->User->validator()->remove('mobile','isUnique');
				$isValidated = $this->User->validates();
					if($isValidated){
						if(!empty($this->data['email'])){
						$condition = "email='".Sanitize::escape($this->data['email'])."' and status = '1' AND deleted = '0' ";
						$emp_details = $this->User->find('first', array("conditions" => $condition, "fields" => array("id","mobile","name","country_code")));
					//	pr($emp_details); die;
							if(!empty($emp_details)){
								// Reset password
								$name    = $emp_details['User']['name'];
								$resetPassword = rand(100000,999999);
								
								$pos = strpos($emp_details['country_code'].$emp_details['mobile'] , '+');
								$number =  ($pos === false) ? '+'.$emp_details['country_code'].$emp_details['mobile'] : $emp_details['country_code'].$emp_details['mobile'];
								$otpdata = [
									'number' => $number,
									'password' => $resetPassword
								];
								$this->Common->send_otp($otpdata,'forgot_password');
											
								$this->User->updateAll(array("password"=>"'".md5($resetPassword)."'" , 'original_password' => $resetPassword ),   array("id"=>$emp_details['User']['id']));
								$result=array('status'=>'1','message'=>'Password changed,check you mobile inbox');
											
							}else{
								$result=array('status'=>'0','message'=>'This user not exist');
							}
						}
						if(!empty($this->data['mobile']) And !empty($this->data['country_code'])){
							$withoutplus = str_replace('+', '', $this->data['country_code']);
						$condition12 = "mobile='".$this->data['mobile']."' AND status = '1' AND deleted = '0' AND (country_code='".$this->data['country_code']."' OR country_code='".$withoutplus."')";
						/*	$condition12 = ['mobile' => $this->data['mobile'], 'status' => '1' , 'deleted' => '0,
											'OR' => array(
                                                array('country_code' => $this->data['country_code'] ),
                                                array('country_code' => '+'.$this->data['country_code']),
                                            )	
											];*/
						//pr($condition12); die;
						$emp_details1 = $this->User->find('first', array(
							"conditions" => $condition12,
							 "fields" => array("id","mobile","name","country_code")));
						//pr($emp_details1); die;

						if(!empty($emp_details1)){
							// Reset password
							$name    = $emp_details1['User']['name'];
							$resetPassword = rand(100000,999999);

							$pos = strpos($emp_details1['User']['country_code'].$emp_details1['User']['mobile'] , '+');
							$number =  ($pos === false) ? '+'.$emp_details1['User']['country_code'].$emp_details1['User']['mobile'] : $emp_details1['User']['country_code'].$emp_details1['User']['mobile'];
							$otpdata = [
										'number' => $number,
										'password' => $resetPassword
									];
							//pr($otpdata); die;
							$this->Common->send_otp($otpdata,'verification');
/*


							$otpdata = [
									'number' => '+'. $emp_details['User']['country_code'] . $emp_details['User']['mobile'],
									'password' => $resetPassword
								];
								$this->Common->send_otp($otpdata,'forgot_password');*/
			
										
											$this->User->updateAll(array("password"=>"'".md5($resetPassword)."'" , 'original_password' => $resetPassword ),array("id"=>$emp_details1['User']['id']));
											$result=array('status'=>'1','message'=>'Password changed,check you mobile inbox');
										
									}else{
									$result=array('status'=>'0','message'=>'This user not exist');
									}
						}
							}else{
									$erros=$this->errorValidation('User');
									$result=array('status'=>'0','message'=>$erros);
							}
							
					}
			
			 $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	#_________________________________________________________________________#

	 /**
    * @Date: 27-jan-1016
    * @Method : fetch_profile
    * @Purpose: This function is used to show user details
    * @Param: none
    * @Return: none 
    **/
	function fetch_profile(){
		$saveArray=$this->data;
		$get_user=$this->User->find('first',array('conditions'=>array('User.id'=>$saveArray['user_id'],'User.deleted'=>1,'User.status'=>2)));
		$record=array();
		$url = BASE_URL."img/profile_images/";
		$defaulturl = BASE_URL."images/common/user_img_placeholder.png";
		if(!empty($get_user)){
		$record['user_id'] = $get_user['User']['id'];
		$record['name'] = $get_user['User']['name'];
		$record['email'] = $get_user['User']['email'];
		if(!empty($get_user['User']['country_code'])){
			$record['country_code'] = $get_user['User']['country_code'];
		}else{
			$record['country_code']="91";
		}
		
		if(!empty($get_user['User']['new_countrycode'])){
			$record['new_countrycode'] = $get_user['User']['new_countrycode'];
		}else{
			$record['new_countrycode']='';
		}
		//$record['mobile'] = $get_user['User']['mobile'];
		if(!empty($get_user['User']['mobile'])){
			$record['mobile'] = $get_user['User']['mobile'];
		}else{
			$record['mobile']='';
		}
		if(!empty($get_user['User']['new_mobile'])){
			$record['new_mobile'] = $get_user['User']['new_mobile'];
		}else{
			$record['new_mobile']='';
		}
		if(!empty($get_user['User']['image'])){
			if($get_user['User']['register_type']=="N"){
				$image = BASE_URL . "img/profile_images/" . $get_user['User']['image'];
			}
			if($get_user['User']['register_type']=="F"){
				$image = $get_user['User']['image'];
			}
		}else{
			$image = $defaulturl;
		}
		$record['gender'] = $get_user['User']['gender'];
		$record['age'] = $get_user['User']['age'];
		$record['designation'] = $get_user['User']['designation'];
		$record['address'] = $get_user['User']['address'];
		$record['user_type'] = $get_user['User']['user_type'];
		$record['image'] = $image;
		
		//$address = $this->get_address_city($record['latitude'],$record['longitude']); 
		$result=array('status'=>'1','result'=>$record);
		}else{ $result=array('status'=>'0','mesaage'=>'User Not found');}
		
		 $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}

	
	/*====================================*/

  function get_groups()
	 {

      $saveArray = $this->data;

        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no'])) 
		{
            $user_exists = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
		 if($user_exists)
		 {
            if (!empty($user_exists) && $user_exists['User']['user_type']=='U') {
               
			
			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id ) OR default_group = '1') order by modified DESC ";
                $group = $this->Group->query($query1);
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id) )";
				$page = $this->Group->query($query);
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
                if (!empty($group))
				{
                    foreach ($group as $k => $values) { 
					    
				      $checkheplgroup =  $values['groups']['has_helpdesk'];

					  if($checkheplgroup == "Yes"){
							 $pmessage=0;
						      $groupmessage = $this->HelpdeskBadgesForApp->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'home','receiver_id'=>$saveArray['user_id']
									 ),
								));

								if(!empty($groupmessage)){
									$m= count($groupmessage);
								}else{
									$m=0;
								}
								 
								  $last_message = $this->SupportChat->find('first',array(
								'conditions'=>array('SupportChat.group_id'=>$values['groups']['id'],
									'SupportChat.receiver_id'=>$saveArray['user_id']
								 ),
								'order'=>array('SupportChat.ID DESC')
							));

							if(count($last_message) > 0){
								//$id = date_create($last_message['SupportChat']['submit_time'])->format('YmdHis');
								$id=$last_message['SupportChat']['id'];
								} else {
										$date = date('Y-m-d');
				                    	 $getgtoups = $this->Group->find('first',array(
													'conditions'=>array('id'=>$values['groups']['id'],
													 ),
													'order'=>array('Group.ID DESC')
										 ));
	                                     if($getgtoups['Group']['created'] <= $date){
	                                      	 $id=0;
	                                      }else{
	                                      	$id=50000;
	                                      }
	                              //$id = date_create($getgtoups['Group']['created'])->format('YmdHis');
								 }

					  }else if($checkheplgroup == "No"){
						  
						  // get the badges count
            	        $allmessage = $this->Chatbadge->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'homepage','receiver_id'=>$saveArray['user_id']
									 ),
								));
							if(!empty($allmessage)){
									$pmessage= count($allmessage);
								}else{
									$pmessage=0;
								}
								
								
                         $groupmessage = $this->Unreadgroupbadge->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'status'=>'unread','receiver_id'=>$saveArray['user_id']
									 ),
								));
								
									if(!empty($groupmessage)){
									$m= count($groupmessage);
								}else{
									$m=0;
								}


								    $last_message = $this->Groupchat->find('first',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],
										'user_id'=>$saveArray['user_id']
									 ),
									'order'=>array('Groupchat.ID DESC')
								));
								if(count($last_message) > 0){
									//$id = date_create($last_message['Groupchat']['submit_time'])->format('YmdHis');
										$id=$last_message['Groupchat']['id'];
									} else {
										$date = date('Y-m-d');
				                    	 $getgtoups = $this->Group->find('first',array(
													'conditions'=>array('id'=>$values['groups']['id'],
													 ),
													'order'=>array('Group.ID DESC')
										 ));
	                                    if($getgtoups['Group']['created'] <= $date){
	                                      	 $id=0;
	                                      }else{
	                                      	$id=50000;
	                                      }
	                                    
								 }

								
					  }
						
								
                         $data_user = $this->Newbroadcast->find('first',array('conditions'=> array('Newbroadcast.group_id' => $values['groups']['id'],'Newbroadcast.user_id' => $saveArray['user_id']),'fields' => array('message','image'),'order'=>array('Newbroadcast.id'=>"desc")
		                  ));

					     if(!empty($data_user))
							{
							    if($data_user['Newbroadcast']['image']== ""){
					                  $img="";
								 }else{
								 	$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user['Newbroadcast']['image'];
								 }

								 if($data_user['Newbroadcast']['message']== ""){

					                  $msg="";

								 }else{
								 	$msg = $data_user['Newbroadcast']['message'];
								 }

							}else{
								$msg="";
								$img="";
							}



                            $totalbade= $pmessage + $m;
                             $data['for_id'] = $id;
                            $data['total_badges'] = $totalbade;
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
							$data['broadcast_message'] = $msg;
							$data['broadcast_image'] = $img;
							$data['user_type']='U';

                          //$data['welcome_message'] = '';
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }



                         usort($data1, function($a, $b) {
						   return  $b['for_id'] - $a['for_id'];
						});	


                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
            }
			else if(!empty($user_exists) && $user_exists['User']['user_type']=='S')
			{
			
			   //$start_limit = $saveArray['page_no']*10-10;
                   // $end_limit = 10;
			  // $query1 = "SELECT * FROM `groups`
			// WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' ) order by created DESC LIMIT $start_limit,$end_limit ";
			  $query1 = "SELECT * FROM `groups` WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id ) OR default_group = '1') order by modified DESC ";
                $group = $this->Group->query($query1);
                $group = $this->Group->query($query1);
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' )";
				 $page = $this->Group->query($query);
				
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
		           //$this->set(compact('data'));
				   //pr($group);die;

                if (!empty($group))
				{
                    foreach ($group as $k => $values) 
					{ 
					
					
                      $checkheplgroup =  $values['groups']['has_helpdesk'];

					  if($checkheplgroup == "Yes"){
							 $pmessage=0;
						      $groupmessage = $this->HelpdeskBadgesForApp->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'home','receiver_id'=>$saveArray['user_id']
									 ),
								));

								if(!empty($groupmessage)){
									$m= count($groupmessage);
								}else{
									$m=0;
								}
								 
								  $last_message = $this->SupportChat->find('first',array(
								'conditions'=>array('SupportChat.group_id'=>$values['groups']['id'],
									'SupportChat.receiver_id'=>$saveArray['user_id']
								 ),
								'order'=>array('SupportChat.ID DESC')
							));

							if(count($last_message) > 0){
								//$id = date_create($last_message['SupportChat']['submit_time'])->format('YmdHis');
								$id=$last_message['SupportChat']['id'];
								} else {
										$date = date('Y-m-d');
				                    	 $getgtoups = $this->Group->find('first',array(
													'conditions'=>array('id'=>$values['groups']['id'],
													 ),
													'order'=>array('Group.ID DESC')
										 ));
	                                     if($getgtoups['Group']['created'] <= $date){
	                                      	 $id=0;
	                                      }else{
	                                      	$id=50000;
	                                      }
	                              //$id = date_create($getgtoups['Group']['created'])->format('YmdHis');
								 }

					  }else if($checkheplgroup == "No"){
						  
						  // get the badges count
            	        $allmessage = $this->Chatbadge->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'homepage','receiver_id'=>$saveArray['user_id']
									 ),
								));
							if(!empty($allmessage)){
									$pmessage= count($allmessage);
								}else{
									$pmessage=0;
								}
								
								
                         $groupmessage = $this->Unreadgroupbadge->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'status'=>'unread','receiver_id'=>$saveArray['user_id']
									 ),
								));
								
									if(!empty($groupmessage)){
									$m= count($groupmessage);
								}else{
									$m=0;
								}


								    $last_message = $this->Groupchat->find('first',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],
										'user_id'=>$saveArray['user_id']
									 ),
									'order'=>array('Groupchat.ID DESC')
								));
								if(count($last_message) > 0){
									//$id = date_create($last_message['Groupchat']['submit_time'])->format('YmdHis');
										$id=$last_message['Groupchat']['id'];
									} else {
										$date = date('Y-m-d');
				                    	 $getgtoups = $this->Group->find('first',array(
													'conditions'=>array('id'=>$values['groups']['id'],
													 ),
													'order'=>array('Group.ID DESC')
										 ));
	                                    if($getgtoups['Group']['created'] <= $date){
	                                      	 $id=0;
	                                      }else{
	                                      	$id=50000;
	                                      }
	                                    
								 }

								
					  }
						
	
                    $data_user = $this->Newbroadcast->find('first',array('conditions'=> array('Newbroadcast.group_id' => $values['groups']['id'],'Newbroadcast.user_id' => $saveArray['user_id']),'fields' => array('message','image'),'order'=>array('Newbroadcast.id'=>"desc")
		                  ));

					     if(!empty($data_user))
							{
							    if($data_user['Newbroadcast']['image']== ""){
					                  $img="";
								 }else{
								 	$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user['Newbroadcast']['image'];
								 }

								 if($data_user['Newbroadcast']['message']== ""){

					                  $msg="";

								 }else{
								 	$msg = $data_user['Newbroadcast']['message'];
								 }

							}else{
								$msg="";
								$img="";
							}

							

                            $totalbade= $pmessage + $m;
                               $data['for_id'] = $id;
                            $data['total_badges'] = $totalbade;
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
							$data['broadcast_message'] = $msg;
							$data['broadcast_image'] = $img;
							$data['user_type']='S';

                          //$data['welcome_message'] = '';
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }


                        usort($data1, function($a, $b) {
						   return  $b['for_id'] - $a['for_id'];
						});	



                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
			}else 
			
			if(!empty($user_exists) && $user_exists['User']['user_type']=='A')
			{
				
			
			   //$start_limit = $saveArray['page_no']*10-10;
                    //$end_limit = 10;
					
			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['id']."' OR default_group = '1') order by modified DESC";
			
			
                $group = $this->Group->query($query1);
				
				
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' )";
				 $page = $this->Group->query($query);
				
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
			 
           //  $this->set(compact('data'));
		   //pr($group);die;
                if (!empty($group)){
                    foreach ($group as $k => $values) {  
								  // get the badges count
					    $checkheplgroup =  $values['groups']['has_helpdesk'];

					  if($checkheplgroup == "Yes"){
							 $pmessage=0;
						      $groupmessage = $this->HelpdeskBadgesForApp->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'home','receiver_id'=>$saveArray['user_id']
									 ),
								));

								if(!empty($groupmessage)){
									$m= count($groupmessage);
								}else{
									$m=0;
								}
								 
								  $last_message = $this->SupportChat->find('first',array(
								'conditions'=>array('SupportChat.group_id'=>$values['groups']['id'],
									'SupportChat.receiver_id'=>$saveArray['user_id']
								 ),
								'order'=>array('SupportChat.ID DESC')
							));

							if(count($last_message) > 0){
								//$id = date_create($last_message['SupportChat']['submit_time'])->format('YmdHis');
								$id=$last_message['SupportChat']['id'];
								} else {
										$date = date('Y-m-d');
				                    	 $getgtoups = $this->Group->find('first',array(
													'conditions'=>array('id'=>$values['groups']['id'],
													 ),
													'order'=>array('Group.ID DESC')
										 ));
	                                     if($getgtoups['Group']['created'] <= $date){
	                                      	 $id=0;
	                                      }else{
	                                      	$id=50000;
	                                      }
	                              //$id = date_create($getgtoups['Group']['created'])->format('YmdHis');
								 }

					  }else if($checkheplgroup == "No"){
						  
						  // get the badges count
            	        $allmessage = $this->Chatbadge->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'homepage','receiver_id'=>$saveArray['user_id']
									 ),
								));
							if(!empty($allmessage)){
									$pmessage= count($allmessage);
								}else{
									$pmessage=0;
								}
								
								
                         $groupmessage = $this->Unreadgroupbadge->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'status'=>'unread','receiver_id'=>$saveArray['user_id']
									 ),
								));
								
									if(!empty($groupmessage)){
									$m= count($groupmessage);
								}else{
									$m=0;
								}


								    $last_message = $this->Groupchat->find('first',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],
										'user_id'=>$saveArray['user_id']
									 ),
									'order'=>array('Groupchat.ID DESC')
								));
								if(count($last_message) > 0){
									//$id = date_create($last_message['Groupchat']['submit_time'])->format('YmdHis');
										$id=$last_message['Groupchat']['id'];
									} else {
										$date = date('Y-m-d');
				                    	 $getgtoups = $this->Group->find('first',array(
													'conditions'=>array('id'=>$values['groups']['id'],
													 ),
													'order'=>array('Group.ID DESC')
										 ));
	                                    if($getgtoups['Group']['created'] <= $date){
	                                      	 $id=0;
	                                      }else{
	                                      	$id=50000;
	                                      }
	                                    
								 }

								
					  }
							
					  
	                    	$data_user = $this->Newbroadcast->find('first',array('conditions'=> array('Newbroadcast.group_id' => $values['groups']['id'],'Newbroadcast.user_id' => $saveArray['user_id']),'fields' => array('message','image'),'order'=>array('Newbroadcast.id'=>"desc")
			                  ));
						     if(!empty($data_user))
								{
								    if($data_user['Newbroadcast']['image']== ""){
						                  $img="";
									 }else{
									 	$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user['Newbroadcast']['image'];
									 }

									 if($data_user['Newbroadcast']['message']== ""){

						                  $msg="";

									 }else{
									 	$msg = $data_user['Newbroadcast']['message'];
									 }

								}else{
									$msg="";
									$img="";
								}

							$totalbade= $pmessage + $m;
							   $data['for_id'] = $id;
                            $data['total_badges'] = $totalbade;
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
							$data['broadcast_message'] = $msg;
							$data['broadcast_image'] = $img;
							$data['user_type']='A';
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }


	               usort($data1, function($a, $b) {
						   return  $b['for_id'] - $a['for_id'];
						});	



                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
			}
		}else {
                $result = array('status' => '0', 'message' => 'User id not found.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }

        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	/**/
	function get_helpdeskgroups()
	{
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no'])) 
		{
            $user_exists = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
		 if($user_exists)
		 {
            if (!empty($user_exists) && $user_exists['User']['user_type']=='U') {
               
			  // $start_limit = $saveArray['page_no']*10-10;
               //$end_limit = 10;
			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id ) OR default_group = '1') order by created DESC ";
                $group = $this->Group->query($query1);
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id) )";
				 $page = $this->Group->query($query);
				
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
                if (!empty($group))
				{
                    foreach ($group as $k => $values) { 
                      // get the badges count

                         $groupmessage = $this->HelpdeskBadgesForApp->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'home','receiver_id'=>$saveArray['user_id']
									 ),
								));
								

								
                         $data_user = $this->Newbroadcast->find('first',array('conditions'=> array('Newbroadcast.group_id' => $values['groups']['id'],'Newbroadcast.user_id' => $saveArray['user_id']),'fields' => array('message','image'),'order'=>array('Newbroadcast.id'=>"desc")
		                  ));

					     if(!empty($data_user))
							{
							    if($data_user['Newbroadcast']['image']== ""){
					                  $img="";
								 }else{
								 	$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user['Newbroadcast']['image'];
								 }

								 if($data_user['Newbroadcast']['message']== ""){

					                  $msg="";

								 }else{
								 	$msg = $data_user['Newbroadcast']['message'];
								 }

							}else{
								$msg="";
								$img="";
							}

                            $totalbade=  count($groupmessage);
                            $data['total_badges'] = $totalbade;
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
							$data['broadcast_message'] = $msg;
							$data['broadcast_image'] = $img;
                          //$data['welcome_message'] = '';
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }

                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
            }else if(!empty($user_exists) && $user_exists['User']['user_type']=='S'){
			
			   //$start_limit = $saveArray['page_no']*10-10;
                   // $end_limit = 10;
			  // $query1 = "SELECT * FROM `groups`
			// WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' ) order by created DESC LIMIT $start_limit,$end_limit ";
			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id ) OR default_group = '1') order by created DESC ";
                $group = $this->Group->query($query1);
                $group = $this->Group->query($query1);
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' )";
				 $page = $this->Group->query($query);
				
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
		           //$this->set(compact('data'));
				   //pr($group);die;

                if (!empty($group)){
                    foreach ($group as $k => $values) { 

					
                         $groupmessage = $this->HelpdeskBadgesForApp->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'home','receiver_id'=>$saveArray['user_id']
									 ),
								));
								
                    $data_user = $this->Newbroadcast->find('first',array('conditions'=> array('Newbroadcast.group_id' => $values['groups']['id'],'Newbroadcast.user_id' => $saveArray['user_id']),'fields' => array('message','image'),'order'=>array('Newbroadcast.id'=>"desc")
		                  ));

					     if(!empty($data_user))
							{
							    if($data_user['Newbroadcast']['image']== ""){
					                  $img="";
								 }else{
								 	$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user['Newbroadcast']['image'];
								 }

								 if($data_user['Newbroadcast']['message']== ""){

					                  $msg="";

								 }else{
								 	$msg = $data_user['Newbroadcast']['message'];
								 }

							}else{
								$msg="";
								$img="";
							}
                            $totalbade=  count($groupmessage);
                            $data['total_badges'] = $totalbade;
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
							$data['broadcast_message'] = $msg;
							$data['broadcast_image'] = $img;
                          //$data['welcome_message'] = '';
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }

                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
			}
			else 
			
			if(!empty($user_exists) && $user_exists['User']['user_type']=='A')
			{
			
			   //$start_limit = $saveArray['page_no']*10-10;
                    //$end_limit = 10;
			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['id']."' OR default_group = '1') order by created DESC LIMIT ";
                $group = $this->Group->query($query1);
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' )";
				 $page = $this->Group->query($query);
				
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
			 
           //  $this->set(compact('data'));
		   //pr($group);die;
                if (!empty($group)){
                    foreach ($group as $k => $values) { 
					
                             $groupmessage = $this->HelpdeskBadgesForApp->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'home','receiver_id'=>$saveArray['user_id']
									 ),
								));					
	                    	$data_user = $this->Newbroadcast->find('first',array('conditions'=> array('Newbroadcast.group_id' => $values['groups']['id'],'Newbroadcast.user_id' => $saveArray['user_id']),'fields' => array('message','image'),'order'=>array('Newbroadcast.id'=>"desc")
			                  ));
						     if(!empty($data_user))
								{
								    if($data_user['Newbroadcast']['image']== ""){
						                  $img="";
									 }else{
									 	$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user['Newbroadcast']['image'];
									 }

									 if($data_user['Newbroadcast']['message']== ""){

						                  $msg="";

									 }else{
									 	$msg = $data_user['Newbroadcast']['message'];
									 }

								}else{
									$msg="";
									$img="";
								}
								   $totalbade=  count($groupmessage);
                            $data['total_badges'] = $totalbade;
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
							$data['broadcast_message'] = $msg;
							$data['broadcast_image'] = $img;
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }

                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
			}
		}else {
                $result = array('status' => '0', 'message' => 'User id not found.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }

        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	

	
	/**/
    
	 #_________________________________________________________________________#

// old function before remove pagination 
/* function get_groups() 
 {
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no'])) {
            $user_exists = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
     if($user_exists){
            if (!empty($user_exists) && $user_exists['User']['user_type']=='U') {
               
			   $start_limit = $saveArray['page_no']*10-10;
               $end_limit = 10;
			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id ) OR default_group = '1') order by created DESC LIMIT $start_limit,$end_limit ";
                $group = $this->Group->query($query1);
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id) )";
				 $page = $this->Group->query($query);
				
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
                if (!empty($group)){
                    foreach ($group as $k => $values) { 
                      // get the badges count
            	        $allmessage = $this->Chatbadge->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'for_check'=>'homepage','receiver_id'=>$saveArray['user_id']
									 ),
								));
                         $groupmessage = $this->Unreadgroupbadge->find('all',array(
									'conditions'=>array('group_id'=>$values['groups']['id'],'status'=>'unread','receiver_id'=>$saveArray['user_id']
									 ),
								));
                            $totalbade= count($allmessage) + count($groupmessage);
                            $data['total_badges'] = $totalbade;
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
                          //$data['welcome_message'] = '';
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }

                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
            }else if(!empty($user_exists) && $user_exists['User']['user_type']=='S'){
			
			   $start_limit = $saveArray['page_no']*10-10;
                    $end_limit = 10;
			  // $query1 = "SELECT * FROM `groups`
			// WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' ) order by created DESC LIMIT $start_limit,$end_limit ";
			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id ) OR default_group = '1') order by created DESC LIMIT $start_limit,$end_limit ";
                $group = $this->Group->query($query1);
                $group = $this->Group->query($query1);
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' )";
				 $page = $this->Group->query($query);
				
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
			 
           //  $this->set(compact('data'));
		   //pr($group);die;
                if (!empty($group)){
                    foreach ($group as $k => $values) {   
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
                          //$data['welcome_message'] = '';
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }

                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
			}else 
			
			if(!empty($user_exists) && $user_exists['User']['user_type']=='A'){
			
			   $start_limit = $saveArray['page_no']*10-10;
                    $end_limit = 10;
			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['id']."' OR default_group = '1') order by created DESC LIMIT $start_limit,$end_limit ";
                $group = $this->Group->query($query1);
				$query = "select count('id') as totalpage from `groups` WHERE (status = '1' AND deleted = '0' AND created_id = '".$user_exists['User']['admin_id']."' )";
				 $page = $this->Group->query($query);
				
                $page_count = $page[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
			 
           //  $this->set(compact('data'));
		   //pr($group);die;
                if (!empty($group)){
                    foreach ($group as $k => $values) {   
							$data['group_id'] = $values['groups']['id'];
							$data['fb_link'] = $values['groups']['fb_link'];
							$data['group_name'] = $values['groups']['name'];
							$data['company_name'] = $values['groups']['company_name'];
							$data['address'] = $values['groups']['address'];
							$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
							$data['image'] = BASE_URL . "img/group_images/" . $values['groups']['image'];
							$data['type'] = $values['groups']['type'];
							$data['allow_chat'] = $values['groups']['allow_chat'];
							$data['allow_chat_private'] = $values['groups']['allow_chat_private'];
							$data['internet_less_chat'] = $values['groups']['internet_less_chat'];
							$data['admin_id'] = $values['groups']['created_id'];
							$data['welcome_message'] = $values['groups']['welcome_message'];
							$data['has_helpdesk'] = $values['groups']['has_helpdesk'];
							$data['country_code'] = $values['groups']['country_code'];
							$data['help_mobile'] = $values['groups']['mobile'];
						  if($values['groups']['staff_group']=='Y'){
						  $data['staff_group']='Y';
							  }else{
							    $data['staff_group']='N';
							  }
                        $data1[] = $data;
                    }

                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1,'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
			}
		}else {
                $result = array('status' => '0', 'message' => 'User id not found.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }

        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }*/
	
	#_______________________________________________________________________________#
	
		function get_groupusers1(){
		ini_set('memory_limit', '256M');
		set_time_limit(0);
        $saveArray = $this->data;
		$sav="2";
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no']) && !empty($saveArray['group_id'])){
			$cond = "friend_id='".$saveArray['user_id']."' And group_id='".$saveArray['group_id']."'";
			$block_status= $this->Block->find('all', array("conditions" =>$cond));
			if(count($block_status) == '2'){
				$result = array('status' => '0', 'message' => 'This User not exist.');
			}else{
				$start_limit = $saveArray['page_no']*10-10;
				$end_limit = 10;
				$sql="SELECT * FROM  `users` AS usr
						INNER JOIN group_members AS gm ON usr.id = gm.user_id
						WHERE usr.user_type =  'U' And usr.id !=  '".$saveArray['user_id']."'
						AND gm.group_id='".$saveArray['group_id']."' ORDER BY gm.id DESC LIMIT $start_limit,$end_limit";
				$users = $this->User->query($sql);
				$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'U'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
				$page = $this->User->query($sql1);
				$page_count = $page[0][0]['totalpage'];	
				$page_count = $page_count / 10;
				$page_count = ceil($page_count);
				if(!empty($users)){
					foreach($users as $users){
						if(!empty($users['usr']['image'])){
							if($users['usr']['register_type']=='F'){
								$image = $users['usr']['image'];
							}  
							if($users['usr']['register_type']=='N'){
								$image = BASE_URL . "img/profile_images/" . $users['usr']['image'];
							}  
						}else{
							$image= BASE_URL."images/common/user_img_placeholder.png";
						}
						$query4 = "SELECT * FROM `groupinformations`
									WHERE (status = '1' AND deleted = '0' AND group_id = '".$users['gm']['group_id']."' AND user_id = '".$users['gm']['user_id']."') order by created DESC ";
						$condition3="Group.id='".$users['gm']['group_id']."' AND deleted = '0'";
						$group_type = $this->Group->find('first',array('conditions'=> $condition3));
						$condition23="Grouptype.type_name='".$group_type['Group']['type']."' AND deleted = '0'";
						$grouptypedetail = $this->Grouptype->find('first',array('conditions'=> $condition23,'fields'=>array('id','type_name','refrence')));
						$groupinfo = $this->Group->query($query4);
						if(!empty($groupinfo)){
							$dt = explode("-",$groupinfo[0]['groupinformations']['check_date']);
							$date_format =$dt[2]."-".$dt[1]."-".$dt[0];
							$seat_no =$groupinfo[0]['groupinformations']['seat_no'];
							$date = $date_format;
						}
						if(empty($groupinfo)){
							$seat_no ="";
							$date = "";
						}
						$condition24 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."'";
						$block_status12 = $this->Block->find('all', array("conditions" =>$condition24));
						if(count($block_status12)== '2'){
							$block_status ="block";
						}else{
							$condition2 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."' And user_id='".$saveArray['user_id']."'";
							$block_status1 = $this->Block->find('first', array("conditions" =>$condition2));
							if(!empty($block_status1)){
								$block_status ="block";
							}
							if(empty($block_status1)){
								$block_status ="unblock";
							}
						}
						if($date==""){
							$date_time= explode(" ",$users['usr']['created']);
							$date12 = explode("-",$date_time[0]);
							$date14 = $date12[2]."-".$date12[1]."-".$date12[0];
							$date = $date14;
						}
						// $groups_count = $this->GroupMember->find('all',array('conditions'=>array('group_id'=>$saveArray['group_id'])));
						// foreach($groups_count as $groups_count){
							// $u[]=$groups_count['GroupMember']['user_id'];
						// }
						// $staff_count= $this->User->find('count', array('conditions' => array('User.id' => $u,'User.user_type =' =>'S')));
						// $user_count= $this->User->find('count', array('conditions' => array('User.id' => $u,'User.user_type =' =>'U')));
						
						$user_count="5547";
						$staff_count="0";
						$record[]=array(
							'user_id'=>$users['usr']['id'],
							'name'=>$users['usr']['name'],
							'address'=>$users['usr']['address'],
							'designation'=>$users['usr']['designation'],
							'roomno'=>$seat_no,
							'date'=>$date,
							'refrence'=>$grouptypedetail['Grouptype']['refrence'],
							'image'=>$image,
							'type'=>$users['usr']['user_type'],
							'block_status'=>$block_status,
							'bluetooth_mac'=>$users['usr']['bluetooth_mac'],
							'gender'=>$users['usr']['gender'],
							'age'=>$users['usr']['age'],
							);
					}
					$result = array('status' => '1', 'message' => 'Successfully.','data' => $record,'staff_count'=>(int)$staff_count, 'user_count'=>$user_count,'totalPages'=>$page_count);
				}else{
					 $result = array('status' => '0', 'user_count'=>"0",'staff_count'=>0,'message' => 'No Group Users found.');
				}
			}
        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	
	
	
	
	function get_groupstaff(){
		$saveArray = $this->data;
		if (!empty($saveArray['user_id']) && !empty($saveArray['page_no']) && !empty($saveArray['group_id'])){
			$start_limit = $saveArray['page_no']*10-10;
			$end_limit = 10;
			$sql="SELECT * FROM  `users` AS usr
						INNER JOIN group_members AS gm ON usr.id = gm.user_id
						WHERE usr.user_type = 'S' And usr.id !=  '".$saveArray['user_id']."'
						AND gm.group_id='".$saveArray['group_id']."' ORDER BY gm.id DESC LIMIT $start_limit,$end_limit";
				$users = $this->User->query($sql);
				$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'S'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
				$page = $this->User->query($sql1);
				$page_count = $page[0][0]['totalpage'];	
				$page_count = $page_count / 10;
				$page_count = ceil($page_count);
				if(!empty($users)){
					foreach($users as $users){
						if(!empty($users['usr']['image'])){
							if($users['usr']['register_type']=='F'){
								$image = $users['usr']['image'];
							}  
							if($users['usr']['register_type']=='N'){
								$image = BASE_URL . "img/profile_images/" . $users['usr']['image'];
							}  
						}else{
							$image= BASE_URL."images/common/user_img_placeholder.png";
						}
						$record[]=array(
							'user_id'=>$users['usr']['id'],
							'name'=>$users['usr']['name'],
							'image'=>$image,
							'designation'=>$users['usr']['designation'],
							'mobile'=>$users['usr']['mobile'],
						);
					}
					$result = array('status' => '1', 'message' => 'Successfully.','data' => $record,'totalPages'=>$page_count);
				}else{
					$result = array('status' => '0', 'message' => 'No Staff added to this group.');
				}
        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	
function get_groupusers()
{
		Configure::write('debug', 2);
		ini_set('memory_limit', '256M');
		set_time_limit(0);
        $saveArray = $this->data;
		$sav="2";
		
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no']) && !empty($saveArray['group_id']))
		{
			$cond = "friend_id='".$saveArray['user_id']."' And group_id='".$saveArray['group_id']."'";
			$block_status= $this->Block->find('all', array("conditions" =>$cond));
			if(count($block_status) == '2')
			{
				$result = array('status' => '0', 'message' => 'This User not exist.');
			}
			else
			{
				$start_limit = $saveArray['page_no']*10-10;
				$end_limit = 10;
				$sql="SELECT * FROM  `users` AS usr
						INNER JOIN group_members AS gm ON usr.id = gm.user_id
						WHERE usr.user_type =  'U' And usr.id !=  '".$saveArray['user_id']."'
						AND gm.group_id='".$saveArray['group_id']."' ORDER BY gm.id DESC LIMIT $start_limit,$end_limit";
				$users = $this->User->query($sql);
				
				$sql2="SELECT * FROM  `users` AS usr
						INNER JOIN group_members AS gm ON usr.id = gm.user_id
						WHERE usr.user_type =  'U' And usr.id !=  '".$saveArray['user_id']."'
						AND gm.group_id='".$saveArray['group_id']."' ORDER BY gm.id DESC";
				$users2 = $this->User->query($sql2);
				$member = count($users2);
			

				$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'U'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
				$page = $this->User->query($sql1);
				$page_count = $page[0][0]['totalpage'];	
				$page_count = $page_count / 10;
				$page_count = ceil($page_count);
				if(!empty($users)){
					foreach($users as $users){
						if(!empty($users['usr']['image'])){
							if($users['usr']['register_type']=='F'){
								$image = $users['usr']['image'];
							}  
							if($users['usr']['register_type']=='N'){
								$image = BASE_URL . "img/profile_images/" . $users['usr']['image'];
							}  
						}
						else
						{
							$image= BASE_URL."images/common/user_img_placeholder.png";
						}
						$query4 = "SELECT * FROM `groupinformations`
									WHERE (status = '1' AND deleted = '0' AND group_id = '".$users['gm']['group_id']."' AND user_id = '".$users['gm']['user_id']."') order by created DESC ";
						$condition3="Group.id='".$users['gm']['group_id']."' AND deleted = '0'";
						$group_type = $this->Group->find('first',array('conditions'=> $condition3));
						$condition23="Grouptype.type_name='".$group_type['Group']['type']."' AND deleted = '0'";
						$grouptypedetail = $this->Grouptype->find('first',array('conditions'=> $condition23,'fields'=>array('id','type_name','refrence')));
						$groupinfo = $this->Group->query($query4);
						if(!empty($groupinfo))
						{
							$dt = explode("-",$groupinfo[0]['groupinformations']['check_date']);
							$date_format =$dt[2]."-".$dt[1]."-".$dt[0];
							$seat_no =$groupinfo[0]['groupinformations']['seat_no'];
							$date = $date_format;
						}
						if(empty($groupinfo))
						{
							$seat_no ="";
							$date = "";
						}
						$condition24 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."'";
						$block_status12 = $this->Block->find('all', array("conditions" =>$condition24));
						if(count($block_status12)== '2'){
							$block_status ="block";
						}else{
							$condition2 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."' And user_id='".$saveArray['user_id']."'";
							$block_status1 = $this->Block->find('first', array("conditions" =>$condition2));
							if(!empty($block_status1)){
								$block_status ="block";
							}
							if(empty($block_status1)){
								$block_status ="unblock";
							}
						}
						if($date==""){
							$date_time= explode(" ",$users['usr']['created']);
							$date12 = explode("-",$date_time[0]);
							$date14 = $date12[2]."-".$date12[1]."-".$date12[0];
							$date = $date14;
						}
						$groups_count = $this->Group->find('first',array('conditions'=>array('id'=>$saveArray['group_id'])));
						
						
						//(usr.id = chats.sender_id AND chats.receiver_id= '".$saveArray['user_id']."') OR (usr.id = chats.receiver_id AND chats.sender_id= '".$saveArray['user_id']."') )
						$condition236="Group.id='".$saveArray['group_id']."' AND deleted = '0'";
						$grouptypedetail56 = $this->Group->find('first',array('conditions'=> $condition236,'fields'=>array('has_helpdesk')));
						//print_r($grouptypedetail56);
						$checkheplgroup =  $grouptypedetail56['Group']['has_helpdesk'];
						

		         //$checkheplgroup =  $group_type['Group']['has_helpdesk'];
				 $checkheplgroup =  $grouptypedetail56['Group']['has_helpdesk'];
					  if($checkheplgroup == "Yes")
					  {
					  	$last_message =	$this->SupportChat->find('first',[
							'conditions' => ['SupportChat.group_id' => $saveArray['group_id'], 'SupportChat.is_closed' => 'No',
											'OR' => array(
												    array('SupportChat.sender_id' => $users['usr']['id'] , 'SupportChat.receiver_id' => $saveArray['user_id'] ),
												    array('SupportChat.sender_id' => $saveArray['user_id'] , 'SupportChat.receiver_id' => $users['usr']['id'] )
											    )
								 ],
							'order'=>array('SupportChat.id DESC')
					    ]);
					
						  if(count($last_message) > 0){
							$currentDateTime = $last_message['SupportChat']['submit_time'];
							$submit_time_zone = $last_message['SupportChat']['time_zone'];
							$user_timezone = $saveArray['timezone'];
							$converted_data = $this->timezone_test($currentDateTime,$submit_time_zone, $user_timezone);
								$num = date_create($converted_data)->format('YmdHis');
								$chec= $converted_data;
								$id=$last_message['SupportChat']['id'];
							} else {
								$num = 0;
								$chec= "0";
								$id=0;
							}

						
					  }else if($checkheplgroup == "No"){
						  
						  	$last_message = $this->Chat->find('first',array(
							'conditions'=>array('group_id'=>$saveArray['group_id'],
								'OR' => array(
											array('sender_id' => $users['usr']['id'] , 'receiver_id' => $saveArray['user_id'] ),
											array('sender_id' => $saveArray['user_id'], 'receiver_id' => $users['usr']['id'] )
										)
							 ),
							'order'=>array('Chat.id DESC')
						 //'order' => array('Chat.id', 'Chat.id DESC'),

						));



							if(count($last_message) > 0)
								{
									/**/
									$timezone_data = $this->Chat->find('first',array(
							'conditions'=>array('group_id'=>$saveArray['group_id'],
								'OR' => array(
											array('sender_id' => $users['usr']['id'] , 'receiver_id' => $saveArray['user_id'] ),
											array('sender_id' => $saveArray['user_id'], 'receiver_id' => $users['usr']['id'] )
										)
							 ),
							'fields'=>array('time_zone')
						

						));
						/* print_r($timezone_data);
						echo $timezone_data['Chat']['time_zone']; */
									/**/
									
									
									
								$currentDateTime = $last_message['Chat']['submit_time'];
								//$submit_time_zone = $last_message['Chat']['time_zone'];
								$submit_time_zone = $timezone_data['Chat']['time_zone'];
								
								if(isset($saveArray['timezone']))
								{
									$user_timezone = $saveArray['timezone'];
								}
								else
								{
									$user_timezone = 'Asia/Kolkata';
								}
								
								
								$converted_data = $this->timezone_test($currentDateTime,$submit_time_zone, $user_timezone);
									$num = date_create($converted_data)->format('YmdHis');
									$chec= $converted_data;
									$id=$last_message['Chat']['id'];
								} else {
									$num = 0;
									$chec= "0";
									$id=0;
								}
							
								
					  }

						//pr($last_message); die;
						$record[]=array(
							'user_id'=>$users['usr']['id'],
							'name'=>$users['usr']['name'],
							'address'=>$users['usr']['address'],
							'designation'=>$users['usr']['designation'],
							'roomno'=>$seat_no,
							'date'=>$date,
							'refrence'=>$grouptypedetail['Grouptype']['refrence'],
							'image'=>$image,
							'type'=>$users['usr']['user_type'],
							'block_status'=>$block_status,
							'bluetooth_mac'=>$users['usr']['bluetooth_mac'],
							'gender'=>$users['usr']['gender'],
							'age'=>$users['usr']['age'],
							'last_chat' => $num,
							'check'=>$chec,
							'id'=>$id
							);
					}

					$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'S'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
						$qq = $this->User->query($sql1);
						$staff_count = $qq[0][0]['totalpage'];


						usort($record, function($a, $b) {
						   return  $b['id'] - $a['id'];
						});	

							
                      $FinalArray= array();
                      
						$groupdatae = $this->Group->find('first',array('conditions'=>array('id'=>$saveArray['group_id'])));
						/**/
						$condition2367="Group.id='".$saveArray['group_id']."' AND deleted = '0'";
						$grouptypedetail567 = $this->Group->find('first',array('conditions'=> $condition2367,'fields'=>array('has_helpdesk')));
						//print_r($grouptypedetail56);
						$checkheplgroup =  $grouptypedetail567['Group']['has_helpdesk'];
						/**/
						
						$checkheplgroup =  $grouptypedetail567['Group']['has_helpdesk'];
				     // $checkheplgroup = $groupdatae['Group']['has_helpdesk'];
                            if($checkheplgroup == "Yes")
							{
								
								   foreach($record as $v)
									   {
												$meber= $v['user_id'];
												$allmessage = $this->HelpdeskBadgesForApp->find('all',array(
													'conditions'=>array('group_id'=>$saveArray['group_id'],'for_check'=>'private','sender_id'=>$meber,'receiver_id'=>$saveArray['user_id']
													 ),
												));
												if(count($allmessage)>0){
													   $total= count($allmessage);
												}else{
													$total="0";
												}
											 $v['total_badges']=$total;
											 $FinalArray[]= $v;
									   }
								
							}else if($checkheplgroup == "No"){
								
								     foreach($record as $v)
									   {
												$meber= $v['user_id'];
												$allmessage = $this->Chatbadge->find('all',array(
													'conditions'=>array('group_id'=>$saveArray['group_id'],'for_check'=>'privatechat','sender_id'=>$meber,'receiver_id'=>$saveArray['user_id']
													 ),
												));
												if(count($allmessage)>0){
													   $total= count($allmessage);
												}else{
													$total="0";
												}
											 $v['total_badges']=$total;
											 $FinalArray[]= $v;
									   }
								
							}
	
						
					

	                 /* usort($record, function($a, $b) {
						   return  strtotime($b['check']) - strtotime($a['check']);
						});*/


						//pr($record); die;
					$result = array('status' => '1', 'message' => 'Successfully.','data' => $FinalArray,'staff_count'=>(int)$staff_count, 'user_count'=>$member,'totalPages'=>$page_count);
				}else{
					 $result = array('status' => '0', 'user_count'=>"0",'staff_count'=>0,'message' => 'No Group Users found.');
				}
			}

        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	/* By shaminder just for checking*/
	
	function get_groupusers_sham()
	{
		Configure::write('debug', 2);
		ini_set('memory_limit', '256M');
		set_time_limit(0);
        $saveArray = $this->data;
		$sav="2";
		
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no']) && !empty($saveArray['group_id']))
		{
			$cond = "friend_id='".$saveArray['user_id']."' And group_id='".$saveArray['group_id']."'";
			$block_status= $this->Block->find('all', array("conditions" =>$cond));
			if(count($block_status) == '2')
			{
				$result = array('status' => '0', 'message' => 'This User not exist.');
			}
			else
			{
				$start_limit = $saveArray['page_no']*10-10;
				$end_limit = 10;
				$sql="SELECT * FROM  `users` AS usr
						INNER JOIN group_members AS gm ON usr.id = gm.user_id
						WHERE usr.user_type =  'U' And usr.id !=  '".$saveArray['user_id']."'
						AND gm.group_id='".$saveArray['group_id']."' ORDER BY gm.id DESC LIMIT $start_limit,$end_limit";
				$users = $this->User->query($sql);

			

				$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'U'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
				$page = $this->User->query($sql1);
				$page_count = $page[0][0]['totalpage'];	
				$page_count = $page_count / 10;
				$page_count = ceil($page_count);
				if(!empty($users)){
					foreach($users as $users){
						if(!empty($users['usr']['image'])){
							if($users['usr']['register_type']=='F'){
								$image = $users['usr']['image'];
							}  
							if($users['usr']['register_type']=='N'){
								$image = BASE_URL . "img/profile_images/" . $users['usr']['image'];
							}  
						}
						else
						{
							$image= BASE_URL."images/common/user_img_placeholder.png";
						}
						$query4 = "SELECT * FROM `groupinformations`
									WHERE (status = '1' AND deleted = '0' AND group_id = '".$users['gm']['group_id']."' AND user_id = '".$users['gm']['user_id']."') order by created DESC ";
						$condition3="Group.id='".$users['gm']['group_id']."' AND deleted = '0'";
						$group_type = $this->Group->find('first',array('conditions'=> $condition3));
						$condition23="Grouptype.type_name='".$group_type['Group']['type']."' AND deleted = '0'";
						$grouptypedetail = $this->Grouptype->find('first',array('conditions'=> $condition23,'fields'=>array('id','type_name','refrence')));
						$groupinfo = $this->Group->query($query4);
						if(!empty($groupinfo))
						{
							$dt = explode("-",$groupinfo[0]['groupinformations']['check_date']);
							$date_format =$dt[2]."-".$dt[1]."-".$dt[0];
							$seat_no =$groupinfo[0]['groupinformations']['seat_no'];
							$date = $date_format;
						}
						if(empty($groupinfo))
						{
							$seat_no ="";
							$date = "";
						}
						$condition24 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."'";
						$block_status12 = $this->Block->find('all', array("conditions" =>$condition24));
						if(count($block_status12)== '2'){
							$block_status ="block";
						}else{
							$condition2 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."' And user_id='".$saveArray['user_id']."'";
							$block_status1 = $this->Block->find('first', array("conditions" =>$condition2));
							if(!empty($block_status1)){
								$block_status ="block";
							}
							if(empty($block_status1)){
								$block_status ="unblock";
							}
						}
						if($date==""){
							$date_time= explode(" ",$users['usr']['created']);
							$date12 = explode("-",$date_time[0]);
							$date14 = $date12[2]."-".$date12[1]."-".$date12[0];
							$date = $date14;
						}
						$groups_count = $this->Group->find('first',array('conditions'=>array('id'=>$saveArray['group_id'])));
						
						
						//(usr.id = chats.sender_id AND chats.receiver_id= '".$saveArray['user_id']."') OR (usr.id = chats.receiver_id AND chats.sender_id= '".$saveArray['user_id']."') )
						$condition236="Group.id='".$saveArray['group_id']."' AND deleted = '0'";
						$grouptypedetail56 = $this->Group->find('first',array('conditions'=> $condition236,'fields'=>array('has_helpdesk')));
						//print_r($grouptypedetail56);
						$checkheplgroup =  $grouptypedetail56['Group']['has_helpdesk'];
						

		         //$checkheplgroup =  $group_type['Group']['has_helpdesk'];
				 $checkheplgroup =  $grouptypedetail56['Group']['has_helpdesk'];
					  if($checkheplgroup == "Yes")
					  {
					  	$last_message =	$this->SupportChat->find('first',[
							'conditions' => ['SupportChat.group_id' => $saveArray['group_id'], 'SupportChat.is_closed' => 'No',
											'OR' => array(
												    array('SupportChat.sender_id' => $users['usr']['id'] , 'SupportChat.receiver_id' => $saveArray['user_id'] ),
												    array('SupportChat.sender_id' => $saveArray['user_id'] , 'SupportChat.receiver_id' => $users['usr']['id'] )
											    )
								 ],
							'order'=>array('SupportChat.id DESC')
					    ]);
					
						  if(count($last_message) > 0){
							$currentDateTime = $last_message['SupportChat']['submit_time'];
							$submit_time_zone = $last_message['SupportChat']['time_zone'];
							$user_timezone = $saveArray['timezone'];
							$converted_data = $this->timezone_test($currentDateTime,$submit_time_zone, $user_timezone);
								$num = date_create($converted_data)->format('YmdHis');
								$chec= $converted_data;
								$id=$last_message['SupportChat']['id'];
							} else {
								$num = 0;
								$chec= "0";
								$id=0;
							}

						
					  }else if($checkheplgroup == "No"){
						  
						  	$last_message = $this->Chat->find('first',array(
							'conditions'=>array('group_id'=>$saveArray['group_id'],
								'OR' => array(
											array('sender_id' => $users['usr']['id'] , 'receiver_id' => $saveArray['user_id'] ),
											array('sender_id' => $saveArray['user_id'], 'receiver_id' => $users['usr']['id'] )
										)
							 ),
							'order'=>array('Chat.id DESC')
						 //'order' => array('Chat.id', 'Chat.id DESC'),

						));



							if(count($last_message) > 0)
								{
									/**/
									$timezone_data = $this->Chat->find('first',array(
							'conditions'=>array('group_id'=>$saveArray['group_id'],
								'OR' => array(
											array('sender_id' => $users['usr']['id'] , 'receiver_id' => $saveArray['user_id'] ),
											array('sender_id' => $saveArray['user_id'], 'receiver_id' => $users['usr']['id'] )
										)
							 ),
							'fields'=>array('time_zone')
						

						));
						/* print_r($timezone_data);
						echo $timezone_data['Chat']['time_zone']; */
									/**/
									
									
									
								$currentDateTime = $last_message['Chat']['submit_time'];
								//$submit_time_zone = $last_message['Chat']['time_zone'];
								$submit_time_zone = $timezone_data['Chat']['time_zone'];
								
								if(isset($saveArray['timezone']))
								{
									$user_timezone = $saveArray['timezone'];
								}
								else
								{
									$user_timezone = 'Asia/Kolkata';
								}
								
								
								$converted_data = $this->timezone_test($currentDateTime,$submit_time_zone, $user_timezone);
									$num = date_create($converted_data)->format('YmdHis');
									$chec= $converted_data;
									$id=$last_message['Chat']['id'];
								} else {
									$num = 0;
									$chec= "0";
									$id=0;
								}
							
								
					  }

						//pr($last_message); die;
						$record[]=array(
							'user_id'=>$users['usr']['id'],
							'name'=>$users['usr']['name'],
							'address'=>$users['usr']['address'],
							'designation'=>$users['usr']['designation'],
							'roomno'=>$seat_no,
							'date'=>$date,
							'refrence'=>$grouptypedetail['Grouptype']['refrence'],
							'image'=>$image,
							'type'=>$users['usr']['user_type'],
							'block_status'=>$block_status,
							'bluetooth_mac'=>$users['usr']['bluetooth_mac'],
							'gender'=>$users['usr']['gender'],
							'age'=>$users['usr']['age'],
							'last_chat' => $num,
							'check'=>$chec,
							'id'=>$id
							);
					}

					$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'S'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
						$qq = $this->User->query($sql1);
						$staff_count = $qq[0][0]['totalpage'];


						usort($record, function($a, $b) {
						   return  $b['id'] - $a['id'];
						});	

							
                      $FinalArray= array();
                      
						$groupdatae = $this->Group->find('first',array('conditions'=>array('id'=>$saveArray['group_id'])));
						/**/
						$condition2367="Group.id='".$saveArray['group_id']."' AND deleted = '0'";
						$grouptypedetail567 = $this->Group->find('first',array('conditions'=> $condition2367,'fields'=>array('has_helpdesk')));
						//print_r($grouptypedetail56);
						$checkheplgroup =  $grouptypedetail567['Group']['has_helpdesk'];
						/**/
						
						$checkheplgroup =  $grouptypedetail567['Group']['has_helpdesk'];
				     // $checkheplgroup = $groupdatae['Group']['has_helpdesk'];
                            if($checkheplgroup == "Yes")
							{
								
								   foreach($record as $v)
									   {
												$meber= $v['user_id'];
												$allmessage = $this->HelpdeskBadgesForApp->find('all',array(
													'conditions'=>array('group_id'=>$saveArray['group_id'],'for_check'=>'private','sender_id'=>$meber,'receiver_id'=>$saveArray['user_id']
													 ),
												));
												if(count($allmessage)>0){
													   $total= count($allmessage);
												}else{
													$total="0";
												}
											 $v['total_badges']=$total;
											 $FinalArray[]= $v;
									   }
								
							}else if($checkheplgroup == "No"){
								
								     foreach($record as $v)
									   {
												$meber= $v['user_id'];
												$allmessage = $this->Chatbadge->find('all',array(
													'conditions'=>array('group_id'=>$saveArray['group_id'],'for_check'=>'privatechat','sender_id'=>$meber,'receiver_id'=>$saveArray['user_id']
													 ),
												));
												if(count($allmessage)>0){
													   $total= count($allmessage);
												}else{
													$total="0";
												}
											 $v['total_badges']=$total;
											 $FinalArray[]= $v;
									   }
								
							}
	
						
					
						
	                 /* usort($record, function($a, $b) {
						   return  strtotime($b['check']) - strtotime($a['check']);
						});*/


						//pr($record); die;
					$result = array('status' => '1', 'message' => 'Successfully.','data' => $FinalArray,'staff_count'=>(int)$staff_count, 'user_count'=>$groups_count['Group']['user_count'],'totalPages'=>$page_count);
				}else{
					 $result = array('status' => '0', 'user_count'=>"0",'staff_count'=>0,'message' => 'No Group Users found.');
				}
			}

        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	/**/
	
	
	
	
	/**/
	function get_helpdeskgroupusers()
{
		Configure::write('debug', 2);
		ini_set('memory_limit', '256M');
		set_time_limit(0);
        $saveArray = $this->data;
		$sav="2";
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no']) && !empty($saveArray['group_id']))
		{
			$cond = "friend_id='".$saveArray['user_id']."' And group_id='".$saveArray['group_id']."'";
			$block_status= $this->Block->find('all', array("conditions" =>$cond));
			if(count($block_status) == '2')
			{
				$result = array('status' => '0', 'message' => 'This User not exist.');
			}
			else
			{
				$start_limit = $saveArray['page_no']*10-10;
				$end_limit = 10;
				$sql="SELECT * FROM  `users` AS usr
						INNER JOIN group_members AS gm ON usr.id = gm.user_id
						WHERE usr.user_type =  'U' And usr.id !=  '".$saveArray['user_id']."'
						AND gm.group_id='".$saveArray['group_id']."' ORDER BY gm.id DESC LIMIT $start_limit,$end_limit";
				$users = $this->User->query($sql);

			

				$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'U'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
				$page = $this->User->query($sql1);
				$page_count = $page[0][0]['totalpage'];	
				$page_count = $page_count / 10;
				$page_count = ceil($page_count);
				if(!empty($users)){
					foreach($users as $users){
						if(!empty($users['usr']['image'])){
							if($users['usr']['register_type']=='F'){
								$image = $users['usr']['image'];
							}  
							if($users['usr']['register_type']=='N'){
								$image = BASE_URL . "img/profile_images/" . $users['usr']['image'];
							}  
						}else{
							$image= BASE_URL."images/common/user_img_placeholder.png";
						}
						$query4 = "SELECT * FROM `groupinformations`
									WHERE (status = '1' AND deleted = '0' AND group_id = '".$users['gm']['group_id']."' AND user_id = '".$users['gm']['user_id']."') order by created DESC ";
						$condition3="Group.id='".$users['gm']['group_id']."' AND deleted = '0'";
						$group_type = $this->Group->find('first',array('conditions'=> $condition3));
						$condition23="Grouptype.type_name='".$group_type['Group']['type']."' AND deleted = '0'";
						$grouptypedetail = $this->Grouptype->find('first',array('conditions'=> $condition23,'fields'=>array('id','type_name','refrence')));
						$groupinfo = $this->Group->query($query4);
						if(!empty($groupinfo)){
							$dt = explode("-",$groupinfo[0]['groupinformations']['check_date']);
							$date_format =$dt[2]."-".$dt[1]."-".$dt[0];
							$seat_no =$groupinfo[0]['groupinformations']['seat_no'];
							$date = $date_format;
						}
						if(empty($groupinfo)){
							$seat_no ="";
							$date = "";
						}
						$condition24 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."'";
						$block_status12 = $this->Block->find('all', array("conditions" =>$condition24));
						if(count($block_status12)== '2'){
							$block_status ="block";
						}else{
							$condition2 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."' And user_id='".$saveArray['user_id']."'";
							$block_status1 = $this->Block->find('first', array("conditions" =>$condition2));
							if(!empty($block_status1)){
								$block_status ="block";
							}
							if(empty($block_status1)){
								$block_status ="unblock";
							}
						}
						if($date==""){
							$date_time= explode(" ",$users['usr']['created']);
							$date12 = explode("-",$date_time[0]);
							$date14 = $date12[2]."-".$date12[1]."-".$date12[0];
							$date = $date14;
						}
						$groups_count = $this->Group->find('first',array('conditions'=>array('id'=>$saveArray['group_id'])));
						
						
						//(usr.id = chats.sender_id AND chats.receiver_id= '".$saveArray['user_id']."') OR (usr.id = chats.receiver_id AND chats.sender_id= '".$saveArray['user_id']."') )
						

		
				
					$last_message =	$this->SupportChat->find('first',[
							'conditions' => ['SupportChat.group_id' => $saveArray['group_id'], 'SupportChat.is_closed' => 'No',
											'OR' => array(
												    array('SupportChat.sender_id' => $users['usr']['id'] , 'SupportChat.receiver_id' => $saveArray['user_id'] ),
												    array('SupportChat.sender_id' => $saveArray['user_id'] , 'SupportChat.receiver_id' => $users['usr']['id'] )
											    )
								 ],
							'order'=>array('SupportChat.id DESC')
					]);
					
					

				
					if(count($last_message) > 0){
						$currentDateTime = $last_message['SupportChat']['submit_time'];
						$submit_time_zone = $last_message['SupportChat']['time_zone'];
						$user_timezone = $saveArray['timezone'];
						$converted_data = $this->timezone_test($currentDateTime,$submit_time_zone, $user_timezone);
							$num = date_create($converted_data)->format('YmdHis');
							$chec= $converted_data;
							$id=$last_message['SupportChat']['id'];
						} else {
							$num = 0;
							$chec= "0";
							$id=0;
						}

						//pr($last_message); die;
						$record[]=array(
							'user_id'=>$users['usr']['id'],
							'name'=>$users['usr']['name'],
							'address'=>$users['usr']['address'],
							'designation'=>$users['usr']['designation'],
							'roomno'=>$seat_no,
							'date'=>$date,
							'refrence'=>$grouptypedetail['Grouptype']['refrence'],
							'image'=>$image,
							'type'=>$users['usr']['user_type'],
							'block_status'=>$block_status,
							'bluetooth_mac'=>$users['usr']['bluetooth_mac'],
							'gender'=>$users['usr']['gender'],
							'age'=>$users['usr']['age'],
							'last_chat' => $num,
							'check'=>$chec,
							'id'=>$id
							);
					}

					$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'S'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
						$qq = $this->User->query($sql1);
						$staff_count = $qq[0][0]['totalpage'];


						usort($record, function($a, $b) {
						   return  $b['id'] - $a['id'];
						});	


                      $FinalArray= array();
                       foreach($record as $v)
                       {
                       	        $meber= $v['user_id'];
                       	        $allmessage = $this->HelpdeskBadgesForApp->find('all',array(
									'conditions'=>array('group_id'=>$saveArray['group_id'],'for_check'=>'private','sender_id'=>$meber,'receiver_id'=>$saveArray['user_id']
									 ),
								));
                                if(count($allmessage)>0){
                                       $total= count($allmessage);
                                }else{
                                	$total="0";
                                }
                             $v['total_badges']=$total;
                             $FinalArray[]= $v;
                       }

					  
	                 /* usort($record, function($a, $b) {
						   return  strtotime($b['check']) - strtotime($a['check']);
						});*/


						//pr($record); die;
					$result = array('status' => '1', 'message' => 'Successfully.','data' => $FinalArray,'staff_count'=>(int)$staff_count, 'user_count'=>$groups_count['Group']['user_count'],'totalPages'=>$page_count);
				}else{
					 $result = array('status' => '0', 'user_count'=>"0",'staff_count'=>0,'message' => 'No Group Users found.');
				}
			}

        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	
	/**/

   

    
	/**/ 
	function get_groupusers_11()
{

		Configure::write('debug', 2);
		ini_set('memory_limit', '256M');
		set_time_limit(0);
        $saveArray = $this->data;
		$sav="2";
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no']) && !empty($saveArray['group_id']))
		{
			$cond = "friend_id='".$saveArray['user_id']."' And group_id='".$saveArray['group_id']."'";
			$block_status= $this->Block->find('all', array("conditions" =>$cond));
			if(count($block_status) == '2')
			{
				$result = array('status' => '0', 'message' => 'This User not exist.');
			}
			else
			{
				$start_limit = $saveArray['page_no']*10-10;
				$end_limit = 10;
				
				$sql="SELECT * FROM  `users` AS usr
						INNER JOIN group_members AS gm ON usr.id = gm.user_id
						WHERE usr.user_type =  'U' And usr.id !=  '".$saveArray['user_id']."'
						AND gm.group_id='".$saveArray['group_id']."' ORDER BY gm.id DESC LIMIT $start_limit,$end_limit";
						
				$users = $this->User->query($sql);
				
				/* new code */
				
				
				
				/* new code */
				
				$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'U'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
				$page = $this->User->query($sql1); 
				
				//$page=count($users);
				$page_count = $page[0][0]['totalpage'];	
				$page_count = $page_count / 10;
				$page_count = ceil($page_count);
				
				if(!empty($users))
				{
					foreach($users as $users)
					{
						if(!empty($users['usr']['image'])){
							if($users['usr']['register_type']=='F')
							{
								$image = $users['usr']['image'];
							}  
							if($users['usr']['register_type']=='N')
							{
								$image = BASE_URL . "img/profile_images/" . $users['usr']['image'];
							}  
						}
						else
						{
							$image= BASE_URL."images/common/user_img_placeholder.png";
						}
						
						$query4 = "SELECT * FROM `groupinformations`
									WHERE (status = '1' AND deleted = '0' AND group_id = '".$users['gm']['group_id']."' AND user_id = '".$users['gm']['user_id']."') order by created DESC ";
									
						$condition3="Group.id='".$users['gm']['group_id']."' AND deleted = '0'";
						$group_type = $this->Group->find('first',array('conditions'=> $condition3));
						$condition23="Grouptype.type_name='".$group_type['Group']['type']."' AND deleted = '0'";
						$grouptypedetail = $this->Grouptype->find('first',array('conditions'=> $condition23,'fields'=>array('id','type_name','refrence')));
						$groupinfo = $this->Group->query($query4);
						
						
						if(!empty($groupinfo))
						{
							$dt = explode("-",$groupinfo[0]['groupinformations']['check_date']);
							$date_format =$dt[2]."-".$dt[1]."-".$dt[0];
							$seat_no =$groupinfo[0]['groupinformations']['seat_no'];
							$date = $date_format;
						}
						if(empty($groupinfo))
						{
							$seat_no ="";
							$date = "";
						}
						$condition24 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."'";
						$block_status12 = $this->Block->find('all', array("conditions" =>$condition24));
						
						if(count($block_status12)== '2')
						{
							echo "1447";
							die;
							$block_status ="block";
						}
						else
						{
							echo "1452";
							die;
							$condition2 = "friend_id='".$users['usr']['id']."' And group_id='".$saveArray['group_id']."' And user_id='".$saveArray['user_id']."'";
							$block_status1 = $this->Block->find('first', array("conditions" =>$condition2));
							if(!empty($block_status1))
							{
								$block_status ="block";
							}
							if(empty($block_status1))
							{
								$block_status ="unblock";
							}
						}
						if($date==""){
							$date_time= explode(" ",$users['usr']['created']);
							$date12 = explode("-",$date_time[0]);
							$date14 = $date12[2]."-".$date12[1]."-".$date12[0];
							$date = $date14;
						}
						$groups_count = $this->Group->find('first',array('conditions'=>array('id'=>$saveArray['group_id'])));
						//(usr.id = chats.sender_id AND chats.receiver_id= '".$saveArray['user_id']."') OR (usr.id = chats.receiver_id AND chats.sender_id= '".$saveArray['user_id']."') )
						
						$last_message = $this->Chat->find('first',array(
								'conditions'=>array('group_id'=>$saveArray['group_id'],
									'OR' => array(
											    array('sender_id' => $users['usr']['id'] , 'receiver_id' => $saveArray['user_id'] ),
											    array('receiver_id' => $users['usr']['id'] , 'sender_id' => $saveArray['user_id'] )
										    )
								 ),
								'order'=>array('created DESC')

							));
							 
							
						if(count($last_message) > 0)
						{
							$num = date_create($last_message['Chat']['created'])->format('YmdHis');
						} 
						else 
						{
							$num = 0;
						}
						//pr($last_message); die;
						
						$record[]=array(
							'user_id'=>$users['usr']['id'],
							'name'=>$users['usr']['name'],
							'address'=>$users['usr']['address'],
							'designation'=>$users['usr']['designation'],
							'roomno'=>$seat_no,
							'date'=>$date,
							'refrence'=>$grouptypedetail['Grouptype']['refrence'],
							'image'=>$image,
							'type'=>$users['usr']['user_type'],
							'block_status'=>$block_status,
							'bluetooth_mac'=>$users['usr']['bluetooth_mac'],
							'gender'=>$users['usr']['gender'],
							'age'=>$users['usr']['age'],
							'last_chat' => $num,
							);
					}
					
					/* $recetnchat=array();
					$newuser= array();
					$previuse=array();
					
					foreach($record as $v){
						
						
						 if($v['last_chat']!="0"){
						   
						   $recetnchat[]=$v;   
					   }
					   
					   if($v['last_chat']=="0"){
						   
						 $newuser[]=$v;   
					   }

				   
					} */
					
					
					//$staff_count = $this->GroupMember->find('count',array('conditions'=>array('group_id'=>$saveArray['group_id'])));
					$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.user_type = 'S'
						And usr.id !=  '".$saveArray['user_id']."' AND gm.group_id='".$saveArray['group_id']."'";
						$qq = $this->User->query($sql1);
						$staff_count = $qq[0][0]['totalpage'];	
					//pr($record);
					 /*usort($record, function($a, $b) {
						    return  $b['last_chat'] - $a['last_chat'];
						});	 */
					//pr($record); die;

							
					$result = array('status' => '1', 'message' => 'Successfully.','data' => $record,'staff_count'=>(int)$staff_count, 'user_count'=>$groups_count['Group']['user_count'],'totalPages'=>$page_count);
				}else{
					 $result = array('status' => '0', 'user_count'=>"0",'staff_count'=>0,'message' => 'No Group Users found.');
				}
			}
        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	
	/**/

	function get_groupstaff1(){
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no'] && !empty($saveArray['group_id']))){
            $user_exists = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
            $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
            if($user_exists){
				$start_limit = $saveArray['page_no']*10-10;
                $end_limit = 10;
				$query1 = "SELECT * FROM `groups`
					WHERE (status = '1' AND deleted = '0' AND id = '".$saveArray['group_id']."' ) order by created DESC ";
                $group = $this->Group->query($query1);
				if(!empty($group)){
					$cid=$group[0]['groups']['created_id'];
					$query2 = "SELECT * FROM `groups`
					WHERE (status = '1' AND deleted = '0' AND created_id = '".$cid."' AND id = '".$saveArray['group_id']."') order by created DESC ";
					$staff_group = $this->Group->query($query2);
					if(!empty($staff_group)){
						foreach($staff_group as $use){
							$u = $use['groups']['user_id'];
							$userid = explode(',',$u);
							$s=0;
							foreach($userid as $uid){
								if($uid != $saveArray['user_id']){
									$query1 = "SELECT * FROM `users`
									WHERE (status = '1' AND deleted = '0' AND id = '".$uid."' AND user_type='S' ) order by created DESC";
									$user = $this->User->query($query1);
									$query1 = "SELECT count('id') as totalpage FROM `users`
									WHERE  (status = '1' AND deleted = '0' AND id = '".$uid."' AND user_type='S' )";
									$user_exist1 = $this->User->query($query1);
									foreach($user as $groupuser){
										$data['user_id'] = $groupuser['users']['id'];
										$data['name'] = $groupuser['users']['name'];
										if(!empty($groupuser['users']['image'])){
											$data['image'] = BASE_URL . "img/profile_images/" . $groupuser['users']['image'];
										}else{
											$data['image'] = $defaulturl;
										}
										$data['designation'] = $groupuser['users']['designation'];
										$data['mobile'] = $groupuser['users']['mobile'];
										$data1[] = $data;
										$page_count = $user_exist1[0][0]['totalpage'];
										$s+=$page_count;
									}
								}
							}
						}
						for($i=$start_limit;$i<$end_limit;$i++){
							if(!empty($data1[$i])){
								$data2[]=$data1[$i];
							}else{
								continue;
							}
						}
						$page_count = $s / 10;
						$page_count = ceil($page_count);
						if (!empty($data2)){
							$result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data2,'totalPages'=>$page_count);
						}else{
							$result = array('status' => '0', 'message' => 'No Staff added to this group.');
						}
					}else{
						$result = array('status' => '0', 'message' => 'Staff Groups not found.');
					}
				}
			}else{
                $result = array('status' => '0', 'message' => 'User id not found.');
            }
        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}
	#_________________________________________________________________________#
	
	
	function get_groupstaff2(){
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['page_no'] && !empty($saveArray['group_id']))){
            $user_exists = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
            $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
            if($user_exists){
				$start_limit = $saveArray['page_no']*10-10;
                $end_limit = 10;
				$query1 = "SELECT * FROM `groups`
					WHERE (status = '1' AND deleted = '0' AND id = '".$saveArray['group_id']."' ) order by created DESC ";
                $group = $this->Group->query($query1);
				if(!empty($group)){
					$cid=$group[0]['groups']['created_id'];
					$query2 = "SELECT * FROM `groups`
					WHERE (status = '1' AND deleted = '0' AND created_id = '".$cid."' AND id = '".$saveArray['group_id']."') order by created DESC ";
					$staff_group = $this->Group->query($query2);
					if(!empty($staff_group)){
						foreach($staff_group as $use){
							$u = $use['groups']['user_id'];
							$userid = explode(',',$u);
							$s=0;
							foreach($userid as $uid){
								if($uid != $saveArray['user_id']){
									$query1 = "SELECT * FROM `users`
									WHERE (status = '1' AND deleted = '0' AND id = '".$uid."' AND user_type='S' ) order by created DESC";
									$user = $this->User->query($query1);
									$query1 = "SELECT count('id') as totalpage FROM `users`
									WHERE  (status = '1' AND deleted = '0' AND id = '".$uid."' AND user_type='S' )";
									$user_exist1 = $this->User->query($query1);
									foreach($user as $groupuser){
										$data['user_id'] = $groupuser['users']['id'];
										$data['name'] = $groupuser['users']['name'];
										if(!empty($groupuser['users']['image'])){
											$data['image'] = BASE_URL . "img/profile_images/" . $groupuser['users']['image'];
										}else{
											$data['image'] = $defaulturl;
										}
										$data['designation'] = $groupuser['users']['designation'];
										$data['mobile'] = $groupuser['users']['mobile'];
										$data1[] = $data;
										$page_count = $user_exist1[0][0]['totalpage'];
										$s+=$page_count;
									}
								}
							}
						}
						for($i=$start_limit;$i<$end_limit;$i++){
							if(!empty($data1[$i])){
								$data2[]=$data1[$i];
							}else{
								continue;
							}
						}
						$page_count = $s / 10;
						$page_count = ceil($page_count);
						if (!empty($data2)){
							$result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data2,'totalPages'=>$page_count);
						}else{
							$result = array('status' => '0', 'message' => 'No Staff added to this group.');
						}
					}else{
						$result = array('status' => '0', 'message' => 'Staff Groups not found.');
					}
				}
			}else{
                $result = array('status' => '0', 'message' => 'User id not found.');
            }
        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}
	#_________________________________________________________________________#
	 function get_groupdetails() {
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) ) {
            $user_exists = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
			if ($user_exists) {

			  $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND FIND_IN_SET('".$saveArray['user_id']."', user_id) AND id= '".$saveArray['group_id']."' ) order by created DESC ";
                $group = $this->Group->query($query1);
			if(empty($group)){
				 $query1 = "SELECT * FROM `groups`
			WHERE (status = '1' AND deleted = '0' AND id= '".$saveArray['group_id']."' ) order by created DESC ";
                $group = $this->Group->query($query1);
			}
				 $query2 = "SELECT * FROM `groupinformations`
			WHERE (status = '1' AND deleted = '0' AND user_id='".$saveArray['user_id']."' AND group_id= '".$saveArray['group_id']."') order by created DESC ";
                $groupinfo = $this->Groupinformation->query($query2);
			 
			$condition2="CheckSetting.user_id='".$saveArray['user_id']."' And CheckSetting.group_id='".$saveArray['group_id']."'";
			$user=$this->CheckSetting->find('first',array('conditions'=>$condition2));
			if($user){
				$private_chat =$user['CheckSetting']['private_chat'];
				$group_chat = $user['CheckSetting']['group_chat'];
				if($private_chat =='1'){
					$private_cht = "off";
				}
				if($private_chat =='0'){
					$private_cht = "on";
				}
				if($group_chat =='1'){
					$group_cht = "off";
				}
				if($group_chat =='0'){
					$group_cht = "on";
				}
			}else{
				$private_cht = "on";
				$group_cht = "on";
			}
                if (!empty($group)){
                    foreach ($group as $k => $values) {   
                        $data['group_id'] = $values['groups']['id'];
                         $data['group_name'] = $values['groups']['name'];
						   $data['compnay_name'] = $values['groups']['company_name'];
						    $data['description'] = $values['groups']['description'];
						  $data['address'] = $values['groups']['address'];
						   $defaulturl = BASE_URL."images/common/dummy.jpg";
						if(!empty($values['groups']['icon'])){
			
			$data['logo'] = BASE_URL . "img/group_logo/" . $values['groups']['icon'];
		}else{
			$data['logo'] = $defaulturl;
		}
						   
						    $data['qr_code'] = BASE_URL . "img/" . $values['groups']['image'];
							  $data['type'] = $values['groups']['type'];
							   $data['allow_chat'] = $values['groups']['allow_chat'];
							    $data['private_chat'] = $private_cht;
							    $data['group_chat'] = $group_cht;
							   $data['admin_id'] = $values['groups']['created_id'];
                            $data['unique_id'] = $values['groups']['qr_code'];
						//	print_r($groupinfo);die;
							if(!empty($groupinfo[0]['groupinformations']['seat_no']) && !empty($groupinfo[0]['groupinformations']['check_date']) ){
                            $data['seat_no'] = $groupinfo[0]['groupinformations']['seat_no']."/".$groupinfo[0]['groupinformations']['check_date'];
							}else{
								$data['seat_no'] = '';
							}
                        $data1 = $data;
                    }

                    if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $data1);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {
                    $result = array('status' => '0', 'message' => 'Groups not found.');
                }
            } else {
                $result = array('status' => '0', 'message' => 'User id not found.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }

        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	#______________________________________________________________________________#

	function send_message() 
	{
		
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) AND !empty($saveArray['group_id']) AND ! empty($saveArray['type'])
                AND ( ($saveArray['type'] == "T" AND !empty($saveArray['message'])) OR ($saveArray['type'] == "L" AND !empty($saveArray['lat'])  AND !empty($saveArray['lng'])) OR ( $saveArray['type'] == "I" AND !empty($_FILES['image']['name'])))) 
				{
					$id = $saveArray['user_id'];
					$users = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
					$sender = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['user_id'])));
					$sender_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];

            if (!empty($users)) 
			{
				
             //date_default_timezone_set($saveArray['timezone']);
				//$current_date = date('Y-m-d H:i:s');
                //$date = date('Y-m-d H:i:s');
				if(isset($saveArray['timezone']))
					{
						date_default_timezone_set($saveArray['timezone']);
						$submit_time_zone = $saveArray['timezone'];
					}
					else
					{
						date_default_timezone_set('Asia/Kolkata');
						$submit_time_zone = 'Asia/Kolkata';
					}
				
				    $own = date('Y-m-d H:i:s');
                    $time = strtotime($own);
                    $time = $time - (6 * 60);
                    $date = date("Y-m-d H:i:s", $time); 
					/* date_default_timezone_set($saveArray['timezone']);
					$date = date('Y-m-d H:i:s'); */

               
                    $saveArray['submit_time'] = $date;
					$saveArr['time_zone'] = $saveArray['timezone'];
                    if ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) 
					{
						
                        $saveArray['image'] = "";
						$saveArray['lat'] ="";
						$saveArray['lng'] ="";
						$saveArray['time_zone'] = $submit_time_zone;
                    }
					if ($saveArray['type'] == "L" AND !empty($saveArray['lat'])  AND !empty($saveArray['lng'])) 
					{
						
                        $saveArr['message'] = "";
                        $saveArr['message'] = $saveArray['message'];
                        $saveArr['image'] = "";
						$saveArr['submit_time'] = $date;
						$saveArr['time_zone'] = $submit_time_zone;
						$saveArr['user_id'] =$saveArray['user_id'];
						$saveArr['group_id'] =$saveArray['group_id'];
						$saveArr['type'] =$saveArray['type'];
						$saveArr['lat'] =$saveArray['lat'];
						$saveArr['lng'] =$saveArray['lng'];
						$groupmsg_img ="";
						$this->Groupchat->save($saveArr,array('validate'=>false));
                    }
                    if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) 
					{
						
					 $destination= WWW_ROOT . 'img/groupchatimg/' ;
                        if (!empty($_FILES['image']['name'])) {
                            $r = rand(1, 99999);
                            $saveArray['message'] = "";
							$saveArray['time_zone'] = $submit_time_zone;
							$saveArray['lat'] ="";
							$saveArray['lng'] ="";
                            $saveArray['image'] = $this->uploadPic($r,$destination, $_FILES['image']);
                        }
                    }
                    $groupchat_id = $this->Groupchat->save($saveArray,array('validate'=>false));
					
					

                    if ($saveArray['type'] == "T" && !empty($saveArray['message'])) 
					{
                        $groupmsg_img = '';
                    }
					else if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) 
					{
                        $getGroupChatImage = $this->Groupchat->find('first', array('conditions' => array('id' => $groupchat_id['Groupchat']['id'])));
                        if (!empty($getGroupChatImage)) 
						{
                            $groupmsg_img = BASE_URL . "img/goupchatimg/" . $getGroupChatImage['Groupchat']['image'];
                        } 
						else 
						{
                            $groupmsg_img = '';
                        }
                    }

                    // End code by Alka

                        $allmembers = $this->GroupMember->find('all',array(
									'conditions'=>array('group_id'=>$saveArray['group_id']
									 ),
								));
	            for($i=0;$i<count($allmembers);$i++)
	            {

		                 if($allmembers[$i]['GroupMember']['user_id'] != $saveArray['user_id'] )
		                 {
		                 	$table_name="Unreadgroupbadge";
		                   	$this->request->data[$table_name]['sender_id'] =$saveArray['user_id'];
			                $this->request->data[$table_name]['receiver_id'] = $allmembers[$i]['GroupMember']['user_id'];
			                $this->request->data[$table_name]['group_id'] = $saveArray['group_id'];
			                $this->$table_name->saveAll($this->request->data);
		                 }
            	}
           

					    




                $for_noti = $users['Group']['user_id'];
                 // $u=$group[0]['groups']['user_id'];
				$userid = explode(',',$for_noti);
				
				if(!empty($userid)){
				$id='';
			foreach($userid as $use){
			
				if($use != $saveArray['user_id']){
				
				 $query1 = "SELECT * FROM `users`
			WHERE (status = '1' AND deleted = '0' AND id = '".$use."' ) order by created DESC ";
                $user = $this->User->query($query1);
				foreach($user as $groupuser){
					$condition3 = "Block.friend_id='" . $sender['User']['id'] . "' And Block.user_id='" . $groupuser['users']['id'] . "' And Block.group_id='" . $saveArray['group_id'] . "' ";
					$data_exist45 = $this->Block->find('first', array('conditions' => $condition3));
					if(!empty($data_exist45)){
						$last_id =$this->Groupchat->getLastInsertId();
						$this->Groupchat->deleteAll(array('id' => $last_id), false);
					}if(empty($data_exist45)){
					$check_id = $groupuser['users']['id'];
					$check_group = $saveArray['group_id'];
					$condition2 = "CheckSetting.user_id='" . $groupuser['users']['id'] . "' And CheckSetting.group_id='" . $check_group . "' ";
					$data_exist4 = $this->CheckSetting->find('first', array('conditions' => $condition2));
					if(!empty($data_exist4)){
						if($data_exist4['CheckSetting']['group_chat']=='0'){	
				$udid = $groupuser['users']['device_id'];
				$did=$sender['User']['device_id'];
				if($udid != $did){
				 // $message = array('message' => "You have new message from " . utf8_encode($sender['User']['name']) . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
				   if($sender['User']['login_type']=='G'){
					
					  $message = array('message' => "You have a new Group Message from Guest User (".$users['Group']['name'].")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
                                         'date' => $date,  'group_id' => $saveArray['group_id'], 'sender_name' => $sender['User']['name'], 'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'], 'group_name' =>$users['Group']['name']);

					  
				 }else{
					  $message = array('message' => "You have a new Group Message from " . $sender['User']['name'] . " (".$users['Group']['name'].")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
                                         'date' => $date,  'group_id' => $saveArray['group_id'], 'sender_name' => $sender['User']['name'], 'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'], 'group_name' =>$users['Group']['name']);
                                        
				 }
				 
                  if ($groupuser['users']['device_type'] == 'A') {
                                  
								   if ($udid) {

                                        $type = "group";
                                        $android_ids = $udid;										
                                        //$this->Common->android_send_notification(array($udid),$message,$type);
										$this->Common->android_send_notification(array($android_ids), $message, 'group');
                                    }
                                } else {
                                    if (!empty($udid)) {
                                        $ios_ids = $udid;
										$c=strlen($ios_ids);
				          $main=64;
							$cond= "PushNotification.user_id='" . $groupuser['users']['id'] . "'";
							$push_noti = $this->PushNotification->find('first', array('conditions' => $cond));
							if(!empty($push_noti)){
								if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								}
							}
							if(empty($push_noti)){
								//if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								//}
							}
									
									
									
									
									
									}
				}
				}
				} 
						}
							if(empty($data_exist4)){
						
				$udid = $groupuser['users']['device_id'];
				$did=$sender['User']['device_id'];
				if($udid != $did){
				 // $message = array('message' => "You have new message from " . utf8_encode($sender['User']['name']) . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
				   if($sender['User']['login_type']=='G'){
					
					  $message = array('message' => "You have a new Group Message from Guest User (".$users['Group']['name'].")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
                                         'date' => $date,  'group_id' => $saveArray['group_id'], 'sender_name' => $sender['User']['name'], 'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'], 'group_name' =>$users['Group']['name']);
				 }else{
					  $message = array('message' => "You have a new Group Message from " . $sender['User']['name'] . " (".$users['Group']['name'].")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
                                         'date' => $date,  'group_id' => $saveArray['group_id'], 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'], 'group_name' =>$users['Group']['name']);
				 }
				 
                  if ($groupuser['users']['device_type'] == 'A') {
                                  
								   if ($udid) {

                                        $type = "group";
                                        $android_ids = $udid;										
                                        //$this->Common->android_send_notification(array($udid),$message,$type);
										$this->Common->android_send_notification(array($android_ids), $message, 'group');
                                    }
                                } else {
                                    if (!empty($udid)) {
                                        $ios_ids = $udid;
										$c=strlen($ios_ids);
				          $main=64;
							$cond= "PushNotification.user_id='" . $groupuser['users']['id'] . "'";
							$push_noti = $this->PushNotification->find('first', array('conditions' => $cond));
							if(!empty($push_noti)){
								if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								}
							}
							if(empty($push_noti)){
								//if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								//}
							}
				           // if( $c == $main && $ios_ids != ""){
										// $this->Common->iphone_send_notification($ios_ids, $message,1);
                                       
                                    // }
									
									
									
									
									
									
									}
				}
				}
			
						}
				}
				$id=$udid;
				}
                    
                   
			}}}
$result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $groupmsg_img);
              // } else {
               //     $result = array('status' => '0', 'message' => 'You can not send group message in this //trip.');
             //   }
            } else {
                $result = array('status' => '0', 'message' => 'User id  or trip id does not exist');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }

#_________________________________________________________________________#
 
	 function clearHomepageBadges()
	 {
               // home page de badges remove 
	 	      // user_id
	 	       // group_id
	 	        $saveArray = $this->data;
             $this->Unreadgroupbadge->deleteAll(array('receiver_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id']), false);

             $this->Chatbadge->deleteAll(array('receiver_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'for_check'=>'homepage'), false);
			 
			 $this->HelpdeskBadgesForApp->deleteAll(array('receiver_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'for_check'=>'home'), false); 

             $result = array('status' => '1', 'message' => 'Successfully');
			$this->set(array('result' => $result,'_serialize' => array('result')));
         
         //pr("Dsfdsf");
         //die;


	 }



	 function clearPrivate()
	 {
               // home page de badges remove 
	 	      // user_id
	 	       // group_id
	 	        $saveArray = $this->data;
             $this->Chatbadge->deleteAll(array('sender_id'=>$saveArray['sender_id'],'receiver_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'for_check'=>'privatechat'), false);
         
		    $this->HelpdeskBadgesForApp->deleteAll(array('sender_id'=>$saveArray['sender_id'],'receiver_id'=>$saveArray['user_id'],'group_id'=>$saveArray['group_id'],'for_check'=>'private'), false);
			
            $result = array('status' => '1', 'message' => 'Successfully');
			$this->set(array('result' => $result,'_serialize' => array('result')));
			 
			 //pr("Dsfdsf");
			 //die;
         

	 }


function chat() 
{
        $saveArray = $this->data;
		
        if (!empty($saveArray['sender_id']) AND !empty($saveArray['receiver_id']) AND !empty($saveArray['group_id'])AND ! empty($saveArray['type'])
                AND 
		
		( ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) OR ( $saveArray['type'] == "I" AND !empty($_FILES['image']['name']))  OR ( $saveArray['type'] == "L" AND !empty($saveArray['lat']) AND !empty($saveArray['lng'])))) 
				{

          $id = $saveArray['sender_id'];
		  $group_nm = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
            $group_name = $group_nm['Group']['name'];
        // $user_statuscheck = $this->CheckSetting->find('first', array('conditions' => array('User.id' => $saveArray['sender_id'])));  
				
           $sender = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['sender_id'])));
            $sender_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
			
				
           $friend = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['receiver_id'])));
            $friend_image = BASE_URL . "img/profile_images/" . $friend['User']['image'];

            if (!empty($sender)) {
            		//pr($saveArray['sender_id']); die;
            		 $gm = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['sender_id'])]
                    	]);
            		 $sender_data = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['receiver_id'])]
                    	]);
                   	//pr(date('YmdHis')); die;
					
             		//date_default_timezone_set('Asia/Kolkata');
					//date_default_timezone_set($saveArray['timezone']);
				//$current_date = date('Y-m-d H:i:s');
                //$date = date('Y-m-d H:i:s');
				if(isset($saveArray['timezone']))
					{
						$new_time_zone = date_default_timezone_set($saveArray['timezone']);
						$user_time_zone = $saveArray['timezone'];
					}
					else
					{
						$new_time_zone = date_default_timezone_set('Asia/Kolkata');
						$user_time_zone = "Asia/Kolkata";
					}
					
				
				 $own = date('Y-m-d H:i:s');
                    $time = strtotime($own);
                    $time = $time - (5 * 60);
                    $date = date("Y-m-d H:i:s", $time);
					
					//$date = date("Y-m-d H:i:s");
                    $saveArray['submit_time'] = $date;
					//$saveArray['time_zone'] = $saveArray['timezone'];
					
					$saveArray['time_zone'] = $user_time_zone;
					
					//$saveArr['time_zone'] = $saveArray['timezone'];
                    if ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) 
					{
                        $saveArray['image'] = "";
						//$saveArray['time_zone'] = $saveArray['timezone'];
						$saveArray['time_zone'] = $user_time_zone;
                    }
					 if ($saveArray['type'] == "L" AND ! empty($saveArray['lat']) AND ! empty($saveArray['lng'])){
						$saveArray['message'] = "";
                        $saveArr['message'] = $saveArray['message'];
                        $saveArr['image'] = "";
						$saveArr['submit_time'] = $date;
						//$saveArr['time_zone'] = $saveArray['timezone'];
						$saveArr['time_zone'] = $user_time_zone;
						$saveArr['sender_id'] =$saveArray['sender_id'];
						$saveArr['receiver_id'] =$saveArray['receiver_id'];
						$saveArr['group_id'] =$saveArray['group_id'];
						$saveArr['type'] =$saveArray['type'];
						$saveArr['lat'] =$saveArray['lat'];
						$saveArr['lng'] =$saveArray['lng'];
						$groupmsg_img ="";
						$this->Chat->save($saveArr,array('validate'=>false));
					}	
                    if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
					 $destination= WWW_ROOT . 'img/groupchatimg/' ;
                        if (!empty($_FILES['image']['name'])) {
                            $r = rand(1, 99999);
                            $saveArray['message'] = " ";
							$saveArray['time_zone'] = $user_time_zone;
                            $saveArray['image'] = $this->uploadPic($r, $destination,$_FILES['image']);
                        }
                    }

                    $groupchat_id = $this->Chat->save($saveArray,array('validate'=>false));



                             
			                $Amm['sender_id'] =$saveArray['sender_id'];
							$Amm['receiver_id'] =$saveArray['receiver_id'];
							$Amm['group_id'] =$saveArray['group_id'];
							$Amm['for_check'] ='homepage';
							$this->Chatbadge->saveAll($Amm,array('validate'=>false));


                             $Amm['sender_id'] =$saveArray['sender_id'];
							$Amm['receiver_id'] =$saveArray['receiver_id'];
							$Amm['group_id'] =$saveArray['group_id'];
							$Amm['for_check'] ='privatechat';
							$this->Chatbadge->saveAll($Amm,array('validate'=>false));


                  /*  if(count($gm) > 0){
                    	
                    	//$d = date_create();
						//$t =  date_timestamp_get($d);
                    	$t =  date('YmdHis');
                    	//$this->GroupMember->id = $gm['GroupMember']['id'];
                    	//$this->Group->save(['last_message_on' => intval($t)]);
                    	//pr($this->GroupMember->id); die;
                    	$this->GroupMember->updateAll(['last_message_on' => intval($t)], ['id' => $gm['GroupMember']['id'] ]);
                    	$this->GroupMember->updateAll(['last_message_on' => intval($t)], ['id' => $sender_data['GroupMember']['id'] ]);
                    }*/
                    
                    if ($saveArray['type'] == "T" && !empty($saveArray['message'])) {
                        $groupmsg_img = '';
                    } else if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
                        $getGroupChatImage = $this->Chat->find('first', array('conditions' => array('id' => $groupchat_id['Chat']['id'])));
                        if (!empty($getGroupChatImage)) {
                            $groupmsg_img = BASE_URL . "img/groupchatimg/" . $getGroupChatImage['Chat']['image'];
                        } else {
                            $groupmsg_img = '';
                        }
                    }
				
				if($friend['User']['id']){
				 $condition3 = "Block.friend_id='" . $saveArray['sender_id'] . "' And Block.user_id='" . $saveArray['receiver_id'] . "' And Block.group_id='" . $saveArray['group_id'] . "' ";
					$data_exist45 = $this->Block->find('first', array('conditions' => $condition3));
					if(!empty($data_exist45)){
						$last_id =$this->Chat->getLastInsertId();
						$this->Chat->deleteAll(array('id' => $last_id), false);
					}
					if(empty($data_exist45)){
				 $udid = $friend['User']['device_id'];
	
				 $did=$sender['User']['device_id'];
				  $check_id = $friend['User']['id'];
					$check_group = $saveArray['group_id'];
					$condition2 = "CheckSetting.user_id='" . $check_id . "' And CheckSetting.group_id='" . $check_group . "' ";
					$data_exist4 = $this->CheckSetting->find('first', array('conditions' => $condition2));
					if(!empty($data_exist4)){
					if($data_exist4['CheckSetting']['private_chat']=='0'){
						
				if($udid != $did){

				 
										if($sender['User']['login_type']=='G'){
				  /*$message = array('message' => "You have new message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
				 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);*/
                     $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
				 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
				   }else{
					    /*$message = array('message' => "You have new message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
				 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);*/
                         $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
				 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);               
				   }		
                  if ($friend['User']['device_type'] == 'A') {
                                    if ($udid) {

                                        $type = "single";
                                        $android_ids = $udid;										
                                        //$this->Common->android_send_notification(array($udid),$message,$type);
										$this->Common->android_send_notification(array($android_ids), $message, 'single');
                                    }
                                } else {
                                    if (!empty($udid)) {
                                       // $ios_ids = $udid;
										 $ios_ids = $udid;
										$c=strlen($ios_ids);
				          $main=64;
							
				           // if( $c == $main && $ios_ids != ""){
										// $this->Common->iphone_send_notification($ios_ids, $message,1);
                                       
                                    // }
							$cond= "PushNotification.user_id='" . $saveArray['receiver_id'] . "'";
							$push_noti = $this->PushNotification->find('first', array('conditions' => $cond));
							if(!empty($push_noti)){
								if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								}
							}
							if(empty($push_noti)){
								//if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								//}
							}		
									
									
									
									
									
									
									}
                                }        
				
                    
                   
				}
					}
					}
					if(empty($data_exist4)){
					
						
				if($udid != $did){
				 // $message = array('message' => "You have new message from " . utf8_encode($sender['User']['name']) . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
				  // $message = array('message' => "You have new message from " . $sender['User']['name'] . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
                                         // 'date' => $date, 'sender_name' => $sender['User']['name'],
                                        // 'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										// 'friend_id' => $friend['User']['id'],'friend_type' => $friend['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        // 'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
										if($sender['User']['login_type']=='G'){
				  $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
				 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
				   }else{
					    $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
				 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
				   }		
                  if ($friend['User']['device_type'] == 'A') {
                                    if ($udid) {

                                        $type = "single";
                                        $android_ids = $udid;										
                                        //$this->Common->android_send_notification(array($udid),$message,$type);
										$this->Common->android_send_notification(array($android_ids), $message, 'single');
                                    }
                                } else {
                                    if (!empty($udid)) {
                                       // $ios_ids = $udid;
										 $ios_ids = $udid;
										$c=strlen($ios_ids);
				          $main=64;
							
				           // if( $c == $main && $ios_ids != ""){
										// $this->Common->iphone_send_notification($ios_ids, $message,1);
                                      
                                    // }
							$cond= "PushNotification.user_id='" . $saveArray['receiver_id'] . "'";
							$push_noti = $this->PushNotification->find('first', array('conditions' => $cond));
							if(!empty($push_noti)){
								if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								}
							}
							if(empty($push_noti)){
								//if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								//}
							}		
									
									
									
									
									
									
									}
                                }        
				
                    
                   
				}
					
					}
					}
}




                    $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $groupmsg_img);
              // } else {
               //     $result = array('status' => '0', 'message' => 'You can not send group message in this //trip.');
             //   }
            } else {
                $result = array('status' => '0', 'message' => 'User id does not exist');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $groupmsg_img);
$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
#_____________________________________________________________________________#

 function update_deviceid()
 {
      $saveArray=$this->data;
      if(!empty($saveArray['user_id']) && !empty($saveArray['device_id']) && !empty($saveArray['device_type'])){
      $condition="User.id='".$saveArray['user_id']."' ";
      $userExist=$this->User->find('first',array('conditions'=> $condition));
		if($userExist)
		{
		  if(!empty($saveArray['bluetooth_mac'])){
			  $bluetooth_mac= $saveArray['bluetooth_mac'];
		  }
		  if(empty($saveArray['bluetooth_mac'])){
			   $bluetooth_mac= "";
		  }
      $this->User->updateAll(array('User.device_id' => "'".$saveArray['device_id']."'",'User.bluetooth_mac' => "'". $bluetooth_mac."'",'User.device_type' => "'".$saveArray['device_type']."'"),array('id'=>$userExist['User']['id']));
	    $del = "0";
		//$condition23="Service.deleted='".$del."' ";
			$condition23="Service.deleted='".$del."' And (Service.group_id='0' or Service.service_status='Yes') ";
			//$condition23= ['Service.deleted' => $del , 'Service.group_id' => '0'] ;
      $services=$this->Service->find('all',array('conditions'=> $condition23));
	  if($services){
	  foreach($services as $services){
		  $record1[]=array(
			'service_name'=>$services['Service']['name'],
			'service_link'=>$services['Service']['link'],
			'phone'=>$services['Service']['phone'],
			'service_icon'=>BASE_URL . "img/profile_images/" .$services['Service']['image'],
		  );
	  }
	  }else{
		  $record1[]=[];
	  }
      $result=array('status'=>'1','message'=>'Success','User Id'=> $userExist['User']['id'],'activate_status'=>'Activate','services'=> $record1);

      }else{
      $result=array('status'=>'0','message'=>'User not found.');
      }
      }else{
      $result=array('status'=>'0','message'=>'Enter all field.');
      }
      $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
      }
#________________________________________________________________________________#




	function get_groupmessage() 
	{

        $saveArray = $this->data;
       // Configure::write('debug', 2);
        //pr($saveArray); die;
        if (!empty($saveArray['user_id']) && !empty($saveArray['timezone']) && !empty($saveArray['group_id']) && !empty($saveArray['page_number']) ) 
		{

                $condition = "User.id='" . $saveArray['user_id'] . "'";
                $user_exist = $this->User->find('first', array('conditions' => $condition));
           // }

            if ($user_exist) 
			{
				 //$m='unread';
				 //$condition3 = "receiver_id='" . $saveArray['user_id'] . "' And status='" . $m . "' And group_id='".$saveArray['group_id']."";
               //$this->Unreadgroupbadge->delete(array('conditions' => $condition3));
               //$var = $this->Unreadgroupbadge->delete(array('receiver_id' =>$saveArray['user_id'],'group_id' =>$saveArray['group_id'],'status'=>'unread');
				 

              
               



               
              /* chatbadges

               for_Check =>'homepage'
               reciver =>saler['user_id'];
               group_id =>
				 delet///////90

				 unread
				 request
				 group*/

             
				//echo "2162";
            	$currentgroup = $this->Group->find('first',[
            			'conditions' => ['id' => $saveArray['group_id']]
            		]);
            	//pr($currentgroup); die;
            	$after = $currentgroup['Group']['history_hours'];
            	if($after == '0')
				{
					//echo "2168";
            		$date1 = new \DateTime();
            		$date1->setTimezone(new DateTimeZone($saveArray['timezone']));
            		$startdate = $date1->format('Y-m-d 00:00:00');
            	} 
				else if($after == '')
				{
					//echo "2175";
            		$date1 = new \DateTime();
            		$date1->setTimezone(new DateTimeZone($saveArray['timezone']));
            		$startdate = $date1->modify("-24 hours")->format('Y-m-d H:i:s');
            	} 
				else 
				{
					//echo "2181";
            		$date1 = new \DateTime();
					
            		$date1->setTimezone(new DateTimeZone($saveArray['timezone']));
					
            		$startdate = $date1->modify("-$after hours")->format('Y-m-d H:i:s');
            	}

            	$date = new \DateTime();
            	$date->setTimezone(new DateTimeZone($saveArray['timezone']));
				$enddate = $date->format('Y-m-d H:i:s');
				

				//pr($enddate); die;
                $user_statuscheck = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
				  $condition3 = "CheckSetting.user_id='" . $saveArray['user_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
                $check_status= $this->CheckSetting->find('first', array('conditions' => $condition3));
			
			if(!empty($check_status))
			{
               $chatstatus_offtime = $check_status['CheckSetting']['groupstatus_offtime'];
				
				if($check_status['CheckSetting']['group_chat'] == 0){

                    $start_limit = $saveArray['page_number']*10-10;
                    $end_limit = 10;
                  
                    $query = "SELECT groupchats.*,
			users.name as sendername,users.id as senderid,users.register_type as registertype,users.image as senderimage,users.user_type as user_type FROM groupchats left JOIN users ON users.id=groupchats.user_id  WHERE groupchats.group_id=" . $saveArray['group_id'] . " order by id DESC LIMIT $start_limit,$end_limit ";
                    $user_details = $this->Groupchat->query($query);
          

                    $query1 = "SELECT count('id') as totalpage FROM `groupchats`
			WHERE  group_id=" . $saveArray['group_id'] . " AND groupchats.created between '$startdate' AND '$enddate'";
                    $user_exist1 = $this->Groupchat->query($query1);
                    $page_count = $user_exist1[0][0]['totalpage'];
                    $page_count = $page_count / 10;
                    $page_count = ceil($page_count);
				}else{
					    $start_limit = $saveArray['page_number']*10-10;
                    $end_limit = 10;
                  
                    $query = "SELECT groupchats.*,
			users.name as sendername,users.id as senderid,users.register_type as registertype,users.image as senderimage,users.user_type as user_type FROM groupchats left JOIN users ON users.id=groupchats.user_id  WHERE groupchats.group_id=" . $saveArray['group_id'] . " and submit_time<='$chatstatus_offtime' order by id DESC LIMIT $start_limit,$end_limit ";
                    $user_details = $this->Groupchat->query($query);
          

                    $query1 = "SELECT count('id') as totalpage FROM `groupchats`
			WHERE  group_id=" . $saveArray['group_id'] . " AND groupchats.created between '$startdate' AND '$enddate' ";
                    $user_exist1 = $this->Groupchat->query($query1);
                    $page_count = $user_exist1[0][0]['totalpage'];
                    $page_count = $page_count / 10;
                    $page_count = ceil($page_count);
				}
			}
			if(empty($check_status))
			{

				  $start_limit = $saveArray['page_number']*10-10;
                    $end_limit = 10;
                  
                    $query = "SELECT groupchats.*,
			users.name as sendername,users.id as senderid,users.register_type as registertype,users.image as senderimage,users.user_type as user_type FROM groupchats left JOIN users ON users.id=groupchats.user_id  WHERE groupchats.group_id=" . $saveArray['group_id'] . " order by id DESC LIMIT $start_limit,$end_limit ";
                    $user_details = $this->Groupchat->query($query);
          

                    $query1 = "SELECT count('id') as totalpage FROM `groupchats`
			WHERE  group_id=" . $saveArray['group_id'] . " AND groupchats.created between '$startdate' AND '$enddate' ";
                    $user_exist1 = $this->Groupchat->query($query1);
                    $page_count = $user_exist1[0][0]['totalpage'];
                    $page_count = $page_count / 10;
                    $page_count = ceil($page_count);
			}
                if (!empty($user_details)) 
				{
					
					
                    foreach ($user_details as $k => $values) 
					{
						if(!empty($values['users']['sendername']))
						{
							$sendrname = $values['users']['sendername'];
						}
						if(empty($values['users']['sendername']))
						{
							$sendrname = "Guest user";
						}
						 $data['msg_id'] = $values['groupchats']['id'];
						 $data['created'] = $values['groupchats']['created'];
						if(!empty($values['users']['senderid']))
						{
								$data['senderid'] = $values['users']['senderid'];
						}
						else
						{
							$data['senderid'] = "";
						}
                         

						 if(!empty($values['users']['senderimage']))
						 {
							
							 if($values['users']['registertype']=="F")
							 {
								$data['senderimage'] = $values['users']['senderimage'];
							 }
							 if($values['users']['registertype']=="N")
							 {
								$data['senderimage'] = BASE_URL . "img/profile_images/" .$values['users']['senderimage'];
							 }
						 }
						 if(empty($values['users']['senderimage']))
						 {
							 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
								$data['senderimage'] = $defaulturl;
						 }
                         $data['sendername'] = $sendrname;
						 
						 if(!empty($values['users']['user_type']))
						 {
							$data['user_type'] = $values['users']['user_type'];
						 }
						 else
						 {
							$data['user_type'] =  "";
						 }
						 
						 
				 // $data['msg'] = !empty($values['groupchats']['message'])?$values['groupchats']['message']:'';
				  if(!empty($values['groupchats']['message']))
				  {
					   $data['msg'] = $values['groupchats']['message']; // we do not need welcome message in groupe
				  	//$data['msg'] = "";
				  }
				  else
				  {
					  $data['msg'] = "";
				  }
				  $data['type'] = $values['groupchats']['type'];
				  if($values['groupchats']['type']=='I')
				  {
			      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['groupchats']['image'];	
				  }
				  else
				  {
					 $data['image'] = ''; 
				  }
				  if($values['groupchats']['type']=='L')
				  {
			      $data['lat'] = $values['groupchats']['lat'];	
			      $data['lng'] = $values['groupchats']['lng'];	
				   $data['msg'] = "";
				  }
				  else
				  {
					$data['lat'] ="";	
					$data['lng'] = "";	
				  
				  }
				  
				  //$currentDateTime = $values['groupchats']['submit_time'];
                 //$currentDateTime = $values['groupchats']['submit_time']; //old code 

                    // nedw c ode start 
                   /*  $own = $values['groupchats']['submit_time'];
                    $time = strtotime($own);
                    $time = $time - (5 * 60);
                    $currentDateTime = date("Y-m-d H:i:s", $time); */
                    ////////////new code end
					$currentDateTime = $values['groupchats']['submit_time'];
					$submit_time_zone = $values['groupchats']['time_zone'];
					
					$user_timezone = $saveArray['timezone'];
					
					$converted_data = $this->timezone_test($currentDateTime,$submit_time_zone, $user_timezone);
					 $day = explode(" ",$converted_data);
					
					$data['date'] = $day[0];
                			 
					$data['time'] = $day[1]." ".$day[2];

                    /* $usertimezone = $saveArray['timezone'];
                    $data['time'] = $this->ConvertTimezoneToAnotherTimezone($currentDateTime, date_default_timezone_get(), $usertimezone); */
					//date_default_timezone_set($saveArray['timezone']);
				//$current_date = date('Y-m-d H:i:s');
                    //(date('Y-m-d H:i-5:s'))
                    //$datetime = explode(" ", $currentDateTime);

                    //$data['time'] = $datetime[1];
                     //$data['time'] = date('h:i A', strtotime($data['time']. "+12 minutes"));
                     /* $data['time'] = date('h:i A', strtotime($data['time']));
                   $data['date'] = $datetime[0]; */
				   
                 /* $day = explode(' ',$values['groupchats']['submit_time']);
				 $data['date'] = $day[0];
                			 
				 $data['time'] = $day[1]; */
                        

                        $data1[] = ($data);
                    }
					 if (!empty($data1)) 
					 {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count);
                     }
					else 
					{
						if($saveArray['page_number'] == '1')
						{
						 $creted_admin = $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
						$admin_detail = $this->User->find('first', array('conditions' => array('User.id' => $creted_admin['Group']['created_id'])));
						 $welcome_mess= $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
						 $welcome_message =$welcome_mess['Group']['welcome_message'];
						$data12['msg_id'] = "1";
						$data12['senderid'] = $admin_detail['User']['id'];
						$data12['sendername'] = $admin_detail['User']['name'];
						$data12['senderimage'] = BASE_URL . "img/profile_images/" .$admin_detail['User']['image'];
						$data12['user_type'] = "A";
						//$data12['msg'] = "Great to have you.";
						//$data12['msg'] =  $welcome_message;
						$data12['type'] = "T";
						$data12['image'] = "";	
						$data12['image'] = "";	
						$data12['lat'] ="";	
						$data12['lng'] = "";	
						date_default_timezone_set($saveArray['timezone']);
						$currentDateTime1=date('Y-m-d,h:i A');
						/* $owning=date('Y-m-d,h:i A');
                                $own = $owning;
                                $time = strtotime($own);
                                $time = $time - (5 * 60);
                                $currentDateTime1 = date("Y-m-d H:i:s", $time); */
						$timeformat=explode(",",$currentDateTime1);
						$newDate = $timeformat[0];
						$newTime =$timeformat[1];
						$dat_Date =explode(" ",$newDate);
						$dat_Time=explode(" ",$newTime);
						$date =$dat_Date[0];
						$time =$dat_Time[0];
						$data12['time'] =$newTime;
						$data12['date'] = $newDate;
						$data13[] = ($data12);
						 $page_count = 1;
						 $result = array('status' => '0', 'message' => 'Chat Messages not found.');
						//$result = array('status' => '1', 'message' => 'Successfully.', 'data' => array_reverse($data13),'totalPages'=>$page_count);
						// $result = array('status' => '1', 'message' => 'Successfully.', 'data' => [],'totalPages'=>$page_count);
						}
						else
						{
							$result = array('status' => '0', 'message' => 'Chat Messages not found.');
						}
                       // $result = array('status' => '0', 'message' => 'Chat Messages not found.');
                    }

                } 
				else 
				{
					if($saveArray['page_number'] == '1'){
                    $creted_admin = $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
						$admin_detail = $this->User->find('first', array('conditions' => array('User.id' => $creted_admin['Group']['created_id'])));
						 $welcome_mess= $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
						 $welcome_message =$welcome_mess['Group']['welcome_message'];
						$data12['msg_id'] = "1";
						$data12['senderid'] = $admin_detail['User']['id'];
						$data12['sendername'] = $admin_detail['User']['name'];
						$data12['senderimage'] = BASE_URL . "img/profile_images/" .$admin_detail['User']['image'];
						$data12['user_type'] = "A";
						//$data12['msg'] = "Great to have you.";
						//$data12['msg'] = $welcome_message;
						$data12['type'] = "T";
						$data12['image'] = "";	
						$data12['lat'] ="";	
						$data12['lng'] = "";	
						date_default_timezone_set($saveArray['timezone']);
						$currentDateTime1=date('Y-m-d,h:i A');
                           /* $owning=date('Y-m-d,h:i A');

                                $own = $owning;
                                $time = strtotime($own);
                                $time = $time - (5 * 60);
                                $currentDateTime1 = date("Y-m-d H:i:s", $time); */

						$timeformat=explode(",",$currentDateTime1);
						$newDate = $timeformat[0];
						$newTime =$timeformat[1];
						$dat_Date =explode(" ",$newDate);
						$dat_Time=explode(" ",$newTime);
						$date =$dat_Date[0];
						$time =$dat_Time[0];
						$data12['time'] =$newTime;
						$data12['date'] = $newDate;
						//$data12['created'] = $newDate;
						 $data13[] = ($data12);
						 $page_count = 1;
						 $result = array('status' => '0', 'message' => 'Chat Messages not found.');
						//$result = array('status' => '1', 'message' => 'Successfully.', 'data' => [],'totalPages'=>$page_count);
					}
					else
					{
                       $result = array('status' => '0', 'message' => 'Chat Messages not found.');
					}
                }
            } 
			else 
			{
                $result = array('status' => '0', 'message' => 'User not Found.');
            }
        } 
		else 
		{
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }

	  #_____________________________________________________________________#	
	  
	  
	  function get_groupmessage1() {
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['timezone']) && !empty($saveArray['group_id']) && !empty($saveArray['page_number']) ) {

                $condition = "User.id='" . $saveArray['user_id'] . "'";
                $user_exist = $this->User->find('first', array('conditions' => $condition));
           // }

            if ($user_exist) {

               

                $user_statuscheck = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));

               // $chatstatus_offtime = $user_statuscheck['User']['groupstatus_offtime'];

               

                    $start_limit = $saveArray['page_number']*10-10;
                    $end_limit = 10;
                   /*  if ($saveArray['trip_id'] != 1) {
                        $groupexit_time = $user_exist['Tripinformation']['groupexit_time'];
                    } else {

                        $groupexit_time = "";
                    }
 */			  $condition12 = "Groupchat.user_id='" . $saveArray['user_id'] . "'";
                $user_tme = $this->Groupchat->find('first', array('conditions' => $condition12));
				$user_time = $user_tme['Groupchat']['submit_time'];
                    $query = "SELECT groupchats.*,
			users.name as sendername,users.id as senderid FROM groupchats left JOIN users ON users.id=groupchats.user_id  WHERE groupchats.group_id=" . $saveArray['group_id'] . " order by modified DESC LIMIT $start_limit,$end_limit ";
                    $user_details = $this->Groupchat->query($query);
           // $query = "SELECT groupchats.*,
			// users.name as sendername,users.id as senderid FROM groupchats left JOIN users ON users.id=groupchats.user_id  WHERE (groupchats.group_id=" . $saveArray['group_id'] . ") And (groupchats.user_id=" . $saveArray['user_id'] . ") order by modified DESC LIMIT $start_limit,$end_limit ";
                    // $user_details = $this->Groupchat->query($query);

                    $query1 = "SELECT count('id') as totalpage FROM `groupchats`
			WHERE  group_id=" . $saveArray['group_id'] . " ";
                    $user_exist1 = $this->Groupchat->query($query1);
                    $page_count = $user_exist1[0][0]['totalpage'];
                    $page_count = $page_count / 10;
                    $page_count = ceil($page_count);
              

                if (!empty($user_details)) {
                  
                    foreach ($user_details as $k => $values) {
						 $data['msg_id'] = $values['groupchats']['id'];
                         $data['senderid'] = $values['users']['senderid'];
                         $data['sendername'] = $values['users']['sendername'];
				  $data['msg'] = !empty($values['groupchats']['message'])?$values['groupchats']['message']:'';
				  $data['type'] = $values['groupchats']['type'];
				  if($values['groupchats']['type']=='I'){
			      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['groupchats']['image'];	
				  }else{
					 $data['image'] = ''; 
				  }
				  
				  $currentDateTime = $values['groupchats']['submit_time'];
                    $usertimezone = $saveArray['timezone'];
                    $data['time'] = $this->ConvertTimezoneToAnotherTimezone($currentDateTime, date_default_timezone_get(), $usertimezone);

                    $datetime = explode(" ", $data['time']);
                    $data['time'] = $datetime[1];
                    $data['time'] = date('h:i A', strtotime($data['time']));
                   $data['date'] = $datetime[0];
				   
                 /* $day = explode(' ',$values['groupchats']['submit_time']);
				 $data['date'] = $day[0];
                			 
				 $data['time'] = $day[1]; */
                        

                        $data1[] = ($data);
                    }
					 if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'Successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count);
                    } else {
						if($saveArray['page_number'] == '1'){
						 $creted_admin = $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
						$admin_detail = $this->User->find('first', array('conditions' => array('User.id' => $creted_admin['Group']['created_id'])));
						 $welcome_mess= $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
						 $welcome_message =$welcome_mess['Group']['welcome_message'];
						$data12['msg_id'] = "1";
						$data12['senderid'] = $admin_detail['User']['id'];
						$data12['sendername'] = $admin_detail['User']['name'];
						//$data12['msg'] = "Great to have you.";
						//$data12['msg'] = $welcome_message;
						$data12['type'] = "T";
						$data12['image'] = "";	
						date_default_timezone_set('Asia/Kolkata');
						$date_time = date('Y-m-d H:i: A');
						$dat_tne =explode(" ",$date_time);
						$date =$dat_tne[0];
						$time =$dat_tne[1];
						//$datetime = explode(" ", $date_time);
					//	$data2['time'] = $datetime[1];
						$data12['time'] =$time;
						$data12['date'] = $date;
						  $data13[] = ($data12);
						  $page_count1 = 1;
						$result = array('status' => '1', 'message' => 'Successfully.', 'data' => array_reverse($data13),'totalPages'=>$page_count1);
						}else{
							$result = array('status' => '0', 'message' => 'Chat Messages not found.');
						}
                       // $result = array('status' => '0', 'message' => 'Chat Messages not found.');
                    }
                } else {
					if($saveArray['page_number'] == '1'){
                    $creted_admin = $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
						$admin_detail = $this->User->find('first', array('conditions' => array('User.id' => $creted_admin['Group']['created_id'])));
						 $welcome_mess= $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
						 $welcome_message =$welcome_mess['Group']['welcome_message'];
						$data12['msg_id'] = "1";
						$data12['senderid'] = $admin_detail['User']['id'];
						$data12['sendername'] = $admin_detail['User']['name'];
						//$data12['msg'] = "Great to have you.";
						$data12['msg'] = $welcome_message;
						$data12['type'] = "T";
						$data12['image'] = "";	
						date_default_timezone_set('Asia/Kolkata');
						$date_time = date('Y-m-d H:i: A');
						$dat_tne =explode(" ",$date_time);
						$date =$dat_tne[0];
						$time =$dat_tne[1];
						//$datetime = explode(" ", $date_time);
					//	$data2['time'] = $datetime[1];
						$data12['time'] =$time;
						$data12['date'] = $date;
						 $data13[] = ($data12);
						 $page_count1 = 1;
						$result = array('status' => '1', 'message' => 'Successfully.', 'data' => array_reverse($data13),'totalPages'=>$page_count1);
					}else{
                       $result = array('status' => '0', 'message' => 'Chat Messages not found.');
					}
                }
            } else {
                $result = array('status' => '0', 'message' => 'User not Found.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	  #_____________________________________________________________________#	
	 /*  function qrcode_scan(){
        $saveArray=$this->data;
		if(!empty($saveArray['seat_no']) && !empty($saveArray['qr_code'])){
			if(!empty($saveArray['user_id'])){
			$condition="User.id='".$saveArray['user_id']."' AND status = '1' AND deleted = '0'";
			$userdetail = $this->User->find('first',array('conditions'=> $condition,'fields'=>array('id','mobile','user_type')));
			
			if(!empty($userdetail)){
				$user = $userdetail['User'];
			$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
			$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id')));
			if($groupdetail){
				$user_id=$groupdetail['Group']['user_id'];
				$userarr=explode(',',$user_id);
				$id=$user['id'];
				if(!(in_array($id,$userarr))){
				$userid=$user_id.','.$id;
				}else{
					$userid=$user_id;
				}
				 $gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'" ),array('id'=>$groupdetail['Group']['id']));
				$condition="Groupinformation.group_id='".$groupdetail['Group']['id']."' AND Groupinformation.user_id='".$groupdetail['Group']['user_id']."' AND status = '1' AND deleted = '0'";
			$groupinfo = $this->Groupinformation->find('first',array('conditions'=> $condition,'fields'=>array('id')));
			if($groupinfo){
				 $res=$this->Groupinformation->updateAll(array('seat_no'=>"'".$saveArray['seat_no']."'"),array('id'=>$groupinfo['Groupinformation']['id']));
			}else{
				$savenew=array();
				$savenew=$saveArray['seat_no'];
				$savenew=$saveArray['user_id'];
				$savenew=$groupdetail['Group']['id'];
				 $res=$this->Groupinformation->save($savenew,array('validate'=>false));
			}
			
				$result = array('status'=>'1','message'=>'Login Successfully','id'=>$user['id'],'user_type' => $user['user_type']);
			}else{
			 $result=array('status'=>'0','message'=>'QR code does not match');
			}}else{
			 $result=array('status'=>'0','message'=>'User does not exist');
			}
		}else{
			if(!empty($saveArray['country_code']) && !empty($saveArray['mobile'])){
				$condition="User.mobile='".$saveArray['mobile']."' AND User.country_code='".$saveArray['country_code']."' AND status = '1' AND deleted = '0'";
			$userdetail = $this->User->find('first',array('conditions'=> $condition,'fields'=>array('id','mobile','user_type','is_verified')));
		//	pr($userdetail);die;
			if(empty($userdetail)){
				 $r_code = rand(0000, 9999);
			 $saveArray['verification_code'] = $r_code;
			$saveArray['login_type'] = 'G';
			//pr($saveArray);die;
            $sms = 'Your Verification code is ' . $r_code;
            $number = '+'.$saveArray['country_code'].$saveArray['mobile'];
				
					 
					 require_once("../Vendor/twilio-php/Services/Twilio.php");
					 
                        // set your AccountSid and AuthToken from www.twilio.com/user/account
                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
							
                            $result = array('status' => '0', 'message' => $e->getMessage());
                            
                        }
		
                $res=$this->User->save($saveArray,array('validate'=>false));
				
				$id1=$this->User->id;
				
			
				
				$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
			$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id')));
			$id=$res['User']['id'];
			if($groupdetail){
				$user_id=$groupdetail['Group']['user_id'];
				$userarr=explode(',',$user_id);
				
				if(!(in_array($id,$userarr))){
				$userid=$user_id.','.$id;
				}else{
					$userid=$user_id;
				}
	             $gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
				 $condition="Groupinformation.group_id='".$groupdetail['Group']['id']."' AND Groupinformation.user_id='".$id."' AND status = '1' AND deleted = '0'";
			$groupinfo = $this->Groupinformation->find('first',array('conditions'=> $condition,'fields'=>array('id')));
			if($groupinfo){
				 $res1=$this->Groupinformation->updateAll(array('seat_no'=>"'".$saveArray['seat_no']."'"),array('id'=>$groupinfo['Groupinformation']['id']));
			}else{
				$savenew=array();
				$savenew=$saveArray['seat_no'];
				$savenew=$id;
				$savenew=$groupdetail['Group']['id'];
				 $res=$this->Groupinformation->save($savenew,array('validate'=>false));
			}
			if($gupdate){			
				$result = array('status'=>'1','message'=>'Register Successfully','id'=>$id1);
		 }
			}else{
				$result=array('status'=>'0','message'=>'QR code does not match');
			}				 
			
			}else if($userdetail['User']['is_verified']=='Y'){
			$result=array('status'=>'1','message'=>'Mobile No Already verified','id'=>
			$userdetail['User']['id']);	
			}else{
				$r_code = rand(0000, 9999);
			 $saveArray['verification_code'] = $r_code;
			$saveArray['register_type'] = 'G';
            $sms = 'Your Verification code is ' . $r_code;;
            $number = '+'.$saveArray['country_code'].$saveArray['mobile'];
				
					 
					 require_once("../Vendor/twilio-php/Services/Twilio.php");
					 
                        // set your AccountSid and AuthToken from www.twilio.com/user/account
                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
							
                            $result = array('status' => '0', 'message' => $e->getMessage());
                            
                        }
						$result=array('status'=>'1','message'=>'Mobile need to be verified','id'=>$userdetail['User']['id']);	
			}
			}else{
		$result=array('status'=>'0','message'=>'Please fill user_id or country_code,mobile');
		} 
		}}else{
		$result=array('status'=>'0','message'=>'Please fill seat_no ,qrcode');
		} 
		
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	} */
	
	 function qrcode_scan(){
        $saveArray=$this->data;
		//if(!empty($saveArray['seat_no'])){
			/******************************* for facebook****************************/
			if(!empty($saveArray['qr_code'])){
		$saveArray['qr_code'] = $saveArray['qr_code'];
			}if(empty($saveArray['qr_code'])){
				$default_group = 1;
				$condition33="Group.default_group='".$default_group."'";
				$group_qr_code = $this->Group->find('first',array('conditions'=>$condition33));
				if(!empty($group_qr_code)){
					$saveArray['qr_code'] = $group_qr_code['Group']['qr_code'];
				}else{
				$saveArray['qr_code'] = "000000";
				}
			}
			$seat_n = rand(00, 99);
			if(!empty($saveArray['seat_no'])){
		$saveArray['seat_no'] = $saveArray['seat_no'];
			}if(empty($saveArray['seat_no'])){
				//$saveArray['seat_no'] = $seat_n;
				$saveArray['seat_no'] = "";
			}
				if(!empty($saveArray['country_code']) && !empty($saveArray['mobile']) && !empty($saveArray['user_id'])){
					$isValidated=$this->User->validates();
					if($isValidated){
						$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
						$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id','staff_count','user_count','welcome_message')));
						//$condition2="User.mobile='".$saveArray['mobile']."' AND User.country_code='".$saveArray['country_code']."'  AND User.id='".$saveArray['user_id']."' AND status = '1' AND deleted = '0'";
						$withoutplus = str_replace('+', '', $this->data['country_code']);
						$condition2 = "User.mobile='".$this->data['mobile']."' AND User.status = '1' AND User.deleted = '0' AND (User.country_code='".$this->data['country_code']."' OR User.country_code='".$withoutplus."')";
					
						$userdetail = $this->User->find('first',array('conditions'=> $condition2,'fields'=>array('id','mobile','user_type','is_verified','login_type','device_type','device_id')));
					if(!empty($groupdetail)){
						if(empty($userdetail)){
							$r_code = rand(0000, 9999);
							$saveArray['verification_code'] = $r_code;
							$saveArray['login_type'] = 'G';
							$sms = 'CoRover Connect Verification code is ' . $r_code;
							//$number = '+'.$saveArray['country_code'].$saveArray['mobile'];
							
							$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
							$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
							$otpdata = [
										'number' => $number,
										'password' => $r_code
									];
							$this->Common->send_otp($otpdata,'verification');
				
							$res=$this->User->save($saveArray,array('validate'=>false));
							$id1=$this->User->id;
							$id=$res['User']['id'];
							$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
							$condition1="User.id='".$id."' AND status = '1' AND deleted = '0'";
							$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id','name','staff_count','user_count','welcome_message')));
							$user = $this->User->find('first',array('conditions'=> $condition1,'fields'=>array('id','device_id','name','device_type','user_type')));
							if($groupdetail){
								$user_id=$groupdetail['Group']['user_id'];
								$userarr=explode(',',$user_id);
								// if(!(in_array($id,$userarr))){
									// $userid=$user_id.','.$id;
								// }else{
									// $userid=$user_id;
								// }
								if(!(in_array($id,$userarr))){
									if(count($userarr) > 0){
										$userid=$user_id.','.$id;
									}else{
										$userid=$id;
									}
								}else{
									$userid=$user_id;
								}
								$gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
								$condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
								$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
								$user_type=$user['User']['user_type'];
								if(!empty($groupmem)){
									$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
								}else{
									$GrpMmbr['user_id']=$id;
									$GrpMmbr['group_id']=$groupdetail['Group']['id'];
									$this->GroupMember->save($GrpMmbr,array('validate'=>false));
									$user_count = $groupdetail['Group']['user_count']+1;
									$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
								}
								$condition="Groupinformation.group_id='".$groupdetail['Group']['id']."' AND Groupinformation.user_id='".$id."' AND status = '1' AND deleted = '0'";
								$groupinfo = $this->Groupinformation->find('first',array('conditions'=> $condition,'fields'=>array('id')));
								date_default_timezone_set('Asia/Kolkata');
								$date_time = date('Y-m-d H:i: A');
								$dat_tne =explode(" ",$date_time);
								$date =$dat_tne[0];
								$check_date =$date;
								if($groupinfo){
									$res1=$this->Groupinformation->updateAll(array('seat_no'=>"'".$saveArray['seat_no']."'",'check_date'=>"'".$check_date."'"),array('id'=>$groupinfo['Groupinformation']['id']));
								}else{
									$savenew=array();
									$savenew['seat_no']=$saveArray['seat_no'];
									$savenew['check_date']=$check_date;
									$savenew['user_id']=$id;
									$savenew['group_id']=$groupdetail['Group']['id'];
									$res=$this->Groupinformation->save($savenew,array('validate'=>false));
								}
								if($gupdate){	
									$data = array('message' => $groupdetail['Group']['welcome_message'], 'noti_for' => 'new_group');
									if ($user['User']['device_type'] == 'I') {
										$deviceIds = $user['User']['device_id'];
										$c = strlen($deviceIds);
										$main = 64;
										
										
												if ($c == $main && $deviceIds != ""){
											$a = $this->Common->iphone_send_notification($deviceIds, $data,1);
										}
											
										
									}elseif ($user['User']['device_type'] == 'A'){
										$registatoin_id = $user['User']['device_id'];
											if ($registatoin_id != "" || $registatoin_id != "(null)"){
												$this->Common->android_send_notification(array($registatoin_id), $data);
											}
									}
									$result = array('status'=>'1','message'=>'Register Successfully','id'=>$id1);
								}
							}else{
								$result=array('status'=>'0','message'=>'QR code does not match');
							}				 
						}else if($userdetail['User']['is_verified']=='Y'){
							$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
							$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id','name','staff_count','user_count','welcome_message')));
							$id=$userdetail['User']['id'];
							if($groupdetail){
								$user_id=$groupdetail['Group']['user_id'];
								$userarr=explode(',',$user_id);
								// if(!(in_array($id,$userarr))){
									// $userid=$user_id.','.$id;
								// }else{
									// $userid=$user_id;
								// }
								if(!(in_array($id,$userarr))){
									if(count($userarr) > 0){
										$userid=$user_id.','.$id;
									}else{
										$userid=$id;
									}
								}else{
									$userid=$user_id;
								}
								$gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
								$condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
								$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
								$user_type=$userdetail['User']['user_type'];
								if(!empty($groupmem)){
									$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
								}else{
									$GrpMmbr['user_id']=$id;
									$GrpMmbr['group_id']=$groupdetail['Group']['id'];
									$this->GroupMember->save($GrpMmbr,array('validate'=>false));
									$user_count = $groupdetail['Group']['user_count']+1;
									$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
								}
								$condition="Groupinformation.group_id='".$groupdetail['Group']['id']."' AND Groupinformation.user_id='".$id."' AND status = '1' AND deleted = '0'";
								$groupinfo = $this->Groupinformation->find('first',array('conditions'=> $condition,'fields'=>array('id')));
								date_default_timezone_set('Asia/Kolkata');
								$date_time = date('Y-m-d H:i: A');
								$dat_tne =explode(" ",$date_time);
								$date =$dat_tne[0];
								$check_date =$date;
								if($groupinfo){
									$res1=$this->Groupinformation->updateAll(array('seat_no'=>"'".$saveArray['seat_no']."'",'check_date'=>"'".$check_date."'"),array('id'=>$groupinfo['Groupinformation']['id']));
								}else{
									$savenew=array();
									$savenew['seat_no']=$saveArray['seat_no'];
									$savenew['check_date']=$check_date;
									$savenew['user_id']=$id;
									$savenew['group_id']=$groupdetail['Group']['id'];
									$res=$this->Groupinformation->save($savenew,array('validate'=>false));
								}
								if($gupdate){	
									$data = array('message' => $groupdetail['Group']['welcome_message'], 'noti_for' => 'new_group');
									if($userdetail['User']['device_type'] == 'I'){
										$deviceIds = $userdetail['User']['device_id'];
										$c = strlen($deviceIds);
										$main = 64;
										
										
												if ($c == $main && $deviceIds != ""){
											$a = $this->Common->iphone_send_notification($deviceIds, $data,1);
										}
											
										
									}elseif($userdetail['User']['device_type'] == 'A'){
										$registatoin_id = $userdetail['User']['device_id'];
										if ($registatoin_id != "" || $registatoin_id != "(null)") {
											 $this->Common->android_send_notification($registatoin_id, $data);
										}
									}
									$condition37="User.id='".$id."' AND status = '1' AND deleted = '0'";
			$usertype = $this->User->find('first',array('conditions'=> $condition37,'fields'=>array('user_type','login_type')));
									$result = array('status'=>'1','message'=>'Mobile No Already verified','id'=>$id,'user_type'=>$usertype['User']['user_type'],'user_type'=>$usertype['User']['login_type']);
								}
							}else{
								$result=array('status'=>'0','message'=>'QR code does not match');
							}
						
						}else{
							$r_code = rand(0000, 9999);
							$saveArray['verification_code'] = $r_code;
							$saveArray['register_type'] = 'G';
							$sms = 'CoRover Connect Verification code is ' . $r_code;;
						
							$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
							$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
							$otpdata = [
										'number' => $number,
										'password' => $r_code
									];
							$this->Common->send_otp($otpdata,'verification');

							if($userdetail['User']['login_type']==""){
								$userdetail['User']['login_type']="";
							}
							$result=array('status'=>'1','message'=>'Mobile need to be verified','id'=>$userdetail['User']['id'],'user_type'=>$userdetail['User']['user_type'],'login_type'=>$userdetail['User']['login_type']);	
						}
					}else{
						// $result=array('status'=>'0','message'=>'Qrcode does not exist');
						if(!empty($userdetail['User']['login_type'])){
								$userdetail['User']['login_type']=$userdetail['User']['login_type'];
							}
						if($userdetail['User']['login_type']==""){
								$userdetail['User']['login_type']="";
							}
							$condition12="User.mobile='".$saveArray['mobile']."' AND User.login_type= 'G' ";
		$exist_GUEST=$this->User->find('first',array('conditions'=>$condition12));

		if($exist_GUEST){
			$r_code = rand(0000, 9999);
							$saveArray['verification_code'] = $r_code;
							$saveArray['login_type'] = 'G';
							$sms = 'CoRover Connect Verification code is ' . $r_code;
							
							$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
							$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
							$otpdata = [
										'number' => $number,
										'password' => $r_code
									];
							$this->Common->send_otp($otpdata,'verification');



			$this->User->updateAll(array('verification_code'=>"'".$r_code."'"),array('id'=>$exist_GUEST['User']['id']));
		
						$result=array('status'=>'1','message'=>'Mobile need to be verified','id'=>$userdetail['User']['id'],'user_type'=>$userdetail['User']['user_type'],'login_type'=>$userdetail['User']['login_type']);	
		}else{
			//pr($saveArray); die;
							$r_code = rand(0000, 9999);
							$saveArray['verification_code'] = $r_code;
							$saveArray['login_type'] = 'G';
							$sms = 'CoRover Connect Verification code is ' . $r_code;
							
							$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
							$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
							$otpdata = [
										'number' => $number,
										'password' => $r_code
									];
							$this->Common->send_otp($otpdata,'verification');

							$res=$this->User->save($saveArray,array('validate'=>false));
						$result=array('status'=>'1','message'=>'Mobile need to be verified','id'=>$userdetail['User']['id'],'user_type'=>$userdetail['User']['user_type'],'login_type'=>$userdetail['User']['login_type']);	
						
		}
					} 
				}else{
					$erros=$this->errorValidation('User');
					$result=array('status'=>'0','message'=>$erros);
				}
				}
				
			if(!empty($saveArray['user_id']) && empty($saveArray['mobile'])){
				
			$condition="User.id='".$saveArray['user_id']."' AND status = '1' AND deleted = '0'";
			$userdetail = $this->User->find('first',array('conditions'=> $condition,'fields'=>array('id','mobile','user_type','device_id','device_type','user_type','device_type','device_id')));
			

			if(!empty($userdetail)){
				
				$user = $userdetail['User'];
			$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
			$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id','name','welcome_message','staff_count','user_count')));
			

			if($groupdetail){
				
				$user_id=$groupdetail['Group']['user_id'];
				$userarr=explode(',',$user_id);
				$id=$userdetail['User']['id'];
				if(!(in_array($id,$userarr))){
									if(count($userarr) > 0){
										$userid=$user_id.','.$id;
									}else{
										$userid=$id;
									}
								}else{
									$userid=$user_id;
								}
				 $gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'" ),array('id'=>$groupdetail['Group']['id']));
				 $condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
								$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
								$user_type=$userdetail['User']['user_type'];
								if(!empty($groupmem)){
									$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
								}else{
									$GrpMmbr['user_id']=$id;
									$GrpMmbr['group_id']=$groupdetail['Group']['id'];
									$this->GroupMember->save($GrpMmbr,array('validate'=>false));
									$user_count = $groupdetail['Group']['user_count']+1;
									$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
								}
				$condition="Groupinformation.group_id='".$groupdetail['Group']['id']."' AND Groupinformation.user_id='".$groupdetail['Group']['user_id']."' AND status = '1' AND deleted = '0'";
			$groupinfo = $this->Groupinformation->find('first',array('conditions'=> $condition,'fields'=>array('id')));
			date_default_timezone_set('Asia/Kolkata');
								$date_time = date('Y-m-d H:i: A');
								$dat_tne =explode(" ",$date_time);
								$date =$dat_tne[0];
								$check_date =$date;
			if($groupinfo){
				 $res=$this->Groupinformation->updateAll(array('seat_no'=>"'".$saveArray['seat_no']."'",'check_date'=>"'".$check_date."'"),array('id'=>$groupinfo['Groupinformation']['id']));
		
						 
                        
			}else{
				$savenew=array();
				
				$savenew['seat_no']=$saveArray['seat_no'];
				$savenew['check_date']=$check_date;
				$savenew['user_id']=$saveArray['user_id'];
				$savenew['group_id']=$groupdetail['Group']['id'];
				 $res=$this->Groupinformation->save($savenew,array('validate'=>false));
	
						
			}

			$data = array('message' => $groupdetail['Group']['welcome_message'], 'noti_for' => 'new_group');
						
					
					   if ($userdetail['User']['device_type'] == 'I') {
                                $deviceIds = $userdetail['User']['device_id'];
                                $c = strlen($deviceIds);
                                $main = 64;
											if ($c == $main && $deviceIds != ""){
											$a = $this->Common->iphone_send_notification($deviceIds, $data,1);
										}
											
                            } elseif ($userdetail['User']['device_type'] == 'A') {

                                $registatoin_id = $userdetail['User']['device_id'];
                                if ($registatoin_id != "" || $registatoin_id != "(null)") {
                                 $this->Common->android_send_notification( array($registatoin_id), $data);

                                }
                         }
  

				
				$condition23="User.id='".$saveArray['user_id']."' AND status = '1' AND deleted = '0'";
						$userdetail1 = $this->User->find('first',array('conditions'=> $condition23,'fields'=>array('id','mobile','user_type','is_verified','login_type')));


                     
				

				 $result = array('status'=>'1','message'=>'Login Successfully','id'=>$userdetail1['User']['id'],'user_type' => $userdetail1['User']['user_type'],'login_type' => $userdetail1['User']['login_type']);
                 
		               $this->set(array(
		               'result' => $result,
		               '_serialize' => array('result')
		           ));

			}else{
			 $result=array('status'=>'0','message'=>'QR code does not match');
			}}else{
			 $result=array('status'=>'0','message'=>'User does not exist');
			}
		}else{
			if(!empty($saveArray['country_code']) && !empty($saveArray['mobile'])){
			$isValidated=$this->User->validates();
		if($isValidated){
				$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
			$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id','staff_count','user_count','welcome_message')));
		
			$withoutplus = str_replace('+', '', $this->data['country_code']);
			$condition2 = "User.mobile='".$this->data['mobile']."' AND User.status = '1' AND User.deleted = '0' AND (User.country_code='".$this->data['country_code']."' OR User.country_code='".$withoutplus."')";
			$userdetail = $this->User->find('first',array('conditions'=> $condition2,'fields'=>array('id','mobile','user_type','is_verified','login_type','user_type','device_type','device_id')));
		//	pr($userdetail);die;
		if(!empty($groupdetail)){
			if(empty($userdetail)){
				 $r_code = rand(0000, 9999);
			 $saveArray['verification_code'] = $r_code;
			$saveArray['login_type'] = 'G';
			//pr($saveArray);die;
            $sms = 'CoRover Connect Verification code is ' . $r_code;

            $pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
			$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
			$otpdata = [
						'number' => $number,
						'password' => $r_code
					];
			$this->Common->send_otp($otpdata,'verification');
          
		
                $res=$this->User->save($saveArray,array('validate'=>false));
				
				$id1=$this->User->id;
				
			
				$id=$res['User']['id'];
				$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
				$condition1="User.id='".$id."' AND status = '1' AND deleted = '0'";
			$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id','name','staff_count','user_count','welcome_message')));
			$user = $this->User->find('first',array('conditions'=> $condition1,'fields'=>array('id','device_id','name','device_type','login_type','user_type')));
			
			if($groupdetail){
				$user_id=$groupdetail['Group']['user_id'];
				$userarr=explode(',',$user_id);
				
				// if(!(in_array($id,$userarr))){
				// $userid=$user_id.','.$id;
				// }else{
					// $userid=$user_id;
				// }
				if(!(in_array($id,$userarr))){
									if(count($userarr) > 0){
										$userid=$user_id.','.$id;
									}else{
										$userid=$id;
									}
								}else{
									$userid=$user_id;
								}
	             $gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
				 $condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
								$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
								if(!empty($groupmem)){
									$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
									$user_type=$userdetail['User']['user_type'];
								}else{
									$GrpMmbr['user_id']=$id;
									$GrpMmbr['group_id']=$groupdetail['Group']['id'];
									$this->GroupMember->save($GrpMmbr,array('validate'=>false));
									$user_count = $groupdetail['Group']['user_count']+1;
									$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
								}
				 $condition="Groupinformation.group_id='".$groupdetail['Group']['id']."' AND Groupinformation.user_id='".$id."' AND status = '1' AND deleted = '0'";
			$groupinfo = $this->Groupinformation->find('first',array('conditions'=> $condition,'fields'=>array('id')));
			date_default_timezone_set('Asia/Kolkata');
								$date_time = date('Y-m-d H:i: A');
								$dat_tne =explode(" ",$date_time);
								$date =$dat_tne[0];
								$check_date =$date;
			if($groupinfo){
				 $res1=$this->Groupinformation->updateAll(array('seat_no'=>"'".$saveArray['seat_no']."'",'check_date'=>"'".$check_date."'"),array('id'=>$groupinfo['Groupinformation']['id']));
			}else{
				$savenew=array();
				$savenew['seat_no']=$saveArray['seat_no'];
				$savenew['check_date']=$check_date;
				$savenew['user_id']=$id;
				$savenew['group_id']=$groupdetail['Group']['id'];
				 $res=$this->Groupinformation->save($savenew,array('validate'=>false));
			}
			if($gupdate){	
				
						$data = array('message' => $groupdetail['Group']['welcome_message'], 'noti_for' => 'new_group');
						
						       if ($user['User']['device_type'] == 'I') {
                                $deviceIds = $user['User']['device_id'];
                                $c = strlen($deviceIds);
                                $main = 64;
								
										
												if ($c == $main && $deviceIds != ""){
											$a = $this->Common->iphone_send_notification($deviceIds, $data,1);
										}
											
                              
                            } elseif ($user['User']['device_type'] == 'A') {

                                $registatoin_id = $user['User']['device_id'];
                                if ($registatoin_id != "" || $registatoin_id != "(null)") {
                                     $this->Common->android_send_notification(array($registatoin_id), $data);
                                }
                            }
                			$result=array('status'=>'1','message'=>'Mobile need to be verified','id'=>$id1,'user_type'=>$user['User']['user_type'],'login_type'=>$user['User']['login_type']);	
				
		 }
			}else{
				$result=array('status'=>'0','message'=>'QR code does not match');
			}				 
			
			}else if($userdetail['User']['is_verified']=='Y'){
				
				$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
			$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id','name','staff_count','user_count','welcome_message')));
			$id=$userdetail['User']['id'];
			if($groupdetail){
				$user_id=$groupdetail['Group']['user_id'];
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
	             $gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
				 $condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
								$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
								$user_type=$userdetail['User']['user_type'];
								if(!empty($groupmem)){
									$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
								}else{
									$GrpMmbr['user_id']=$id;
									$GrpMmbr['group_id']=$groupdetail['Group']['id'];
									$this->GroupMember->save($GrpMmbr,array('validate'=>false));
									$user_count = $groupdetail['Group']['user_count']+1;
									$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
								}
				 $condition="Groupinformation.group_id='".$groupdetail['Group']['id']."' AND Groupinformation.user_id='".$id."' AND status = '1' AND deleted = '0'";
			$groupinfo = $this->Groupinformation->find('first',array('conditions'=> $condition,'fields'=>array('id')));
			date_default_timezone_set('Asia/Kolkata');
								$date_time = date('Y-m-d H:i: A');
								$dat_tne =explode(" ",$date_time);
								$date =$dat_tne[0];
								$check_date =$date;
			if($groupinfo){
				 $res1=$this->Groupinformation->updateAll(array('seat_no'=>"'".$saveArray['seat_no']."'",'check_date'=>"'".$check_date."'"),array('id'=>$groupinfo['Groupinformation']['id']));
			}else{
				$savenew=array();
				$savenew['seat_no']=$saveArray['seat_no'];
				$savenew['check_date']=$check_date;
				$savenew['user_id']=$id;
				$savenew['group_id']=$groupdetail['Group']['id'];
				 $res=$this->Groupinformation->save($savenew,array('validate'=>false));
			}
			if($gupdate){	
     
						$data = array('message' => $groupdetail['Group']['welcome_message'], 'noti_for' => 'new_group');
						//$sms = 'Welcome to group ' . $groupdetail['Group']['name'];
						       if ($userdetail['User']['device_type'] == 'I') {
                                $deviceIds = $userdetail['User']['device_id'];
                                $c = strlen($deviceIds);
                                $main = 64;
								
												if ($c == $main && $deviceIds != ""){
											$a = $this->Common->iphone_send_notification($deviceIds, $data,1);
										}
											
									
                            } elseif ($userdetail['User']['device_type'] == 'A') {

                                $registatoin_id = $userdetail['User']['device_id'];
                                if ($registatoin_id != "" || $registatoin_id != "(null)") {
                                     $this->Common->android_send_notification($registatoin_id, $data);
                                }
                            }
                		$condition38="User.id='".$id."' AND status = '1' AND deleted = '0'";		
				$usertype = $this->User->find('first',array('conditions'=> $condition38,'fields'=>array('user_type','login_type')));
				
				if($usertype['User']['login_type']=='G'){
				$r_code = rand(0000, 9999);
			 $saveArray['verification_code'] = $r_code;
			$saveArray['register_type'] = 'G';
            $sms = 'CoRover Connect Verification code is ' . $r_code;;
           	
           	$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
			$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
			$otpdata = [
						'number' => $number,
						'password' => $r_code
					];
			$this->Common->send_otp($otpdata,'verification');

						$this->User->updateAll(array('verification_code'=>"'".$saveArray['verification_code']."'"),array('id'=>$id));
				
					}
									$result = array('status'=>'1','message'=>'Mobile No Already verified','id'=>$id,'user_type'=>$usertype['User']['user_type'],'login_type'=>$usertype['User']['login_type']);
					 }
						}else{
							$result=array('status'=>'0','message'=>'QR code does not match');
						}
			
			}else{
				//pr($userdetail); die;  //HERE
				$r_code = rand(0000, 9999);
			 $saveArray['verification_code'] = $r_code;
			$saveArray['register_type'] = 'G';
            $sms = 'CoRover Connect Verification code is ' . $r_code;;
           	
           	$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
			$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
			$otpdata = [
						'number' => $number,
						'password' => $r_code
					];
			$this->Common->send_otp($otpdata,'verification');

        
						if($userdetail['User']['login_type']==""){
								$userdetail['User']['login_type']="";
							}
						$this->User->updateAll(array('verification_code'=>"'".$r_code."'"),array('id'=>$userdetail['User']['id']));
						$result=array('status'=>'1','message'=>'Mobile need to be verified','id'=>$userdetail['User']['id'],'user_type'=>$userdetail['User']['user_type'],'login_type'=>$userdetail['User']['login_type']);	
			}

		}else{
		// $result=array('status'=>'0','message'=>'Qrcode does not exist');
		if(!empty($userdetail['User']['login_type'])){
								$userdetail['User']['login_type']=$userdetail['User']['login_type'];
							}
		if($userdetail['User']['login_type']==""){
								$userdetail['User']['login_type']="";
							}
							$condition12="User.mobile='".$saveArray['mobile']."' AND User.login_type= 'G' ";
		$exist_GUEST1=$this->User->find('first',array('conditions'=>$condition12));
		if($exist_GUEST1){
			$r_code = rand(0000, 9999);
							$saveArray['verification_code'] = $r_code;
							$saveArray['login_type'] = 'G';
							$sms = 'CoRover Connect Verification code is ' . $r_code;

							$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
							$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
							$otpdata = [
										'number' => $number,
										'password' => $r_code
									];
							$this->Common->send_otp($otpdata,'verification');		

			$this->User->updateAll(array('verification_code'=>"'".$r_code."'"),array('id'=>$exist_GUEST1['User']['id']));
		
						$result=array('status'=>'1','message'=>'Mobile need to be verified','id'=>$userdetail['User']['id'],'user_type'=>$userdetail['User']['user_type'],'login_type'=>$userdetail['User']['login_type']);	
		}else{
							$r_code = rand(0000, 9999);
							$saveArray['verification_code'] = $r_code;
							$saveArray['login_type'] = 'G';
							$sms = 'CoRover Connect Verification code is ' . $r_code;
							
							$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
							$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
							$otpdata = [
										'number' => $number,
										'password' => $r_code
									];
							$this->Common->send_otp($otpdata,'verification');	
					

							
							$this->User->save($saveArray,array('validate'=>false));
							$last_id = $this->User->getLastInsertID();
							$condition123="User.id='".$last_id."'";
		$last_data=$this->User->find('first',array('conditions'=>$condition123));
		
						$result=array('status'=>'1','message'=>'Mobile need to be verified','id'=>$last_data['User']['id'],'user_type'=>$last_data['User']['user_type'],'login_type'=>$last_data['User']['login_type']);	
						
		}
		} 
			}
			else{
		$erros=$this->errorValidation('User');
		$result=array('status'=>'0','message'=>$erros);
		}
			
			
			}else{
		$result=array('status'=>'0','message'=>'Please fill user_id or country_code,mobile');
		} 
		
		}
		// }else{
		// $result=array('status'=>'0','message'=>'Please fill seat_no ,qrcode');
		// } 
		
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}
#____________________________________________________________________________#	  	  
    
    /**
    * @Date: 21 aug 1015
    * @Method : edit_profile
    * @Purpose: This function is used to edit profile
    * @Param: none
    * @Return: none 
    **/
	function edit_profile(){ 
		$saveArray=$this->data;
		//print_r($saveArray);die;
		if(!empty($saveArray['user_id']))
		{
			$user_con="User.id=".$saveArray['user_id']." ";
			$this->User->set($saveArray);
			$user_exist=$this->User->find('first',array('conditions'=>$user_con));
			 if(!empty($saveArray['mobile']) && $saveArray['mobile'] == $user_exist['User']['mobile'] OR !empty($saveArray['country_code']) && $saveArray['country_code'] == $user_exist['User']['country_code'])
			   {
		   $this->User->validator()->remove('mobile', 'isUnique');
				   }
				    if(!empty($saveArray['email']) && $saveArray['email'] == $user_exist['User']['email'] )
			   {
		   $this->User->validator()->remove('email', 'isUnique');
				   }
        $isValidated = $this->User->validates();
		 
		 	
        if ($isValidated) {
		
		    
			if(!empty($user_exist)){
				if(!empty($saveArray['name']))
			   {
					$fname = $saveArray['name'];
					
			   }else if(!empty($user_exist['User']['name'])){
					$fname = $user_exist['User']['name'];
				
			   }else{
				   $fname = '';
			   }
			   if(!empty($saveArray['email']))
			   {
					$email = $saveArray['email'];
					
			   }else if(!empty($user_exist['User']['email'])){
			  
					$email = $user_exist['User']['email'];
					
				
			   }else{
				   $email = '';
			   }
			  
			   if(!empty($saveArray['age']))
			   {
					$age = $saveArray['age'];
					
			   }else if(!empty($user_exist['User']['age'])){
					$age = $user_exist['User']['age'];
				
			   }else{
				  $age = ''; 
			   }
			    if(!empty($saveArray['gender']))
			   {
					$gender = $saveArray['gender'];
					
			   }else if(!empty($user_exist['User']['gender'])){
					$gender = $user_exist['User']['gender'];
				
			   }else{
				   $gender = '';
			   }
			  
					if(!empty($saveArray['country_code'])){
						$country_code = $saveArray['country_code'];
					}else if(!empty($user_exist['User']['country_code'])){
						$country_code = $user_exist['User']['country_code'];
					}
					else if(empty($saveArray['country_code'])){
						$country_code ="91";
					}
			
			   
			   if(!empty($saveArray['address']))
			   {
					$address = $saveArray['address'];
					
			   }else if(!empty($user_exist['User']['address'])){
					$address = $user_exist['User']['address'];
				
			   }else{
				   $address = '';
			   }
			   if(!empty($saveArray['mobile']) && $saveArray['mobile'] != $user_exist['User']['mobile'] OR !empty($saveArray['country_code']) && $saveArray['country_code'] != $user_exist['User']['country_code'])
			   {
				   
					$mobile = $saveArray['mobile'];
					 $r_code = rand(0000, 9999);
			 $verification_code = $r_code;
			
            $sms = 'CoRover Connect Verification code is ' . $r_code;;
            //$number = '+'.$saveArray['country_code'].$saveArray['mobile'];
			
			$pos = strpos($saveArray['country_code'].$saveArray['mobile'] , '+');
			$number =  ($pos === false) ? '+'.$saveArray['country_code'].$saveArray['mobile'] : $saveArray['country_code'].$saveArray['mobile'];
			$otpdata = [
						'number' => $number,
						'password' => $r_code
					];
			$this->Common->send_otp($otpdata,'verification');
	
					 
					/* require_once("../Vendor/twilio-php/Services/Twilio.php");
					 
                        // set your AccountSid and AuthToken from www.twilio.com/user/account
                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
                            $result = array('status' => '0', 'message' => $e->getMessage());
                            
                        }*/
						$is_verified = 'C';
						$this->User->updateAll(array('verification_code'=>"'".$verification_code."'",'is_verified'=>"'".$is_verified."'",'new_mobile'=>"'".$mobile."'",'new_countrycode'=>"'".$country_code."'"),array('id'=>$saveArray['user_id']));
						$verified='not verified';
					
			   }else{
			  
				   $verified='verified';
			   }
			   if(!empty($saveArray['designation']))
			   {
					$designation = $saveArray['designation'];
					
			   }else if(!empty($user_exist['User']['designation'])){
					$designation = $user_exist['User']['designation'];
				
			   }else{
				   $designation = '';
			   }
			    
			   $condit="User.id=".$saveArray['user_id']." ";
			
			$user_chk=$this->User->find('first',array('conditions'=> $condit));
			if(!empty($_FILES['image']['name'])){
		$destination=realpath('../../app/webroot/img/profile_images'). DS;
		$filename = $this->uploadPic($user_exist['User']['id'],$destination,$_FILES['image']);
		$eventImage=$filename ;
		$saveArray['image'] = $eventImage;
		if(!empty($filename)){	
			if($user_chk['User']['register_type']=='F'){
				$image = BASE_URL."img/profile_images/".$saveArray['image'];
			}else{
				$image = $saveArray['image'];
			}
		}
		}else if(!empty($user_exist['User']['image'])){
			$image = $user_exist['User']['image'];
		}else{
			$image = '';
		}
			 
			 
			 
			  
				$id = $user_exist['User']['id'];
												
				$this->User->updateAll(array('name'=>"'".
				($fname)."'",'age'=>"'".$age."'",'gender'=>"'".$gender."'",'address'=>"'".$address."'",'designation'=>"'".$designation."'",'image'=>"'".$image."'",'email'=>"'".$email."'"),array('id'=>$saveArray['user_id']));
				
				 $result=array('status'=>'1','message'=>'Updated successfully','is_verified'=>$verified);  
			  // }
			  }else{
				$result=array('status'=>'0','message'=>'User does not exist');   
			  }
			
			} else {
            $errors = $this->errorValidation('User');
            $result = array('status' => '0', 'message' => $errors);
        }

			}else{
			  $result=array('status'=>'0','message'=>'User Id is mendetory');   
			  }
		   $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}	


#_________________________________________________________________________#

    /**
    * @Date: 27-may-1016
    * @Method : Login with Facebook
    * @Purpose: This function is used to Login with Facebook
    * @Param: none
    * @Return: none 
    **/
	function Facebook_Login()
	{
		$saveArray = $this->data;
		$this->User->Set($saveArray);
		if(!empty($saveArray['name']) && !empty($saveArray['unique_id']))
		{
			//$saveArray['access_token'] = !empty($saveArray['access_token'])?$saveArray['access_token']:'';
			$saveArray['email'] = !empty($saveArray['email'])?$saveArray['email']:'';
			if(!empty($saveArray['gender'])){
				$saveArray['gender'] = $saveArray['gender'];
			}
			if(empty($saveArray['gender'])){
				$saveArray['gender'] ="N";
			}
			//$saveArray['gender'] = !empty($saveArray['gender'])?$saveArray['gender']:'';
			if(!empty($saveArray['gender'])){
				$saveArray['gender'] = $saveArray['gender'];
			}
			if(empty($saveArray['gender'])){
				$saveArray['gender'] ="N";
			}
			$saveArray['mobile'] = !empty($saveArray['mobile_no'])?$saveArray['mobile_no']:'';
			//$saveArray['mobile']= $saveArray['mobile_no'];
			$saveArray['register_type']= 'F';
			// $r_code = rand(0000, 9999);
			// $saveArray['verification_code'] = $r_code;
			if(!empty($saveArray['email']))
			{
				$condition="User.email='".$saveArray['email']."' AND User.user_type= 'U' ";
				$exist_record=$this->User->find('first',array('conditions'=>$condition));
				if($exist_record)
				{
					$condition_check_status = "User.email='".$saveArray['email']."' AND User.user_type= 'U' AND User.activate_status = 'Activate'";
				$exist_record_check_status = $this->User->find('first',array('conditions'=>$condition_check_status));
				if($exist_record_check_status)
				{
					
				
				
				if(!empty($_FILES['image']['name']))
				{
					$destination=realpath('../../app/webroot/img/profile_images'). DS;
					$filename = $this->uploadPic($exist_record['User']['id'],$destination);
					$eventImage=$filename ;
					$saveArray['image'] = $eventImage;
					if(!empty($filename))
					{
						$image = $saveArray['image'];
					}
					
				}
				else
				{
						$image = $exist_record['User']['image'];
				}
				
				$id = $exist_record['User']['id'];
				$default_group="1";
				$condition33="Group.default_group='".$default_group."'";
				$groupdetail = $this->Group->find('first',array('conditions'=> $condition33,'fields'=>array('id','user_id','name','staff_count','user_count')));
								if(!empty($groupdetail)){
								
									$user_id=$groupdetail['Group']['user_id'];
									$userarr=explode(',',$user_id);
									// if(!(in_array($id,$userarr))){
										// $userid=$user_id.','.$id;
									// }else{
										// $userid=$user_id;
									// }
									
									if(!(in_array($id,$userarr))){
										if(count($userarr) > 0){
											$userid=$user_id.','.$id;
										}else{
											$userid=$id;
										}
									}else{
										$userid=$user_id;
									}
									$gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
									$condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
									$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
									$condition12="User.id='".$id."'";
									$usertye =$this->User->find('first',array('conditions'=>$condition12));
									$user_type= $usertye['User']['user_type'];
									if(!empty($groupmem)){
										$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
									}else{
										$GrpMmbr['user_id']=$id;
										$GrpMmbr['group_id']=$groupdetail['Group']['id'];
										$this->GroupMember->save($GrpMmbr,array('validate'=>false));
										$user_count = $groupdetail['Group']['user_count']+1;
										$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
									}
								}
				$updateRecord = $this->User->updateAll(array('name'=>"'".($saveArray['name'])."'",'gender'=>"'".($saveArray['gender'])."'",'age'=>"'".($saveArray['age'])."'",'mobile'=>"'".($saveArray['mobile'])."'",'image'=>"'".($image)."'",'unique_id_F'=>"'".($saveArray['unique_id'])."'",'register_type'=>"'F'",'is_verified'=>"'Y'"),array('id'=>$exist_record['User']['id']));
				$result=array('status'=>'1','message'=>'success','id'=>$exist_record['User']['id'],'register_type' => 'F');
			}
			else
			{
				$result=array('status'=>'0','message'=>'Your Account Deactivate');
			}
			
			}
			
			else
			{
				$saveArray['unique_id_F'] = $saveArray['unique_id'];
				$is_validate = $this->User->validates();
				if($is_validate)
				{
					$saveArray['is_verified'] = "Y";
					
					$res=$this->User->save($saveArray,array('validate'=>false));

					$id = $this->User->id;
					$default_group="1";
					$condition33="Group.default_group='".$default_group."'";
							$groupdetail = $this->Group->find('first',array('conditions'=> $condition33,'fields'=>array('id','user_id','name','staff_count','user_count')));
								if(!empty($groupdetail)){
								
									$user_id=$groupdetail['Group']['user_id'];
									$userarr=explode(',',$user_id);
									// if(!(in_array($id,$userarr))){
										// $userid=$user_id.','.$id;
									// }else{
										// $userid=$user_id;
									// }
									
									if(!(in_array($id,$userarr))){
										if(count($userarr) > 0){
											$userid=$user_id.','.$id;
										}else{
											$userid=$id;
										}
									}else{
										$userid=$user_id;
									}
									$gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
									$condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
									$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
									$condition12="User.id='".$id."'";
									$usertye =$this->User->find('first',array('conditions'=>$condition12));
									$user_type= $usertye['User']['user_type'];
									if(!empty($groupmem)){
										$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
									}else{
										$GrpMmbr['user_id']=$id;
										$GrpMmbr['group_id']=$groupdetail['Group']['id'];
										$this->GroupMember->save($GrpMmbr,array('validate'=>false));
										$user_count = $groupdetail['Group']['user_count']+1;
										$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
										}
								}
					if(!empty($_FILES['image']['name']))
					{
						$destination=realpath('../../app/webroot/img/profile_images'). DS;
						$filename = $this->uploadPic($id,$destination);
						$eventImage = $filename ;
						$saveArray['image'] = $eventImage;
						if(!empty($filename))
					   {
							$image = $saveArray['image'];						
					   }
						$this->User->updateAll(array('image'=>"'".$image."'"),array('id'=>$id));
					 }			  
					$result=array('status'=>'1','message'=>'success','id'=> $id,'register_type' => 'F');
				}else
				{
					$errors = $this->errorValidation('User');
					$result = array('status'=>'0','message'=>$errors);
					
				}
			}
			}
			else
			{	//save a new record	
				//$saveArray['access_token_F'] = $saveArray['access_token'];
				
				$saveArray['unique_id_F'] = $saveArray['unique_id'];
				$condition="User.unique_id_F='".$saveArray['unique_id_F']."' AND User.user_type= 'U' ";
			$exist_record=$this->User->find('first',array('conditions'=>$condition));
			if($exist_record)
			{
				
				$condition_check_status = "User.unique_id_F='".$saveArray['unique_id_F']."' AND User.user_type= 'U' AND User.activate_status = 'Activate'";
				$exist_record_check_status = $this->User->find('first',array('conditions'=>$condition_check_status));
				if($exist_record_check_status)
				{
					
				
				if(!empty($_FILES['image']['name'])){
					$destination=realpath('../../app/webroot/img/profile_images'). DS;
					$filename = $this->uploadPic($exist_record['User']['id'],$destination);
					$eventImage=$filename ;
					$saveArray['image'] = $eventImage;
					if(!empty($filename))
					{
						$image = $saveArray['image'];
					}
				  }else{
						$image = $exist_record['User']['image'];
				 }
				 $id = $exist_record['User']['id'];
				 $default_group="1";
					$condition33="Group.default_group='".$default_group."'";
							$groupdetail = $this->Group->find('first',array('conditions'=> $condition33,'fields'=>array('id','user_id','name','staff_count','user_count')));
								if(!empty($groupdetail)){
								
									$user_id=$groupdetail['Group']['user_id'];
									$userarr=explode(',',$user_id);
									// if(!(in_array($id,$userarr))){
										// $userid=$user_id.','.$id;
									// }else{
										// $userid=$user_id;
									// }
									
									if(!(in_array($id,$userarr))){
										if(count($userarr) > 0){
											$userid=$user_id.','.$id;
										}else{
											$userid=$id;
										}
									}else{
										$userid=$user_id;
									}
									$gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
									$condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
									$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
									$condition12="User.id='".$id."'";
									$usertye =$this->User->find('first',array('conditions'=>$condition12));
									$user_type= $usertye['User']['user_type'];
									if(!empty($groupmem)){
										$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
									}else{
										$GrpMmbr['user_id']=$id;
										$GrpMmbr['group_id']=$groupdetail['Group']['id'];
										$this->GroupMember->save($GrpMmbr,array('validate'=>false));
										$user_count = $groupdetail['Group']['user_count']+1;
										$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
									}
								}
				$updateRecord = $this->User->updateAll(array('name'=>"'".($saveArray['name'])."'",'gender'=>"'".($saveArray['gender'])."'",'age'=>"'".($saveArray['age'])."'",'mobile'=>"'".($saveArray['mobile'])."'",'image'=>"'".($image)."'",'register_type'=>"'F'",'is_verified'=>"'Y'"),array('id'=>$exist_record['User']['id']));
				$result=array('status'=>'1','message'=>'success','id'=>$exist_record['User']['id'],'register_type' => 'F');
			}
			else
			{
				$result=array('status'=>'0','message'=>'Your Account Deactivate');
			}
			}
			else
			{
				$this->User->validator()->remove('email');
				$is_validate = $this->User->validates();
				if($is_validate){
						$saveArray['is_verified'] = "Y";
					$res=$this->User->save($saveArray,array('validate'=>false));

					$id = $this->User->id;
					$default_group="1";
					$condition33="Group.default_group='".$default_group."'";
							$groupdetail = $this->Group->find('first',array('conditions'=> $condition33,'fields'=>array('id','user_id','name','staff_count','user_count')));
								if(!empty($groupdetail)){
								
									$user_id=$groupdetail['Group']['user_id'];
									$userarr=explode(',',$user_id);
									// if(!(in_array($id,$userarr))){
										// $userid=$user_id.','.$id;
									// }else{
										// $userid=$user_id;
									// }
									
									if(!(in_array($id,$userarr))){
										if(count($userarr) > 0){
											$userid=$user_id.','.$id;
										}else{
											$userid=$id;
										}
									}else{
										$userid=$user_id;
									}
									$gupdate=$this->Group->updateAll(array('user_id'=>"'".$userid."'"),array('id'=>$groupdetail['Group']['id']));
									$condition37="GroupMember.user_id='".$id."' And GroupMember.group_id='".$groupdetail['Group']['id']."'";
									$groupmem= $this->GroupMember->find('first',array('conditions'=> $condition37));
									$condition12="User.id='".$id."'";
									$usertye =$this->User->find('first',array('conditions'=>$condition12));
									$user_type= $usertye['User']['user_type'];
									if(!empty($groupmem)){
										$this->GroupMember->updateAll(array('user_id'=>"'".$id."'",'group_id'=>"'".$groupdetail['Group']['id']."'"),array('id'=>$groupmem['GroupMember']['id']));
									}else{
										$GrpMmbr['user_id']=$id;
										$GrpMmbr['group_id']=$groupdetail['Group']['id'];
										$this->GroupMember->save($GrpMmbr,array('validate'=>false));
										$user_count = $groupdetail['Group']['user_count']+1;
										$this->Group->updateAll(array('user_count' =>$user_count), array('id' => $groupdetail['Group']['id']));
									}
								}
					
					if(!empty($_FILES['image']['name']))
					{
						$destination=realpath('../../app/webroot/img/profile_images'). DS;
						$filename = $this->uploadPic($id,$destination);
						$eventImage = $filename ;
						$saveArray['image'] = $eventImage;
						if(!empty($filename))
					   {
							$image = $saveArray['image'];						
					   }
						$this->User->updateAll(array('image'=>"'".$image."'"),array('id'=>$id));
					 }			  
					$result=array('status'=>'1','message'=>'success','id'=> $id,'register_type' => 'F');
				}else
				{
					$errors = $this->errorValidation('User');
					$result = array('status'=>'0','message'=>$errors);
					
			}}
			}
		}
		else
		{
			$result=array('status'=>'0','message'=>'Please fill all fields');
		}
		 $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}

	#_________________________________________________________________________#
	
	function get_chat() 
	{
        $saveArray = $this->data;
        if (!empty($saveArray['sender_id']) && !empty($saveArray['timezone']) && !empty($saveArray['page_number']) && !empty($saveArray['reciever_id']) && !empty($saveArray['group_id'])) {

                $condition = " User.id='" . $saveArray['sender_id'] . "' ";
                $user_exist = $this->User->find('first', array('conditions' => $condition));

            if ($user_exist)
            {
            	

            $condition3 = "CheckSetting.user_id='" . $saveArray['sender_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
                $check_status= $this->CheckSetting->find('first', array('conditions' => $condition3));
				if(!empty($check_status)){
               $privatestatus_offtime = $check_status['CheckSetting']['privatestatus_offtime'];

				if($check_status['CheckSetting']['private_chat'] == 0){

               
                    $start_limit = $saveArray['page_number']*10-10;
                    $end_limit = 10;
                  
                      $query = "SELECT chats.*,
			users.name as sendername,users.image as senderimage,
			users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM chats INNER JOIN users ON users.id=chats.sender_id inner join users f 
			on f.id=chats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
			AND receiver_id=" . $saveArray['reciever_id'] . ")
			OR( sender_id=" . $saveArray['reciever_id'] . " 
			AND receiver_id=" . $saveArray['sender_id'] . ")) AND (chats.group_id=" . $saveArray['group_id'] . ")) order by id DESC LIMIT $start_limit,$end_limit ";

                $user_details = $this->Chat->query($query);


                $query1 = "SELECT count('id') as totalpage FROM `chats`
			WHERE (((sender_id=" . $saveArray['sender_id'] . "
			AND receiver_id=" . $saveArray['reciever_id'] . ")
			OR( sender_id=" . $saveArray['reciever_id'] . " 
			AND receiver_id=" . $saveArray['sender_id'] . ")) AND (chats.group_id=" . $saveArray['group_id'] . "))";
                $user_exist1 = $this->Chat->query($query1);
                $page_count = $user_exist1[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
				}else{
					
                    $start_limit = $saveArray['page_number']*10-10;
                    $end_limit = 10;
                  
                      $query = "SELECT chats.*,
			users.name as sendername,users.image as senderimage,
			users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM chats INNER JOIN users ON users.id=chats.sender_id inner join users f 
			on f.id=chats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
			AND receiver_id=" . $saveArray['reciever_id'] . ")
			OR( sender_id=" . $saveArray['reciever_id'] . " 
			AND receiver_id=" . $saveArray['sender_id'] . ")) AND (chats.group_id=" . $saveArray['group_id'] . ")) AND submit_time<='$privatestatus_offtime' order by id DESC LIMIT $start_limit,$end_limit ";

                $user_details = $this->Chat->query($query);


                $query1 = "SELECT count('id') as totalpage FROM `chats`
			WHERE (((sender_id=" . $saveArray['sender_id'] . "
			AND receiver_id=" . $saveArray['reciever_id'] . ")
			OR( sender_id=" . $saveArray['reciever_id'] . " 
			AND receiver_id=" . $saveArray['sender_id'] . ")) AND (chats.group_id=" . $saveArray['group_id'] . "))";
                $user_exist1 = $this->Chat->query($query1);
                $page_count = $user_exist1[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
				}
				}
              if(empty($check_status)){
				    $start_limit = $saveArray['page_number']*10-10;
                    $end_limit = 10;
                  
                      $query = "SELECT chats.*,
			users.name as sendername,users.image as senderimage,
			users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM chats INNER JOIN users ON users.id=chats.sender_id inner join users f 
			on f.id=chats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
			AND receiver_id=" . $saveArray['reciever_id'] . ")
			OR( sender_id=" . $saveArray['reciever_id'] . " 
			AND receiver_id=" . $saveArray['sender_id'] . ")) AND (chats.group_id=" . $saveArray['group_id'] . ")) order by id DESC LIMIT $start_limit,$end_limit ";

                $user_details = $this->Chat->query($query);


                $query1 = "SELECT count('id') as totalpage FROM `chats`
			WHERE (((sender_id=" . $saveArray['sender_id'] . "
			AND receiver_id=" . $saveArray['reciever_id'] . ")
			OR( sender_id=" . $saveArray['reciever_id'] . " 
			AND receiver_id=" . $saveArray['sender_id'] . ")) AND (chats.group_id=" . $saveArray['group_id'] . "))";
                $user_exist1 = $this->Chat->query($query1);
                $page_count = $user_exist1[0][0]['totalpage'];
                $page_count = $page_count / 10;
                $page_count = ceil($page_count);
			  }
            // pr($user_details);die;
                if (!empty($user_details)) {
                    foreach ($user_details as $k => $values) {
						 $data['msg_id'] = $values['chats']['id'];
                         $data['senderid'] = $values['chats']['sender_id'];
						 if(!empty($values['users']['senderimage'])){
							 if($values['users']['register_type']=="F"){
								$data['senderimage'] = $values['users']['senderimage'];
							 }
							 if($values['users']['register_type']=="N"){
								$data['senderimage'] = BASE_URL . "img/profile_images/" .$values['users']['senderimage'];
							 }
						 }
						 if(empty($values['users']['senderimage'])){
							 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
								$data['senderimage'] = $defaulturl;
						 }
						// pr($values);die;
                         $data['sendername'] = $values['users']['sendername'];
					 $data['msg'] = !empty($values['chats']['message'])?$values['chats']['message']:'';
				  $data['type'] = $values['chats']['type'];
				  if($values['chats']['type']=='I'){
			      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['chats']['image'];	
				  }else{
					 $data['image'] = ''; 
				  }
				   if($values['chats']['type']=='L'){
			      $data['lat'] = $values['chats']['lat'];	
			      $data['lng'] =$values['chats']['lng'];
				  }else{
					$data['lat'] = "";	
			        $data['lng'] ="";
				  }
				  	//pr($values['chats']); die;
					
					/**/ $currentDateTime = $values['chats']['submit_time'];
					/**/ $submit_time_zone = $values['chats']['time_zone'];
					/**/ $user_timezone = $saveArray['timezone'];
					/**/ $converted_data = $this->timezone_test_get_chat($currentDateTime,$submit_time_zone, $user_timezone);
					$day = explode(" ",$converted_data);
					$data['date'] = $day[0];
                			 
					$data['time'] = $day[1]." ".$day[2];
					
					
					
					
				    /* $date = new DateTime($values['chats']['submit_time'], new DateTimeZone($saveArray['timezone']));
					//$date->modify('-5 minutes');
					$currentDateTime = $date->format('Y-m-d H:i:s');
                    $usertimezone = $saveArray['timezone'];
					date_default_timezone_set($saveArray['timezone']);
                    $data['time'] =$date->format('h:i A');
                    $data['date'] = $date->format('Y-m-d'); */



                        $data1[] = ($data);
                    }
					 if (!empty($data1)) {
                        $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count);
                    } else {

                        $result = array('status' => '0', 'message' => 'Groups not found.');
                    }
                } else {

                    $result = array('status' => '0', 'message' => 'Message not found.');
                }
            } else {
                $result = array('status' => '0', 'message' => 'group_id or user_id not match.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	#__________________________________________________________________________#
 








    /**

    * @Date: 18-jan-1016

    * @Method : uploadPic

    * @Purpose: This function is used to upload fan pic

    * @Param: $id

    * @Return: none

    **/

	 function uploadPic($id = null, $destination = null, $name = null) {
        if (!empty($_FILES)) {
            $file = $name;
            if ($file['size'] != 0) {
                if (empty($destination)) 
				{
                    $destination = realpath(BASE_URL . 'app/webroot/img/profile_images/') . DS;
                }
                $ext = $this->Common->file_extension($file['name']);
                $filename = $id . '_' . time() . '.' . strtolower($ext);
                $size = $name['size'];
                if ($size > 0) {
                    $files = $this->Common->get_files($destination, "/^" . $id . "_/i");
                    if (!empty($files)) 
					{
                        foreach ($files as $x) 
						{
                            @unlink($destination . $x);
                        }
                    }
					
                        $result = $this->Upload->upload($file, $destination, $filename);
						
                        return $filename;
                }
            }
        }
    }
	#_________________________________________________________________________#
	
	 function change_password(){
		 $saveArray=$this->data;
		 if (!empty($saveArray['user_id']) && !empty($saveArray['old_password']) && !empty($saveArray['password']) ) {
				$this->User->set($this->data);
				$this->User->validator()->remove('old_password');
				$isValidated = $this->User->validates();
					if($isValidated){
					/* if($saveArray['old_password'] != $saveArray['password']){ */
						$condition = "password='".md5($this->data['old_password'])."' and status = '1' AND deleted = '0' and id='".$this->data['user_id']."'";
						$emp_details = $this->User->find('first', array("conditions" => $condition, "fields" => array("id","mobile","name","country_code")));
							if(!empty($emp_details)){
							 if($saveArray['old_password'] != $saveArray['password']){ 
								// Reset password
									$name    = $emp_details['User']['name'];
								$newPassword = $this->data['password'];
					
									$this->User->updateAll(array("password"=>"'".md5($newPassword)."'"),array("id"=>$emp_details['User']['id']));
									$result=array('status'=>'1','message'=>'Password changed successfully');
								
							}else{
						$result=array('status'=>'0','message'=>'New password cant be same as old password.');
					}}else{
							$result=array('status'=>'0','message'=>'This user not exist');
					}/* }else{
						$result=array('status'=>'0','message'=>'New password cant be same as old password.');
					} */
					}else{
							$erros=$this->errorValidation('User');
		                    $result=array('status'=>'0','message'=>$erros);
					}
			}else{
				$result=array('status'=>'0','message'=>'Fill all fields');
			}
			 $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	#_________________________________________________________________________________#
	function feedback() {
        $saveArray = $this->data;
        $this->Feedback->set($saveArray);
        if(!empty($saveArray['user_id']) && !empty($saveArray['group_id']) && !empty($saveArray['comment']) && !empty($saveArray['rating'])){
            $user_exist = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
			$group_exist = $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
			 $admin_id = $this->User->find('first', array('conditions' => array('User.id' => $group_exist['Group']['created_id'])));
            if (!empty($user_exist) && !empty($group_exist)){
                $this->Feedback->save($saveArray,array('validate'=>false));
				$resetPassword = rand(100000,999999);
					$groupname = $group_exist['Group']['name'];
					$username= (isset($user_exist['User']['name'])) ? $user_exist['User']['name'] : 'Guest';
					$phone = 	(isset($user_exist['User']['mobile'])) ? $user_exist['User']['mobile'] : 'Not Provided';
					$email = 	(isset($user_exist['User']['email'])) ? $user_exist['User']['email'] : 'Not Provided';
					$subject = "A New Feedback For ".$groupname." ";
					$name    = $admin_id['User']['name'];
					$senderemail = trim($admin_id['User']['email']);
					//$senderemail = 'dev.drawtopic@gmail.com';
					$group_logo = $group_exist['Group']['icon'];
					$comment = $saveArray['comment'];
					$rating = $saveArray['rating'];
					$this->Email->to       = trim($senderemail);
					$this->Email->subject  = $subject;
					$this->Email->replyTo  = ADMIN_EMAIL;
					$static_email  ="email@CoRover.mobi";
					$this->Email->fromName = "CoRover";
					$this->Email->from     = trim($static_email);
					$this->Email->bcc     = trim('email@corover.mobi');
					$this->Email->sendAs   = 'html';
					 $this->Email->template = 'feedback'; // note no '.ctp'
					$mes  ="User has submitted feedback and rating.";
					$this->set('vars', array('name' => $name,'subtitle' => $mes,'username' => $username,'group_name' => $groupname, 'comment' => $comment , 'rating' => $rating ,'base' => BASE_URL,'group_logo' => $group_logo,'phone' => $phone , 'email' => $email ) );	
					$this->Email->send();
				
                $result = array('status' => '1', 'message' => "Feedback Submitted  Successfully", 'user_name' => $user_exist['User']['name'],'group_name' => $group_exist['Group']['name'],'comment'=>$saveArray['comment'],'rating'=>$saveArray['rating']);
            } else {
                $result = array('status' => '0', 'message' => "Wrong Information");
            }
		}else{
		$result=array('status'=>'0','message'=>'Please fill all fields');
		} 
       /*  echo json_encode($result);
        die; */
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
#____________________________________________________________________________________________#
/* function edit_phone(){ 
		$saveArray=$this->data;
		if(!empty($saveArray['user_id']))
		{
			$user_con="User.id=".$saveArray['user_id']." ";
			
			$user_exist=$this->User->find('first',array('conditions'=>$user_con));
		
			if(!empty($user_exist)){
				
			  
			   
			   
			    if(!empty($saveArray['country_code']))
			   {
					$country_code = $saveArray['country_code'];
					
			   }else if(!empty($user_exist['User']['country_code'])){
					$country_code = $user_exist['User']['country_code'];
				
			   }
			   
			   if(!empty($saveArray['mobile']) && $saveArray['mobile'] != $user_exist['User']['mobile'])
			   {
					$mobile = $saveArray['mobile'];
					 $r_code = rand(0000, 9999);
			 $verification_code = $r_code;
			
            $sms = 'Your Verification code is ' . $r_code;;
            $number = '+'.$saveArray['country_code'].$saveArray['mobile'];
				
					 
					 require_once("../Vendor/twilio-php/Services/Twilio.php");
					 
                        // set your AccountSid and AuthToken from www.twilio.com/user/account
                               $AccountSid = "AC1e92409b59e005ed08abbfa880c59e0e";
                        $AuthToken = "3e1979a0725924f10bc91dfc85ed184b";
                        $client = new Services_Twilio($AccountSid, $AuthToken);  
                  
                        try {
                            $message = $client->account->messages->create(array(
                                "From" => "(857) 267-6837",
                                "To" => $number,
                                "Body" => $sms,
                            ));
                        } catch (Services_Twilio_RestException $e) {
                            $result = array('status' => '0', 'message' => $e->getMessage());
                            
                        }
						$is_verified = '0';
						$this->User->updateAll(array('verification_code'=>"'".$verification_code."'",'is_verified'=>"'".$is_verified."'"),array('id'=>$saveArray['user_id']));
					
			   }else{
					$mobile = $user_exist['User']['mobile'];
				
			   }
		
				$id = $user_exist['User']['id'];
												
				$this->User->updateAll(array('new_country_code'=>"'".$country_code."'",'new_mobile'=>"'".$mobile."'"),array('id'=>$saveArray['user_id']));
				
				
			/* $groupdetail = $this->Group->find('first',array('conditions'=> array(FIND_IN_SET("'".$saveArray['user_id']."'",'user_id'),'status'=>'2','deleted'=>'1'),'fields'=>array('id')));
				$this->GroupInformation->updateAll(array('seat_no'=>"'".$seat_no."'"),array('user_id'=>$saveArray['user_id'],'group_id'=>$groupdetail['Group']['id'])); */
				
				/* if(!empty($saveArray['mobile']) && $saveArray['mobile'] != $user_exist['User']['mobile'])
			   {
				$result=array('status'=>'1','message'=>'updated successfully','is_verified'=>'not verified');
			   }else{ */
				 //$result=array('status'=>'1','message'=>'updated successfully');  
			  // }
			/*  }else{
				$result=array('status'=>'0','message'=>'user does not exist');   
			  }
			}else{
			  $result=array('status'=>'0','message'=>'User Id is mendetory');   
			  }
		   $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}}	 */
	function get_refrence(){
        $saveArray=$this->data;
		if(!empty($saveArray['qr_code'])){
	
			$condition="Group.qr_code='".$saveArray['qr_code']."' AND status = '1' AND deleted = '0'";
			$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','type')));
				//print_r($groupdetail);die;
			if($groupdetail){
				$type_id=$groupdetail['Group']['type'];
			
				$condition="Grouptype.type_name='".$type_id."' AND deleted = '0'";
			$grouptypedetail = $this->Grouptype->find('first',array('conditions'=> $condition,'fields'=>array('id','type_name','refrence')));
				//print_r($grouptypedetail);die;
			if($grouptypedetail){
				 $data=$grouptypedetail;
			}else{
			$data=array();
			}
			
				$result = array('status'=>'1','message'=>'Login Successfully','data'=>$data);
			}else{
			 $result=array('status'=>'0','message'=>'QR code does not match');
			}
		}else{
		$result=array('status'=>'0','message'=>'Please fill qrcode');
		} 
		
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}
#_______----------------------------------------#
function logout($id = null) {
			
			 $saveArray=$this->data;
		if(!empty($saveArray['user_id'])){
				//pr($this->data);
			
		
			 $post_id = $saveArray['user_id'];
			$user = $this->User->find('first', array('conditions'=>array('id'=>Sanitize::escape($post_id))));
			if(!empty($user)){
				
				$isValidated = $this->User->validates();
				if($isValidated){
					
					
					$device_id='';
					//pr($userData);
					
		$updated = $this->User->updateAll(array('User.device_id'=>"'".$device_id."'"),
					                       array('User.id'=>$post_id));	
					if($updated){
						$result=array('status'=>'1','message'=>'Device Id updated successfully');
					}else{
							$result=array('status'=>'0','message'=>'Error in updation');
					}
					
					
				}
				
			}else{
			$result=array('status'=>'0','message'=>'User not found');
			}
		
			
		}else{
		$result=array('status'=>'0','message'=>'Please fill user_id');
		} 
		
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}
	
	 /**
     * @Method :block user
     * @Param: none
     * */

    function block(){
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['friend_id']) && !empty($saveArray['group_id']) && !empty($saveArray['reason'])){
            if ($saveArray['user_id'] == $saveArray['friend_id']) {
				$result = array('status' => '0', 'message' => 'Can not block');
            }
			if ($saveArray['group_id'] != 1){
                $condition = "(Groupinformation.user_id='" . $saveArray['user_id'] . "' AND group_id='" . $saveArray['group_id'] . "')
				      or (Groupinformation.user_id='" . $saveArray['friend_id'] . "' AND group_id='" . $saveArray['group_id'] . "')";
                $user_exist = $this->Groupinformation->find('first', array('conditions' => $condition));
            }else{
				$condition = "(User.id !='" . $saveArray['user_id'] . "' AND user_type='U' AND  (is_verified = '1' AND register_type ='N') OR (register_type ='F') )";
				$user_exist = $this->User->find('all', array("conditions" => $condition));
            }
			
            if($user_exist){
				$condition = "Block.user_id='" . $saveArray['user_id'] . "' AND group_id='" . $saveArray['group_id'] . "' AND friend_id='" . $saveArray['friend_id'] . "'";
                $exist = $this->Block->find('first', array('conditions' => $condition));
				if(empty($exist)){
					$this->Block->save($saveArray);
                    $group_id = $saveArray['group_id'];
                    $friend_id = $saveArray['friend_id'];
					$query1 = "UPDATE groupinformations SET count=count+1  WHERE group_id =$group_id 
								AND user_id=$friend_id";
					$this->Groupinformation->query($query1);
					$condition2 = "group_id='" . $saveArray['group_id'] . "' AND friend_id='" . $saveArray['friend_id'] . "'";
			$already_block = $this->Block->find('first', array('conditions' => $condition2));
			if(!empty($already_block)){
					if($already_block['Block']['friend_id'] == $saveArray['friend_id'] && $already_block['Block']['group_id'] == $saveArray['group_id']){
						$group_id1 = $saveArray['group_id'];
						$friend_id1 = $saveArray['friend_id'];
						$query3 = "UPDATE users SET count=count+1  WHERE id =$friend_id1";
						$this->User->query($query3);
					}
					else{
						$query1 = "UPDATE users SET count=1  WHERE id =$friend_id";
						$this->User->query($query1);
					}
				}
				$result = array('status' => '1', 'message' => 'successfully.');
                }else{
					$result = array('status' => '0', 'message' => 'You have already submitted the block request for this user As per our terms, user will be removed from the group if there are atleast two block requests.');
                }
            }else{
                $result = array('status' => '0', 'message' => 'user id or friend_id id or group_id id not found.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'please fill all fields.');
        }
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }

    #_____________________________________________________________________#	

/**
     * @Method : Notification_status
     * @Param: none
     * */

    function chat_on_off() 
	{
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['group_id']) && !empty($saveArray['group_chat']) && !empty($saveArray['private_chat'])) {
            $group_chat = $saveArray['group_chat'];
            $private_chat = $saveArray['private_chat'];
           // $chat_notification = $saveArray['chatNotification'];
            $user_id = $saveArray['user_id'];
            $group_id = $saveArray['group_id'];
            $condition = "User.id='" . $saveArray['user_id'] . "'";
            $user_exist = $this->User->find('first', array('conditions' => $condition));
            if ($user_exist) {
				$on="0";
				$off="1";
           
              //  for privatechat
			  	date_default_timezone_set('Asia/Kolkata');
				$current_date = date('Y-m-d H:i:s');
              //  $current_date = date('Y-m-d H:i:s');
                if ($private_chat) {
                    if ($private_chat == 'on') 
					{
						$condition2 = "CheckSetting.user_id='" . $saveArray['user_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
						$data_exist1 = $this->CheckSetting->find('first', array('conditions' => $condition2));
						if(!empty($data_exist1))
						{
							$save_like=$this->CheckSetting->updateAll(array('CheckSetting.private_chat'=>"'".$on ."'",'CheckSetting.privatestatus_offtime'=>"'".$current_date ."'",'CheckSetting.group_id'=>"'".$group_id ."'"),array('CheckSetting.id'=>$data_exist1['CheckSetting']['id']));
						}
						else
						{
							$saveAr['user_id']= $user_id;
							$saveAr['group_id']= $group_id;
							$saveAr['privatestatus_offtime']=$current_date;
							$saveAr['private_chat']= $on;
							$this->CheckSetting->save($saveAr,array('validate'=>false));
						}
                        $result = array('status' => '1', 'message' => 'successfully');
                    }
                    if ($private_chat == 'off') 
					{
                       $condition2 = "CheckSetting.user_id='" . $saveArray['user_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
						$data_exist2 = $this->CheckSetting->find('first', array('conditions' => $condition2));
						if(!empty($data_exist2))
						{
							$save_like=$this->CheckSetting->updateAll(array('CheckSetting.private_chat'=>"'".$off ."'",'CheckSetting.privatestatus_offtime'=>"'".$current_date ."'",'CheckSetting.group_id'=>"'".$group_id ."'"),array('CheckSetting.id'=>$data_exist2['CheckSetting']['id']));
						}
						else
						{
							$saveAr['user_id']= $user_id;
							$saveAr['group_id']= $group_id;
							$saveAr['privatestatus_offtime']=$current_date;
							$saveAr['private_chat']= $off;
							$this->CheckSetting->save($saveAr,array('validate'=>false));
						}
                        $result = array('status' => '1', 'message' => 'successfully');
                    }
                }

               // for groupchat
                	date_default_timezone_set('Asia/Kolkata');
				$current_date = date('Y-m-d H:i:s');
                if ($group_chat) 
				{
                    if ($group_chat == 'on') 
					{
                       
							$condition2 = "CheckSetting.user_id='" . $saveArray['user_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
						$data_exist3 = $this->CheckSetting->find('first', array('conditions' => $condition2));
						if(!empty($data_exist3))
						{
							$save_like=$this->CheckSetting->updateAll(array('CheckSetting.group_chat'=>"'".$on ."'",'CheckSetting.groupstatus_offtime'=>"'".$current_date ."'",'CheckSetting.group_id'=>"'".$group_id ."'"),array('CheckSetting.id'=>$data_exist3['CheckSetting']['id']));
						}
						else
						{
							$saveAr['user_id']= $user_id;
							$saveAr['group_id']= $group_id;
							$saveAr['groupstatus_offtime']=$current_date;
							$saveAr['group_chat']= $on;
							$this->CheckSetting->save($saveAr,array('validate'=>false));
						}
                        $result = array('status' => '1', 'message' => 'successfully');
                    }
                    if ($group_chat == 'off') 
					{
                      $condition2 = "CheckSetting.user_id='" . $saveArray['user_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
						$data_exist4 = $this->CheckSetting->find('first', array('conditions' => $condition2));
						if(!empty($data_exist4))
						{
							$save_like=$this->CheckSetting->updateAll(array('CheckSetting.group_chat'=>"'".$off ."'",'CheckSetting.groupstatus_offtime'=>"'".$current_date ."'",'CheckSetting.group_id'=>"'".$group_id ."'"),array('CheckSetting.id'=>$data_exist4['CheckSetting']['id']));
						}
						else
						{
							$saveAr['user_id']= $user_id;
							$saveAr['group_id']= $group_id;
							$saveAr['groupstatus_offtime']=$current_date;
							$saveAr['group_chat']= $off;
							$this->CheckSetting->save($saveAr,array('validate'=>false));
						}
                        $result = array('status' => '1', 'message' => 'successfully');
                    }
					
                }
            } 
			else 
			{
                $result = array('status' => '0', 'message' => 'user_id not found.');
            }
        } 
		else 
		{
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
       $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }	
	    #_____________________________________________________________________#	
	
	/**
     * @Method : Notification_status
     * @Param: none
     * */

    function notification_status() {
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['notification_status']) ) {
            $user_id = $saveArray['user_id'];
            $notification_status = $saveArray['notification_status'];
			$condition = "User.id='" . $saveArray['user_id'] . "'";
            $user_exist = $this->User->find('first', array('conditions' => $condition));
            if ($user_exist) {
				$on="0";
				$off="1";
				date_default_timezone_set('Asia/Kolkata');
				$current_date = date('Y-m-d H:i:s');
				if ($notification_status == 'on'){
						$condition2 = "PushNotification.user_id='" . $saveArray['user_id'] . "'";
						$data_exist1 = $this->PushNotification->find('first', array('conditions' => $condition2));
						if(!empty($data_exist1)){
							$save_like=$this->PushNotification->updateAll(array('PushNotification.notification_status'=>"'".$on ."'",'PushNotification.notification_time'=>"'".$current_date ."'"),array('PushNotification.id'=>$data_exist1['PushNotification']['id']));
						}else{
							$saveAr['user_id']= $user_id;
							$saveAr['notification_time']=$current_date;
							$saveAr['notification_status']= $on;
							$this->PushNotification->save($saveAr,array('validate'=>false));
						}
                        $result = array('status' => '1', 'message' => 'successfully');
				}
                if ($notification_status == 'off') {
                      $condition2 = "PushNotification.user_id='" . $saveArray['user_id'] . "'";
						$data_exist1 = $this->PushNotification->find('first', array('conditions' => $condition2));
						if(!empty($data_exist1)){
							$save_like=$this->PushNotification->updateAll(array('PushNotification.notification_status'=>"'".$off ."'",'PushNotification.notification_time'=>"'".$current_date ."'"),array('PushNotification.id'=>$data_exist1['PushNotification']['id']));
						}else{
							$saveAr['user_id']= $user_id;
							$saveAr['notification_time']=$current_date;
							$saveAr['notification_status']= $off;
							$this->PushNotification->save($saveAr,array('validate'=>false));
						}
                        $result = array('status' => '1', 'message' => 'successfully');
                }
            }else{
                $result = array('status' => '0', 'message' => 'user_id not found.');
            }
        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
       $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }	
	
	/**
     * @Method : Get Notification_status
     * @Param: none
     * */

    function get_notification_status() {
        $saveArray = $this->data;
        if (!empty($saveArray['user_id'])) {
            $user_id = $saveArray['user_id'];
			$condition = "User.id='" . $saveArray['user_id'] . "'";
            $user_exist = $this->User->find('first', array('conditions' => $condition));
            if ($user_exist) {
				$on="0";
				$off="1";
				$condition2 = "PushNotification.user_id='" . $saveArray['user_id'] . "'";
				$push_noti = $this->PushNotification->find('first', array('conditions' => $condition2));
				if(!empty($push_noti)){
					if ($push_noti['PushNotification']['notification_status'] == '1'){
						$notification_status='off';
					}
					if ($push_noti['PushNotification']['notification_status'] == '0'){
						$notification_status='on';
					}
					$result = array('status' => '1', 'message' => 'successfully','notification_status'=>$notification_status);
                }
				if(empty($push_noti)){
					$notification_status='on';
					$result = array('status' => '1', 'message' => 'successfully','notification_status'=>$notification_status);
				}
			 }else{
                $result = array('status' => '0', 'message' => 'user_id not found.');
            }
        }else{
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
       $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }	
	    #_____________________________________________________________________#	
		
	/**
     * @Method : get_public_group_list
     * @Param: none
     * */

    function get_public_group_list() {
        $saveArray = $this->data;
		$group_type = "Pb";
        $condition = "Group.group_type='". $group_type . "'";
            $public_groups= $this->Group->find('all', array('conditions' => $condition,
            	'fields' => ['id','name','qr_code','icon','address','has_helpdesk','country_code','mobile'],
            	'order'=>array('Group.id'=>'desc')));
            if ($public_groups) {
				foreach($public_groups as $public_groups){
					//pr($public_groups); die;
					$is_added = 'No';
					if(isset($saveArray['user_id'])){
						$exists = $this->GroupMember->find('first',[
							'conditions' => ['user_id' => $saveArray['user_id'] ,'group_id' => $public_groups['Group']['id'] ]
							]);
						if(count($exists) > 0 ){
							$is_added = 'Yes';
						}
					}
					$record[]= array(
						'group_id'=> $public_groups['Group']['id'],
						'group_name'=> $public_groups['Group']['name'],
						'qrcode'=> $public_groups['Group']['qr_code'],
						'group_icon'=> BASE_URL . "img/group_logo/" .$public_groups['Group']['icon'],
						'location'=> $public_groups['Group']['address'],
						'has_joined' => $is_added,
						'has_helpdesk' => $public_groups['Group']['has_helpdesk'],
						'country_code' => $public_groups['Group']['country_code'],
						'help_mobile' => $public_groups['Group']['mobile']
					);
				}
				$result = array('status'=>'1', 'message' => 'Succesfully','data'=>$record);
			}else{
            $result = array('status' => '0', 'message' => 'Public Group Not Found');
        }
       $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }	
	    #_____________________________________________________________________#	
		
			/**
     * @Method : get_services
     * @Param: none
     * */

    function get_services() {
        $saveArray = $this->data;
		 if (!empty($saveArray['group_id'])) {
			  $condition = "ServiceGroup.group_id='" . $saveArray['group_id'] . "'";
            $all_services= $this->ServiceGroup->find('all', array('conditions' => $condition));
			if(count($all_services) > 0){
			foreach($all_services as $all_services){
				$del = "0";
				$condition23="Service.deleted='".$del."' And Service.id='".$all_services['ServiceGroup']['service_id']."'";
			  $services=$this->Service->find('all',array('conditions'=> $condition23));
			  if(count($services) > 0){
			  		  foreach($services as $services){
						  $record1[]=array(
							'service_name'=>$services['Service']['name'],
							'service_link'=>$services['Service']['link'],
							'phone'=>$services['Service']['phone'],
							'service_icon'=>BASE_URL . "img/profile_images/" .$services['Service']['image'],
						  );
					  }
			  } else {
			  			$record1 =[];
			  }	
			
			}
			 if(count($record1) > 0){
			  	$result=array('status'=>'1','message'=>'Success','services'=> $record1);
			  } else{
			  	$result = array('status' => '0', 'message' => 'No data');
			  }
	     
	  }else{
		 $result = array('status' => '0', 'message' => 'No data');
	  }
		}else{
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
       $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }	
	    #_____________________________________________________________________#	
		
		 /**
     * @Method : delete_group
     * @Param: none
     * */

    function delete_group() {
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) && !empty($saveArray['group_id'])) {
           $group = $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
			if($group){
				if($group['Group']['default_group']=='1'){
					 $record1 = array('status' => '1', 'message' => 'You cannot delete this group,as it is default group.');
					 $result = array('result'=>$record1);
				}else{
					$del_user_id = $saveArray['user_id'];
					$user_id=$group['Group']['user_id'];
					$userarr=explode(',',$user_id);
					$exists = $this->GroupMember->find('first',[
							'conditions' => ['user_id' => $saveArray['user_id'] ,'group_id' => $group['Group']['id'] ]
							]);
					if(count($exists) > 0){
						$this->GroupMember->delete($exists['GroupMember']['id']);	
					}
					$userarr1 = [];
					foreach($userarr as $userarr){
						if($userarr == $del_user_id){
							continue;
						}else{
							$userarr1[]=$userarr;
						}
					}
					$imp_ids =implode(',',$userarr1);
					$update=$this->Group->updateAll(array('user_id'=>"'".$imp_ids."'"),array('id'=>$group['Group']['id']));
					if($update){
						$record = array('status' => '1', 'message' => 'Deleted Successfully.');
						$result = array('result'=>$record);
					}else{
						 $result = array('status' => '0', 'message' => 'Failed.');
					}
				}
			}else{
				$result = array('status' => '0', 'message' => 'Group not found.');
            }
        }else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        echo json_encode($result);
        die;
    }
	
	function help_api() {
	//	Configure::write('debug', 2);
        $saveArray = $this->data;
        if (!empty($saveArray['user_id']) AND !empty($saveArray['group_id']) AND !empty($saveArray['lat']) AND !empty($saveArray['lng'])){
				   $group = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
				  $staff_ids =  $this->GroupMember->find('list', array(
						         'conditions' => array('group_id'=>$group['Group']['id']),
						         'fields' => array('id','user_id')
						    ));
				   if((intval($group['Group']['id']) == 43 || intval($group['Group']['id']) == 44 ) ){
				   	   if(count($staff_ids) == 1){
				          $staffs = $this->User->find('all', array(
						         'conditions' => array('id'=>array_values($staff_ids)[0])
						    ));	
				        } else  if(count($staff_ids) > 0){
				          $staffs = $this->User->find('all', array(
						         'conditions' => array('id IN'=>array_values($staff_ids))
						    ));	
				        } else {
				        	$staffs = [];
				        }
				   	$total = count($staffs);
				   	$counter = 1;
				   		if(count($staffs) > 0 ){
				   				foreach ($staffs as $staff) {
						   			$locationdata = [];	
						   			$locationdata['sender_id'] = $saveArray['user_id'];
						   			$locationdata['receiver_id'] = $staff['User']['id'];
						   			$locationdata['group_id'] = $group['Group']['id'];
						   			$locationdata['type'] = "L";
						   			$locationdata['message'] ="";
						   			$locationdata['lat'] = $saveArray['lat'];
						   			$locationdata['lng'] = $saveArray['lng'];
						   			$this->Common->triggerchat($locationdata);
						   			$textdata = [];
						   			$textdata['sender_id'] = $saveArray['user_id'];
						   			$textdata['receiver_id'] = $staff['User']['id'];
						   			$textdata['group_id'] = $group['Group']['id'];
						   			$textdata['type'] = "T";
						   			$textdata['lat'] = '';
						   			$textdata['lng'] = '';
						   			$textdata['message'] ="PANIC: I need immediate help at the location!";
						   			$this->Common->triggerchat($textdata);
						   			if($total < $counter){
						   				$total++;	
						   			} else {
						   				//echo "done";
						   				$result = array('status' => '1', 'message' => 'Successfully Sent');
						   			}
						   			
						   		}
				   		} else {
				   			$result = array('status' => '1', 'message' => 'Successfully Sent');
				   		}
				   		
				   		//echo $total;
				   		//die;
				   	

				   } else {


				  // }
				 	//pr($group); die;
				//foreach($group as  $group){
					
					$messag2="";
					$saveArra['user_id']=$saveArray['user_id'];
					$saveArra['message']=$messag2;
					$saveArra['lat']=$saveArray['lat'];
					$saveArra['lng']=$saveArray['lng'];
					$saveArra['type']="L"; 
					
					if(isset($saveArray['timezone']))
					{
						date_default_timezone_set($saveArray['timezone']);
						$submit_timezone = $saveArray['timezone'];
						
					}
					else
					{
							date_default_timezone_set('Asia/Kolkata');
							$submit_timezone = 'Asia/Kolkata';
							
					}
					/* date_default_timezone_set('Asia/Kolkata');
					$date = date('Y-m-d H:i:s'); */
					
					$own = date('Y-m-d H:i:s');
                    $time = strtotime($own);
                    $time = $time - (6 * 60);
                    $date = date("Y-m-d H:i:s", $time);
					
					
					
					$saveArra['submit_time'] = $date;
					$saveArra['time_zone'] = $submit_timezone;
					$saveArra['group_id'] =$group['Group']['id'];
					$this->Groupchat->saveAll($saveArra,array('validate'=>false));
					$messag1="PANIC: I need immediate help at the location!";
					$saveArray['user_id']=$saveArray['user_id'];
					$saveArray['group_id']=$group['Group']['id'];
					$saveArray['message']=$messag1;
					$saveArray['lat']="";
					$saveArray['lng']="";
					$saveArray['type']="T";  
					/* date_default_timezone_set('Asia/Kolkata');
					$date = date('Y-m-d H:i:s'); */
					if(isset($saveArray['timezone']))
					{
						date_default_timezone_set($saveArray['timezone']);
						$submit_timezone = $saveArray['timezone'];
					}
					else
					{
						date_default_timezone_set('Asia/Kolkata');
						$submit_timezone = 'Asia/Kolkata';
							
					}
					$own = date('Y-m-d H:i:s');
                    $time = strtotime($own);
                    $time = $time - (6 * 60);
                    $date = date("Y-m-d H:i:s", $time);
					
					$saveArray['submit_time'] = $date;
					$saveArray['time_zone'] = $submit_timezone;
					$save =$this->Groupchat->saveAll($saveArray,array('validate'=>false));
					if($save){
						$sender = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['user_id'])));
						  $sender_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
$groupmsg_img ="";	
$for_noti = $group['Group']['user_id'];
        $userid = explode(',',$for_noti);
		if(!empty($userid)){
			$id='';
			foreach($userid as $use){
				if($use != $saveArray['user_id']){
				$query1 = "SELECT * FROM `users`
					WHERE (status = '1' AND deleted = '0' AND id = '".$use."' ) order by created DESC ";
                $user = $this->User->query($query1);
				foreach($user as $groupuser){
					$condition3 = "Block.friend_id='" . $sender['User']['id'] . "' And Block.user_id='" . $groupuser['users']['id'] . "' And Block.group_id='" . $saveArray['group_id'] . "' ";
					$data_exist45 = $this->Block->find('first', array('conditions' => $condition3));
					if(!empty($data_exist45)){
						$last_id =$this->Groupchat->getLastInsertId();
						$this->Groupchat->deleteAll(array('id' => $last_id), false);
					}if(empty($data_exist45)){
					$check_id = $groupuser['users']['id'];
					$check_group = $saveArray['group_id'];
					$condition2 = "CheckSetting.user_id='" . $groupuser['users']['id'] . "' And CheckSetting.group_id='" . $check_group . "' ";
					$data_exist4 = $this->CheckSetting->find('first', array('conditions' => $condition2));
					if(!empty($data_exist4)){
						if($data_exist4['CheckSetting']['group_chat']=='0'){	
						$udid = $groupuser['users']['device_id'];
						$did=$sender['User']['device_id'];
						if($udid != $did){
							// $message = array('message' => "You have new message from " . utf8_encode($sender['User']['name']) . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
							$message = array('message' => "You have a new Group Message from " . $sender['User']['name'] . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
												 'date' => $date,  'group_id' => $saveArray['group_id'], 'sender_name' => $sender['User']['name'], 'name' => $sender['User']['name'] , 
												'sender_image' => $sender_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
							if ($groupuser['users']['device_type'] == 'A') {
								if ($udid) {
									$type = "group";
									$android_ids = $udid;										
									$this->Common->android_send_notification(array($android_ids), $message, 'group');
								}
							}else{
                            if (!empty($udid)) {
                                $ios_ids = $udid;
								$c=strlen($ios_ids);
								$main=64;
								$cond= "PushNotification.user_id='" . $groupuser['users']['id'] . "'";
								$push_noti = $this->PushNotification->find('first', array('conditions' => $cond));
								if(!empty($push_noti)){
									if($push_noti['PushNotification']['notification_status']== '0'){
										if( $c == $main && $ios_ids != ""){
											$this->Common->iphone_send_notification($ios_ids, $message,1);
										}
									}
								}
								if(empty($push_noti)){
									//if($push_noti['PushNotification']['notification_status']== '0'){
										if( $c == $main && $ios_ids != ""){
											$this->Common->iphone_send_notification($ios_ids, $message,1);
										}
									//}
								}
							}
							}
							}
						} 
					}
					if(empty($data_exist4)){
						$udid = $groupuser['users']['device_id'];
						$did=$sender['User']['device_id'];
						if($udid != $did){
						 // $message = array('message' => "You have new message from " . utf8_encode($sender['User']['name']) . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
						 $message = array('message' => "You have a new Group Message from " . $sender['User']['name'] . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'group',
												 'date' => $date,  'group_id' => $saveArray['group_id'], 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
												'sender_image' => $sender_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
					if ($groupuser['users']['device_type'] == 'A') {
                                  
								   if ($udid) {

                                        $type = "group";
                                        $android_ids = $udid;										
                                        //$this->Common->android_send_notification(array($udid),$message,$type);
										$this->Common->android_send_notification(array($android_ids), $message, 'group');
                                    }
                                } else {
                                    if (!empty($udid)) {
                                        $ios_ids = $udid;
										$c=strlen($ios_ids);
				          $main=64;
							$cond= "PushNotification.user_id='" . $groupuser['users']['id'] . "'";
							$push_noti = $this->PushNotification->find('first', array('conditions' => $cond));
							if(!empty($push_noti)){
								if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								}
							}
							if(empty($push_noti)){
								//if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != ""){
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								//}
							}
				           // if( $c == $main && $ios_ids != ""){
										// $this->Common->iphone_send_notification($ios_ids, $message,1);
                                       
                                    // }
									
									
									
									
									
									
									}
					}
				}
			
						}
				}
				$id=$udid;
				}
                    
                   	
			}}}
$result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $groupmsg_img);
					}else{
						$result = array('status' => '0', 'message' => 'Failed.');
					}
				//} //Here
				}
				
		}else{
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
	}

    #_____________________________________________________________________#		
		
	 function ConvertTimezoneToAnotherTimezone($time, $currentTimezone, $timezoneRequired) {


        $system_timezone = date_default_timezone_get();
        $local_timezone = $currentTimezone;


        date_default_timezone_set($local_timezone);
        $local = date("Y-m-d H:i:s");

        date_default_timezone_set("GMT");
        $gmt = date("Y-m-d H:i:s");

        $require_timezone = $timezoneRequired;
        date_default_timezone_set($require_timezone);
        $required = date("Y-m-d H:i:s");

        date_default_timezone_set($system_timezone);

        $diff1 = (strtotime($gmt) - strtotime($local));
        $diff2 = (strtotime($required) - strtotime($gmt));

        $date = new DateTime($time);

        $date->modify("+$diff1 seconds");
        $date->modify("+$diff2 seconds");
        $timestamp = $date->format("Y-m-d H:i:s ");

        return $timestamp;
    }
	
	function find_users1()
    {
     	$saveArray = $this->data;
      	//pr($saveArray);
      	if (!empty($saveArray['group_id']) && !empty($saveArray['name']) ) 
      	{
      		$condition="Group.id='".$saveArray['group_id']."' ";
      		$groupdetail = $this->Group->find('first',array('conditions'=> $condition,'fields'=>array('id','user_id','name','fb_link','company_name','address','icon','image','type','allow_chat','allow_chat_private','internet_less_chat')));
      		/*$data['group_id'] = $groupdetail['Group']['id'];
      		$data['fb_link'] = $groupdetail['Group']['fb_link'];
            $data['group_name'] = $groupdetail['Group']['name'];
			$data['company_name'] = $groupdetail['Group']['company_name'];
			$data['address'] = $groupdetail['Group']['address'];
			$data['logo'] = BASE_URL . "img/group_logo/" . $groupdetail['Group']['icon'];
			$data['image'] = BASE_URL . "img/group_images/" . $groupdetail['Group']['image'];
			$data['type'] = $groupdetail['Group']['type'];
            $data['allow_chat'] = $groupdetail['Group']['allow_chat'];
            $data['allow_chat_private'] = $groupdetail['Group']['allow_chat_private'];
            $data['internet_less_chat'] =$groupdetail['Group']['internet_less_chat'];*/

            //pr($groupdetail);
            if (!empty($groupdetail))
            {
            	$user_id=explode(',', $groupdetail['Group']['user_id']);	pr($user_id);
      			$conditions1=[ 'name Like' =>'%'.$saveArray['name'].'%' ,'id IN' =>$user_id ]; 
      			$userdetail= $this->User->find('all',array('conditions'=>$conditions1));
      		 
      			if (!empty($userdetail))
      			{
                    $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $userdetail);
                }
                else
                {
				    $result =array('status' => '0', 'message' =>'Groups not found.');
                }
                /*$this->set(array('result' => $result,'_serialize' => array('result')
        ));
  			
      		    $result = array('status' => '1', 'message' => 'Successfully.', 'data' => $groupdetail);*/
            } 
            else
            {

                $result = array('status' => '0', 'message' => 'Groups not found.');
            }
      		 		
      		//pr($data1);
      	  		 
      	}
   	 	else
   	 	{

   	 		$result = array('status' => '0', 'message' => 'Please fill all fields');
   	 	}
   	 	$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
  			 	
	}
	
	
	function search_group_users(){
		$saveArray = $this->data;
		$sav="2";
        if (!empty($saveArray['name']) && !empty($saveArray['group_id']) && !empty($saveArray['page_no'])) {
			$condition = "GroupMember.group_id='" . $saveArray['group_id'] . "'";
			$start_limit = $saveArray['page_no']*10-10;
            $end_limit = 10;
			$sql ="SELECT * FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.name like  '".$saveArray['name']."%' and gm.group_id='".$saveArray['group_id']."' LIMIT $start_limit,$end_limit";
			$users = $this->User->query($sql);
			$sql1 ="SELECT count('id') as totalpage FROM `users` as usr INNER JOIN group_members as gm ON usr.id = gm.user_id  WHERE  usr.name like  '".$saveArray['name']."%' and gm.group_id='".$saveArray['group_id']."'";
			$page = $this->User->query($sql1);
			$page_count = $page[0][0]['totalpage'];	
            $page_count = $page_count / 10;
            $page_count = ceil($page_count);
			if(!empty($users)){
						foreach($users as $users){
							if(!empty($users['usr']['image'])){
								if($users['usr']['register_type']=='F'){
									$image = $users['usr']['image'];
								}  
								if($users['usr']['register_type']=='N'){
									$image = BASE_URL . "img/profile_images/" . $users['usr']['image'];
								}  
							}else{
								$image= BASE_URL."images/common/user_img_placeholder.png";
							}
							$query4 = "SELECT * FROM `groupinformations`
									WHERE (status = '1' AND deleted = '0' AND group_id = '".$users['gm']['group_id']."' AND user_id = '".$users['gm']['user_id']."') order by created DESC ";
									$condition3="Group.id='".$users['gm']['group_id']."' AND deleted = '0'";
									$group_type = $this->Group->find('first',array('conditions'=> $condition3));
									$condition23="Grouptype.type_name='".$group_type['Group']['type']."' AND deleted = '0'";
									$grouptypedetail = $this->Grouptype->find('first',array('conditions'=> $condition23,'fields'=>array('id','type_name','refrence')));
							$groupinfo = $this->Group->query($query4);
							if(!empty($groupinfo)){
								$dt = explode("-",$groupinfo[0]['groupinformations']['check_date']);
								$date_format =$dt[2]."-".$dt[1]."-".$dt[0];
								$seat_no =$groupinfo[0]['groupinformations']['seat_no'];
								$date = $date_format;
							}
							if(empty($groupinfo)){
								$seat_no ="";
								$date = "";
							}
							if($date==""){
								$date_time= explode(" ",$users['usr']['created']);
								$date12 = explode("-",$date_time[0]);
								$date14 = $date12[2]."-".$date12[1]."-".$date12[0];
								$date = $date14;
							}
							$block_status ="unblock";
							$record[]=array(
								'user_id'=>$users['usr']['id'],
								'name'=>$users['usr']['name'],
								'address'=>$users['usr']['address'],
								'designation'=>$users['usr']['designation'],
								'roomno'=>$seat_no,
								'date'=>$date,
								'refrence'=>$grouptypedetail['Grouptype']['refrence'],
								'image'=>$image,
								'type'=>$users['usr']['user_type'],
								'block_status'=>$block_status,
								'bluetooth_mac'=>$users['usr']['bluetooth_mac'],
								'gender'=>$users['usr']['gender'],
								'age'=>$users['usr']['age'],
							
							);
						}
						$result = array('status' => '1', 'message' => 'Successfully.','data' => $record ,'user_count'=>"0",'staff_count'=>0, 'user_count'=>"0",'totalPages'=>$page_count);
					}else{
						   $result = array('status' => '0', 'user_count'=>"0",'staff_count'=>0,'message' => 'No Group Users found.');
					}
				
		}else{
            $result = array('status' => '0', 'message' => 'Please fill all fields.');
        }

        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }


    	function get_welcome_message(){
				$saveArray = $this->data;
				
		        if (!empty($saveArray['group_id']) ) {
							$group = $this->Group->find('first',[
								'conditions' => ['id' => $saveArray['group_id'] ],	
								'fields' => ['id','welcome_message','welcome_message_small']
							]);
						 $result = array('status' => '1', 'welcome_message' => $group['Group']['welcome_message'],'welcome_message_small' => empty($group['Group']['welcome_message_small']) ? '' : $group['Group']['welcome_message_small'] );
				}else{
		            $result = array('status' => '0', 'message' => 'Please fill all fields.');
		        }

		        $this->set(array(
		            'result' => $result,
		            '_serialize' => array('result')
		        ));
    	}

    	



    	function testmail() {

	    	//Configure::write('debug', 2);
	        $saveArray = $this->data;
	       // pr($this->data); die;
	        //$this->Feedback->set($saveArray);
        if(!empty($saveArray['user_id'])){
        /* $isValidated = $this->User->validates();
        if ($isValidated) { */

            if (true){
               // $this->Feedback->save($saveArray,array('validate'=>false));

				$resetPassword = rand(100000,999999);
					$groupname = 'Test Group Name';
					$subject = "Feedback(".$groupname.")";
					$name    = 'Test Name';
					$senderemail = 'manyder@gmail.com';
					$username='The Username';
					$comment = 'Teskkdh Comment Is here Your Feedback is not awsome';
					$rating = 5;

					$this->Email->to       = trim($senderemail);
					$this->Email->subject  = $subject;
					$this->Email->replyTo  = ADMIN_EMAIL;
					$static_email  ="email@CoRover.mobi";
					//$this->Email->from     = trim($user_exist['User']['email']);
					$this->Email->from     = trim($static_email);
					$this->Email->bcc     = trim('dev.drawtopic@gmail.com');
					$this->Email->fromName = SITE_NAME;
					$this->Email->sendAs   = 'html';
					 $this->Email->template = 'feedback'; // note no '.ctp'
					//$this->Email->template('feedback', 'default');
					$mes  ="User has submitted feedback and rating.";
					 $this->set('vars', array('name' => $name,'subtitle' => $mes,'username' => $username,'group_name' => $groupname, 'comment' => $comment , 'rating' => $rating ,'base' => BASE_URL ) );
					//$this->Email->viewVars(array('name' => $name,'subtitle' => $mes,'username' => $username,'group_name' => $groupname, 'comment' => $comment , 'rating' => $rating  ));
					
					//$message = "Dear <span style='color:#666666'>".$name."</span>,<br/><br/>".$mes."<br/><br/>Feedback Details:<br/><br/>Username: ".$user_exist['User']['name']."<br/>Group Name: ".$group_exist['Group']['name']."<br/>Comment: ".$saveArray['comment']."<br/>Rating: ".$saveArray['rating']."";
					$this->Email->send();
                $result = array('status' => '1', 'message' => "Added Successfully", 'user_name' => $username,'group_name' => $groupname,'comment'=>$comment,'rating'=>$rating);
            } else {
                $result = array('status' => '0', 'message' => "Wrong Information");
            }
       /*  } else {
            $errors = $this->errorValidation('User');
            $result = array('status' => '0', 'message' => $errors);
        } */
		}else{
		$result=array('status'=>'0','message'=>'Please fill all fields');
		} 
       /*  echo json_encode($result);
        die; */
		  $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	function testnotification(){
		$saveArray = $this->data;
				  $message = array('message' => "You have new message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
	                 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],'name' => $sender['User']['name'],
                                        'friend_id' => $friend['User']['id'],'friend_type' => 'U','group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);


	}
	
	/* Time zone conversion function */
	function timezone_test($submited_time,$submited_time_zone,$user_time_zone)
	{
		if(!empty($submited_time_zone))
		{
			$submited_time_zone = $submited_time_zone;
		}
		else
		{
			 $submited_time_zone = "Asia/Kolkata";;
		}
		 
		$time= $submited_time;
		
		$dt = new DateTime($time, new DateTimeZone($submited_time_zone));
		$dt->setTimezone(new DateTimeZone($user_time_zone));
		//return  $dt->format('Y-m-d h:i a');
		       $timestamp = $dt->format("Y-m-d h:i");
			   $a = $timestamp ." " .$dt->format("A");
			   return $a;
		
	}
	
	
	
	/* second function*/
	/* Time zone conversion function */
	function timezone_test_get_chat($submited_time,$submited_time_zone,$user_time_zone)
	{
		
		if(!empty($submited_time_zone))
		{
			$submited_time_zone = $submited_time_zone;
		}
		else
		{
			 $submited_time_zone = "Asia/Kolkata";;
		}
		 
		$time= $submited_time;
		
		$dt = new DateTime($time, new DateTimeZone($submited_time_zone));
		$dt->setTimezone(new DateTimeZone($user_time_zone));
		//return  $dt->format('Y-m-d h:i a');
		       $timestamp = $dt->format("Y-m-d h:i");
			   $a = $timestamp ." " .$dt->format("A");
			   return $a;
		
	}
	/* second function*/
	
	
	function an($array)
   {
     $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

     foreach ($result as $key => $value)
     {
       if (is_array($value))
       {
         $result[$key] = an($value);
       }
     }

     return $result;
   }


   function save_group_badge($sender_id,$receiver_id,$group_id)

	{
		
		    $Badges= array();
			$Badges['sender_id'] = $sender_id;
			$Badges['receiver_id'] = $receiver_id;
			$Badges['group_id'] = $group_id;

		$this->Unreadgroupbadge->save($Badges);
		
			  
		
	}
	
	function new_broadcast()
	{
		$saveArray = $this->data;
		if(!empty($saveArray['user_id']) && !empty($saveArray['group_id']) ){
		//print_r($saveArray);
		$data_user = $this->Newbroadcast->find('first',array('conditions'=> array('Newbroadcast.group_id' => $saveArray['group_id'],'Newbroadcast.user_id' => $saveArray['user_id']),'fields' => array('message','image'),'order'=>array('Newbroadcast.id'=>"desc")
		));
		if(!empty($data_user))
		{
			
			
			 if($data_user['Newbroadcast']['image']== ""){

                  $img="";
                  
			 }else{

			 	$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user['Newbroadcast']['image'];
			 }

			 if($data_user['Newbroadcast']['message']== ""){

                  $msg="";

			 }else{

			 	$msg = $data_user['Newbroadcast']['message'];
			 }


			$record = array('image' =>$img,'message' => $msg);
			
		$result = array('status' =>'1','message' => 'Succesfully','result' => $record);	
		}
		
		else
		{
			$result = array('status' => '0','message' => 'Invalid User');
		}
		
		}
		
		else
		{
			$result = array('status' => '0','message' => 'Please fill all fields');
		}
		
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
		
	}
	
	/* APi For deactivate to report abuse  account */
	function report_abuse()
	{
		$saveArray = $this->data;
		if(!empty($saveArray['user_id']))
		{
			$condition="ReportAbuse.user_id='".$saveArray['user_id']."' ";
			$user_record=$this->ReportAbuse->find('all',array('conditions'=> $condition));
			
			if(count($user_record) < '3')
			{
				$save_data = ['user_id'=>$saveArray['user_id']];	
				$this->ReportAbuse->saveAll($save_data,array('validate'=>false));
				$result=array('status'=>'1','message'=>'successfully');
				$condition_1="ReportAbuse.user_id='".$saveArray['user_id']."' ";
				$user_record_again=$this->ReportAbuse->find('all',array('conditions'=> $condition_1));
				if(count($user_record_again) >= '3')
				{
					$status = "Deactivate";
					$this->User->updateAll(array('User.activate_status' => "'".$status."'"),array('User.id'=>$saveArray['user_id']));
					$result=array('status'=>'1','message'=>'successfully');	
				}
				
			}
			else
			{
				$result=array('status'=>'0','message'=>'Your Account Have De-Activated');	
			}
		}
		else
		{
			$result = array('status' => '0','message' => 'Please fill all fields');
		}
		
		$this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));

		
	}
	
	
	
}