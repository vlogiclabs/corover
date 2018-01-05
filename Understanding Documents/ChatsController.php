<?php

App::uses('AppController', 'Controller');
class ChatsController extends AppController 
{
	
	 var $name       	=  "Chats";
    /*
	* Specifies helpers classes used in the view pages
	* @access public
	*/
    public $helpers    	=  array('Html', 'Form', 'Js', 'Session','General','Paginator');
    /**
	* Specifies components classes used
	* @access public
    */
    var $components =  array('RequestHandler','Email','Common','Paginator','Upload');
    var $paginate  =  array();
    var $uses  =  array('Feedback','User','Group','Chat','GroupMember','GroupChat','SupportChat','Block','PushNotification','CheckSetting','SupportMember','SupportFeedback','UnreadNotifications'
	,'HelpdeskBadgesForApp','Newbroadcast'); // For Default Model
	/**********
	******
	*******
	*********/
	
	function getdata()
	{
		if($this->data){
			$saveArray = $this->data;
			$limit = '20';
			$offset = $this->data['offset'];
			//$this->autoRender = false;
			$userSession = $this->Session->read("SESSION_ADMIN");
			//pr($userSession); die;
			$this->set('userSession',$userSession); 
			if($this->data['type'] == 'init'){
				if($userSession['user_type'] == 'S')
				{
					$allgroups= $this->GroupMember->find('list',array('conditions'=> ['user_id' => $userSession['id'] ,'type' => 'S' ], 
													  'fields' => ['id','group_id']
													  ));
					//pr($allgroups); die;
					$groups= $this->Group->find('list',array('conditions'=> ['id' => array_values($allgroups) ,'has_helpdesk' => 'Yes' ], 
													  'fields' => ['id','id']
													  ));
					//pr($groups); die;
					$group_admins = $this->Group->find('list',array('conditions'=> ['id' => array_values($allgroups) ,'has_helpdesk' => 'Yes' ], 
													  'fields' => ['created_id','created_id']
													  ));
					$mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => array_values($groups) ,'type' => 'S' ], 
													  'fields' => ['id','user_id']
														  ));
					//pr($mygroupsmembers);
					if(count($mygroupsmembers) > 0)
					{
						$groupmembersdata = $this->User->find('list',array('conditions'=> ['id' => array_values($mygroupsmembers) , array('NOT' => array('id' => $userSession['id'] ) )	 ], 
													  'fields' => ['id','name']
													));	
					} else {
						$groupmembersdata = [];
					}
					
					//pr(); die;
					if(count($groups) > 0){
						/*$data1 = $this->SupportChat->find('all',[
							'conditions' => ['SupportChat.group_id' => array_values($groups),'SupportChat.is_closed '=>'No',
											 //'NOT' => array('Sender.id' => array_unique(array_values($mygroupsmembers)) ),
											'OR' => array(
												    array('SupportChat.receiver_id' => 0, 'SupportChat.is_taken' => 'No'),
												    //array('SupportChat.sender_id !=' => $userSession['id'] , 'SupportChat.is_taken' => 'Yes', 'SupportChat.taken_by' => $userSession['id'] )
												   // array('NOT' => array('Sender.id' => array_unique(array_merge(array_values($group_admins),array_values($mygroupsmembers))) ))
											    )
								 ],
							//'fields' => ['DISTINCT (SupportChat.sender_id) AS _id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.sender_id' , 'SupportChat.receiver_id','SupportChat.id', 'SupportChat.taken_by', 'SupportChat.is_taken'],
							'fields' => ['DISTINCT (SupportChat.group_id) AS _id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.sender_id' , 'SupportChat.receiver_id','SupportChat.id', 'SupportChat.taken_by', 'SupportChat.is_taken'],
							'group' => array('SupportChat.group_id'), // Comment By Maninder
							'order'=>array('SupportChat.id DESC')
						]);*/
							//print_r(array_values($groups));
							$data1 = $this->SupportChat->find('all',[
							'conditions' => ['SupportChat.group_id' => array_values($groups),'SupportChat.is_closed '=>'No',
							  'NOT' => array('SupportChat.receiver_id' => $userSession['id'] )	
								 				],
							//'fields' => ['DISTINCT (SupportChat.sender_id) AS _id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.sender_id' , 'SupportChat.receiver_id','SupportChat.id', 'SupportChat.taken_by', 'SupportChat.is_taken'],
							'fields' => ['DISTINCT (SupportChat.group_id) AS g_id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.sender_id' , 'SupportChat.receiver_id','SupportChat.id', 'SupportChat.taken_by', 'SupportChat.is_taken'],
							//'group' => array('SupportChat.group_id'), // Comment By Maninder
							'order'=>array('SupportChat.id DESC'),
						]);
						//print_r($data1); die;
						$data = [];
						$uc = [];
						foreach($data1 as $v) {
							 $cond = array('SupportChat.group_id'=>$v['Group']['id'], 'SupportChat.is_closed '=>'No',
                                    'OR' => array(
                                                array('SupportChat.sender_id' => $userSession['id'] , 'SupportChat.receiver_id' => $v['Sender']['id'] ),
                                                array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $v['Sender']['id'] ),
                                                array('SupportChat.sender_id' => $v['Sender']['id'], 'SupportChat.receiver_id' => $userSession['id'] ),
                                                array('SupportChat.sender_id' => $v['Sender']['id']),
                                            )
                                 );
							 /* $countcond = array('SupportChat.group_id'=>$v['Group']['id'], 'SupportChat.is_closed '=>'No', 'SupportChat.is_read'=>'No',
                                    'OR' => array(
                                               //array('SupportChat.sender_id' => $userSession['id'] , 'SupportChat.receiver_id' => $v['Sender']['id'] ),
                                                array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $v['Sender']['id'] ),
                                                array('SupportChat.sender_id' => $v['Sender']['id'], 'SupportChat.receiver_id' => $userSession['id'] ),
                                                array('SupportChat.sender_id' => $v['Sender']['id']),
                                                //array('SupportChat.receiver_id' => $v['Sender']['id']),
                                            )
                                 );
							  


							  $chatcount = $this->SupportChat->find('count',array(
		                                'conditions'=> $countcond,
		                                'fields' => ['SupportChat.id', 'SupportChat.is_read ']
		                             ));*/

		                     $delcond =   [ 
                                                    'group_id' => $v['Group']['id'],
                                                    'notification_to' => $userSession['id'],
                                                    'OR' => array(
                                                                array('UnreadNotifications.receiver_id' => $v['Sender']['id'] ),
                                                                array('UnreadNotifications.receiver_id' => 0 , 'UnreadNotifications.sender_id' => $v['Sender']['id'] ),
                                                                array('UnreadNotifications.sender_id' => $v['Sender']['id']),
                                                            )
                                                ]; 
                             $chatcount = $this->UnreadNotifications->find('count',array(
		                                'conditions'=> $delcond,
		                                'fields' => ['UnreadNotifications.id' ,'UnreadNotifications.notification_to']
		                             ));

							 $data2 = $this->SupportChat->find('first',array(
                                'conditions'=> $cond,
                                'fields' => ['SupportChat.created','SupportChat.submit_time','SupportChat.id','SupportChat.message','SupportChat.sender_id','SupportChat.receiver_id','SupportChat.lat','SupportChat.lng'],
                                'order'=>array('SupportChat.id DESC'),
                             ));
							 if(count($data2) > 0)
							 {
								$num = date_create($data2['SupportChat']['created'])->format('YmdHis');
								$date = new DateTime($data2['SupportChat']['submit_time'], new DateTimeZone('Asia/Kolkata'));

			                     $date->modify('-6 minutes');
			                     $usertimezone = 'Asia/Kolkata';
			                     date_default_timezone_set('Asia/Kolkata'); 


								$v['LastChat'] = $data2['SupportChat'];
								$v['LastChat']['datetime'] = $date->format('M j, Y, g:i a');
							}
							else 
							{
								$num = 0;
								$v['LastChat'] = [];
							}
							 //pr(); die;
							$v['SupportChat']['lat']= $data2['SupportChat']['lat'];
							$v['SupportChat']['lng']= $data2['SupportChat']['lng'];
							$v['SupportChat']['last_chat'] = $num;
							$v['SupportChat']['unread'] =  $chatcount;
							$v['Sender']['name'] = (empty($v['Sender']['name'])) ? 'Guest' : $v['Sender']['name']; 
							$v['Sender']['image'] = (empty($v['Sender']['image']) || (!is_file(getcwd()."/img/profile_images/".$v['Sender']['image']))) ? 'no-image.jpg' : $v['Sender']['image']; 
							if(isset($uc[$v['Sender']['id']])){
							//	print_r($uc);
								if($uc[$v['Sender']['id']] !==  $v['Group']['id']){
										//$data[] = $v;
										$uc[$v['Sender']['id']] =  $v['Group']['id'];		
								}
							} else {
								$data[] = $v;
								$uc[$v['Sender']['id']] =  $v['Group']['id'];
							}

							
							

						}

						//pr($data); die;

						$result = ['status'=>'1','data' => $data,'topuser' => $userSession['id'], 'groupmembersdata' => $groupmembersdata , 'feedbacks' => [] ];

					} else {
						$data =[];
						//$result = ['status'=>'1','data' => [] ];
					}

					if($saveArray['get_chat'] == 'Yes'){
						$resultdata = $this->Common->getuserchat($saveArray, $userSession,$this->data['type']);
						$lastchatcond = array('SupportChat.group_id'=>$saveArray['group_id'],
                                    'OR' => array(
                                                array('SupportChat.sender_id' => $userSession['id'] , 'SupportChat.receiver_id' => $saveArray['user_id'] ),
                                                array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['user_id'] ),
                                                array('SupportChat.sender_id' => $saveArray['user_id'] , 'SupportChat.receiver_id' => $userSession['id'] ),
                                                array('SupportChat.sender_id' => $saveArray['user_id']),
                                                array('SupportChat.receiver_id' => $saveArray['user_id']),
                                            )
                                 );
						$lastchat =	$this->SupportChat->find('first',[
							'conditions' => $lastchatcond,
							'fields' => ['SupportChat.receiver_id', 'SupportChat.sender_id','SupportChat.taken_by' ,'SupportChat.id','SupportChat.message','ChatTaker.id','ChatTaker.name','ChatTaker.image'],
							'order'=>array('SupportChat.id DESC')
					]);
						$result = ['status' => '1' , 'chatdata' => $resultdata['data'] , 'nextPage' => $resultdata['nextPage'] , 'data' =>  $data , 'topuser' => $userSession['id'] ,'groupmembersdata' => $groupmembersdata,'lastchat' => $lastchat ,'feedbacks' =>  $resultdata['feedbacks']];
					} else {
						
						$result = ['status'=>'1','data' => $data , 'topuser' => $userSession['id'], 'groupmembersdata' => $groupmembersdata,'lastchat' => [], 'feedbacks' => [] ];
					}



				}	
				} else if( $this->data['type'] == 'get_userchat' ){
				$mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => $saveArray['group_id'] ,'type' => 'S' ], 
													  'fields' => ['id','user_id']
									));
				//pr($mygroupsmembers); die;
				if(count($mygroupsmembers) > 0){
						$groupmembersdata = $this->User->find('list',array('conditions'=> ['id' => array_values($mygroupsmembers) , array('NOT' => array('id' => $userSession['id'] ) )	 ], 
													  'fields' => ['id','name']
													));	
					} else {
						$groupmembersdata = [];
					}

					$resultdata = $this->Common->getuserchat($saveArray, $userSession, $this->data['type']);
				 
					//$lastchat = "";

				$result = ['status' => '1' , 'data' => $resultdata['data'] , 'nextPage' => $resultdata['nextPage'] ,'currentPage' => $resultdata['currentPage'] ,'totalPages' => $resultdata['totalPages'] ,'feedbacks' =>  $resultdata['feedbacks'] , 'groupmembersdata' => $groupmembersdata];
;
			} else if( $this->data['type'] == 'paginate_chat' ){
				$resultdata = $this->Common->getuserchat($saveArray, $userSession,$this->data['type']);
				
					//$lastchat = "";
				$result = ['status' => '1' , 'data' => $resultdata['data'] , 'nextPage' => $resultdata['nextPage'],'currentPage' => $resultdata['currentPage'] ,'totalPages' => $resultdata['totalPages'] ,'feedbacks' =>  $resultdata['feedbacks'] ];
			}
			
			

			  $this->set(array(
	            'result' => $result,
	            '_serialize' => array('result')
	        ));
			/*$this->set(compact('result'));      //  
    		$this->set('_serialize', array('result'));  */

		}
	}

	function getreceiver(){
		  $saveArray = $this->data;	
		    if (!empty($saveArray['user_id']) AND  !empty($saveArray['group_id'])){
		    	$record = $this->SupportChat->find('first',[
		    			'conditions' => ['SupportChat.is_closed' => 'No', 'SupportChat.group_id'=>$saveArray['group_id'],
		    						'OR' => array(
											    array('SupportChat.sender_id' => $saveArray['user_id']),
											    array('SupportChat.receiver_id' => $saveArray['user_id'])
										    )
		    			]
		    		]);
		    	if(count($record) > 0){
		    		$receiver_id = ($record['SupportChat']['sender_id'] == $saveArray['user_id']) ? $record['SupportChat']['receiver_id'] :  $record['SupportChat']['sender_id'] ;
		    		$result = ['status'=>'1','receiver_id' =>  $receiver_id ];

		    	} else {
		    		$result = ['status'=>'1','receiver_id' =>  '0' ];
		    	}
		    	
		    } else {
		    	$result = array('status' => '0', 'message' => 'Please fill all fields');
		    }
		    $this->set(array(
	            'result' => $result,
	            '_serialize' => array('result')
	        ));
			
	}

	function chat() 
	{
        $saveArray = $this->data;
        //pr($saveArray); die;
		if(!empty($saveArray['receiver_id'] != '0'))
		{
			if (!empty($saveArray['sender_id']) AND  !empty($saveArray['group_id'])AND ! empty($saveArray['type'])
                AND ( ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) OR ( $saveArray['type'] == "I" AND !empty($_FILES['image']['name']))  OR ( $saveArray['type'] == "L" AND !empty($saveArray['lat']) AND !empty($saveArray['lng'])))) 
				{
					$is_taken = ($saveArray['sender_id'] == 'me') ? 'Yes' : 'No';
					$saveArray['sender_id'] = ($saveArray['sender_id'] == 'me') ? $this->Session->read("SESSION_ADMIN")['id'] : $saveArray['sender_id'];
					$id = $saveArray['sender_id'];
					$group_nm = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
					$group_name = $group_nm['Group']['name'];
					$sender = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['sender_id'])));
					$friend = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['receiver_id'])));
				if (!empty($sender))
					{
						$sender_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
						$friend_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
						$lastchat =	$this->SupportChat->find('first',[
							'conditions' => ['SupportChat.group_id' => $saveArray['group_id'], 'SupportChat.is_closed' => 'No',
											'OR' => array(
												    array('SupportChat.sender_id' => $saveArray['receiver_id'] , 'SupportChat.receiver_id' => $saveArray['sender_id'] ),
												    array('SupportChat.sender_id' => $saveArray['sender_id'] , 'SupportChat.receiver_id' => $saveArray['receiver_id'] )
											    )
								 ],
							'fields' => ['Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.sender_id' , 'SupportChat.receiver_id','SupportChat.id','SupportChat.is_taken','SupportChat.taken_by'],
							'order'=>array('SupportChat.id DESC')
					]);
            	//pr($lastchat); die;
            		/*$supportmember = $this->SupportMember->find('first',[
            			'conditions' => ['user_id' =>  $saveArray['sender_id'],   ]
            		]);*/
            		 $gm = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['sender_id'])]
                    	]);
            		 $sender_data = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['receiver_id'])]
                    	]);
                   	
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
				
					if(isset($saveArray['time']))
					{
						//date_default_timezone_set($saveArray['timezone']);
						$date = $saveArray['time'];
						
						
					}
					else
					{
						$own = date('Y-m-d H:i:s');
						$time = strtotime($own);
						$time = $time - (6 * 60);
						$date = date("Y-m-d H:i:s", $time); 

					}
					
					
					
					
                    $saveArray['submit_time'] = $date;
					$saveArray['time_zone'] = $submit_time_zone;
                    if ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) 
					{
                        $saveArray['image'] = "";
                    }
					 if ($saveArray['type'] == "L" AND ! empty($saveArray['lat']) AND ! empty($saveArray['lng'])){
						$saveArray['message'] = "";
                        $saveArr['message'] = $saveArray['message'];
                        $saveArr['image'] = "";
						$saveArr['submit_time'] = $date;
						$saveArr['time_zone'] = $submit_time_zone;
						$saveArr['sender_id'] =$saveArray['sender_id'];
						$saveArr['receiver_id'] = (empty($saveArray['receiver_id'])) ? 0 :  $saveArray['receiver_id'];
						$saveArr['group_id'] =$saveArray['group_id'];
						$saveArr['type'] =$saveArray['type'];
						$saveArr['lat'] =$saveArray['lat'];
						$saveArr['lng'] =$saveArray['lng'];
						$groupmsg_img ="";
						$this->SupportChat->save($saveArr,array('validate'=>false));
					}	
                    if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
					 $destination= WWW_ROOT . 'img/groupchatimg/' ;
                        if (!empty($_FILES['image']['name'])) {
                            $r = rand(1, 99999);
                            $saveArray['message'] = " ";
                            $saveArray['image'] = $this->uploadPic($r, $destination,$_FILES['image']);
                        }
                    }
                    if($is_taken == 'Yes'){
            	 		$saveArray['taken_by'] =$saveArray['sender_id'];
            	 		$saveArray['is_taken'] = 'Yes';	
            	 			//$saveArray['is_read'] = 'Yes'; // IF SENDER IS SUPPORT CHAT MEMBER.
            	 	} else {
            	 		if(count($lastchat) > 0){
            	 			$saveArray['taken_by'] = $lastchat['SupportChat']['taken_by'];
            	 			$saveArray['is_taken'] = $lastchat['SupportChat']['is_taken'];
            	 		}
            	 		
            	 	}
            	 	//pr($lastchat);die;
                    $groupchat_id = $this->SupportChat->save($saveArray,array('validate'=>false));
                    $lastchatdata = $this->SupportChat->find('first',[
								'conditions' => ['SupportChat.id' => $groupchat_id['SupportChat']['id'], 
									 ],
								'fields' => ['SupportChat.receiver_id', 'SupportChat.sender_id' ,'SupportChat.id','SupportChat.message','SupportChat.type' ,'SupportChat.group_id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.taken_by', 'SupportChat.is_taken' , 'SupportChat.submit_time', 'SupportChat.is_closed','SupportChat.is_read'],
						]);
                    $mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => $groupchat_id['SupportChat']['group_id'] ,'type' => 'S' ], 
													  'fields' => ['id','user_id']
										));
                   
				   
				   	$allmembers = $this->GroupMember->find('all',array('conditions'=>array('group_id'=>$saveArray['group_id'])));
					  for($i=0;$i<count($allmembers);$i++)
					   {
							if($allmembers[$i]['GroupMember']['user_id'] != $saveArray['sender_id'] )
							{
								$table_name="HelpdeskBadgesForApp";
								$this->request->data[$table_name]['sender_id'] =$saveArray['sender_id'];
								$this->request->data[$table_name]['receiver_id'] = $allmembers[$i]['GroupMember']['user_id'];
								$this->request->data[$table_name]['group_id'] = $saveArray['group_id'];
								$this->request->data[$table_name]['for_check'] = "home";
								$this->$table_name->saveAll($this->request->data);
								
								$table_name="HelpdeskBadgesForApp";
								$this->request->data[$table_name]['sender_id'] =$saveArray['sender_id'];
								$this->request->data[$table_name]['receiver_id'] = $allmembers[$i]['GroupMember']['user_id'];
								$this->request->data[$table_name]['group_id'] = $saveArray['group_id'];
								$this->request->data[$table_name]['for_check'] = "private";
								$this->$table_name->saveAll($this->request->data);
								
							}  
					   }
		   
		   
                    $groupmembersids = (count($mygroupsmembers) > 0) ? array_values($mygroupsmembers) : [];
                    if(count($groupmembersids) > 0){
                    	//pr($groupmembersids); die;
                    	$notificationdata = [];
                    	foreach ($groupmembersids as  $member_id) {
                    		// CHecking if not adding notification for Himself.
                    		
                    		if($saveArray['sender_id'] !== $member_id)
							{
							$groupmember_Detail = $this->User->find('first',['conditions'=>['id'=>$member_id],'fields'=>['id','device_id','device_type']]);
							
							$group_nm_1 = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
							$group_name_1 = $group_nm_1['Group']['name'];
							
							
							$user_detail_1 = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['sender_id'])));
							
							$user_detail_name = $user_detail_1['User']['image'];
							
							
							$friend_detail_1 = $this->User->find('first', array("conditions" => array('User.id' =>$saveArray['receiver_id'])));
							
							$friend_detail_name = $friend_detail_1['User']['name'];
							
							$friend_detail_id = $friend_detail_1['User']['id'];
							$friend_detail_type = $friend_detail_1['User']['user_type'];
							$friend_detail_image = $friend_detail_1['User']['image'];
							
							
							
							if($groupmember_Detail)
							{
								$message = array('message' => "You have a new Support Message", 
								'sender_id' => $saveArray['sender_id'],
								'receiver_id' => $saveArray['receiver_id'],
								'noti_for' => 'helpdesk',
								  'group_name'=>$group_name_1,
									'country_code'=>"",
									'mobile'=>"",
									'image' => "",
                                        'date' => "", 'sender_name' =>"",
                                        'sender_image' =>$user_detail_name,'friend_name' =>$friend_detail_name, 'name' => $friend_detail_name,
										'friend_id' =>$friend_detail_id,'friend_type' =>$friend_detail_type,'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_detail_image, 'message_img' => "", 'message_msg' =>"",'broadcast_message'=>"",'broadcast_image'=>"");
								if ($groupmember_Detail['User']['device_type']=='A') 
								{
										//pr($groupmember_Detail['User']['device_id']);
										$this->Common->android_send_notification(array($groupmember_Detail['User']['device_id']), $message);
                                }
									
								//pr($member_id); die;
                    			$notificationdata[] = [
                    							'sender_id' => $saveArray['sender_id'],
                    							'receiver_id' => $saveArray['receiver_id'],
                    							'group_id' => $saveArray['group_id'],
                    							'record_id' =>  $groupchat_id['SupportChat']['id'],
                    							'notification_to' => $member_id
                    							];
                    			//pr($notificationdata);
								}
                    					
                    		}
							//print_r($message);
                    	}
                    	//pr($notificationdata);
                    	$this->UnreadNotifications->saveAll($notificationdata);
                    	//die;
						//print_r($message);
                    }
                    $date = new DateTime($lastchatdata['SupportChat']['submit_time'], new DateTimeZone('Asia/Kolkata'));
                    $date->modify('-1 minutes');
                    $currentDateTime = $date->format('Y-m-d H:i:s');
					
                    $usertimezone = 'Asia/Kolkata';
                    date_default_timezone_set('Asia/Kolkata');
                    $lastchatdata['SupportChat']['date'] = $date->format('M j, Y, g:i a');
                    $lastchatdata['Sender']['name'] = (empty($lastchatdata['Sender']['name'])) ? 'Guest' : $lastchatdata['Sender']['name']; 
                    $lastchatdata['Sender']['image'] = (empty($lastchatdata['Sender']['image'])) ? 'no-image.jpg' : $lastchatdata['Sender']['image']; 


                   	if(count($lastchat) > 0)
					{
                   			// IF CHAT IS NOT TAKEN BY ANYBODY
                   		//pr($lastchat['SupportChat']['is_taken']); die;
            	 		if($lastchat['SupportChat']['is_taken'] == 'No')
						{
            	 			// IF MESSAGE IS SENT FROM WEBEND
            	 			if($is_taken == 'Yes')
							{
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $saveArray['sender_id'];
	            	 			$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$records = 	$this->SupportChat->find('all',[
											'conditions' => ['SupportChat.group_id' => $saveArray['group_id'],'SupportChat.sender_id' => $saveArray['receiver_id'] , 'SupportChat.receiver_id' => 0, 'SupportChat.is_taken' => 'No' ]
											]);
	            	 			//pr($records); die;
	            	 			//$records[] = $groupchat_id;
	            	 			foreach($records as $record)
								{
									$this->SupportChat->read(null,$record['SupportChat']['id']);
									$this->SupportChat->set($savedata);
									$this->SupportChat->save();
	            	 			}	
            	 			}
            	 			
            	 		} /*else {
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $lastchat['SupportChat']['taken_by'];
	            	 			//$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$g = $this->SupportChat->read(null,$groupchat_id['SupportChat']['id']);
	            	 			//pr($g); die;
								$this->SupportChat->set($savedata);
								$this->SupportChat->save();
            	 		}*/
            	 	}

            	 	/*if($is_taken == 'Yes'){
            	 				//IF CHAT IS ALREADY TAKEN BY SOMEONE , WE JUST UPDATE THE CURRENT TAKER DATA IN CURRENT RECORD
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $lastchat['SupportChat']['taken_by'];
	            	 			//$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$this->SupportChat->read(null,$groupchat_id['SupportChat']['id']);
								$this->SupportChat->set($savedata);
								$this->SupportChat->save();
            	 		} */

                   
                    if ($saveArray['type'] == "T" && !empty($saveArray['message']))
						{
                        $groupmsg_img = '';
                    } else if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
                        $getGroupChatImage = $this->SupportChat->find('first', array('conditions' => array('id' => $groupchat_id['SupportChat']['id'])));
                        if (!empty($getGroupChatImage)) {
                            $groupmsg_img = BASE_URL . "img/groupchatimg/" . $getGroupChatImage['SupportChat']['image'];
                        } else {
                            $groupmsg_img = '';
                        }
                    }
				
				if($friend){
				 $condition3 = "Block.friend_id='" . $saveArray['sender_id'] . "' And Block.user_id='" . $saveArray['receiver_id'] . "' And Block.group_id='" . $saveArray['group_id'] . "' ";
					$data_exist45 = $this->Block->find('first', array('conditions' => $condition3));
					if(!empty($data_exist45)){
						/* $last_id =$this->SupportChat->getLastInsertId();
						$this->SupportChat->deleteAll(array('id' => $last_id), false);*/
					}
					if(empty($data_exist45))
					{
				 $udid = $friend['User']['device_id'];
	
				 $did=$sender['User']['device_id'];
				  $check_id = $friend['User']['id'];
					$check_group = $saveArray['group_id'];
					$condition2 = "CheckSetting.user_id='" . $check_id . "' And CheckSetting.group_id='" . $check_group . "' ";
					$data_exist4 = $this->CheckSetting->find('first', array('conditions' => $condition2));
					if(!empty($data_exist4))
					{
						if(!empty($data_user_valid))
							{
							    if($data_user_valid['SupportChat']['image']== "")
								{
					                  $img="";
								}
								else
								{
									$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user_valid['SupportChat']['image'];
								}

								 if($data_user_valid['SupportChat']['message']== "")
								 {

					                  $msg="";

								 }
								 else
								 {
								 	$msg = $data_user_valid['SupportChat']['message'];
								 }

							}
							else
							{
								$msg="";
								$img="";
							}
							/* new code end*/
							
					if($data_exist4['CheckSetting']['private_chat']=='0')
					{
						
					if($udid != $did)
					{

				 
					if($sender['User']['login_type']=='G')
					{
                     $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
							 'group_name'=>$group_nm['Group']['name'],
								'country_code'=>$group_nm['Group']['country_code'],
								'mobile'=>$group_nm['Group']['mobile'],
								'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
				   }
				   else
				   {
                         $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);               
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
					if(empty($data_exist4))
					{
						
						/* new code start*/
						$data_user_valid = $this->SupportChat->find('first',array('conditions'=> array('SupportChat.group_id' => $saveArray['group_id'],'SupportChat.sender_id' => $saveArray['sender_id'],'SupportChat.receiver_id' => $saveArray['receiver_id']),'fields' => array('message','image'),'order'=>array('SupportChat.id'=>"desc")
		                  ));
						 

					     if(!empty($data_user_valid))
							{
							    if($data_user_valid['SupportChat']['image']== "")
								{
					                  $img="";
								}
								else
								{
									$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user_valid['SupportChat']['image'];
								}

								 if($data_user_valid['SupportChat']['message']== "")
								 {

					                  $msg="";

								 }
								 else
								 {
								 	$msg = $data_user_valid['SupportChat']['message'];
								 }

							}
							else
							{
								$msg="";
								$img="";
							}
							/* new code end*/
						
						
						
						
					
						
				if($udid != $did){
			
										if($sender['User']['login_type']=='G')
										{
				  $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				                         'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
				   }else{
					    $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
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


                    $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $groupmsg_img,'last_id' => $lastchatdata );
              // } else {
               //     $result = array('status' => '0', 'message' => 'You can not send group message in this //trip.');
             //   }
            } else {
                $result = array('status' => '0', 'message' => 'User id does not exist');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
		}
		else
			{
			if (!empty($saveArray['sender_id']) AND  !empty($saveArray['group_id'])AND ! empty($saveArray['type'])
                AND ( ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) OR ( $saveArray['type'] == "I" AND !empty($_FILES['image']['name']))  OR ( $saveArray['type'] == "L" AND !empty($saveArray['lat']) AND !empty($saveArray['lng'])))) 
				{
					$is_taken = ($saveArray['sender_id'] == 'me') ? 'Yes' : 'No';
					$saveArray['sender_id'] = ($saveArray['sender_id'] == 'me') ? $this->Session->read("SESSION_ADMIN")['id'] : $saveArray['sender_id'];
					$id = $saveArray['sender_id'];
					$group_nm = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
					$group_name = $group_nm['Group']['name'];
					$sender = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['sender_id'])));
					$friend = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['receiver_id'])));
				if (!empty($sender))
					{
						$sender_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
						$friend_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
						$lastchat =	$this->SupportChat->find('first',[
							'conditions' => ['SupportChat.group_id' => $saveArray['group_id'], 'SupportChat.is_closed' => 'No',
											'OR' => array(
												    array('SupportChat.sender_id' => $saveArray['receiver_id'] , 'SupportChat.receiver_id' => $saveArray['sender_id'] ),
												    array('SupportChat.sender_id' => $saveArray['sender_id'] , 'SupportChat.receiver_id' => $saveArray['receiver_id'] )
											    )
								 ],
							'fields' => ['Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.sender_id' , 'SupportChat.receiver_id','SupportChat.id','SupportChat.is_taken','SupportChat.taken_by'],
							'order'=>array('SupportChat.id DESC')
					]);
            	//pr($lastchat); die;
            		/*$supportmember = $this->SupportMember->find('first',[
            			'conditions' => ['user_id' =>  $saveArray['sender_id'],   ]
            		]);*/
            		 $gm = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['sender_id'])]
                    	]);
            		 $sender_data = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['receiver_id'])]
                    	]);
                   	
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
				
					if(isset($saveArray['time']))
					{
						//date_default_timezone_set($saveArray['timezone']);
						$date = $saveArray['time'];
						
						
					}
					else
					{
						$own = date('Y-m-d H:i:s');
						$time = strtotime($own);
						$time = $time - (6 * 60);
						$date = date("Y-m-d H:i:s", $time); 

					}
				 
					
					
					
                    $saveArray['submit_time'] = $date;
					$saveArray['time_zone'] = $submit_time_zone;
                    if ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) 
					{
                        $saveArray['image'] = "";
                    }
					 if ($saveArray['type'] == "L" AND ! empty($saveArray['lat']) AND ! empty($saveArray['lng'])){
						$saveArray['message'] = "";
                        $saveArr['message'] = $saveArray['message'];
                        $saveArr['image'] = "";
						$saveArr['submit_time'] = $date;
						
						
						
						$saveArr['time_zone'] = $submit_time_zone;
						$saveArr['sender_id'] =$saveArray['sender_id'];
						$saveArr['receiver_id'] = (empty($saveArray['receiver_id'])) ? 0 :  $saveArray['receiver_id'];
						$saveArr['group_id'] =$saveArray['group_id'];
						$saveArr['type'] =$saveArray['type'];
						$saveArr['lat'] =$saveArray['lat'];
						$saveArr['lng'] =$saveArray['lng'];
						$groupmsg_img ="";
						$this->SupportChat->save($saveArr,array('validate'=>false));
					}	
                    if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
					 $destination= WWW_ROOT . 'img/groupchatimg/' ;
                        if (!empty($_FILES['image']['name'])) {
                            $r = rand(1, 99999);
                            $saveArray['message'] = " ";
                            $saveArray['image'] = $this->uploadPic($r, $destination,$_FILES['image']);
                        }
                    }
                    if($is_taken == 'Yes')
					{
            	 		$saveArray['taken_by'] =$saveArray['sender_id'];
            	 		$saveArray['is_taken'] = 'Yes';	
            	 			//$saveArray['is_read'] = 'Yes'; // IF SENDER IS SUPPORT CHAT MEMBER.
            	 	} 
					else 
					{
            	 		if(count($lastchat) > 0)
						{
            	 			$saveArray['taken_by'] = $lastchat['SupportChat']['taken_by'];
            	 			$saveArray['is_taken'] = $lastchat['SupportChat']['is_taken'];
            	 		}
            	 		
            	 	}
            	 	//pr($lastchat);die;
                    $groupchat_id = $this->SupportChat->save($saveArray,array('validate'=>false));
                    $lastchatdata = $this->SupportChat->find('first',[
								'conditions' => ['SupportChat.id' => $groupchat_id['SupportChat']['id'], 
									 ],
								'fields' => ['SupportChat.receiver_id', 'SupportChat.sender_id' ,'SupportChat.id','SupportChat.message','SupportChat.type' ,'SupportChat.group_id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.taken_by', 'SupportChat.is_taken' , 'SupportChat.submit_time', 'SupportChat.is_closed','SupportChat.is_read'],
						]);
                    $mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => $groupchat_id['SupportChat']['group_id'] ,'type' => 'S' ], 
													  'fields' => ['id','user_id']
										));
                   
				   
				   	$allmembers = $this->GroupMember->find('all',array('conditions'=>array('group_id'=>$saveArray['group_id'])));
					  for($i=0;$i<count($allmembers);$i++)
					   {
							if($allmembers[$i]['GroupMember']['user_id'] != $saveArray['sender_id'] )
							{
								$table_name="HelpdeskBadgesForApp";
								$this->request->data[$table_name]['sender_id'] =$saveArray['sender_id'];
								$this->request->data[$table_name]['receiver_id'] = $allmembers[$i]['GroupMember']['user_id'];
								$this->request->data[$table_name]['group_id'] = $saveArray['group_id'];
								$this->request->data[$table_name]['for_check'] = "home";
								$this->$table_name->saveAll($this->request->data);
								
								$table_name="HelpdeskBadgesForApp";
								$this->request->data[$table_name]['sender_id'] =$saveArray['sender_id'];
								$this->request->data[$table_name]['receiver_id'] = $allmembers[$i]['GroupMember']['user_id'];
								$this->request->data[$table_name]['group_id'] = $saveArray['group_id'];
								$this->request->data[$table_name]['for_check'] = "private";
								$this->$table_name->saveAll($this->request->data);
								
							}  
					   }
		   
		   
                    $groupmembersids = (count($mygroupsmembers) > 0) ? array_values($mygroupsmembers) : [];
                    if(count($groupmembersids) > 0){
                    	//pr($groupmembersids); die;
                    	$notificationdata = [];
                    	foreach ($groupmembersids as  $member_id) {
                    		// CHecking if not adding notification for Himself.
                    		
                    		if($saveArray['sender_id'] !== $member_id)
							{
							$groupmember_Detail = $this->User->find('first',['conditions'=>['id'=>$member_id],'fields'=>['id','device_id','device_type']]);
							
							$group_nm_1 = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
							$group_name_1 = $group_nm_1['Group']['name'];
							
							
							$user_detail_1 = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['sender_id'])));
							
							$user_detail_name = $user_detail_1['User']['image'];
							
							
							//$friend_detail_1 = $this->User->find('first', array("conditions" => array('User.id' =>$saveArray['receiver_id'])));
							
							$friend_detail_name = " ";
							//$friend_detail_1['User']['name'];
							
							$friend_detail_id =  " ";
							//$friend_detail_1['User']['id'];
							$friend_detail_type = " ";
							//$friend_detail_1['User']['user_type'];
							$friend_detail_image = " ";
							//$friend_detail_1['User']['image'];
							
							
							
							if($groupmember_Detail)
							{
								$message=array('message' => "You have a new Support Message", 
								'sender_id' => $saveArray['sender_id'],
								'receiver_id' => $saveArray['receiver_id'],
								'noti_for' => 'helpdesk',
								  'group_name'=>$group_name_1,
									'country_code'=>"",
									'mobile'=>"",
									'image' => "",
                                         'date' => "", 'sender_name' =>"",
                                        'sender_image' =>$user_detail_name,'friend_name' =>$friend_detail_name, 'name' => $friend_detail_name,
										'friend_id' =>$friend_detail_id,'friend_type' =>$friend_detail_type,'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_detail_image, 'message_img' => "", 'message_msg' =>"",'broadcast_message'=>"",'broadcast_image'=>"");
								if ($groupmember_Detail['User']['device_type']=='A') 
								{
										//pr($groupmember_Detail['User']['device_id']);
										$this->Common->android_send_notification(array($groupmember_Detail['User']['device_id']), $message);
                                }
									
								//pr($member_id); die;
                    			$notificationdata[] = [
                    							'sender_id' => $saveArray['sender_id'],
                    							'receiver_id' => $saveArray['receiver_id'],
                    							'group_id' => $saveArray['group_id'],
                    							'record_id' =>  $groupchat_id['SupportChat']['id'],
                    							'notification_to' => $member_id
                    							];
                    			//pr($notificationdata);
								}
                    					
                    		}
							//print_r($message);
                    	}
                    	//pr($notificationdata);
                    	$this->UnreadNotifications->saveAll($notificationdata);
                    	//die;
						//print_r($message);
                    }
                    $date = new DateTime($lastchatdata['SupportChat']['submit_time'], new DateTimeZone('Asia/Kolkata'));
                    $date->modify('-5 minutes');
                    $currentDateTime = $date->format('Y-m-d H:i:s');
					
                    $usertimezone = 'Asia/Kolkata';
                    date_default_timezone_set('Asia/Kolkata');
                    $lastchatdata['SupportChat']['date'] = $date->format('M j, Y, g:i a');
                    $lastchatdata['Sender']['name'] = (empty($lastchatdata['Sender']['name'])) ? 'Guest' : $lastchatdata['Sender']['name']; 
                    $lastchatdata['Sender']['image'] = (empty($lastchatdata['Sender']['image'])) ? 'no-image.jpg' : $lastchatdata['Sender']['image']; 


                   	if(count($lastchat) > 0)
					{
                   			// IF CHAT IS NOT TAKEN BY ANYBODY
                   		//pr($lastchat['SupportChat']['is_taken']); die;
            	 		if($lastchat['SupportChat']['is_taken'] == 'No')
						{
            	 			// IF MESSAGE IS SENT FROM WEBEND
            	 			if($is_taken == 'Yes')
							{
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $saveArray['sender_id'];
	            	 			$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$records = 	$this->SupportChat->find('all',[
											'conditions' => ['SupportChat.group_id' => $saveArray['group_id'],'SupportChat.sender_id' => $saveArray['receiver_id'] , 'SupportChat.receiver_id' => 0, 'SupportChat.is_taken' => 'No' ]
											]);
	            	 			//pr($records); die;
	            	 			//$records[] = $groupchat_id;
	            	 			foreach($records as $record)
								{
									$this->SupportChat->read(null,$record['SupportChat']['id']);
									$this->SupportChat->set($savedata);
									$this->SupportChat->save();
	            	 			}	
            	 			}
            	 			
            	 		} 
            	 	}

            	 	
                   
                    if ($saveArray['type'] == "T" && !empty($saveArray['message']))
						{
                        $groupmsg_img = '';
                    } else if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
                        $getGroupChatImage = $this->SupportChat->find('first', array('conditions' => array('id' => $groupchat_id['SupportChat']['id'])));
                        if (!empty($getGroupChatImage)) {
                            $groupmsg_img = BASE_URL . "img/groupchatimg/" . $getGroupChatImage['SupportChat']['image'];
                        } else {
                            $groupmsg_img = '';
                        }
                    }
				
				if($friend){
				 $condition3 = "Block.friend_id='" . $saveArray['sender_id'] . "' And Block.user_id='" . $saveArray['receiver_id'] . "' And Block.group_id='" . $saveArray['group_id'] . "' ";
					$data_exist45 = $this->Block->find('first', array('conditions' => $condition3));
					if(!empty($data_exist45)){
						/* $last_id =$this->SupportChat->getLastInsertId();
						$this->SupportChat->deleteAll(array('id' => $last_id), false);*/
					}
					if(empty($data_exist45))
					{
				 $udid = $friend['User']['device_id'];
	
				 $did=$sender['User']['device_id'];
				  $check_id = $friend['User']['id'];
					$check_group = $saveArray['group_id'];
					$condition2 = "CheckSetting.user_id='" . $check_id . "' And CheckSetting.group_id='" . $check_group . "' ";
					$data_exist4 = $this->CheckSetting->find('first', array('conditions' => $condition2));
					if(!empty($data_exist4))
					{
						if(!empty($data_user_valid))
							{
							    if($data_user_valid['SupportChat']['image']== "")
								{
					                  $img="";
								}
								else
								{
									$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user_valid['SupportChat']['image'];
								}

								 if($data_user_valid['SupportChat']['message']== "")
								 {

					                  $msg="";

								 }
								 else
								 {
								 	$msg = $data_user_valid['SupportChat']['message'];
								 }

							}
							else
							{
								$msg="";
								$img="";
							}
							/* new code end*/
							
					if($data_exist4['CheckSetting']['private_chat']=='0')
					{
						
					if($udid != $did)
					{

				 
					if($sender['User']['login_type']=='G')
					{
                     $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
							 'group_name'=>$group_nm['Group']['name'],
								'country_code'=>$group_nm['Group']['country_code'],
								'mobile'=>$group_nm['Group']['mobile'],
								'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
				   }
				   else
				   {
                         $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);               
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
					if(empty($data_exist4))
					{
						
						/* new code start*/
						$data_user_valid = $this->SupportChat->find('first',array('conditions'=> array('SupportChat.group_id' => $saveArray['group_id'],'SupportChat.sender_id' => $saveArray['sender_id'],'SupportChat.receiver_id' => $saveArray['receiver_id']),'fields' => array('message','image'),'order'=>array('SupportChat.id'=>"desc")
		                  ));
						 

					     if(!empty($data_user_valid))
							{
							    if($data_user_valid['SupportChat']['image']== "")
								{
					                  $img="";
								}
								else
								{
									$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user_valid['SupportChat']['image'];
								}

								 if($data_user_valid['SupportChat']['message']== "")
								 {

					                  $msg="";

								 }
								 else
								 {
								 	$msg = $data_user_valid['SupportChat']['message'];
								 }

							}
							else
							{
								$msg="";
								$img="";
							}
							/* new code end*/
						
						
						
						
					
						
				if($udid != $did){
			
										if($sender['User']['login_type']=='G')
										{
				  $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				                         'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
				   }else{
					    $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
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


                    $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $groupmsg_img,'last_id' => $lastchatdata );
              // } else {
               //     $result = array('status' => '0', 'message' => 'You can not send group message in this //trip.');
             //   }
            } else {
                $result = array('status' => '0', 'message' => 'User id does not exist');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
		}
		
	
        
        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	/* Api for helpdesk group messgae */
	function helpdesk_chat()
	{
		
        $saveArray = $this->data;
		
        //pr($saveArray); die;
        if (!empty($saveArray['sender_id']) AND  !empty($saveArray['group_id'])AND ! empty($saveArray['type'])
                AND ( ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) OR ( $saveArray['type'] == "I" AND !empty($_FILES['image']['name']))  OR ( $saveArray['type'] == "L" AND !empty($saveArray['lat']) AND !empty($saveArray['lng'])))) {
        	$is_taken = ($saveArray['sender_id'] == 'me') ? 'Yes' : 'No';
        	$saveArray['sender_id'] = ($saveArray['sender_id'] == 'me') ? $this->Session->read("SESSION_ADMIN")['id'] : $saveArray['sender_id'];
            $id = $saveArray['sender_id'];
		  $group_nm = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
            $group_name = $group_nm['Group']['name'];
           $sender = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['sender_id'])));
           $friend = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['receiver_id'])));
            if (!empty($sender)) {
            	$sender_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
            	 $friend_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
            	$lastchat =	$this->SupportChat->find('first',[
							'conditions' => ['SupportChat.group_id' => $saveArray['group_id'], 'SupportChat.is_closed' => 'No',
											'OR' => array(
												    array('SupportChat.sender_id' => $saveArray['receiver_id'] , 'SupportChat.receiver_id' => $saveArray['sender_id'] ),
												    array('SupportChat.sender_id' => $saveArray['sender_id'] , 'SupportChat.receiver_id' => $saveArray['receiver_id'] )
											    )
								 ],
							'fields' => ['Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.sender_id' , 'SupportChat.receiver_id','SupportChat.id','SupportChat.is_taken','SupportChat.taken_by'],
							'order'=>array('SupportChat.id DESC')
					]);
            	//pr($lastchat); die;
            		/*$supportmember = $this->SupportMember->find('first',[
            			'conditions' => ['user_id' =>  $saveArray['sender_id'],   ]
            		]);*/
            		 $gm = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['sender_id'])]
                    	]);
            		 $sender_data = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['receiver_id'])]
                    	]);
                   	
				if(isset($saveArray['timezone']))
					{
						date_default_timezone_set($saveArray['timezone']);
					}
					else
					{
						date_default_timezone_set('Asia/Kolkata');
					}
				
				 $own = date('Y-m-d H:i:s');
                    $time = strtotime($own);
                    $time = $time - (5 * 60);
                    $date = date("Y-m-d H:i:s", $time); 
					
					
					
					
                    $saveArray['submit_time'] = $date;
                    if ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) {
                        $saveArray['image'] = "";
                    }
					 if ($saveArray['type'] == "L" AND ! empty($saveArray['lat']) AND ! empty($saveArray['lng'])){
						$saveArray['message'] = "";
                        $saveArr['message'] = $saveArray['message'];
                        $saveArr['image'] = "";
						$saveArr['submit_time'] = $date;
						$saveArr['sender_id'] =$saveArray['sender_id'];
						$saveArr['receiver_id'] = (empty($saveArray['receiver_id'])) ? 0 :  $saveArray['receiver_id'];
						$saveArr['group_id'] =$saveArray['group_id'];
						$saveArr['type'] =$saveArray['type'];
						$saveArr['lat'] =$saveArray['lat'];
						$saveArr['lng'] =$saveArray['lng'];
						$groupmsg_img ="";
						$this->SupportChat->save($saveArr,array('validate'=>false));
					}	
                    if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
					 $destination= WWW_ROOT . 'img/groupchatimg/' ;
                        if (!empty($_FILES['image']['name'])) {
                            $r = rand(1, 99999);
                            $saveArray['message'] = " ";
                            $saveArray['image'] = $this->uploadPic($r, $destination,$_FILES['image']);
                        }
                    }
                    if($is_taken == 'Yes'){
            	 		$saveArray['taken_by'] =$saveArray['sender_id'];
            	 		$saveArray['is_taken'] = 'Yes';	
            	 			//$saveArray['is_read'] = 'Yes'; // IF SENDER IS SUPPORT CHAT MEMBER.
            	 	} else {
            	 		if(count($lastchat) > 0)
						{
            	 			$saveArray['taken_by'] = $lastchat['SupportChat']['taken_by'];
            	 			$saveArray['is_taken'] = $lastchat['SupportChat']['is_taken'];
            	 		}
            	 		
            	 	}
            	 	//pr($lastchat);die;
                    $groupchat_id = $this->SupportChat->save($saveArray,array('validate'=>false));
                    $lastchatdata = $this->SupportChat->find('first',[
								'conditions' => ['SupportChat.id' => $groupchat_id['SupportChat']['id'], 
									 ],
								'fields' => ['SupportChat.receiver_id', 'SupportChat.sender_id' ,'SupportChat.id','SupportChat.message','SupportChat.type' ,'SupportChat.group_id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.taken_by', 'SupportChat.is_taken' , 'SupportChat.submit_time', 'SupportChat.is_closed','SupportChat.is_read'],
						]);
                    $mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => $groupchat_id['SupportChat']['group_id'] ,'type' => 'S' ], 
													  'fields' => ['id','user_id']
										));
                    //pr($mygroupsmembers);die;
                    $groupmembersids = (count($mygroupsmembers) > 0) ? array_values($mygroupsmembers) : [];
                    if(count($groupmembersids) > 0){
                    	//pr($groupmembersids); die;
                    	$notificationdata = [];
                    	foreach ($groupmembersids as  $member_id) {
                    		// CHecking if not adding notification for Himself.
                    		
                    		if($saveArray['sender_id'] !== $member_id){
                    		//pr($saveArray['sender_id']);
                    		//pr($member_id); die;
                    			$notificationdata[] = [
                    							'sender_id' => $saveArray['sender_id'],
                    							'receiver_id' => $saveArray['receiver_id'],
                    							'group_id' => $saveArray['group_id'],
                    							'record_id' =>  $groupchat_id['SupportChat']['id'],
                    							'notification_to' => $member_id
                    							];
                    			//pr($notificationdata);
                    					
                    		}
                    	}
                    	//pr($notificationdata);
                    	$this->UnreadNotifications->saveAll($notificationdata);
                    	//die;
                    }
                    $date = new DateTime($lastchatdata['SupportChat']['submit_time'], new DateTimeZone('Asia/Kolkata'));
                    $date->modify('-5 minutes');
                    $currentDateTime = $date->format('Y-m-d H:i:s');
					
                    $usertimezone = 'Asia/Kolkata';
                    date_default_timezone_set('Asia/Kolkata');
                    $lastchatdata['SupportChat']['date'] = $date->format('M j, Y, g:i a');
                    $lastchatdata['Sender']['name'] = (empty($lastchatdata['Sender']['name'])) ? 'Guest' : $lastchatdata['Sender']['name']; 
                    $lastchatdata['Sender']['image'] = (empty($lastchatdata['Sender']['image'])) ? 'no-image.jpg' : $lastchatdata['Sender']['image']; 


                   	if(count($lastchat) > 0)
					{
                   			// IF CHAT IS NOT TAKEN BY ANYBODY
                   		//pr($lastchat['SupportChat']['is_taken']); die;
            	 		if($lastchat['SupportChat']['is_taken'] == 'No')
						{
            	 			// IF MESSAGE IS SENT FROM WEBEND
            	 			if($is_taken == 'Yes')
							{
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $saveArray['sender_id'];
	            	 			$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$records = 	$this->SupportChat->find('all',[
											'conditions' => ['SupportChat.group_id' => $saveArray['group_id'],'SupportChat.sender_id' => $saveArray['receiver_id'] , 'SupportChat.receiver_id' => 0, 'SupportChat.is_taken' => 'No' ]
											]);
	            	 			//pr($records); die;
	            	 			//$records[] = $groupchat_id;
	            	 			foreach($records as $record)
								{
									$this->SupportChat->read(null,$record['SupportChat']['id']);
									$this->SupportChat->set($savedata);
									$this->SupportChat->save();
	            	 			}	
            	 			}
            	 			
            	 		} /*else {
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $lastchat['SupportChat']['taken_by'];
	            	 			//$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$g = $this->SupportChat->read(null,$groupchat_id['SupportChat']['id']);
	            	 			//pr($g); die;
								$this->SupportChat->set($savedata);
								$this->SupportChat->save();
            	 		}*/
            	 	}

            	 	/*if($is_taken == 'Yes'){
            	 				//IF CHAT IS ALREADY TAKEN BY SOMEONE , WE JUST UPDATE THE CURRENT TAKER DATA IN CURRENT RECORD
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $lastchat['SupportChat']['taken_by'];
	            	 			//$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$this->SupportChat->read(null,$groupchat_id['SupportChat']['id']);
								$this->SupportChat->set($savedata);
								$this->SupportChat->save();
            	 		} */

                   
                    if ($saveArray['type'] == "T" && !empty($saveArray['message'])) {
                        $groupmsg_img = '';
                    } else if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
                        $getGroupChatImage = $this->SupportChat->find('first', array('conditions' => array('id' => $groupchat_id['SupportChat']['id'])));
                        if (!empty($getGroupChatImage)) {
                            $groupmsg_img = BASE_URL . "img/groupchatimg/" . $getGroupChatImage['SupportChat']['image'];
                        } else {
                            $groupmsg_img = '';
                        }
                    }
				
				if($friend)
				{
				 $condition3 = "Block.friend_id='" . $saveArray['sender_id'] . "' And Block.user_id='" . $saveArray['receiver_id'] . "' And Block.group_id='" . $saveArray['group_id'] . "' ";
					$data_exist45 = $this->Block->find('first', array('conditions' => $condition3));
					if(!empty($data_exist45))
					{
						/* $last_id =$this->SupportChat->getLastInsertId();
						$this->SupportChat->deleteAll(array('id' => $last_id), false);*/
					}
					if(empty($data_exist45))
					{
						$udid = $friend['User']['device_id'];
						$did=$sender['User']['device_id'];
						$check_id = $friend['User']['id'];
						$check_group = $saveArray['group_id'];
						$condition2 = "CheckSetting.user_id='" . $check_id . "' And CheckSetting.group_id='" . $check_group . "' ";
						$data_exist4 = $this->CheckSetting->find('first', array('conditions' => $condition2));
					
					if(!empty($data_exist4))
					{
						if(!empty($data_user_valid))
							{
							    if($data_user_valid['SupportChat']['image']== "")
								{
					                  $img="";
								}
								else
								{
									$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user_valid['SupportChat']['image'];
								}

								 if($data_user_valid['SupportChat']['message']== "")
								 {

					                  $msg="";

								 }
								 else
								 {
								 	$msg = $data_user_valid['SupportChat']['message'];
								 }

							}
							else
							{
								$msg="";
								$img="";
							}
							/* new code end*/
					if($data_exist4['CheckSetting']['private_chat']=='0'){
						
					if($udid != $did)
					{

				 
										if($sender['User']['login_type']=='G'){
                     $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
							 'group_name'=>$group_nm['Group']['name'],
								'country_code'=>$group_nm['Group']['country_code'],
								'mobile'=>$group_nm['Group']['mobile'],
								'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
				   }else{
                         $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);               
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
							if(!empty($push_noti))
							{
								if($push_noti['PushNotification']['notification_status']== '0')
								{
									if( $c == $main && $ios_ids != "")
									{
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								}
							}
							if(empty($push_noti))
							{
								//if($push_noti['PushNotification']['notification_status']== '0'){
									if( $c == $main && $ios_ids != "")
									{
										$this->Common->iphone_send_notification($ios_ids, $message,1);
									}
								//}
							}		
									
									
									
									
									
									
									}
                                }        
				
                    
                   
				}
					}
					}
					if(empty($data_exist4))
					{
						
						/* new code start*/
						$data_user_valid = $this->SupportChat->find('first',array('conditions'=> array('SupportChat.group_id' => $saveArray['group_id'],'SupportChat.sender_id' => $saveArray['sender_id'],'SupportChat.receiver_id' => $saveArray['receiver_id']),'fields' => array('message','image'),'order'=>array('SupportChat.id'=>"desc")
		                  ));
						 

					     if(!empty($data_user_valid))
							{
							    if($data_user_valid['SupportChat']['image']== "")
								{
					                  $img="";
								}
								else
								{
									$img=BASE_URL."/app/webroot/img/groupchatimg/".$data_user_valid['SupportChat']['image'];
								}

								 if($data_user_valid['SupportChat']['message']== "")
								 {

					                  $msg="";

								 }
								 else
								 {
								 	$msg = $data_user_valid['SupportChat']['message'];
								 }

							}
							else
							{
								$msg="";
								$img="";
							}
							/* new code end*/
						
						
						
						
					
						
				if($udid != $did){
			
										if($sender['User']['login_type']=='G')
										{
				  $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				                         'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
				   }else{
					    $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message'],'broadcast_message'=>$msg,'broadcast_image'=>$img);
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


                    $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $groupmsg_img,'last_id' => $lastchatdata );
              // } else {
               //     $result = array('status' => '0', 'message' => 'You can not send group message in this //trip.');
             //   }
            } else {
                $result = array('status' => '0', 'message' => 'User id does not exist');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	/*end code*/
	
	/* new broadcasting functionality */
	function chat_1() 
	{
		
		
        $saveArray = $this->data;
        //pr($saveArray); die;
        if (!empty($saveArray['sender_id']) AND  !empty($saveArray['group_id'])AND ! empty($saveArray['type'])
                AND 
			(($saveArray['type'] == "T" AND ! empty($saveArray['message'])) OR ( $saveArray['type'] == "I" AND !empty($_FILES['image']['name']))  OR ( $saveArray['type'] == "L" AND !empty($saveArray['lat']) AND !empty($saveArray['lng'])))) 
			{
        	$is_taken = ($saveArray['sender_id'] == 'me') ? 'Yes' : 'No';
        	$saveArray['sender_id'] = ($saveArray['sender_id'] == 'me') ? $this->Session->read("SESSION_ADMIN")['id'] : $saveArray['sender_id'];
            $id = $saveArray['sender_id'];
		  $group_nm = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
            $group_name = $group_nm['Group']['name'];
           $sender = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['sender_id'])));
           $friend = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['receiver_id'])));
            if (!empty($sender)) 
			{
            	$sender_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
            	 $friend_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
            	$lastchat =	$this->SupportChat->find('first',[
							'conditions' => ['SupportChat.group_id' => $saveArray['group_id'], 'SupportChat.is_closed' => 'No',
											'OR' => array(
												    array('SupportChat.sender_id' => $saveArray['receiver_id'] , 'SupportChat.receiver_id' => $saveArray['sender_id'] ),
												    array('SupportChat.sender_id' => $saveArray['sender_id'] , 'SupportChat.receiver_id' => $saveArray['receiver_id'] )
											    )
								 ],
							'fields' => ['Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.sender_id' , 'SupportChat.receiver_id','SupportChat.id','SupportChat.is_taken','SupportChat.taken_by'],
							'order'=>array('SupportChat.id DESC')
					]);
            	//pr($lastchat); die;
            		/*$supportmember = $this->SupportMember->find('first',[
            			'conditions' => ['user_id' =>  $saveArray['sender_id'],   ]
            		]);*/
            		 $gm = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['sender_id'])]
                    	]);
            		 $sender_data = $this->GroupMember->find('first',[
                    		'conditions' => ['group_id' => $saveArray['group_id'] , 'user_id' =>  intval($saveArray['receiver_id'])]
                    	]);
                   	//pr(date('YmdHis')); die;
             		/* date_default_timezone_set('Asia/Kolkata');
					
					
					$date = date("Y-m-d H:i:s"); */
					
					//date_default_timezone_set($saveArray['timezone']);
				//$current_date = date('Y-m-d H:i:s');
                //$date = date('Y-m-d H:i:s');
				if(isset($saveArray['timezone']))
					{
						date_default_timezone_set($saveArray['timezone']);
					}
					else
					{
						date_default_timezone_set('Asia/Kolkata');
					}
				
				 $own = date('Y-m-d H:i:s');
                    $time = strtotime($own);
                    $time = $time - (5 * 60);
                    $date = date("Y-m-d H:i:s", $time); 
					
					
					
					
                    $saveArray['submit_time'] = $date;
                    if ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) 
					{
						echo "693";
						die;
                        $saveArray['image'] = "";
                    }
					
					if ($saveArray['type'] == "L" AND ! empty($saveArray['lat']) AND ! empty($saveArray['lng']))
					{
						$saveArray['message'] = "";
                        $saveArr['message'] = $saveArray['message'];
                        $saveArr['image'] = "";
						$saveArr['submit_time'] = $date;
						$saveArr['sender_id'] =$saveArray['sender_id'];
						$saveArr['receiver_id'] = (empty($saveArray['receiver_id'])) ? 0 :  $saveArray['receiver_id'];
						$saveArr['group_id'] =$saveArray['group_id'];
						$saveArr['type'] =$saveArray['type'];
						$saveArr['lat'] =$saveArray['lat'];
						$saveArr['lng'] =$saveArray['lng'];
						$groupmsg_img ="";
						$this->SupportChat->save($saveArr,array('validate'=>false));
					}	
                    if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) 
					{
						echo "7";
						die;
					 $destination= WWW_ROOT . 'img/groupchatimg/' ;
                        if (!empty($_FILES['image']['name'])) 
						{
                            $r = rand(1, 99999);
                            $saveArray['message'] = " ";
                            $saveArray['image'] = $this->uploadPic($r, $destination,$_FILES['image']);
                        }
                    }
                    if($is_taken == 'Yes')
					{
            	 		$saveArray['taken_by'] =$saveArray['sender_id'];
            	 		$saveArray['is_taken'] = 'Yes';	
            	 			//$saveArray['is_read'] = 'Yes'; // IF SENDER IS SUPPORT CHAT MEMBER.
            	 	} 
					else 
					{
            	 		if(count($lastchat) > 0)
						{
            	 			$saveArray['taken_by'] = $lastchat['SupportChat']['taken_by'];
            	 			$saveArray['is_taken'] = $lastchat['SupportChat']['is_taken'];
            	 		}
            	 		
            	 	}
            	 	//pr($lastchat);die;
                    $groupchat_id = $this->SupportChat->save($saveArray,array('validate'=>false));
                    $lastchatdata = $this->SupportChat->find('first',[
								'conditions' => ['SupportChat.id' => $groupchat_id['SupportChat']['id'], 
									 ],
								'fields' => ['SupportChat.receiver_id', 'SupportChat.sender_id' ,'SupportChat.id','SupportChat.message','SupportChat.type' ,'SupportChat.group_id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.taken_by', 'SupportChat.is_taken' , 'SupportChat.submit_time', 'SupportChat.is_closed','SupportChat.is_read'],
						]);
                    $mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => $groupchat_id['SupportChat']['group_id'] ,'type' => 'S' ], 
													  'fields' => ['id','user_id']
										));
                    //pr($mygroupsmembers);die;
                    $groupmembersids = (count($mygroupsmembers) > 0) ? array_values($mygroupsmembers) : [];
                    if(count($groupmembersids) > 0){
                    	//pr($groupmembersids); die;
                    	$notificationdata = [];
                    	foreach ($groupmembersids as  $member_id) {
                    		// CHecking if not adding notification for Himself.
                    		
                    		if($saveArray['sender_id'] !== $member_id){
                    		//pr($saveArray['sender_id']);
                    		//pr($member_id); die;
                    			$notificationdata[] = [
                    							'sender_id' => $saveArray['sender_id'],
                    							'receiver_id' => $saveArray['receiver_id'],
                    							'group_id' => $saveArray['group_id'],
                    							'record_id' =>  $groupchat_id['SupportChat']['id'],
                    							'notification_to' => $member_id
                    							];
                    			//pr($notificationdata);
                    					
                    		}
                    	}
                    	//pr($notificationdata);
                    	$this->UnreadNotifications->saveAll($notificationdata);
                    	//die;
                    }
                    $date = new DateTime($lastchatdata['SupportChat']['submit_time'], new DateTimeZone('Asia/Kolkata'));
                    $date->modify('-5 minutes');
                    $currentDateTime = $date->format('Y-m-d H:i:s');
					
                    $usertimezone = 'Asia/Kolkata';
                    date_default_timezone_set('Asia/Kolkata');
                    $lastchatdata['SupportChat']['date'] = $date->format('M j, Y, g:i a');
                    $lastchatdata['Sender']['name'] = (empty($lastchatdata['Sender']['name'])) ? 'Guest' : $lastchatdata['Sender']['name']; 
                    $lastchatdata['Sender']['image'] = (empty($lastchatdata['Sender']['image'])) ? 'no-image.jpg' : $lastchatdata['Sender']['image']; 


                   	if(count($lastchat) > 0){
                   			// IF CHAT IS NOT TAKEN BY ANYBODY
                   		//pr($lastchat['SupportChat']['is_taken']); die;
            	 		if($lastchat['SupportChat']['is_taken'] == 'No'){
            	 			// IF MESSAGE IS SENT FROM WEBEND
            	 			if($is_taken == 'Yes'){
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $saveArray['sender_id'];
	            	 			$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$records = 	$this->SupportChat->find('all',[
											'conditions' => ['SupportChat.group_id' => $saveArray['group_id'],'SupportChat.sender_id' => $saveArray['receiver_id'] , 'SupportChat.receiver_id' => 0, 'SupportChat.is_taken' => 'No' ]
											]);
	            	 			//pr($records); die;
	            	 			//$records[] = $groupchat_id;
	            	 			foreach($records as $record){
									$this->SupportChat->read(null,$record['SupportChat']['id']);
									$this->SupportChat->set($savedata);
									$this->SupportChat->save();
	            	 			}	
            	 			}
            	 			
            	 		} /*else {
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $lastchat['SupportChat']['taken_by'];
	            	 			//$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$g = $this->SupportChat->read(null,$groupchat_id['SupportChat']['id']);
	            	 			//pr($g); die;
								$this->SupportChat->set($savedata);
								$this->SupportChat->save();
            	 		}*/
            	 	}

            	 	/*if($is_taken == 'Yes'){
            	 				//IF CHAT IS ALREADY TAKEN BY SOMEONE , WE JUST UPDATE THE CURRENT TAKER DATA IN CURRENT RECORD
            	 				$savedata = [];
	            	 			$savedata['is_taken'] =  'Yes';
	            	 			$savedata['taken_by'] =  $lastchat['SupportChat']['taken_by'];
	            	 			//$savedata['receiver_id'] =  $saveArray['sender_id'];
	            	 			$this->SupportChat->read(null,$groupchat_id['SupportChat']['id']);
								$this->SupportChat->set($savedata);
								$this->SupportChat->save();
            	 		} */

                   
                    if ($saveArray['type'] == "T" && !empty($saveArray['message'])) {
                        $groupmsg_img = '';
                    } else if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {
                        $getGroupChatImage = $this->SupportChat->find('first', array('conditions' => array('id' => $groupchat_id['SupportChat']['id'])));
                        if (!empty($getGroupChatImage)) {
                            $groupmsg_img = BASE_URL . "img/groupchatimg/" . $getGroupChatImage['SupportChat']['image'];
                        } else {
                            $groupmsg_img = '';
                        }
                    }
				
				if($friend){
				 $condition3 = "Block.friend_id='" . $saveArray['sender_id'] . "' And Block.user_id='" . $saveArray['receiver_id'] . "' And Block.group_id='" . $saveArray['group_id'] . "' ";
					$data_exist45 = $this->Block->find('first', array('conditions' => $condition3));
					if(!empty($data_exist45)){
						/* $last_id =$this->SupportChat->getLastInsertId();
						$this->SupportChat->deleteAll(array('id' => $last_id), false);*/
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
                     $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				                                 'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'], 'name' => $sender['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
				   }else{
                         $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
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
				  $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
                                         'date' => $date, 'sender_name' => $sender['User']['name'],'name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
										'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
				   }else{
					    $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'helpdesk',
				  'group_name'=>$group_nm['Group']['name'],
					'country_code'=>$group_nm['Group']['country_code'],
					'mobile'=>$group_nm['Group']['mobile'],
					'image' => BASE_URL . "img/group_images/" . $group_nm['Group']['image'],
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


                    $result = array('status' => '1', 'message' => 'Successfully Sent', 'groupmsg_img' => $groupmsg_img,'last_id' => $lastchatdata );
              // } else {
               //     $result = array('status' => '0', 'message' => 'You can not send group message in this //trip.');
             //   }
            } else {
                $result = array('status' => '0', 'message' => 'User id does not exist');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }




    function get_chat() {
        $saveArray = $this->data;

 if($saveArray['reciever_id'] == "0"){

        if (!empty($saveArray['sender_id']) && !empty($saveArray['timezone']) && !empty($saveArray['page_number']) /*&& !empty($saveArray['reciever_id'])*/ && !empty($saveArray['group_id'])) {

                $condition = " User.id='" . $saveArray['sender_id'] . "' ";
                $user_exist = $this->User->find('first', array('conditions' => $condition));

            if ($user_exist){
            $condition3 = "CheckSetting.user_id='" . $saveArray['sender_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
                $check_status= $this->CheckSetting->find('first', array('conditions' => $condition3));
				if(!empty($check_status)){
				               $privatestatus_offtime = $check_status['CheckSetting']['privatestatus_offtime'];
				               $start_limit = $saveArray['page_number']*10-10;
				                $end_limit = 10;
								if($check_status['CheckSetting']['private_chat'] == 0){
				                  	$cond = ['SupportChat.group_id'=>$saveArray['group_id'],
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']/* ,'SupportChat.receiver_id' => $saveArray['reciever_id'] */),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ ),
													    //array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['sender_id']),
												    )
				    			   ];
				                /*      $query = "SELECT supportchats.*,
							users.name as sendername,users.image as senderimage,
							users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM supportchats INNER JOIN users ON users.id=supportchats.sender_id inner join users f 
							on f.id=supportchats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
							AND receiver_id=" . $saveArray['reciever_id'] . ")
							OR( sender_id=" . $saveArray['reciever_id'] . " 
							AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . ")) order by created DESC LIMIT $start_limit,$end_limit ";

				                $user_details = $this->Chat->query($query);*/

				                

				              /*  $query1 = "SELECT count('id') as totalpage FROM `supportchats`
							WHERE (((sender_id=" . $saveArray['sender_id'] . "
							AND receiver_id=" . $saveArray['reciever_id'] . ")
							OR( sender_id=" . $saveArray['reciever_id'] . " 
							AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
				                $user_exist1 = $this->Chat->query($query1);
				                $page_count = $user_exist1[0][0]['totalpage'];
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);*/
								}else{
									$cond = ['SupportChat.group_id'=>$saveArray['group_id'],'SupportChat.submit_time <=' => $privatestatus_offtime,
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id'] /*,'SupportChat.receiver_id' => $saveArray['reciever_id']*/ ),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ )
												    )
				    			   ];

									
				               /*     $start_limit = $saveArray['page_number']*10-10;
				                    $end_limit = 10;
				                  
				                      $query = "SELECT supportchats.*,
							users.name as sendername,users.image as senderimage,
							users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM supportchats INNER JOIN users ON users.id=supportchats.sender_id inner join users f 
							on f.id=supportchats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
							AND receiver_id=" . $saveArray['reciever_id'] . ")
							OR( sender_id=" . $saveArray['reciever_id'] . " 
							AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . ")) AND submit_time<='$privatestatus_offtime' order by created DESC LIMIT $start_limit,$end_limit ";

				                $user_details = $this->Chat->query($query);


				                $query1 = "SELECT count('id') as totalpage FROM `supportchats`
							WHERE (((sender_id=" . $saveArray['sender_id'] . "
							AND receiver_id=" . $saveArray['reciever_id'] . ")
							OR( sender_id=" . $saveArray['reciever_id'] . " 
							AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
				                $user_exist1 = $this->Chat->query($query1);
				                $page_count = $user_exist1[0][0]['totalpage'];
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);*/
							}
							//pr($cond); die;
							$user_details = $this->SupportChat->find('all',[
				    			'conditions' => $cond,
				    			'offset' => $start_limit,
				    			'limit' => $offset,
				    			'order'=>array('SupportChat.id DESC')
				    		]);

				    		$count = $this->SupportChat->find('count',[
				    			'conditions' => $cond,
				    			'order'=>array('SupportChat.id DESC')
				    		]);

				    			 $page_count =$count;
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);




				}

              if(empty($check_status)){
              		//	pr($saveArray); die;
						    $start_limit = $saveArray['page_number']*10-10;
		                    $end_limit = 10;
						/*		                     [auth_key] => 123
						    [sender_id] => 459
						    [timezone] => Asia/Kolkata
						    [page_number] => 1
						    [reciever_id] => 0
						    [group_id] => 44
						)*/
											



		                  
		            /*          $query = "SELECT supportchats.*,
					users.name as sendername,users.image as senderimage,
					users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM supportchats INNER JOIN users ON users.id=supportchats.sender_id inner join users f 
					on f.id=supportchats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
					AND receiver_id=" . $saveArray['reciever_id'] . ")
					OR( sender_id=" . $saveArray['reciever_id'] . " 
					AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . ")) order by created DESC LIMIT $start_limit,$end_limit ";*/
					$user_details = $this->SupportChat->find('all',[
		    			'conditions' => ['SupportChat.group_id'=>$saveArray['group_id'],
		    									'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']/* ,'SupportChat.receiver_id' => $saveArray['reciever_id'] */),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ ),
													    //array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['sender_id']),
												    )
		    						/*'OR' => array(
											    array('SupportChat.sender_id' => $saveArray['sender_id'] ,'SupportChat.receiver_id' => $saveArray['reciever_id'] ),
											    array('SupportChat.receiver_id' => $saveArray['sender_id'] , 'SupportChat.sender_id' => $saveArray['reciever_id'] )
										    )*/
		    			],
		    			'offset' => $start_limit,
		    			'limit' => $end_limit,
		    			'order'=>array('SupportChat.id DESC')
		    		]);
			           //$user_details = $this->Chat->query($query);
		               // pr($query);
		                //pr($saveArray); die;
		               // pr($user_details); die;

		                $query1 = "SELECT count('id') as totalpage FROM `supportchats`
					WHERE (((sender_id=" . $saveArray['sender_id'] . "
					AND receiver_id=" . $saveArray['reciever_id'] . ")
					OR( sender_id=" . $saveArray['reciever_id'] . " 
					AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
		                $user_exist1 = $this->Chat->query($query1);
		                $page_count = $user_exist1[0][0]['totalpage'];
		                $page_count = $page_count / 10;
		                $page_count = ceil($page_count);
			  }
            // pr($user_details);die;
                if (!empty($user_details)) {
                   /* foreach ($user_details as $k => $values) {
						 $data['msg_id'] = $values['supportchats']['id'];
                         $data['senderid'] = $values['supportchats']['sender_id'];
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
						 $data['msg'] = !empty($values['supportchats']['message'])?$values['supportchats']['message']:'';
					  $data['type'] = $values['supportchats']['type'];
					  if($values['supportchats']['type']=='I'){
				      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['supportchats']['image'];	
					  }else{
						 $data['image'] = ''; 
					  }
					   if($values['supportchats']['type']=='L'){
				      $data['lat'] = $values['supportchats']['lat'];	
				      $data['lng'] =$values['supportchats']['lng'];
					  }else{
						$data['lat'] = "";	
				        $data['lng'] ="";
					  }
					  	//pr($values['chats']); die;

					    $date = new DateTime($values['supportchats']['submit_time'], new DateTimeZone($saveArray['timezone']));
						//pr($date->format('Y-m-d H:i:s'));
						$date->modify('-5 minutes');
						$currentDateTime = $date->format('Y-m-d H:i:s');
	                    $usertimezone = $saveArray['timezone'];
	                    //$data['time'] = $this->ConvertTimezoneToAnotherTimezone($currentDateTime, date_default_timezone_get(), $usertimezone);
						date_default_timezone_set($saveArray['timezone']);
						//$current_date = date('Y-m-d H:i:s');
	                    //pr(date('Y-m-d H:i:s'));
						//pr($currentDateTime); die;
	                    //$datetime = explode(" ",$currentDateTime);
	                   // $data['time'] = $datetime[1];
	                     //$data['time'] = date('h:i A', strtotime($data['time']. "+12 minutes"));
	                    $data['time'] =$date->format('h:i A');
	                    $data['date'] = $date->format('Y-m-d');
	                        $data1[] = ($data);
	                    }
						 if (!empty($data1)) {
	                        $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count);
	                    } else {
	                        $result = array('status' => '0', 'message' => 'Groups not found.');
	                    }*/
	                    
	                     foreach ($user_details as  $values) {
	                     	//pr($values); die;
						 $data['msg_id'] = $values['SupportChat']['id'];
                         $data['senderid'] = $values['SupportChat']['sender_id'];
						 if(!empty($values['Sender']['image'])){
							 if($values['Sender']['register_type']=="F"){
								$data['senderimage'] = $values['Sender']['image'];
							 }
							 if($values['Sender']['register_type']=="N"){
								$data['senderimage'] = BASE_URL . "img/profile_images/" .$values['Sender']['image'];
							 }
						 }
						 if(empty($values['Sender']['image'])){
							 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
								$data['senderimage'] = $defaulturl;
						 }
						// pr($values);die;
                         $data['sendername'] = $values['Sender']['name'];
						 $data['msg'] = !empty($values['SupportChat']['message'])?$values['SupportChat']['message']:'';
					  $data['type'] = $values['SupportChat']['type'];
					  if($values['SupportChat']['type']=='I'){
				      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['SupportChat']['image'];	
					  }else{
						 $data['image'] = ''; 
					  }
					   if($values['SupportChat']['type']=='L'){
				      $data['lat'] = $values['SupportChat']['lat'];	
				      $data['lng'] =$values['SupportChat']['lng'];
					  }else{
						$data['lat'] = "";	
				        $data['lng'] ="";
					  }
					  	//pr($values['chats']); die;

					   /* $date = new DateTime($values['SupportChat']['submit_time'], new DateTimeZone($saveArray['timezone']));
						$date->modify('-5 minutes');
						$currentDateTime = $date->format('Y-m-d H:i:s');
	                    $usertimezone = $saveArray['timezone'];
						date_default_timezone_set($saveArray['timezone']);
*/

						$currentDateTime = $values['SupportChat']['submit_time'];
						$submit_time_zone =$values['SupportChat']['time_zone'];
	                    $usertimezone = $saveArray['timezone'];
						$converted_data = $this->timezone_test_get_chat($currentDateTime,$submit_time_zone, $usertimezone);
						$day = explode(" ",$converted_data);
						
						$data['time'] =$day[1]." ".$day[2];
	                    $data['date'] = $day[0];
						


						if(!empty($values['SupportChat']['close_time']) && $values['SupportChat']['close_time'] !== null){
							 $close_time = new DateTime($values['SupportChat']['close_time'], new DateTimeZone($saveArray['timezone']));
							$close_time->modify('-5 minutes');
							date_default_timezone_set($saveArray['timezone']);
							$data['close_time'] = $close_time->format('M j, Y, g:i a') ;
						} else {
							$data['close_time'] = "" ;	
						}
	                    //$data['time'] =$date->format('h:i A');
	                   // $data['date'] = $date->format('Y-m-d');
	                    $data['is_closed'] =$values['SupportChat']['is_closed'];
	                    $data['session_id'] =$values['SupportChat']['session_id'];
	                        $data1[] = ($data);
	                    }
						 if (!empty($data1)) {
	                        $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count );
	                    } else {
	                        $result = array('status' => '0', 'message' => 'Groups not found.');
	                    }





                } else {
                	// Lands Here
                    $result = array('status' => '0', 'message' => 'Message not found.');
                }
            } else {
                $result = array('status' => '0', 'message' => 'group_id or user_id not match.');
            }






        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }
 	
 }else{



		  if (!empty($saveArray['sender_id']) && !empty($saveArray['timezone']) && !empty($saveArray['page_number']) && !empty($saveArray['group_id'])) 
		{

                $condition = " User.id='" . $saveArray['sender_id'] . "' ";
                $user_exist = $this->User->find('first', array('conditions' => $condition));
           // }
// pr("jik");die;
            if ($user_exist){


            $condition3 = "CheckSetting.user_id='" . $saveArray['sender_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";

                $check_status= $this->CheckSetting->find('first', array('conditions' => $condition3));

				if(!empty($check_status)){
				               $privatestatus_offtime = $check_status['CheckSetting']['privatestatus_offtime'];
				               $start_limit = $saveArray['page_number']*10-10;
				                $end_limit = 10;
								if($check_status['CheckSetting']['private_chat'] == 0){
				                  	$cond = ['SupportChat.group_id'=>$saveArray['group_id'],
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']/* ,'SupportChat.receiver_id' => $saveArray['reciever_id'] */),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ ),
													    //array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['sender_id']),
												    )
				    			   ];
				                
								}else{
									$cond = ['SupportChat.group_id'=>$saveArray['group_id'],'SupportChat.submit_time <=' => $privatestatus_offtime,
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id'] /*,'SupportChat.receiver_id' => $saveArray['reciever_id']*/ ),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ )
												    )
				    			   ];

								
							}
							//pr($cond); die;
							$user_details = $this->SupportChat->find('all',[
				    			'conditions' => $cond,
				    			'offset' => $start_limit,
				    			'limit' => $offset,
				    			'order'=>array('SupportChat.created DESC')
				    		]);

				    		$count = $this->SupportChat->find('count',[
				    			'conditions' => $cond,
				    			'order'=>array('SupportChat.created DESC')
				    		]);

				    			 $page_count =$count;
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);




				}
				//pr($check_status);

              if(empty($check_status)){
              		//	pr($saveArray); die;
						    $start_limit = $saveArray['page_number']*10-10;
		                    $end_limit = 10;
						
					$user_details = $this->SupportChat->find('all',[
		    			'conditions' => ['SupportChat.group_id'=>$saveArray['group_id'],
		    									'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']),
													   array('SupportChat.receiver_id' => $saveArray['sender_id']), 
													   array('SupportChat.sender_id' => $saveArray['reciever_id']), 
													   array('SupportChat.receiver_id' => $saveArray['reciever_id']), 
													   //'SupportChat.sender_id' =>$saveArray['reciever_id']),
												    )
		    			],
		    			'offset' => $start_limit,
		    			'limit' => $end_limit,
		    			'order'=>array('SupportChat.id DESC')
		    		]);
			           
			      
		                $query1 = "SELECT count('id') as totalpage FROM `supportchats`
					WHERE (((sender_id=" . $saveArray['sender_id'] . "
					AND receiver_id=" . $saveArray['reciever_id'] . ")
					OR( sender_id=" . $saveArray['reciever_id'] . " 
					AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
		                $user_exist1 = $this->Chat->query($query1);
		                $page_count = $user_exist1[0][0]['totalpage'];
		                $page_count = $page_count / 10;
		                $page_count = ceil($page_count);
			  }
            //pr($user_details);
                if (!empty($user_details)) {
                  
	                    
	                     foreach ($user_details as  $values) {
	                     	//pr($values); die;
						 $data['msg_id'] = $values['SupportChat']['id'];
                         $data['senderid'] = $values['SupportChat']['sender_id'];
						 if(!empty($values['Sender']['image'])){
							 if($values['Sender']['register_type']=="F"){
								$data['senderimage'] = $values['Sender']['image'];
							 }
							 if($values['Sender']['register_type']=="N"){
								$data['senderimage'] = BASE_URL . "img/profile_images/" .$values['Sender']['image'];
							 }
						 }
						 if(empty($values['Sender']['image'])){
							 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
								$data['senderimage'] = $defaulturl;
						 }
						// pr($values);die;
                         $data['sendername'] = $values['Sender']['name'];
						 $data['msg'] = !empty($values['SupportChat']['message'])?$values['SupportChat']['message']:'';
					     $data['type'] = $values['SupportChat']['type'];
					  if($values['SupportChat']['type']=='I'){
				          $data['image'] = BASE_URL . "img/groupchatimg/" . $values['SupportChat']['image'];	
					  }else{
						 $data['image'] = ''; 
					  }
					   if($values['SupportChat']['type']=='L'){
				      $data['lat'] = $values['SupportChat']['lat'];	
				      $data['lng'] =$values['SupportChat']['lng'];
					  }else{
						$data['lat'] = "";	
				        $data['lng'] ="";
					  }
					  	//pr($values['chats']); die;

					   // $date = new DateTime($values['SupportChat']['submit_time'], new DateTimeZone($saveArray['timezone']));
						//$date->modify('-5 minutes');
						//$currentDateTime = $date->format('Y-m-d H:i:s');
						
						
						$currentDateTime = $values['SupportChat']['submit_time'];
						$submit_time_zone =$values['SupportChat']['time_zone'];
	                    $usertimezone = $saveArray['timezone'];
						$converted_data = $this->timezone_test_get_chat($currentDateTime,$submit_time_zone, $usertimezone);
						$day = explode(" ",$converted_data);
						
						$data['time'] =$day[1]." ".$day[2];
	                    $data['date'] = $day[0];
						
						date_default_timezone_set($saveArray['timezone']);
						if(!empty($values['SupportChat']['close_time']) && $values['SupportChat']['close_time'] !== null)
						{
							 $close_time = new DateTime($values['SupportChat']['close_time'], new DateTimeZone($saveArray['timezone']));
							$close_time->modify('-5 minutes');
							date_default_timezone_set($saveArray['timezone']);
							$data['close_time'] = $close_time->format('M j, Y, g:i a') ;
						} 
						else 
						{
							$data['close_time'] = "" ;	
						}
	                    
	                    $data['is_closed'] =$values['SupportChat']['is_closed'];
	                    $data['session_id'] =$values['SupportChat']['session_id'];
						$data['taken_by'] =$values['SupportChat']['taken_by'];
						
	                        $data1[] = ($data);
	                    }
						 if (!empty($data1)) {
	                        $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count );
	                    } else {
	                        $result = array('status' => '0', 'message' => 'Groups not found.');
	                    }





                } else {
                	// Lands Here
                    $result = array('status' => '0', 'message' => 'Message not found.');
                }
            } 
			else 
			{
                $result = array('status' => '0', 'message' => 'group_id or user_id not match.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }


 }






        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
    }
	
	
	
	/**/
	function get_chat_11() 
	{
        $saveArray = $this->data;
        if (!empty($saveArray['sender_id']) && !empty($saveArray['timezone']) && !empty($saveArray['page_number']) && !empty($saveArray['group_id'])) 
		{

                $condition = " User.id='" . $saveArray['sender_id'] . "' ";
                $user_exist = $this->User->find('first', array('conditions' => $condition));
           // }
// pr("jik");die;
            if ($user_exist){
            $condition3 = "CheckSetting.user_id='" . $saveArray['sender_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
                $check_status= $this->CheckSetting->find('first', array('conditions' => $condition3));
				if(!empty($check_status)){
				               $privatestatus_offtime = $check_status['CheckSetting']['privatestatus_offtime'];
				               $start_limit = $saveArray['page_number']*10-10;
				                $end_limit = 10;
								if($check_status['CheckSetting']['private_chat'] == 0){
				                  	$cond = ['SupportChat.group_id'=>$saveArray['group_id'],
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']/* ,'SupportChat.receiver_id' => $saveArray['reciever_id'] */),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ ),
													    //array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['sender_id']),
												    )
				    			   ];
				                
								}else{
									$cond = ['SupportChat.group_id'=>$saveArray['group_id'],'SupportChat.submit_time <=' => $privatestatus_offtime,
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id'] /*,'SupportChat.receiver_id' => $saveArray['reciever_id']*/ ),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ )
												    )
				    			   ];

								
							}
							//pr($cond); die;
							$user_details = $this->SupportChat->find('all',[
				    			'conditions' => $cond,
				    			'offset' => $start_limit,
				    			'limit' => $offset,
				    			'order'=>array('SupportChat.created DESC')
				    		]);

				    		$count = $this->SupportChat->find('count',[
				    			'conditions' => $cond,
				    			'order'=>array('SupportChat.created DESC')
				    		]);

				    			 $page_count =$count;
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);




				}
				//pr($check_status);
              if(empty($check_status)){
              		//	pr($saveArray); die;
						    $start_limit = $saveArray['page_number']*10-10;
		                    $end_limit = 10;
						
					
					$user_details = $this->SupportChat->find('all',[
		    			'conditions' => ['SupportChat.group_id'=>$saveArray['group_id'],
		    									'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']),
													   array('SupportChat.receiver_id' => $saveArray['sender_id']), 
													   array('SupportChat.sender_id' => $saveArray['reciever_id']), 
													   array('SupportChat.receiver_id' => $saveArray['reciever_id']), 
													   //'SupportChat.sender_id' =>$saveArray['reciever_id']),
												    )
		    			],
		    			'offset' => $start_limit,
		    			'limit' => $end_limit,
		    			'order'=>array('SupportChat.id DESC')
		    		]);
			           

		                $query1 = "SELECT count('id') as totalpage FROM `supportchats`
					WHERE (((sender_id=" . $saveArray['sender_id'] . "
					AND receiver_id=" . $saveArray['reciever_id'] . ")
					OR( sender_id=" . $saveArray['reciever_id'] . " 
					AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
		                $user_exist1 = $this->Chat->query($query1);
		                $page_count = $user_exist1[0][0]['totalpage'];
		                $page_count = $page_count / 10;
		                $page_count = ceil($page_count);
			  }
            //pr($user_details);
                if (!empty($user_details)) {
                  
	                    
	                     foreach ($user_details as  $values) {
	                     	//pr($values); die;
						 $data['msg_id'] = $values['SupportChat']['id'];
                         $data['senderid'] = $values['SupportChat']['sender_id'];
						 if(!empty($values['Sender']['image'])){
							 if($values['Sender']['register_type']=="F"){
								$data['senderimage'] = $values['Sender']['image'];
							 }
							 if($values['Sender']['register_type']=="N"){
								$data['senderimage'] = BASE_URL . "img/profile_images/" .$values['Sender']['image'];
							 }
						 }
						 if(empty($values['Sender']['image'])){
							 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
								$data['senderimage'] = $defaulturl;
						 }
						// pr($values);die;
                         $data['sendername'] = $values['Sender']['name'];
						 $data['msg'] = !empty($values['SupportChat']['message'])?$values['SupportChat']['message']:'';
					  $data['type'] = $values['SupportChat']['type'];
					  if($values['SupportChat']['type']=='I'){
				      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['SupportChat']['image'];	
					  }else{
						 $data['image'] = ''; 
					  }
					   if($values['SupportChat']['type']=='L'){
				      $data['lat'] = $values['SupportChat']['lat'];	
				      $data['lng'] =$values['SupportChat']['lng'];
					  }else{
						$data['lat'] = "";	
				        $data['lng'] ="";
					  }
					  	//pr($values['chats']); die;

					    $date = new DateTime($values['SupportChat']['submit_time'], new DateTimeZone($saveArray['timezone']));
						//$date->modify('-5 minutes');
						$currentDateTime = $date->format('Y-m-d H:i:s');
	                    $usertimezone = $saveArray['timezone'];
						date_default_timezone_set($saveArray['timezone']);
						if(!empty($values['SupportChat']['close_time']) && $values['SupportChat']['close_time'] !== null){
							 $close_time = new DateTime($values['SupportChat']['close_time'], new DateTimeZone($saveArray['timezone']));
							$close_time->modify('-5 minutes');
							date_default_timezone_set($saveArray['timezone']);
							$data['close_time'] = $close_time->format('M j, Y, g:i a') ;
						} else {
							$data['close_time'] = "" ;	
						}
	                    $data['time'] =$date->format('h:i A');
	                    $data['date'] = $date->format('Y-m-d');
	                    $data['is_closed'] =$values['SupportChat']['is_closed'];
	                    $data['session_id'] =$values['SupportChat']['session_id'];
						$data['taken_by'] =$values['SupportChat']['taken_by'];
						
	                        $data1[] = ($data);
	                    }
						 if (!empty($data1)) {
	                        $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count );
	                    } else {
	                        $result = array('status' => '0', 'message' => 'Groups not found.');
	                    }





                } else {
                	// Lands Here
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
	
	/**/
	
	
	
	
	/* get helpdesk chat*/
	function get_helpdesk_chat() 
		{
        $saveArray = $this->data;
        if (!empty($saveArray['sender_id']) && !empty($saveArray['timezone']) && !empty($saveArray['page_number']) /*&& !empty($saveArray['reciever_id'])*/ && !empty($saveArray['group_id'])) 
		{

                $condition = " User.id='" . $saveArray['sender_id'] . "' ";
                $user_exist = $this->User->find('first', array('conditions' => $condition));
           // }
// pr("jik");die;
            if ($user_exist)
			{
            $condition3 = "CheckSetting.user_id='" . $saveArray['sender_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
                $check_status= $this->CheckSetting->find('first', array('conditions' => $condition3));
				if(!empty($check_status))
				{
				               $privatestatus_offtime = $check_status['CheckSetting']['privatestatus_offtime'];
				               $start_limit = $saveArray['page_number']*10-10;
				                $end_limit = 10;
								if($check_status['CheckSetting']['private_chat'] == 0){
				                  	$cond = ['SupportChat.group_id'=>$saveArray['group_id'],
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']/* ,'SupportChat.receiver_id' => $saveArray['reciever_id'] */),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ ),
													    //array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['sender_id']),
												    )
				    			   ];
				                /*      $query = "SELECT supportchats.*,
							users.name as sendername,users.image as senderimage,
							users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM supportchats INNER JOIN users ON users.id=supportchats.sender_id inner join users f 
							on f.id=supportchats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
							AND receiver_id=" . $saveArray['reciever_id'] . ")
							OR( sender_id=" . $saveArray['reciever_id'] . " 
							AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . ")) order by created DESC LIMIT $start_limit,$end_limit ";

				                $user_details = $this->Chat->query($query);*/

				                

				              /*  $query1 = "SELECT count('id') as totalpage FROM `supportchats`
							WHERE (((sender_id=" . $saveArray['sender_id'] . "
							AND receiver_id=" . $saveArray['reciever_id'] . ")
							OR( sender_id=" . $saveArray['reciever_id'] . " 
							AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
				                $user_exist1 = $this->Chat->query($query1);
				                $page_count = $user_exist1[0][0]['totalpage'];
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);*/
								}
								else
								{
									$cond = ['SupportChat.group_id'=>$saveArray['group_id'],'SupportChat.submit_time <=' => $privatestatus_offtime,
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id'] /*,'SupportChat.receiver_id' => $saveArray['reciever_id']*/ ),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ )
												    )
				    			   ];

									
				               /*     $start_limit = $saveArray['page_number']*10-10;
				                    $end_limit = 10;
				                  
				                      $query = "SELECT supportchats.*,
							users.name as sendername,users.image as senderimage,
							users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM supportchats INNER JOIN users ON users.id=supportchats.sender_id inner join users f 
							on f.id=supportchats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
							AND receiver_id=" . $saveArray['reciever_id'] . ")
							OR( sender_id=" . $saveArray['reciever_id'] . " 
							AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . ")) AND submit_time<='$privatestatus_offtime' order by created DESC LIMIT $start_limit,$end_limit ";

				                $user_details = $this->Chat->query($query);


				                $query1 = "SELECT count('id') as totalpage FROM `supportchats`
							WHERE (((sender_id=" . $saveArray['sender_id'] . "
							AND receiver_id=" . $saveArray['reciever_id'] . ")
							OR( sender_id=" . $saveArray['reciever_id'] . " 
							AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
				                $user_exist1 = $this->Chat->query($query1);
				                $page_count = $user_exist1[0][0]['totalpage'];
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);*/
							}
							//pr($cond); die;
							$user_details = $this->SupportChat->find('all',[
				    			'conditions' => $cond,
				    			'offset' => $start_limit,
				    			'limit' => $offset,
				    			'order'=>array('SupportChat.created DESC')
				    		]);

				    		$count = $this->SupportChat->find('count',[
				    			'conditions' => $cond,
				    			'order'=>array('SupportChat.created DESC')
				    		]);

				    			 $page_count =$count;
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);




				}

              if(empty($check_status)){
              		//	pr($saveArray); die;
						    $start_limit = $saveArray['page_number']*10-10;
		                    $end_limit = 10;
						/*		                     [auth_key] => 123
						    [sender_id] => 459
						    [timezone] => Asia/Kolkata
						    [page_number] => 1
						    [reciever_id] => 0
						    [group_id] => 44
						)*/
											



		                  
		            /*          $query = "SELECT supportchats.*,
					users.name as sendername,users.image as senderimage,
					users.image,users.register_type,f.register_type,f.name receviver_name,f.image receviver_img FROM supportchats INNER JOIN users ON users.id=supportchats.sender_id inner join users f 
					on f.id=supportchats.receiver_id where (((sender_id=" . $saveArray['sender_id'] . "
					AND receiver_id=" . $saveArray['reciever_id'] . ")
					OR( sender_id=" . $saveArray['reciever_id'] . " 
					AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . ")) order by created DESC LIMIT $start_limit,$end_limit ";*/
					$user_details = $this->SupportChat->find('all',[
		    			'conditions' => ['SupportChat.group_id'=>$saveArray['group_id'],
		    									'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']/* ,'SupportChat.receiver_id' => $saveArray['reciever_id'] */),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ ),
													    //array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['sender_id']),
												    )
		    						/*'OR' => array(
											    array('SupportChat.sender_id' => $saveArray['sender_id'] ,'SupportChat.receiver_id' => $saveArray['reciever_id'] ),
											    array('SupportChat.receiver_id' => $saveArray['sender_id'] , 'SupportChat.sender_id' => $saveArray['reciever_id'] )
										    )*/
		    			],
		    			'offset' => $start_limit,
		    			'limit' => $end_limit,
		    			'order'=>array('SupportChat.id DESC')
		    		]);
			           //$user_details = $this->Chat->query($query);
		               // pr($query);
		                //pr($saveArray); die;
		               // pr($user_details); die;

		                $query1 = "SELECT count('id') as totalpage FROM `supportchats`
					WHERE (((sender_id=" . $saveArray['sender_id'] . "
					AND receiver_id=" . $saveArray['reciever_id'] . ")
					OR( sender_id=" . $saveArray['reciever_id'] . " 
					AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
		                $user_exist1 = $this->Chat->query($query1);
		                $page_count = $user_exist1[0][0]['totalpage'];
		                $page_count = $page_count / 10;
		                $page_count = ceil($page_count);
			  }
            // pr($user_details);die;
                if (!empty($user_details)) 
				{
                   /* foreach ($user_details as $k => $values) {
						 $data['msg_id'] = $values['supportchats']['id'];
                         $data['senderid'] = $values['supportchats']['sender_id'];
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
						 $data['msg'] = !empty($values['supportchats']['message'])?$values['supportchats']['message']:'';
					  $data['type'] = $values['supportchats']['type'];
					  if($values['supportchats']['type']=='I'){
				      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['supportchats']['image'];	
					  }else{
						 $data['image'] = ''; 
					  }
					   if($values['supportchats']['type']=='L'){
				      $data['lat'] = $values['supportchats']['lat'];	
				      $data['lng'] =$values['supportchats']['lng'];
					  }else{
						$data['lat'] = "";	
				        $data['lng'] ="";
					  }
					  	//pr($values['chats']); die;

					    $date = new DateTime($values['supportchats']['submit_time'], new DateTimeZone($saveArray['timezone']));
						//pr($date->format('Y-m-d H:i:s'));
						$date->modify('-5 minutes');
						$currentDateTime = $date->format('Y-m-d H:i:s');
	                    $usertimezone = $saveArray['timezone'];
	                    //$data['time'] = $this->ConvertTimezoneToAnotherTimezone($currentDateTime, date_default_timezone_get(), $usertimezone);
						date_default_timezone_set($saveArray['timezone']);
						//$current_date = date('Y-m-d H:i:s');
	                    //pr(date('Y-m-d H:i:s'));
						//pr($currentDateTime); die;
	                    //$datetime = explode(" ",$currentDateTime);
	                   // $data['time'] = $datetime[1];
	                     //$data['time'] = date('h:i A', strtotime($data['time']. "+12 minutes"));
	                    $data['time'] =$date->format('h:i A');
	                    $data['date'] = $date->format('Y-m-d');
	                        $data1[] = ($data);
	                    }
						 if (!empty($data1)) {
	                        $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count);
	                    } else {
	                        $result = array('status' => '0', 'message' => 'Groups not found.');
	                    }*/
	                    
	                     foreach ($user_details as  $values) {
	                     	//pr($values); die;
						 $data['msg_id'] = $values['SupportChat']['id'];
                         $data['senderid'] = $values['SupportChat']['sender_id'];
						 if(!empty($values['Sender']['image'])){
							 if($values['Sender']['register_type']=="F"){
								$data['senderimage'] = $values['Sender']['image'];
							 }
							 if($values['Sender']['register_type']=="N"){
								$data['senderimage'] = BASE_URL . "img/profile_images/" .$values['Sender']['image'];
							 }
						 }
						 if(empty($values['Sender']['image'])){
							 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
								$data['senderimage'] = $defaulturl;
						 }
						// pr($values);die;
                         $data['sendername'] = $values['Sender']['name'];
						 $data['msg'] = !empty($values['SupportChat']['message'])?$values['SupportChat']['message']:'';
					  $data['type'] = $values['SupportChat']['type'];
					  if($values['SupportChat']['type']=='I'){
				      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['SupportChat']['image'];	
					  }else{
						 $data['image'] = ''; 
					  }
					   if($values['SupportChat']['type']=='L'){
				      $data['lat'] = $values['SupportChat']['lat'];	
				      $data['lng'] =$values['SupportChat']['lng'];
					  }else{
						$data['lat'] = "";	
				        $data['lng'] ="";
					  }
					  	//pr($values['chats']); die;

					    $date = new DateTime($values['SupportChat']['submit_time'], new DateTimeZone($saveArray['timezone']));
						//$date->modify('-5 minutes');
						$currentDateTime = $date->format('Y-m-d H:i:s');
	                    $usertimezone = $saveArray['timezone'];
						date_default_timezone_set($saveArray['timezone']);
						if(!empty($values['SupportChat']['close_time']) && $values['SupportChat']['close_time'] !== null){
							 $close_time = new DateTime($values['SupportChat']['close_time'], new DateTimeZone($saveArray['timezone']));
							$close_time->modify('-5 minutes');
							date_default_timezone_set($saveArray['timezone']);
							$data['close_time'] = $close_time->format('M j, Y, g:i a') ;
						} else {
							$data['close_time'] = "" ;	
						}
	                    $data['time'] =$date->format('h:i A');
	                    $data['date'] = $date->format('Y-m-d');
	                    $data['is_closed'] =$values['SupportChat']['is_closed'];
	                    $data['session_id'] =$values['SupportChat']['session_id'];
	                        $data1[] = ($data);
	                    }
						 if (!empty($data1)) {
	                        $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count );
	                    } else {
	                        $result = array('status' => '0', 'message' => 'Groups not found.');
	                    }





                } else {
                	// Lands Here
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
	
	/*end code*/

    
    	function closechat()
		{
    			$saveArray = $this->data;
				
		        if (!empty($saveArray['group_id']) && !empty($saveArray['user_id']) ) 
				{
		        		$cond = array('SupportChat.group_id'=>$saveArray['group_id'], 'SupportChat.is_closed '=>'No',
                                    'OR' => array(
                                                array('SupportChat.sender_id' => $saveArray['user_id'] ),
                                                array('SupportChat.receiver_id' => $saveArray['user_id']),
                                            )
                                 );
							$chats = $this->SupportChat->find('all',[
										'conditions' => $cond,	
										'fields' => ['id']
									]);
							$uniqueid = date('YmdHis').$saveArray['group_id'];
						   	
						   	$total = count($chats);
							$cc = 1;
							$date1 = new \DateTime();
							
							if(isset($saveArray['timezone']))
							{
							$date1->setTimezone(new DateTimeZone($saveArray['timezone']));	
							}
							else
							{
								$date1->setTimezone(new DateTimeZone('Asia/Calcutta'));
							}
							
							//$startdate = $date1->format('Y-m-d H:i:s');
							foreach($chats as $chat){
								if($cc < $total){
									$savedata =['is_closed' => 'Yes','session_id' => $uniqueid , 'close_time' => $date1->format('Y-m-d H:i:s') ];
						   				$cc++;	

						   			} else {
						   				//pr($counter);
						   				//pr($total); die;
						   				$savedata =['is_closed' => 'Yes','session_id' => $uniqueid , 'close_time' => $date1->format('Y-m-d H:i:s') ,'is_last_message' => 'Yes'];
						   			}
						   			//pr($chat['SupportChat']['id']);
						   			//pr($savedata); die;
								$this->SupportChat->read(null,$chat['SupportChat']['id']);
								$this->SupportChat->set($savedata);
								$this->SupportChat->save();
							}
						 $result = array('status' => '1', 'message' => 'Chat Closed!','session_id' => $uniqueid);
				}else{
		            $result = array('status' => '0', 'message' => 'Please fill all fields.');
		        }
		       // die;
		        $this->set(array(
		            'result' => $result,
		            '_serialize' => array('result')
		        ));		
    	}

    function transferchat(){
    	$saveArray = $this->data;
    		//pr($saveArray); die;
    	if(!empty($saveArray['group_id']) && !empty($saveArray['user_id'])  && !empty($saveArray['staff_id'])) {
    		$userSession = $this->Session->read("SESSION_ADMIN");
			$this->set('userSession',$userSession); 
			/*$cond = array('SupportChat.group_id'=>$saveArray['group_id'],'SupportChat.is_closed '=>'No',
                                    'OR' => array(
                                                array('SupportChat.sender_id' => $saveArray['user_id'] , 'SupportChat.receiver_id' => $userSession['id'] ),
                                                array('SupportChat.receiver_id' => $saveArray['user_id'] , 'SupportChat.sender_id' => $userSession['id']),
                                            )
                                 );*/
            $cond = array('SupportChat.group_id'=>$saveArray['group_id'],'SupportChat.is_closed '=>'No',
                                    'OR' => array(
                                                array('SupportChat.sender_id' => $userSession['id'] , 'SupportChat.receiver_id' => $saveArray['user_id'] ),
                                                array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['user_id'] ),
                                                array('SupportChat.sender_id' => $saveArray['user_id'] , 'SupportChat.receiver_id' => $userSession['id'] ),
                                                array('SupportChat.sender_id' => $saveArray['user_id']),
                                                array('SupportChat.receiver_id' => $saveArray['user_id']),
                                            )
                                 );
							$chats = $this->SupportChat->find('all',[
										'conditions' => $cond,	
										'fields' => ['id','taken_by']
									]);
							//pr($chats); die;
							//$uniqueid = date('YmdHis').$saveArray['group_id'];
							//pr($chats); die;
							//pr($saveArray['staff_id']); die;
							foreach($chats as $chat){
								$savedata =['taken_by' => $saveArray['staff_id']];
								//pr($this->SupportChat->read(null,$chat['SupportChat']['id'])); die;
								$this->SupportChat->read(null,$chat['SupportChat']['id']);
								$this->SupportChat->set($savedata);
								$this->SupportChat->save();
							}
						 $result = array('status' => '1', 'message' => 'Chats Transfered');
    		//$allchats = 
    	} else {
		            $result = array('status' => '0', 'message' => 'Please fill all fields.');
		        }

		        $this->set(array(
		            'result' => $result,
		            '_serialize' => array('result')
		        ));		
    }

    function feedback() {
        $saveArray = $this->data;
        $this->SupportFeedback->set($saveArray);
        if(!empty($saveArray['user_id']) && !empty($saveArray['group_id'])  && !empty($saveArray['rating'])  && !empty($saveArray['session_id']) ) {
            $user_exist = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['user_id'])));
            $staff_exist = $this->User->find('first', array('conditions' => array('User.id' => $saveArray['staff_id'])));
			$group_exist = $this->Group->find('first', array('conditions' => array('Group.id' => $saveArray['group_id'])));
			 $admin_id = $this->User->find('first', array('conditions' => array('User.id' => $group_exist['Group']['created_id'])));
            if (!empty($user_exist) && !empty($group_exist)){
                $this->SupportFeedback->save($saveArray,array('validate'=>false));
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
					//$this->Email->send();
				
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

    	function help_api() 
		{
	//	Configure::write('debug', 2);
        $saveArray = $this->data;
        if (!empty($saveArray['sender_id']) AND !empty($saveArray['group_id']) AND !empty($saveArray['lat']) AND !empty($saveArray['lng'])){
				   $group = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
				  $staff_ids =  $this->GroupMember->find('list', array(
						         'conditions' => array('group_id'=>$group['Group']['id'],'type'=>'S'),
						         'fields' => array('id','user_id')
						    ));
				  $saveArray['user_id'] = $saveArray['sender_id'];
				   if((intval($group['Group']['id']) == 4300000654654654646540 || intval($group['Group']['id']) == 44000054546787987986746548 ) )
				   {
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
				   	

				   } 
				   else 
				   {
					
					$messag2="";
					$saveArra['sender_id']=$saveArray['sender_id'];
					$saveArra['receiver_id'] =(empty($saveArray['receiver_id']) || !isset($saveArray['receiver_id'])) ? 0 :  $saveArray['receiver_id'];
					$saveArra['taken_by']= $saveArray['receiver_id'];
					//$saveArra['user_id']=$saveArray['user_id'];
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
					
					$own = date('Y-m-d H:i:s');
                    $time = strtotime($own);
                    $time = $time - (6 * 60);
                    $date = date("Y-m-d H:i:s", $time);
					
					$saveArra['submit_time'] = $date;
					$saveArra['time_zone'] = $submit_timezone;
					$saveArra['group_id'] =$group['Group']['id'];
					$this->SupportChat->saveAll($saveArra,array('validate'=>false));
					$messag1="PANIC: I need immediate help at the location!";
					$saveArray['sender_id']=$saveArray['sender_id'];
					$saveArray['receiver_id'] =(empty($saveArray['receiver_id']) || !isset($saveArray['receiver_id'])) ? 0 :  $saveArray['receiver_id'];
					$saveArray['taken_by']= $saveArray['receiver_id'];
					$saveArray['group_id']=$group['Group']['id'];
					$saveArray['message']=$messag1;
					$saveArray['lat']="";
					$saveArray['lng']="";
					$saveArray['type']="T";  
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
					//$date = date('Y-m-d H:i:s');
					$saveArray['submit_time'] = $date;
					
					$saveArray['time_zone'] = $submit_timezone;
					$save =$this->SupportChat->saveAll($saveArray,array('validate'=>false));
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
						$last_id =$this->SupportChat->getLastInsertId();
						$this->SupportChat->deleteAll(array('id' => $last_id), false);
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
	
	
	/* new code for helpdesk badges*/
	function badges_for_helpdesk_message()
	{
		$saveArray = $this->data;
		$allmembers = $this->GroupMember->find('all',array('conditions'=>array('group_id'=>$saveArray['group_id'])));
	      for($i=0;$i<count($allmembers);$i++)
	       {
		        if($allmembers[$i]['GroupMember']['user_id'] != $saveArray['sender_id'] )
				{
		         	$table_name="HelpdeskBadgesForApp";
		           	$this->request->data[$table_name]['sender_id'] =$saveArray['sender_id'];
			        $this->request->data[$table_name]['receiver_id'] = $allmembers[$i]['GroupMember']['user_id'];
			        $this->request->data[$table_name]['group_id'] = $saveArray['group_id'];
					$this->request->data[$table_name]['for_check'] = "home";
			        $this->$table_name->saveAll($this->request->data);
					
					$table_name="HelpdeskBadgesForApp";
		           	$this->request->data[$table_name]['sender_id'] =$saveArray['sender_id'];
			        $this->request->data[$table_name]['receiver_id'] = $allmembers[$i]['GroupMember']['user_id'];
			        $this->request->data[$table_name]['group_id'] = $saveArray['group_id'];
					$this->request->data[$table_name]['for_check'] = "private";
			        $this->$table_name->saveAll($this->request->data);
					
		        }  
           }
		   die;
			/* end code for badges */
	}



 function  newchat(){


 $saveArray = $this->data;

  $userSession['id'] =$saveArray['sender_id'];
 	//$resultdata = $this->Common->getuserchat($saveArray, $userSession,"get_userchat");
   $mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => $saveArray['group_id'] ,'type' => 'S' ], 
								 'fields' => ['id','user_id']
									));
				//pr($mygroupsmembers); die;
				if(count($mygroupsmembers) > 0){
						$groupmembersdata = $this->User->find('list',array('conditions'=> ['id' => array_values($mygroupsmembers) , array('NOT' => array('id' => $userSession['id'] ) )	 ], 
													  'fields' => ['id','name']
													));	
					} else {
						$groupmembersdata = [];
					}

					$resultdata = $this->Common->getuserchat($saveArray, $userSession, "get_userchat");
				 
		$result = ['status' => '1' , 'data' => $resultdata['data'] , 'nextPage' => $resultdata['nextPage'] ,'currentPage' => $resultdata['currentPage'] ,'totalPages' => $resultdata['totalPages'] ,'feedbacks' =>  $resultdata['feedbacks'] , 'groupmembersdata' => $groupmembersdata];

		 $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));

}



function get_chat23() 
	{
        
      $saveArray = $this->data;
      $condition = " User.id='" . $saveArray['sender_id'] . "' ";
      $senderdata = $this->User->find('first', array('conditions' => $condition));

          $usernamewhoclick= $saveArray['sender_id'];
          $Currentlogged= $saveArray['reciever_id'];

      $condition = " User.id='" . $saveArray['reciever_id'] . "' ";
      $user_exist = $this->User->find('first', array('conditions' => $condition));
			if($user_exist['User']['user_type'] == "S"){

				$saveArray['user_id'] = $saveArray['reciever_id'];
				$saveArray['last_id'] = 0;
	            $userSession['id'] =$saveArray['sender_id'];
	 	

	            $mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => $saveArray['group_id'] ,'type' => 'S' ], 
									 'fields' => ['id','user_id']
										));
				 if(count($mygroupsmembers) > 0){
						$groupmembersdata = $this->User->find('list',array('conditions'=> ['id' => array_values($mygroupsmembers) , array('NOT' => array('id' => $userSession['id'] ) )	 ], 
													  'fields' => ['id','name']
													));	
					} else {
						$groupmembersdata = [];
					}

					$resultdata = $this->Common->getuserchat($saveArray, $userSession, "get_userchat");


                $FInal =array();
                 foreach($resultdata['data'] as $v){
	              
                   $FInal[] = $v['SupportChat'];

                }

                $LAsting = array();
                 foreach($FInal as $v){
                 	
                                $v1['msg_id'] = $v['id']; 
										$v1['senderid'] = $v['sender_id']; 
										 if(!empty($senderdata['User']['image'])){
											 if($senderdata['User']['register_type']=="F"){
												$v1['senderimage'] = $senderdata['User']['image'];
											 }
											 if($senderdata['User']['register_type']=="N"){
												$v1['senderimage'] = BASE_URL . "img/profile_images/" .$senderdata['User']['image'];
											 }
										 }
										 if(empty($senderdata['Sender']['image'])){
											 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
												$v1['senderimage'] = $defaulturl;
										 }
										$v1['sendername'] = $senderdata['User']['name'];//
										$v1['msg'] = $v['message'];
										$v1['type'] = $v['type'];
										  $v1['msg'] = !empty($v['message'])?$v['message']:'';
										  $v1['type'] = $v['type'];
										  if($v['type']=='I'){
									      $v1['image'] = BASE_URL . "img/groupchatimg/" . $v['image'];	
										  }else{
											 $v1['image'] = ''; 
										  }
										   if($v['type']=='L'){
									       $v1['lat'] = $v['lat'];	
									       $v1['lng'] =$v['lng'];
										  }else{
											$v1['lat'] = "";	
									        $v1['lng'] ="";
										  }
									    $date = new DateTime($v['submit_time'], new DateTimeZone($saveArray['timezone']));
										//$date->modify('-5 minutes');
										$currentDateTime = $date->format('Y-m-d H:i:s');
					                    $usertimezone = $saveArray['timezone'];
										date_default_timezone_set($saveArray['timezone']);
										if(!empty($v['close_time']) && $v['close_time'] !== null){
											 $close_time = new DateTime($v['close_time'], new DateTimeZone($saveArray['timezone']));
											$close_time->modify('-5 minutes');
											date_default_timezone_set($saveArray['timezone']);
											$v1['close_time'] = $close_time->format('M j, Y, g:i a') ;
										} else {
											$v1['close_time'] = "" ;	
										}

					                    $v1['time'] =$date->format('h:i A');
					                    $v1['date'] = $date->format('Y-m-d');
					                    $v1['is_closed'] =$v['is_closed'];
					                    $v1['session_id'] =$v['session_id'];
				                        $LAsting[] = $v1;
                 

				                  	  
                 }


	   $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($LAsting),'totalPages'=>$resultdata['totalPages'] );

			}else{


        if (!empty($saveArray['sender_id']) && !empty($saveArray['timezone']) && !empty($saveArray['page_number']) && !empty($saveArray['group_id'])) 
		{

                $condition = " User.id='" . $saveArray['sender_id'] . "' ";
                $user_exist = $this->User->find('first', array('conditions' => $condition));

            if ($user_exist){
            $condition3 = "CheckSetting.user_id='" . $saveArray['sender_id'] . "' And CheckSetting.group_id='" . $saveArray['group_id'] . "'";
                $check_status= $this->CheckSetting->find('first', array('conditions' => $condition3));
				if(!empty($check_status)){
				               $privatestatus_offtime = $check_status['CheckSetting']['privatestatus_offtime'];
				               $start_limit = $saveArray['page_number']*10-10;
				                $end_limit = 10;
								if($check_status['CheckSetting']['private_chat'] == 0){
				                  	$cond = ['SupportChat.group_id'=>$saveArray['group_id'],
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']/* ,'SupportChat.receiver_id' => $saveArray['reciever_id'] */),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ ),
													    //array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['sender_id']),
												    )
				    			   ];
				
								}else{
									$cond = ['SupportChat.group_id'=>$saveArray['group_id'],'SupportChat.submit_time <=' => $privatestatus_offtime,
				    						'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id'] /*,'SupportChat.receiver_id' => $saveArray['reciever_id']*/ ),
													    array('SupportChat.receiver_id' => $saveArray['sender_id'] /*, 'SupportChat.sender_id' => $saveArray['reciever_id']*/ )
												    )
				    			   ];

							}
							$user_details = $this->SupportChat->find('all',[
				    			'conditions' => $cond,
				    			'offset' => $start_limit,
				    			'limit' => $offset,
				    			'order'=>array('SupportChat.created DESC')
				    		]);

				    		$count = $this->SupportChat->find('count',[
				    			'conditions' => $cond,
				    			'order'=>array('SupportChat.created DESC')
				    		]);

				    			 $page_count =$count;
				                $page_count = $page_count / 10;
				                $page_count = ceil($page_count);

				}

              if(empty($check_status)){
						    $start_limit = $saveArray['page_number']*10-10;
		                    $end_limit = 10;
					
					 $user_details = $this->SupportChat->find('all',[
		    			'conditions' => ['SupportChat.group_id'=>$saveArray['group_id'],
		    									'OR' => array(
													    array('SupportChat.sender_id' => $saveArray['sender_id']),
													   array('SupportChat.receiver_id' => $saveArray['sender_id']), 
													   array('SupportChat.sender_id' => $saveArray['reciever_id']), 
													   array('SupportChat.receiver_id' => $saveArray['reciever_id']), 
													   //'SupportChat.sender_id' =>$saveArray['reciever_id']),
												    )
		    			],
		    			'offset' => $start_limit,
		    			'limit' => $end_limit,
		    			'order'=>array('SupportChat.id DESC')
		    		]);

		                $query1 = "SELECT count('id') as totalpage FROM `supportchats`
					WHERE (((sender_id=" . $saveArray['sender_id'] . "
					AND receiver_id=" . $saveArray['reciever_id'] . ")
					OR( sender_id=" . $saveArray['reciever_id'] . " 
					AND receiver_id=" . $saveArray['sender_id'] . ")) AND (supportchats.group_id=" . $saveArray['group_id'] . "))";
		                $user_exist1 = $this->Chat->query($query1);
		                $page_count = $user_exist1[0][0]['totalpage'];
		                $page_count = $page_count / 10;
		                $page_count = ceil($page_count);
			  }
            //pr($user_details);
                if (!empty($user_details)) {
                  
	                     foreach ($user_details as  $values) {
	                     	//pr($values); die;
						 $data['msg_id'] = $values['SupportChat']['id'];
                         $data['senderid'] = $values['SupportChat']['sender_id'];
						 if(!empty($values['Sender']['image'])){
							 if($values['Sender']['register_type']=="F"){
								$data['senderimage'] = $values['Sender']['image'];
							 }
							 if($values['Sender']['register_type']=="N"){
								$data['senderimage'] = BASE_URL . "img/profile_images/" .$values['Sender']['image'];
							 }
						 }
						 if(empty($values['Sender']['image'])){
							 $defaulturl = BASE_URL."images/common/user_img_placeholder.png";
								$data['senderimage'] = $defaulturl;
						 }
						// pr($values);die;
                         $data['sendername'] = $values['Sender']['name'];
						 $data['msg'] = !empty($values['SupportChat']['message'])?$values['SupportChat']['message']:'';
					  $data['type'] = $values['SupportChat']['type'];
					  if($values['SupportChat']['type']=='I'){
				      $data['image'] = BASE_URL . "img/groupchatimg/" . $values['SupportChat']['image'];	
					  }else{
						 $data['image'] = ''; 
					  }
					   if($values['SupportChat']['type']=='L'){
				       $data['lat'] = $values['SupportChat']['lat'];	
				       $data['lng'] =$values['SupportChat']['lng'];
					  }else{
						$data['lat'] = "";	
				        $data['lng'] ="";
					  }
					  	//pr($values['chats']); die;

					    $date = new DateTime($values['SupportChat']['submit_time'], new DateTimeZone($saveArray['timezone']));
						//$date->modify('-5 minutes');
						$currentDateTime = $date->format('Y-m-d H:i:s');
	                    $usertimezone = $saveArray['timezone'];
						date_default_timezone_set($saveArray['timezone']);
						if(!empty($values['SupportChat']['close_time']) && $values['SupportChat']['close_time'] !== null){
							 $close_time = new DateTime($values['SupportChat']['close_time'], new DateTimeZone($saveArray['timezone']));
							$close_time->modify('-5 minutes');
							date_default_timezone_set($saveArray['timezone']);
							$data['close_time'] = $close_time->format('M j, Y, g:i a') ;
						} else {
							$data['close_time'] = "" ;	
						}
	                    $data['time'] =$date->format('h:i A');
	                    $data['date'] = $date->format('Y-m-d');
	                    $data['is_closed'] =$values['SupportChat']['is_closed'];
	                    $data['session_id'] =$values['SupportChat']['session_id'];
						//$data['taken_by'] =$values['SupportChat']['taken_by'];
						
	                        $data1[] = ($data);
	                    }



						 if (!empty($data1)) {
	                        $result = array('status' => '1', 'message' => 'successfully.', 'data' => array_reverse($data1),'totalPages'=>$page_count );
	                    } else {
	                        $result = array('status' => '0', 'message' => 'Groups not found.');
	                    }





                } else {
                	// Lands Here
                    $result = array('status' => '0', 'message' => 'Message not found.');
                }
            } else {
                $result = array('status' => '0', 'message' => 'group_id or user_id not match.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please fill all fields');
        }


			}

        $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));



    }
	


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
	


	function timezoneprivate($submited_time,$submited_time_zone,$user_time_zone)
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
		       $timestamp = $dt->format('M j, Y, g:i a');
			   return $timestamp;
		
	}







	function testing()
	{

	    $saveArray = $this->data;
       $currentDateTime =  '2017-12-20 12:49:57';
		$submit_time_zone = 'Asia/Kolkata';
        $usertimezone =  'Asia/Kolkata';
		$converted_data = $this->timezoneprivate($currentDateTime,$submit_time_zone, $usertimezone);
       //		print_r($converted_data);


/*$date = new DateTime('2017-12-20 12:49:57', new DateTimeZone('Asia/Kolkata'));	
pr($date);
die;*/

print_r($converted_data);
die;




	}



	function broadcast_for_helpdesk()
	{
		$saveArray = $this->data;
		if(!empty($saveArray['user_id']) AND !empty($saveArray['group_id']) AND (!empty($_FILES['image']['name']) OR !empty($saveArray['message'])))
		{
				$group_id =  $saveArray['group_id'];
				//$user_id =  $this->saveArray['user_id'];
			if(!empty($_FILES['image']['name']))
			{
				$destination= WWW_ROOT . 'img/groupchatimg/' ;
				$file =time(). $_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'],$destination.$file);
				$image_name = $file;
			}
			else
			{
				$image_name = '';
			}
			
			if(!empty($saveArray['message']))
			{
				$message =  $saveArray['message'];
			}
			else
			{
				$message = " ";
			}
			 $mygroupsmembers = $this->GroupMember->find('list',array('conditions'=> ['group_id' => $saveArray['group_id']], 
														  'fields' => ['id','user_id']
														  ));
			$groupname = $this->Group->find('first',array('conditions'=> ['id' => $saveArray['group_id']]));
			$sender_user = $this->User->find('first',array('conditions'=> ['id' => $saveArray['user_id']]));
			if(!empty($sender_user['User']['image']))
			{
				$sender_image = $sender_user['User']['image'];
			}
			else
			{
				$sender_image = "default.png";
			}
			
			
					foreach($mygroupsmembers as $member_id)
					{
						$user_id = $member_id;
						$saveArra['group_id'] = $group_id;
						$saveArra['user_id'] = $member_id;
						$saveArra['message'] = $message;
						$saveArra['image'] =$image_name;
						$this->Newbroadcast->saveAll($saveArra,array('validate'=>false));
						
						$userExist=$this->User->find('first',array('conditions'=> array('User.id'=>$user_id)));
						if($userExist['User']['device_type']=='A' &&  !empty($userExist['User']['device_id']))
						{
								//echo "exist";
							$device_id=$userExist['User']['device_id'];
							
							$group_name= $groupname['Group']['name'];
							$date = date('Y-m-d H:i:s');
							//$sender['User']['name']="chetan";
							$sender['User']['name']= $sender_user['User']['name'];
							$sender_image = $sender_image;
							$friend['User']['name']= $sender_user['User']['name'];
							$friend['User']['id']= $sender_user['User']['id'];
							$sender['User']['user_type'] = $sender_user['User']['user_type'];
							//$saveArray['group_id']=66;
							$friend_image='https://www.corover.co.in/JKtourismdev/img/profile_images/'.$sender_image;
							$groupmsg_img='https://www.corover.co.in/JKtourismdev/img/group_logo/'.$groupname['Group']['image'];
							$saveArray['message']="hello";
							//pr()
							 if(!empty($grp_img['Group']['mobile']))
							 {
								$mobile = $grp_img['Group']['mobile'];
							 }
							 else
							 {
								 $mobile =0;
							 }
							$message1 = array(
							'message' => "You have a new Broadcast Message",
							'noti_for' => 'helpdesk',
							'date' => $date, 
							'sender_name' => $sender_user['User']['name'],
							'sender_image' => BASE_URL . "img/group_logo/" .$groupname['Group']['icon'],
							'friend_name' => $sender_user['User']['name'], 
							'name' => $sender_user['User']['name'],
							'sender_id' => $sender_user['User']['id'],
							'friend_type' => $sender_user['User']['user_type'],
							'group_id' => $group_id,
							
							 'broadcast_image' => $image_name,
							'broadcast_message' => $message,
							
							'friend_image' =>$friend_image,
							'message_img' => $groupmsg_img,
							'message_msg' =>$saveArray['message'],
							'group_name'=>$groupname['Group']['name'],
							'country_code'=>$groupname['Group']['country_code'],
							'mobile'=>$mobile,
							'image' => BASE_URL . "img/group_images/" . $groupname['Group']['image'],
							);
							
							$this->Common->android_send_notification(array($device_id),$message1);
						}
						
					}
					print_r($message1);
		}
		else
		{
			$result = array('status' => '0','message' => 'Please fill all fields');
		}
				$result = array('status' => '1', 'message' => 'Successfully Sent');
				
				 $this->set(array(
            'result' => $result,
            '_serialize' => array('result')
        ));
													  
		
		
	}
	
	
	
	
}