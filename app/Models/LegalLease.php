<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LegalLease extends Model
{
    protected $fillable = [
        'LeaseId',
        'MappingID',
        'AreaAcres',
        'Abstract',
        'AbstractNo',
        'Block',
        'CountyParish',
        'Created',
        'Geometry',
        'LatitudeWGS84',
        'LongitudeWGS84',
        'Grantee',
        'GranteeAddress',
        'GranteeAlias',
        'Grantor',
        'GrantorAddress',
        'MaxDepth',
        'MinDepth',
        'Range',
        'Section',
        'Township',
        'RecordDate'
    ];

    protected $table = 'legal_leases';
}
