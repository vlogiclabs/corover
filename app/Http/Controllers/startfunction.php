
public function verification(Request $request){
        $saveArray =  $request->all();
         $helper = new Helpers();
         $status= $helper->CheckAuthKey($saveArray);
         if($status=="true"){ 
               $validator = Validator::make($request->all(), [
                    'country_code' => 'required',
                    'mobile' => 'required',
                    'password' => 'required',
                ]);
               if ($validator->fails()) {
                  $result = array('status'=>0,'message' =>"Please fill all fields");
                }else{

                }
         }else{
             $result = array('status'=>0,'message' => "Auth Key not matched");
         }
     echo json_encode($result);
    die;
}