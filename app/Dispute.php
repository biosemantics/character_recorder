<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'label',
        'definition',
        'IRI',
        'deprecated_reason',
        'disputed_by',
        'dispute_date',
        'disputed_reason',
        'new_definition',
        'example_sentence',
    ];
    //
}
