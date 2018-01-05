<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Moloquent;

class GroupBadgeModel extends Moloquent
{
	//protected $connection = 'mysql';
    protected $connection = 'mongodb';
    protected $collection = 'groupbadges';

    public function rules(){

    }
    

/*    public function Chatusers() {
       // protected $connection = 'mysql';
       return $this->belongsTo('App\Register','sender_id','id');

  }*/


        // The blog post is valid, store in database...
}