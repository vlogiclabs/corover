<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Moloquent;

class UsersModel extends Moloquent
{
	//protected $connection = 'mysql';
    protected $connection = 'mongodb';
    protected $collection = 'users';

    public function rules(){

    }
    
        // The blog post is valid, store in database...
}