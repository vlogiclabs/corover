<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class MyModel extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'users';
}
