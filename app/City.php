<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Moloquent;

class City extends Moloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'cities';

    public function rules(){

    }
   
   	public function ownerdata()
		{
		    return $this->belongsTo('App\UsersModel', 'owner_id', '_id');
		} 

	public function countrydata()
		{
		    return $this->belongsTo('App\Country', 'country_id', '_id');
		} 	


        // The blog post is valid, store in database...
}