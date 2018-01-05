<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Moloquent;
class ApiModel extends Moloquent
{
    //protected $connection = 'mongodb';
        protected $connection = 'mysql';
    protected $collection = 'users';
}
