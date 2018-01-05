<?php

namespace App;
use HybridRelations;
use Illuminate\Database\Eloquent\Model;
use Moloquent;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class GroupMember extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

 
    /**
     * The database table used by the model.
     *
     * @var string
     */
        protected $connection = 'mysql';
         protected $table = 'group_members';

            public $timestamps = true; //use for update the column created or updated 

  


 public function GetUSer() {
        
    return $this->belongsTo('App\Register','user_id','id');

  }


 public function GetGroupData() {
        
    return $this->belongsTo('App\Group','group_id','id')->where(['status'=>'1','deleted'=>'0']);

  }



} // main function ends 
