<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OwnerNote extends Model
{
    protected $fillable = [
        'operator_name', 'lease_name', 'notes'
    ];

    protected $table = 'owner_notes';
}
