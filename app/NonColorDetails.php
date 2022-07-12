<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NonColorDetails extends Model
{
    //
    protected $fillable = ['value_id',
        'negation',
        'pre_constraint',
        'certainty_constraint',
        'degree_constraint',
        'main_value',
        'main_value_IRI',
        'post_constraint',
        'not_remove',
    ];
}
