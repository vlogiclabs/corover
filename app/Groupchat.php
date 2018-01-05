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

class Groupchat extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

 
    /**
     * The database table used by the model.
     *
     * @var string
     */
        protected $connection = 'mysql';
         protected $table = 'groupchats';

            public $timestamps = true; //use for update the column created or updated 

 

 public function GroupChatusers() {
        
    return $this->belongsTo('App\User','user_id','id');

  }


} // main function ends 
