<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermitNote extends Model
{
    protected $fillable = [
         'lease_name', 'notes'
    ];

    protected $table = 'permit_notes';
}
