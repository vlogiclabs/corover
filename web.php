



<h3> Send Message in Private chat </h3>

<br>


<br>
<br><br><br>
<form action = "http://localhost:3000/privatechat" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;" >
<!-- 
<input type="file" name="image"> -->
<input name="timezone" type="text" placeholder="timezone">
<input name="sender_id" type="text" placeholder="sender_id ">
<input name="receiver_id" type="text" placeholder="receiver_id ">
<input name="submit_time" type="text" placeholder="submit_time ">
<input name="status" type="text" placeholder="status ">
<input name="group_id" type="text" placeholder="group_id ">
<input name="type" type="text" placeholder="Type ">
<input name="message" type="text" placeholder="messgae ">
<input name="lat" type="text" placeholder="Lattiude ">
<input name="long" type="text" placeholder="Longtitude ">

<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>









<?php
$url = "http://localhost/jk/api/";
$auth_key = "123";
?>

<h3>1. Register </h3>

BULK values :  
auth_key:123
email:sdfdsf@gmail.com
password:123
name:dsf
mobile:dsf
address:dsf
designation:dsf
gender:dsf
country_code:+91
timezone:Asia/kolkata


ALTER TABLE `users` ADD `timezone` VARCHAR(255) NULL DEFAULT 'Asia/kolkata' AFTER `role`;
ALTER TABLE `users` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;


<br><br><br>
<form action = "<?php echo $url;?>register" method="POST" enctype="multipart/form-data" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input type="file" name="image">
<input name="name" type="text" placeholder="first name">
<input name="email" type="text" placeholder="email">
<input name="mobile" type="text" placeholder="phone">
<input name="address" type="text" placeholder="address ">
<input name="designation" type="text" placeholder="designation">
<input name="gender" type="text" placeholder="gender">
<input name="password" type="text" placeholder="password">
<input name="country_code" type="text" placeholder="country_code">
<input name="timezone" type="text" placeholder="timezone">
  <input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
  <input type="submit" value="Signup">
</form>

 


<h3>2. Login Api </h3>
<br>

Bulk keys
auth_key:123
password:12345
mobile:9916277876
country_code:91
timezone:Asia/kolkata

<br><br><br>
<form action = "<?php echo $url;?>login" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="mobile" type="text" placeholder="phone">
<input name="password" type="text" placeholder="password">
<input name="country_code" type="text" placeholder="country_code">
<input name="timezone" type="text" placeholder="timezone">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>


<h3>Timezone test </h3>

<br><br><br>
<form action = "<?php echo $url;?>testingtime" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>3. Verifuication  </h3>
<br>
ALTER TABLE `groups` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;

<br>
ALTER TABLE `group_members` ADD `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created`, ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;

<br><br><br>
<form action = "<?php echo $url;?>verification" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="user_id" type="text" placeholder="USer id">
<input name="verification_code" type="text" placeholder="Verification code ">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>

<h3>4. Resend Code  </h3>

<br><br><br>
<form action = "<?php echo $url;?>resend_code" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="user_id" type="text" placeholder="USer id">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>

<h3>5. Forgot password  </h3>

<br><br><br>
<form action = "<?php echo $url;?>forgot_password" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="email" type="text" placeholder="Email id">
<input name="mobile" type="text" placeholder="mobile">
<input name="country_code" type="text" placeholder="country code">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>


<h3>6. Fetch Profile  </h3>

<br><br><br>
<form action = "<?php echo $url;?>fetch_profile" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="user_id" type="text" placeholder="user_id id">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>7. Get GRoups</h3>

<br><br><br>
<form action = "<?php echo $url;?>get_groups" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="user_id" type="text" placeholder="user_id id">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>8. Get GRoups Staff</h3>

<br><br><br>
<form action = "<?php echo $url;?>get_groupstaff" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="user_id" type="text" placeholder="user_id ">
<input name="group_id" type="text" placeholder="group_id ">
<input name="page_no" type="text" placeholder="page_no ">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>9. Get Groups Users</h3>
ALTER TABLE `groupinformations` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;
<br><br><br>
<form action = "<?php echo $url;?>get_groupusers" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="user_id" type="text" placeholder="user_id ">
<input name="group_id" type="text" placeholder="group_id ">
<input name="page_no" type="text" placeholder="page_no ">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>




<h3>10. Get Groups Details</h3>
<br><br><br>
<form action = "<?php echo $url;?>get_groupdetails" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="user_id" type="text" placeholder="user_id ">
<input name="group_id" type="text" placeholder="group_id ">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>




<h3>11. Send Message in Group </h3>
username : m.geniusappdeveloper@gmail.com
pass :vll@2017
<br>

ALTER TABLE `unreadgroupbadges` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;

<br>
<br><br><br>
<form action = "<?php echo $url;?>send_message" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">

<input type="file" name="image">
<input name="timezone" type="text" placeholder="timezone">
<input name="user_id" type="text" placeholder="user_id ">
<input name="group_id" type="text" placeholder="group_id ">
<input name="type" type="text" placeholder="Type ">
<input name="message" type="text" placeholder="messgae ">
<input name="lat" type="text" placeholder="Lattiude ">
<input name="long" type="text" placeholder="Longtitude ">

<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>12. Send Message in Private chat </h3>

<br>


<br>
<br><br><br>
<form action = "<?php echo $url;?>send_privatemessage" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;" enctype="multipart/form-data">

<input type="file" name="image">
<input name="timezone" type="text" placeholder="timezone">
<input name="sender_id" type="text" placeholder="sender_id ">
<input name="receiver_id" type="text" placeholder="receiver_id ">
<input name="submit_time" type="text" placeholder="submit_time ">
<input name="status" type="text" placeholder="status ">
<input name="group_id" type="text" placeholder="group_id ">
<input name="type" type="text" placeholder="Type ">
<input name="message" type="text" placeholder="messgae ">
<input name="lat" type="text" placeholder="Lattiude ">
<input name="long" type="text" placeholder="Longtitude ">

<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>13. Get Services </h3>
username : m.geniusappdeveloper@gmail.com
pass :vll@2017
<br>

ALTER TABLE `unreadgroupbadges` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;

<br>
<br><br><br>
<form action = "<?php echo $url;?>get_services" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">



<input name="group_id" type="text" placeholder="group_id ">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>

<h3>14. Get Public Group List </h3>
username : m.geniusappdeveloper@gmail.com
pass :vll@2017
<br>

ALTER TABLE `unreadgroupbadges` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;

<br>
<br><br><br>
<form action = "<?php echo $url;?>get_public_group_list" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">



<input name="user_id" type="text" placeholder="user_id ">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>

<h3>15. Delete Group </h3>
username : m.geniusappdeveloper@gmail.com
pass :vll@2017
<br>

<br>
<br><br><br>
<form action = "<?php echo $url;?>delete_group" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="group_id" type="text" placeholder="group_id">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>


<h3>16. Chat On Off </h3>
username : m.geniusappdeveloper@gmail.com
pass :vll@2017
<br>

<br>
<br><br><br>
<form action = "<?php echo $url;?>chat_on_off" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="group_id" type="text" placeholder="group_id">

<input name="group_chat" type="text" placeholder="0n/off">
<input name="private_chat" type="text" placeholder="on/off">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input name="timezone" type="text" placeholder="timezone">
<input type="submit" value="Signup">
</form>




<h3>17. Get PRivate Chat</h3>

<br><br><br>
<form action = "<?php echo $url;?>get_chat" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="timezone" type="text" placeholder="timezone">
<input name="sender_id" type="text" placeholder="sender_id ">
<input name="reciever_id" type="text" placeholder="reciever_id ">
<input name="group_id" type="text" placeholder="group_id ">
<input name="page_no" type="text" placeholder="page_no ">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>


<h3>18 Block</h3>

<br><br><br>
<form action = "<?php echo $url;?>block" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">

<input name="user_id" type="text" placeholder="user_id">
<input name="group_id" type="text" placeholder="group_id ">
<input name="friend_id" type="text" placeholder="friend_id ">
<input name="reason" type="text" placeholder="reason ">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>

<h3>19. change password  </h3>

<br><br><br>
<form action = "<?php echo $url;?>change_password" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="old_password" type="text" placeholder="old_password">
<input name="password" type="text" placeholder="password">

<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>


<h3>20. Update Device id  </h3>

<br><br><br>
<form action = "<?php echo $url;?>update_deviceid" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="device_id" type="text" placeholder="device_id">
<input name="timezone" type="text" placeholder="timezone">
<input name="bluetooth_mac" type="text" placeholder="bluetooth_mac">
<input name="device_type" type="text" placeholder="device_type">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>

<h3>21. Edit Profile </h3>

<br><br><br>
<form action = "<?php echo $url;?>edit_profile" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="name" type="text" placeholder="name">
<input name="address" type="text" placeholder="address">
<input name="country_code" type="text" placeholder="country_code">
<input name="mobile" type="text" placeholder="mobile">
<input name="designation" type="text" placeholder="designation">
<input name="gender" type="text" placeholder="gender">
<input name="age" type="text" placeholder="age">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>


<h3>22. Feedback </h3>

<br><br><br>
<form action = "<?php echo $url;?>feedback" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="group_id" type="text" placeholder="group_id">
<input name="comment" type="text" placeholder="comment">
<input name="rating" type="text" placeholder="rating">
<input name="session_id" type="text" placeholder="session_id">
<input name="staff_id" type="text" placeholder="staff_id">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>23. Chat Feedback </h3>

<br><br><br>
<form action = "<?php echo $url;?>chat_feedback" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="group_id" type="text" placeholder="group_id">
<input name="comment" type="text" placeholder="comment">
<input name="rating" type="text" placeholder="rating">
<input name="session_id" type="text" placeholder="session_id">
<input name="staff_id" type="text" placeholder="staff_id">

<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>


<h3>24. Search Group Users </h3>

<br><br><br>
<form action = "<?php echo $url;?>search_group_users" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="name" type="text" placeholder="name">
<input name="group_id" type="text" placeholder="group_id">
<input name="page_no" type="text" placeholder="page_no">

<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>25. QR code Scan  </h3>
<br><br><br>
<form action = "<?php echo $url;?>qrcode_scan" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="qr_code" type="text" placeholder="qr_code">
<input name="country_code" type="text" placeholder="country_code">
<input name="mobile" type="text" placeholder="mobile">
<input name="seat_no" type="text" placeholder="seat_no">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>25.Facebook_Login</h3>
<br><br><br>
<form action = "<?php echo $url;?>Facebook_Login" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="unique_id" type="text" placeholder="unique_id">
<input name="name" type="text" placeholder="name">
<input name="image" type="text" placeholder="image">
<input name="email" type="text" placeholder="email">
<input name="gender" type="text" placeholder="gender">
<input name="age" type="text" placeholder="age">
<input name="mobile_no" type="text" placeholder="mobile_no">
<input name="timezone" type="text" placeholder="timezone">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>25.Get Chat (this is in chat controller get_chat)</h3>
<br><br><br>
<form action = "<?php echo $url;?>helpdeskChat" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="sender_id" type="text" placeholder="sender_id">
<input name="receiver_id" type="text" placeholder="receiver_id">
<input name="group_id" type="text" placeholder="group_id">
<input name="page_no" type="text" placeholder="page_no">
<input name="timezone" type="text" placeholder="timezone">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>25. closechat (this is in chat controller get_chat)</h3>
<br><br><br>
<form action = "<?php echo $url;?>closechat" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="group_id" type="text" placeholder="group_id">
<input name="timezone" type="text" placeholder="timezone">
<input name="close_time" type="text" placeholder="close_time">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>





<h3>25. getreceiver (this is in chat controller get_chat)</h3>
<br><br><br>
<form action = "<?php echo $url;?>getreceiver" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="user_id" type="text" placeholder="user_id">
<input name="group_id" type="text" placeholder="group_id">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>



<h3>26. help_api (this is in chat controller get_chat)</h3>
<br><br><br>
<form action = "<?php echo $url;?>help_api" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
<input name="receiver_id" type="text" placeholder="receiver_id">
<input name="group_id" type="text" placeholder="group_id">
<input name="sender_id" type="text" placeholder="sender_id">
<input name="lat" type="text" placeholder="lat">
<input name="lng" type="text" placeholder="lng">
<input name="timezone" type="text" placeholder="timezone">
<input name="auth_key" type="hidden" value="<?php echo $auth_key;?>" />
<input type="submit" value="Signup">
</form>

