<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Matrix extends Model
{
    //
    protected $fillable = ['user_id', 'matrix_name', 'named_user_id'];
}
