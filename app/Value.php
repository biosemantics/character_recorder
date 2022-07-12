<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    //
    protected $fillable = ['character_id', 'header_id', 'value','not_remove'];
}
