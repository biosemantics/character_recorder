<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class character extends Model
{
    protected $fillable = [
        'name',
        'IRI',
        'method_from',
        'method_to',
        'method_include',
        'method_exclude',
        'method_where',
        'creator',
        'unit',
        'standard',
        'username',
        'owner_name',
        'standard_tag',
        'usage_count',
        'show_flag',
        'standard_tag',
        'summary',
    ];
    //
}
