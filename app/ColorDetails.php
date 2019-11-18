<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColorDetails extends Model
{
    //
    protected $fillable = ['value_id',
        'negation',
        'pre_constraint',
        'certainty_constraint',
        'degree_constraint',
        'brightness',
        'reflectance',
        'saturation',
        'colored',
        'multi_colored',
        'post_constraint'];

}
