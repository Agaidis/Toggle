<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OwnerPhoneNumber extends Model
{
    protected $fillable = [
        'phone_desc', 'phone_number', 'owner_name'
    ];

    protected $table = 'owner_phone_numbers';
}
