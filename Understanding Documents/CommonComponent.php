<?php


/*
  This Component is used for creating common function which could be reuse by other controllers
 */

App::uses('Component', 'Controller');

class CommonComponent extends Component { //cake extends Object for creating component
    /**  @Date: 21-Aug-2010
     * @Method : licenceCode (This 
     * @Purpose: This function is used to generate licence code.
     * */

    function randomCode($plength = '8') {

        $code = "";
        $chars = 'ABCDEFGHJKLMNPQRTUVWXY346789ABCDEFGHJKLMNPQRTUVWXY346789'; //string by which new code will be generated
        mt_srand(microtime() * 1000000);
        for ($i = 0; $i < $plength; $i++) {
            $key = mt_rand(0, strlen($chars) - 1);
            $code = $code . $chars{$key};
        }
        $code = trim($code);
        return $code;
    }

    /**

     * @Date: 11-Nov-2009
     * @Method : changeDateFormat
     * @Purpose:Gets Details of a Date Span . Called Via AJAX.
     * */
    function changeDateFormat($date = "", $format_in = "", $format_to = "") {

        $tmp_date = explode("-", $date);
        switch ($format_in) {
            case "Y-m-d":
                $t_stmp = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
            case "m-d-Y":
                $t_stmp = mktime(0, 0, 0, $tmp_date[0], $tmp_date[1], $tmp_date[2]);
        }
        return date($format_to, $t_stmp);
    }

    /**
     * @Date: 1-Dec-2009
     * @Method : getRandomNumber
     * @Purpose: Generates a random number
     * */
    function getRandomNumber() {

        srand((double) microtime() * 1000000);
        $random_number = rand();
        return $random_number;
    }

    /**
     * @Date: 16-Dec-2009
     * @Method : getMonthsArray
     * @Purpose: Get an array of Months
     * */
    function getMonthsArray() {

        return array(
            "" => "-Select-",
            "01" => "January",
            "02" => "February",
            "03" => "March",
            "04" => "April",
            "05" => "May",
            "06" => "June",
            "07" => "July",
            "08" => "August",
            "09" => "September",
            "10" => "October",
            "11" => "November",
            "12" => "December"
        );
    }

    /**
     * @Date: 16-Dec-2009
     * @Method : getDaysArray
     * @Purpose: Get an array of days
     * */
    function getDaysArray() {

        $i = 1;
        $array = array();
        while ($i <= 31) {
            $array[$i] = $i;
            $i++;
        }
        return $array;
    }

    /**
     * @Date: 16-Dec-2009
     * @Method : getDaysArray
     * @Purpose: Get an array of years
     * */
    function getYearsArray() {

        $i = date("Y") - 100;
        $array = array();
        while ($i <= date("Y")) {
            $array[$i] = $i;
            $i++;
        }
        return $array;
    }

    /**
     * @Date: 29-April-2012
     * @Method : getDaysArray
     * @Purpose: Get an array of years
     * */
    function getCustomYearsArray() {

        $i = 2011;
        $array = array();
        while ($i <= date("Y")) {
            $array[$i] = $i;
            $i++;
        }
        return $array;
    }

    /** @Date: 11-Jan-2009
     * @Method : getRandomString
     * @Purpose: generates random number.
     * */
    function getRandomString($length) {
        if ($length > 0) {
            $rand_id = "";
            for ($i = 1; $i <= $length; $i++) {
                mt_srand((double) microtime() * 1000000);
                $num = mt_rand(1, 36);
                $rand_id .= $this->assign_rand_value($num);
            }
        }
        return $rand_id;
    }

    /** @Date: 11-Jan-2009
     * @Method : assign_rand_value
     * @Purpose: generates random number. This function is used by getRandomString function.
     * */
    function assign_rand_value($num) {
        // accepts 1 - 36
        switch ($num) {
            case "1":
                $rand_value = "a";
                break;
            case "2":
                $rand_value = "b";
                break;
            case "3":
                $rand_value = "c";
                break;
            case "4":
                $rand_value = "d";
                break;
            case "5":
                $rand_value = "e";
                break;
            case "6":
                $rand_value = "f";
                break;
            case "7":
                $rand_value = "g";
                break;
            case "8":
                $rand_value = "h";
                break;
            case "9":
                $rand_value = "i";
                break;
            case "10":
                $rand_value = "j";
                break;
            case "11":
                $rand_value = "k";
                break;
            case "12":
                $rand_value = "l";
                break;
            case "13":
                $rand_value = "m";
                break;
            case "14":
                $rand_value = "n";
                break;
            case "15":
                $rand_value = "o";
                break;
            case "16":
                $rand_value = "p";
                break;
            case "17":
                $rand_value = "q";
                break;
            case "18":
                $rand_value = "r";
                break;
            case "19":
                $rand_value = "s";
                break;
            case "20":
                $rand_value = "t";
                break;
            case "21":
                $rand_value = "u";
                break;
            case "22":
                $rand_value = "v";
                break;
            case "23":
                $rand_value = "w";
                break;
            case "24":
                $rand_value = "x";
                break;
            case "25":
                $rand_value = "y";
                break;
            case "26":
                $rand_value = "z";
                break;
            case "27":
                $rand_value = "0";
                break;
            case "28":
                $rand_value = "1";
                break;
            case "29":
                $rand_value = "2";
                break;
            case "30":
                $rand_value = "3";
                break;
            case "31":
                $rand_value = "4";
                break;
            case "32":
                $rand_value = "5";
                break;
            case "33":
                $rand_value = "6";
                break;
            case "34":
                $rand_value = "7";
                break;
            case "35":
                $rand_value = "8";
                break;
            case "36":
                $rand_value = "9";
                break;
        }
        return $rand_value;
    }

    /**
     * @Date: 15-Feb-2010
     * @Method : validEmailId
     * @Purpose: Validate email Id if filled
     * @Param:  $value
     * @Return: boolean
     * */
    function validEmailId($value = null) {

        $v1 = trim($value);
        if ($v1 != "" && !eregi("^[\'+\\./0-9A-Z^_\`a-z{|}~\-]+@[a-zA-Z0-9_\-]+(\.[a-zA-Z0-9_\-]+){1,3}$", $v1)) {
            return false;
        }
        return true;
    }

    function file_exists_in_directory($directory, $pattern = false, $filename = false) {
//echo $pattern."+++++".$directory;

        if (!isset($directory) OR ! isset($filename) OR is_dir($directory) == false OR strlen($filename) < 0)
            return false;

        $returnval = false;
        if (false != ($handle = opendir($directory))) {

            while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != "..") {

                    if ($pattern != false) {

                        if (preg_match("$pattern", $file) > 0) {
                            $returnval = $file;
                            break;
                        }
                    } else {
                        if ($file == $filename) {
                            $returnval = $file;
                            break;
                        }
                    }
                }
            }
        }
        closedir($handle);
        return $returnval;
    }

    // return an array of files in directory else false if none found

    function get_files($directory, $pattern = false) {

        if (!isset($directory) OR is_dir($directory) == false)
            return false;
        $returnval = array();

        if (false != ($handle = opendir($directory))) {
            while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != "..") {

                    if ($pattern != false) {

                        if (preg_match("$pattern", $file) > 0) {
                            $returnval[] = $file;
                        }
                    } else {
                        $returnval[] = $file;
                    }
                }
            }
        }
        closedir($handle);
        return $returnval;
    }

    /**
     * Makes directory, returns TRUE if exists or made
     * @param string $pathname The directory path.
     * @return boolean returns TRUE if exists or made or FALSE on failure.
     */
    function mkdir_recursive($path, $mode = 0777) {
        $basicPath = ROOT . DS . "app" . DS . "webroot" . DS . "contents" . DS;
        $dirs = explode(DS, $path);
        $count = count($dirs);
        $path = '';
        for ($i = 0; $i < $count; ++$i) {
            $path .= $dirs[$i] . DS;
            if (!is_dir($basicPath . rtrim($path, "/"))) {
                mkdir($basicPath . $path, $mode);
            }
        }
        return true;
    }

    /**
     * Remove directory, returns TRUE if exists or made
     * @param string $pathname The directory path.
     */
    function rmdir_recursive($dir) {

        $basicPath = ROOT . DS . "app" . DS . "webroot" . DS . "contents" . DS;
        if (is_dir($basicPath . $dir)) {
            $files = scandir($basicPath . $dir);
            array_shift($files);    // remove '.' from array
            array_shift($files);    // remove '..' from array

            foreach ($files as $file) {
                $file = $basicPath . $dir . DS . $file;
                if (is_dir($file)) {
                    rmdir_recursive($file);
                    rmdir($file);
                } else {

                    unlink($file);
                }
            }
            rmdir($basicPath . $dir);
        }
    }

    function file_extension($filename) {
        $path_info = pathinfo($filename);
        return $path_info['extension'];
    }

    //changes sql timestamp format to compare
    function expiredTime($modified_date) {
        $diff = abs(time() - strtotime($modified_date));
        if ($diff < 0)
            $diff = 0;
        $dl = floor($diff / 60 / 60 / 24);
        $hl = floor(($diff - $dl * 60 * 60 * 24) / 60 / 60);
        $ml = floor(($diff - $dl * 60 * 60 * 24 - $hl * 60 * 60) / 60);
        $sl = floor(($diff - $dl * 60 * 60 * 24 - $hl * 60 * 60 - $ml * 60));
        // OUTPUT
        $hl = ($dl * 24) + $hl;
        $return = array('hours' => $hl, 'minutes' => $ml, 'seconds' => $sl);
        return $return;
    }

    function dateDiff($time1, $time2, $precision = 6) {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
        $diffs = array();

        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Set default diff to 0
            $diffs[$interval] = 0;
            // Create temp time from time1 and interval
            $ttime = strtotime("+1 " . $interval, $time1);
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                $time1 = $ttime;
                $diffs[$interval] ++;
                // Create new temp time from time1 and interval
                $ttime = strtotime("+1 " . $interval, $time1);
            }
        }

        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval 
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

        // Return string with times
        return implode(", ", $times);
    }

    /** 	 @Date: 04-April-2013
     * @Method : getuser (This 
     * @Purpose: This function is used to active user list.
     * */
    function getuser() {
        App::import("Model", "User");
        $this->User = new User;
        $result = $this->User->find('list', array(
            'conditions' => array('status' => '1'),
            'fields' => array('id', 'user_name'),
            'order' => 'user_name  ASC'
        ));
        $result = array('' => '--Select--') + $result;
        // pr($result);
        return $result;
    }

    // get permissions for menus 
    function menu_permissions($controller, $action, $userID) {
        App::import('Model', 'Permission');
        $this->Permission = new Permission;
        $cond = "WHERE controller='$controller' AND action='$action'";
        $permissionsResult = $this->Permission->find('first', array(
            'fields' => array('user_id'),
            'conditions' => $cond,
        ));
        if (!empty($permissionsResult)) {
            $users = explode(",", $permissionsResult['Permission']['user_id']);
            if (in_array($userID, $users)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    function getSecurityQuestions($id = null) {
        $result = array(
            '1' => 'Model of first car',
            '2' => "Mothers maiden name",
            '3' => 'Name of best friend in high school',
            '4' => 'Name of first pet',
            '5' => 'Fathers middle name',
        );
        if (!empty($id)) {
            return $result[$id];
        } else {
            return $result;
        }
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

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

    /*  public function iphone_send_notification($deviceIds, $msg, $badge_count) {

      $result = '';
      $message = $this->tr_to_utf($msg['message']);
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
      $ctx = stream_context_create();
      $passphrase = '';

      stream_context_set_option($ctx, 'ssl', 'local_cert', 'certs/CoroverDevPush.pem');
      stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

      $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 4, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

      if ($fp) {
      $msg = chr(0) . pack('n', 32) . pack('H*', $deviceIds) . pack('n', strlen($payload)) . $payload;
      $result = fwrite($fp, $msg, strlen($msg));
      fclose($fp);
      }
      //pr($message);
      //pr($result);
      // die;
      return $result;
      } */

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

    public function getLatLong($address) {
        if (!empty($address)) {
            //Formatted address
            $formattedAddr = str_replace(' ', '+', $address);
            //Send request and receive json data by address
            $geocodeFromAddr = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddr . '&sensor=false');
            $output = json_decode($geocodeFromAddr);
            //Get latitude and longitute from json data
            $data['latitude'] = (float) $output->results[0]->geometry->location->lat;
            $data['longitude'] = (float) $output->results[0]->geometry->location->lng;
            //Return latitude and longitude of the given address
            if (!empty($data)) {
                //var_dump($data);
                //die;
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function tr_to_utf($text) {
        $text = trim($text);
        $search = array('Ü', 'Þ', '�?', 'Ç', '�?', 'Ö', 'ü', 'þ', 'ð', 'ç', 'ý', 'ö');
        $replace = array('Ãœ', 'Åž', '&#286;ž', 'Ã‡', 'Ä°', 'Ã–', 'Ã¼', 'ÅŸ', 'ÄŸ', 'Ã§', 'Ä±', 'Ã¶');
        $new_text = str_replace($search, $replace, $text);
        return $new_text;
    }

       function post_to_url($url, $post) {
              /*  $fields = '';
                foreach($data as $key => $value) {
                    $fields .= $key . '=' . $value . '&';
                }
                //pr($fields); die;
                rtrim($fields, '&');
                $post = curl_init();
                curl_setopt($post, CURLOPT_URL, $url);
                curl_setopt($post, CURLOPT_POST, count($data));
                curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($post);
                curl_close($post);*/
                // set post fields
               /* $post = [
                    'username' => 'user1',
                    'password' => 'passuser1',
                    'gender'   => 1,
                ];*/

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

                // execute!
                $response = curl_exec($ch);

                // close the connection, release resources used
                curl_close($ch);

                // do anything you want with your response
                //pr($response); die;
                //var_dump($response);

            }

    function triggerchat($formdata) {
        //Configure::write('debug', 2);
        App::import("Model", "Group");
        $this->Group = new Group;

        App::import("Model", "User");
        $this->User = new User;

        App::import("Model", "Chat");
        $this->Chat = new Chat;

        App::import("Model", "Block");
        $this->Block = new Block;

         App::import("Model", "CheckSetting");
        $this->CheckSetting = new CheckSetting;

         App::import("Model", "PushNotification");
        $this->PushNotification = new PushNotification;
        
        $saveArray = $formdata;
        if (!empty($saveArray['sender_id']) AND !empty($saveArray['receiver_id']) AND !empty($saveArray['group_id'])AND ! empty($saveArray['type'])
                AND ( ($saveArray['type'] == "T" AND ! empty($saveArray['message'])) OR ( $saveArray['type'] == "I" AND !empty($_FILES['image']['name']))  OR ( $saveArray['type'] == "L" AND !empty($saveArray['lat']) AND !empty($saveArray['lng'])))) {

            $id = $saveArray['sender_id'];
        //pr($id); die;
          $group_nm = $this->Group->find('first', array("conditions" => array('Group.id' => $saveArray['group_id'])));
            $group_name = $group_nm['Group']['name'];

        // $user_statuscheck = $this->CheckSetting->find('first', array('conditions' => array('User.id' => $saveArray['sender_id'])));  
                
           $sender = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['sender_id'])));

            $sender_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];
            
                
           $friend = $this->User->find('first', array("conditions" => array('User.id' => $saveArray['receiver_id'])));
           //pr($friend['User']['id']);
            $friend_image = BASE_URL . "img/profile_images/" . $sender['User']['image'];

            if (!empty($sender) && count($friend) > 0) {

                date_default_timezone_set('Asia/Kolkata');
                $date = date('Y-m-d H:i:s');
               // $date = date('Y-m-d H:i:s');

              

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
                            $saveArray['image'] = $this->uploadPic($r, $destination,$_FILES['image']);
                        }
                    }
                    $groupchat_id = $this->Chat->save($saveArray,array('validate'=>false));


                    

                    if ($saveArray['type'] == "T" && !empty($saveArray['message'])) {
                        $groupmsg_img = '';
                    } else if ($saveArray['type'] == "I" AND ! empty($_FILES['image']['name'])) {

                        $getGroupChatImage = $this->Chat->find('first', array('conditions' => array('id' => $groupchat_id['Chat']['id'])));
                       // pr(   $getGroupChatImage); die;
                        if (!empty($getGroupChatImage)) {
                            $groupmsg_img = BASE_URL . "img/groupchatimg/" . $getGroupChatImage['Chat']['image'];
                        } else {
                            $groupmsg_img = '';
                        }
                    }

                    // End code by Alka

                     
                  
                       
                 // $u=$group[0]['groups']['user_id'];
                //$userid = explode(',',$for_noti);
                
                
                if($friend['User']['id']){
                 $condition3 = "Block.friend_id='" . $saveArray['sender_id'] . "' And Block.user_id='" . $saveArray['receiver_id'] . "' And Block.group_id='" . $saveArray['group_id'] . "' ";
                    $data_exist45 = $this->Block->find('first', array('conditions' => $condition3));
                    //pr($data_exist45); die;
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
                   // pr($data_exist4); die;
                    if(!empty($data_exist4)){
                    if($data_exist4['CheckSetting']['private_chat']=='0'){
                        
                if($udid != $did){
                 // $message = array('message' => "You have new message from " . utf8_encode($sender['User']['name']) . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
                  // $message = array('message' => "You have new message from " . $sender['User']['name'] . "", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
                 
                                         // 'date' => $date, 'sender_name' => $sender['User']['name'],
                                        // 'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
                                        // 'friend_id' => $friend['User']['id'],'friend_type' => $friend['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        // 'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
                                        if($sender['User']['login_type']=='G'){
                  $message = array('message' => "You have a new Private Message from Guest User (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
                 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
                                        'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
                   }else{
                        $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
                 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
                                        'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
                   }        
                  if ($friend['User']['device_type'] == 'A') {
                                    if ($udid) {

                                        $type = "single";
                                        $android_ids = $udid;                                       
                                        //$this->Common->android_send_notification(array($udid),$message,$type);
                                        $this->android_send_notification(array($android_ids), $message, 'single');
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
                                        $this->iphone_send_notification($ios_ids, $message,1);
                                    }
                                }
                            }
                            if(empty($push_noti)){
                                //if($push_noti['PushNotification']['notification_status']== '0'){
                                    if( $c == $main && $ios_ids != ""){
                                        $this->iphone_send_notification($ios_ids, $message,1);
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
                 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
                                        'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
                   }else{
                        $message = array('message' => "You have a new Private Message from " . $sender['User']['name'] . " (".$group_name.")", 'sender_id' => $sender['User']['id'], 'noti_for' => 'private',
                 
                                         'date' => $date, 'sender_name' => $sender['User']['name'],
                                        'sender_image' => $sender_image,'friend_name' => $friend['User']['name'],
                                        'friend_id' => $friend['User']['id'],'friend_type' => $sender['User']['user_type'],'group_id' => $saveArray['group_id'],
                                        'friend_image' => $friend_image, 'message_img' => $groupmsg_img, 'message_msg' =>$saveArray['message']);
                   }        
                  if ($friend['User']['device_type'] == 'A') {
                                    if ($udid) {

                                        $type = "single";
                                        $android_ids = $udid;                                       
                                        //$this->Common->android_send_notification(array($udid),$message,$type);
                                        $this->android_send_notification(array($android_ids), $message, 'single');
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
                                        $this->iphone_send_notification($ios_ids, $message,1);
                                    }
                                }
                            }
                            if(empty($push_noti)){
                                //if($push_noti['PushNotification']['notification_status']== '0'){
                                    if( $c == $main && $ios_ids != ""){
                                        $this->iphone_send_notification($ios_ids, $message,1);
                                    }
                                //}
                            }       
                                    
                                    
                                    
                                    
                                    
                                    
                                    }
                                }        
                
                    
                   
                }
                    
                            }
                        }
                    }
            } else {
            }
        } else {
        }
    }

    function send_otp($data,$type){
        Configure::write('debug', 2);
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
           
         //   $d = http_build_query($data);
         
            //OTP Code Ends Here
            }
    }

 function verify_otp($data){
    $otp = $post['otp'];  // access all user input  otp
    $session_id =  Session::get('otp');
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "http://2factor.in/API/V1/dab9406c-ffef-11e5-9a14-00163ef91450/SMS/VERIFY/".$session_id."/".$otp."",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => "",
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
        }else {
        $user = User::where('phone_no',$phone)->first(); // get current  user phone number  from user table
        $x = json_decode($response);
        if($x->Details =='OTP Matched'){
            Session::forget('otp');
            if(isset($post['location'])){
                if($post['location']=='account'){
                    return \Response::json("phone_no_verified");// return response json to ajax function
                }
            }
            return \Response::json(url('/').'/reset/'.$user->login_key, 200);// return response json to ajax function
            } else {
            return \Response::json('not_matched');// return response json to ajax function
        }
        // "Details":"OTP Matched"
    }
}

function unlinkfile($file,$folder){
    $url =  getcwd()."/".$folder.'/'.$file;
    $urls = str_replace("\\","/",$url);
     if (is_file($urls)){
         unlink($urls);
    }
    return "success";
}

function getuserchat($saveArray ,$userSession , $type ){
      App::import("Model", "SupportChat");
      $this->SupportChat = new SupportChat;
      App::import("Model", "SupportFeedback");
      $this->SupportFeedback = new SupportFeedback;
      App::import("Model", "UnreadNotifications");
      $this->UnreadNotifications = new UnreadNotifications;
    $start_limit = ($saveArray['page_number'] ==1 ) ? 0 :  ($saveArray['page_number']*20-20);
                $end_limit = 20;
                if($type == 'paginate_chat'){
                $cond = array('SupportChat.group_id'=>$saveArray['group_id'], /*'SupportChat.is_closed '=>'No',*/
                                    'OR' => array(
                                                array('SupportChat.sender_id' => $userSession['id'] , 'SupportChat.receiver_id' => $saveArray['user_id']),
                                                array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['user_id']),
                                                array('SupportChat.sender_id' => $saveArray['user_id'],'SupportChat.receiver_id' => $userSession['id']),
                                                array('SupportChat.sender_id' => $saveArray['user_id']),
                                                array('SupportChat.receiver_id' => $saveArray['user_id']),
                                            )
                                 );
                } else {
                $cond = array('SupportChat.group_id'=>$saveArray['group_id'], 'SupportChat.id >'=>$saveArray['last_id'] ,
                                    'OR' => array(
                                                array('SupportChat.sender_id' => $userSession['id'] , 'SupportChat.receiver_id' => $saveArray['user_id'] ),
                                                array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['user_id'] ),
                                                array('SupportChat.sender_id' => $saveArray['user_id'] , 'SupportChat.receiver_id' => $userSession['id'] ),
                                                array('SupportChat.sender_id' => $saveArray['user_id']),
                                                array('SupportChat.receiver_id' => $saveArray['user_id']),
                                            )
                                 );
                }
                $data = $this->SupportChat->find('all',array(
                                'conditions'=> $cond,
                                'fields' => ['SupportChat.receiver_id', 'SupportChat.sender_id' ,'SupportChat.id','SupportChat.message','SupportChat.type','SupportChat.group_id', 'Sender.id','Sender.name','Sender.image', 'Receiver.id','Receiver.name','Receiver.image','Group.id','Group.name' , 'SupportChat.taken_by', 'SupportChat.is_taken' , 'SupportChat.submit_time', 'SupportChat.is_closed','SupportChat.is_read','SupportChat.taken_by','SupportChat.close_time','SupportChat.is_last_message','SupportChat.session_id','ChatTaker.id','ChatTaker.name','ChatTaker.image'],
                                'order'=>array('SupportChat.created DESC'),
                                'limit' => $end_limit,
                                'offset' => $start_limit
                            ));
                $total = $this->SupportChat->find('count',array(
                                'conditions'=>$cond,
                                'fields' => ['SupportChat.id'],
                                'order'=>array('SupportChat.created DESC')
                            ));
                $finalarr =[];
                //pr($data); die;
                $feedbacksarr = [];
                $last_session_id = 0;
                foreach($data as $chat){
                    //pr($chat); die;
                    $date = new DateTime($chat['SupportChat']['submit_time'], new DateTimeZone('Asia/Kolkata'));
                    $date->modify('-5 minutes');
                    $currentDateTime = $date->format('Y-m-d H:i:s');
                    $usertimezone = 'Asia/Kolkata';
                    date_default_timezone_set('Asia/Kolkata');
                    $chat['SupportChat']['date'] = $date->format('M j, Y, g:i a');
                    $chat['Sender']['name'] = (empty($chat['Sender']['name'])) ? 'Guest' : $chat['Sender']['name']; 
                    $chat['Sender']['image'] = (empty($chat['Sender']['image']) || (!is_file(getcwd()."/img/profile_images/".$chat['Sender']['image']))) ? 'no-image.jpg' : $chat['Sender']['image']; 
                    $finalarr[] = $chat;
                    $savedata =[];
                    //pr($chat['SupportChat']['session_id']); die;
                    $feedback = $this->SupportFeedback->find('first',[
                                'conditions' => ['session_id' => $chat['SupportChat']['session_id'] ],
                                'fields' => ['session_id','comment','rating','id','created'],
                            ]);
                    //pr($feedback); die;
                    if(count($feedback) > 0){
                                $feedbacksarr[$chat['SupportChat']['session_id']] = $feedback;
                    }
                    /*if($chat['SupportChat']['is_closed'] == 'Yes' && $chat['SupportChat']['session_id'] > 0  && $last_session_id !== $chat['SupportChat']['session_id'] && $chat['SupportChat']['sender_id'] !== $userSession['id'] ){
                           $last_session_id = $chat['SupportChat']['session_id'];
                           $feedback = $this->SupportFeedback->find('first',[
                                'conditions' => ['session_id' => $last_session_id ],
                                'fields' => ['session_id','comment','rating','id','created'],
                            ]);
                           if(count($feedback) > 0){
                                $feedbacksarr[$last_session_id] = $feedback;
                           }
                    }*/
                    // Marking Messages as Read for the records in which staff is not sender 
                    /*$notifications = $this->UnreadNotifications->find('all',[
                                'conditions' => [ 
                                                    'record_id' => $chat['SupportChat']['id'],
                                                    'group_id' => $chat['SupportChat']['group_id'],
                                                    'OR' => array(
                                                                array('SupportChat.sender_id' => $userSession['id'] , 'SupportChat.receiver_id' => $saveArray['user_id'] ),
                                                                array('SupportChat.receiver_id' => 0 , 'SupportChat.sender_id' => $saveArray['user_id'] ),
                                                                array('SupportChat.sender_id' => $saveArray['user_id'] , 'SupportChat.receiver_id' => $userSession['id'] ),
                                                            )

                                                ]
                        ]);*/
                        $delcond =         [        
                                                    //'record_id' => $chat['SupportChat']['id'],
                                                    'group_id' => $chat['SupportChat']['group_id'],
                                                    'notification_to' => $userSession['id'],
                                                    'OR' => array(
                                                                array('UnreadNotifications.sender_id' => $userSession['id'] , 'UnreadNotifications.receiver_id' => $saveArray['user_id'] ),
                                                                array('UnreadNotifications.receiver_id' => 0 , 'UnreadNotifications.sender_id' => $saveArray['user_id'] ),
                                                                array('UnreadNotifications.sender_id' => $saveArray['user_id'] , 'UnreadNotifications.receiver_id' => $userSession['id'] ),
                                                                array('UnreadNotifications.receiver_id' => $saveArray['user_id'] ),
                                                                array('UnreadNotifications.receiver_id' => 0 , 'UnreadNotifications.sender_id' => $saveArray['user_id'] ),
                                                                array('UnreadNotifications.sender_id' => $saveArray['user_id']),
                                                            )
                                                ];
                    //pr($delcond); die;
                    $delete = $this->UnreadNotifications->deleteAll($delcond, false);
                    //$r = $this->UnreadNotifications->find('first',['conditions' => $delcond]);
                    //pr($r); die;
                    if($chat['SupportChat']['sender_id'] == $saveArray['user_id'] ){
                        $current = $this->SupportChat->read(null,$chat['SupportChat']['id']);
                        $this->SupportChat->set('is_read','Yes');
                        $this->SupportChat->save();
                    }
                     
                }
                    
                //pr($finalarr); die;
                $fdata =  array_reverse($finalarr);
              //  pr($fdata); die;
               // $fdata =  $finalarr;
                //pr($feedbacksarr); die;
                $page_count = $total / 20;
                $page_count = ceil($page_count);
                $next_page = ($page_count == $saveArray['page_number'] ) ? $saveArray['page_number'] : $saveArray['page_number'] + 1 ; 
                $result = ['data' => $fdata, 'nextPage' => $next_page, 'totalPages' => $page_count, 'currentPage' => $saveArray['page_number'] ,'feedbacks' => $feedbacksarr];
                return $result;


}




}

?>