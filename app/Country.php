<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Country extends Moloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'countries';

    public function rules(){

    }
   
   	public function ownerdata()
		{
		    return $this->belongsTo('App\UsersModel', 'owner_id', '_id');
		} 	


        // The blog post is valid, store in database...
}