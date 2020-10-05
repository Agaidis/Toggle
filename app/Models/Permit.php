<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    protected $fillable = [
        'permit_id', 'notes', 'assignee'
    ];

    protected $table = 'permits';
}
