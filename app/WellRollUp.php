<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WellRollUp extends Model
{
    protected $fillable = [
        'API14',
        'CountyParish',
        'OperatorCompanyName',
        'ReportedOperator',
        'WellName',
        'WellNumber',
        'WellStatus',
        'CompletionDate',
        'CreatedDate',
        'CumGas',
        'CumOil',
        'DrillType',
        'LeaseName',
        'MeasuredDepth',
        'Abstract',
        'Range',
        'District',
        'Section',
        'Township',
        'SurfaceHoleLatitudeWGS84',
        'SurfaceHoleLongitudeWGS84',
        'BottomHoleLatitudeWGS84',
        'BottomHoleLongitudeWGS84',
        'FirstProdDate',
        'LastProdDate'
    ];

    protected $table = 'well_rollups';
}
