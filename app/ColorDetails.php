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
        'brightness_IRI',
        'reflectance',
        'reflectance_IRI',
        'saturation',
        'saturation_IRI',
        'colored',
        'colored_IRI',
        'multi_colored',
        'multi_colored_IRI',
        'post_constraint'];

}
