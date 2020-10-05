<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LandtracLease extends Model
{
    protected $fillable = [
        'LeaseId',
        'AreaAcres',
        'State',
        'CountyParish',
        'CreatedDate',
        'Geometry',
        'Grantee',
        'GranteeAddress',
        'Grantor',
        'GrantorAddress',
        'CentroidLatitude',
        'CentroidLongitude',
        'MinDepth',
        'MaxDepth
        '];

    protected $table = 'landtrac_leases';
}
