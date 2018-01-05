<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Moloquent;

class GroupModel extends Moloquent
{
	
    protected $connection = 'mongodb';
    protected $collection = 'groups';

    public function rules(){

    }
    
        // The blog post is valid, store in database...
}