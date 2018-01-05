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

class Register extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

 
    /**
     * The database table used by the model.
     *
     * @var string
     */
        protected $connection = 'mysql';
         protected $table = 'users';

            public $timestamps = true; //use for update the column created or updated 

  


 public function GetGroupUSer() {
        
    return $this->belongsTo('App\GroupMember','id','user_id');

  }



} // main function ends 
