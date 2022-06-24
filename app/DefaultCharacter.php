<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultCharacter extends Model
{
    protected $fillable = [
        'name',
        'IRI',
        'parent_term',
        'method_from',
        'method_to',
        'method_include',
        'method_exclude',
        'method_where',
        'method_as',
        'elucidation',
        'creator',
        'unit',
        'standard',
        'username',
        'owner_name',
        'standard_tag',
        'usage_count',
        'show_flag',
        'summary',
        'images',
        'numeric_flag',
    ];
    //
}
