<?php

namespace App\Console\Commands;

use App\ErrorLog;
use App\Http\Controllers\APIManager;
use App\Permit;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetPermits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:getPermits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will loop through by date and grab all permits by all counties';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $eagleInterestCountiesArray = array('ATASCOSA','BEE','DEWITT','GONZALES','KARNES','LIVE%20OAK','LAVACA','WILSON');
            $wtxInterestCountiesArray = array('ANDREWS', 'DAWSON', 'GAINES', 'BORDEN', 'CRANE', 'ECTOR', 'STERLING', 'MITCHELL', 'JEFF%20DAVIS');
            $nmInterestCountiesArray = array('LEA', 'EDDY');
            $etxInterestCountiesArray = array('CASS', 'GREGG', 'HARRISON', 'MARION', 'MORRIS', 'NACOGDOCHES', 'PANOLA', 'SAN%20AUGUSTINE', 'RUSK', 'SHELBY', 'UPSHUR');
            $laInterestCountiesArray = array('BIENVILLE', 'BOSSIER', 'CADDO', 'DE%20SOTO', 'NATCHITOCHES', 'RED%20RIVER', 'SABINE', 'WEBSTER');


         //   $this->getCountyPermitData('2020-01-01', 'eagleford', $eagleInterestCountiesArray);

         //   $this->getCountyPermitData('2020-04-01', 'wtx', $wtxInterestCountiesArray);

        //    $this->getCountyPermitData('2020-04-01', 'nm', $nmInterestCountiesArray);

            $this->getCountyPermitData('2020-04-01', 'etx', $etxInterestCountiesArray);

            $this->getCountyPermitData('2020-04-01', 'la', $laInterestCountiesArray);


        return 'success';
        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
            return 'error';
        }
    }

    public function getCountyPermitData ($date, $interestArea, $counties) {

        try {
            $apiManager = new APIManager();

            $token = $apiManager->getToken();

            foreach ($counties as $county) {

                $permits = $apiManager->getPermits($county, $token->access_token, $date, $interestArea);

                if ($permits != null && $permits != '' && isset($permits)) {
                    $decodedPermits = json_decode($permits);

                    for ($i = 0; $i < count($decodedPermits); $i++) {

                        if ($decodedPermits[$i]->BottomHoleLongitudeWGS84 != '' && $decodedPermits[$i]->BottomHoleLongitudeWGS84 != null) {
                            $btmLatLng = '{"lng": ' . $decodedPermits[$i]->BottomHoleLongitudeWGS84 . ', "lat": ' . $decodedPermits[$i]->BottomHoleLatitudeWGS84 . "}";
                        } else {
                            $btmLatLng = '';
                        }

                        $doesPermitExist = Permit::where('permit_id', $decodedPermits[$i]->PermitID)->get();

                        if ($doesPermitExist->isEmpty()) {

                            $newPermit = new Permit();

                            $newPermit->permit_id = $decodedPermits[$i]->PermitID;
                            $newPermit->notes = '';
                            $newPermit->abstract = $decodedPermits[$i]->Abstract;
                            $newPermit->approved_date = $decodedPermits[$i]->ApprovedDate;
                            $newPermit->block = $decodedPermits[$i]->Block;
                            $newPermit->county_parish = $decodedPermits[$i]->CountyParish;
                            $newPermit->drill_type = $decodedPermits[$i]->DrillType;
                            $newPermit->lease_name = $decodedPermits[$i]->LeaseName;
                            $newPermit->operator_alias = $decodedPermits[$i]->OperatorAlias;
                            $newPermit->permit_type = $decodedPermits[$i]->PermitType;
                            $newPermit->range = $decodedPermits[$i]->Range;
                            $newPermit->section = $decodedPermits[$i]->Section;
                            $newPermit->state = $decodedPermits[$i]->StateProvince;
                            $newPermit->survey = $decodedPermits[$i]->Survey;
                            $newPermit->township = $decodedPermits[$i]->Township;
                            $newPermit->well_type = $decodedPermits[$i]->WellType;
                            $newPermit->btm_geometry = $btmLatLng;
                            $newPermit->reported_operator = $decodedPermits[$i]->ReportedOperator;
                            $newPermit->permit_number = $decodedPermits[$i]->PermitNumber;
                            $newPermit->permit_status = $decodedPermits[$i]->PermitStatus;
                            $newPermit->district = $decodedPermits[$i]->District;
                            $newPermit->created_date = $decodedPermits[$i]->CreatedDate;
                            $newPermit->submitted_date = $decodedPermits[$i]->SubmittedDate;
                            $newPermit->toggle_status = 'yellow';
                            $newPermit->interest_area = $interestArea;
                            $newPermit->SurfaceLatitudeWGS84 = $decodedPermits[$i]->SurfaceLatitudeWGS84;
                            $newPermit->SurfaceLongitudeWGS84 = $decodedPermits[$i]->SurfaceLongitudeWGS84;
                            $newPermit->is_producing = 1;
                            $newPermit->save();

                        } else {

                            Permit::where('permit_id', $decodedPermits[$i]->PermitID)->update([
                                'abstract' => $decodedPermits[$i]->Abstract,
                                'approved_date' => $decodedPermits[$i]->ApprovedDate,
                                'block' => $decodedPermits[$i]->Block,
                                'county_parish' => $decodedPermits[$i]->CountyParish,
                                'drill_type' => $decodedPermits[$i]->DrillType,
                                'lease_name' => $decodedPermits[$i]->LeaseName,
                                'operator_alias' => $decodedPermits[$i]->OperatorAlias,
                                'permit_type' => $decodedPermits[$i]->PermitType,
                                'range' => $decodedPermits[$i]->Range,
                                'section' => $decodedPermits[$i]->Section,
                                'state' => $decodedPermits[$i]->StateProvince,
                                'survey' => $decodedPermits[$i]->Survey,
                                'township' => $decodedPermits[$i]->Township,
                                'well_type' => $decodedPermits[$i]->WellType,
                                'btm_geometry' => $btmLatLng,
                                'reported_operator' => $decodedPermits[$i]->ReportedOperator,
                                'permit_number' => $decodedPermits[$i]->PermitNumber,
                                'permit_status' => $decodedPermits[$i]->PermitStatus,
                                'district' => $decodedPermits[$i]->District,
                                'created_date' => $decodedPermits[$i]->CreatedDate,
                                'submitted_date' => $decodedPermits[$i]->SubmittedDate,
                                'interest_area' => $interestArea,
                                'SurfaceLatitudeWGS84' => $decodedPermits[$i]->SurfaceLatitudeWGS84,
                                'SurfaceLongitudeWGS84' => $decodedPermits[$i]->SurfaceLongitudeWGS84
                            ]);
                        }
                    }
                }
            }
            return 'success';

        } catch ( Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
            return 'error';
        }
    }
}
