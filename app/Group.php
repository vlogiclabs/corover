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
use Auth;

class Group extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

 
    /**
     * The database table used by the model.
     *
     * @var string
     */
        protected $connection = 'mysql';
         protected $table = 'groups';

            public $timestamps = true; //use for update the column created or updated 

  

 

  public function BroadCast() {
        
     
   return $this->belongsTo('App\Newbroadcast','id','group_id')->orderby('id');

   }


 public function GroupType() {
       return $this->belongsTo('App\Grouptype','type','type_name')->where(['deleted'=>'0']);
   }

 

 public function MemberJoin() {
        
    return $this->belongsTo('App\GroupMember','id','group_id');

  }






} // main function ends 
