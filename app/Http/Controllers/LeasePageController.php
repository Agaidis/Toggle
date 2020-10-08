<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\GeneralSetting;

use App\MineralOwner;
use App\OwnerEmail;
use App\OwnerNote;
use App\OwnerPhoneNumber;
use App\Permit;
use App\PermitNote;
use App\Models\User;
use App\WellRollUp;
use DateTime;
use Illuminate\Http\Request;
use JavaScript;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeasePageController extends Controller
{
    private $txInterestAreas = ['eagleford', 'wtx', 'tx', 'etx'];
    private $nonTexasInterestAreas = ['nm', 'la'];

    public function index(Request $request) {
        $users = User::select('id','name')->get();

        $permitId = $request->permitId;
        $leaseName = $request->leaseName;
        $interestArea = $request->interestArea;
        $txInterestAreas = ['eagleford', 'wtx', 'etx', 'tx'];
        $nonTexasInterestAreas = ['nm', 'la'];
        $mineralOwnerLeases = '';
        $isProducing = $request->isProducing;
        $leaseString = '';


        $permitValues = Permit::where('permit_id', $permitId)->first();

        try {
            $dateArray = array();
            $onProductionArray = array();
            $oilArray = array();
            $gasArray = array();
            $leaseArray = array();
            $usingLegalLeases = false;
            $notes = '';
            $wellArray = explode('|', $permitValues->selected_well_name);

            array_push($wellArray, $permitValues->lease_name);
            $allRelatedPermits = Permit::where('lease_name', $permitValues->lease_name)->where('SurfaceLatitudeWGS84', '!=', null)->get();


            //Its a texas Permit. Lets break down the options
            if (in_array($request->interestArea, $this->txInterestAreas)) {

                //Grab all of the mineral owners for the select dropdown
                $mineralOwnerLeases = MineralOwner::select('lease_name')->groupBy('lease_name')->get();

                $leaseArray = explode('|', $permitValues->selected_lease_name);
                array_push($leaseArray, $permitValues->lease_name);
                if ( $permitValues->selected_lease_name != null ) {
                    $owners = MineralOwner::select('id', 'lease_name', 'assignee', 'wellbore_type', 'follow_up_date', 'owner', 'owner_address', 'owner_city', 'owner_zip', 'owner_decimal_interest', 'owner_interest_type')->whereIn('lease_name',  $leaseArray)->groupBy('owner')->orderBy('owner_decimal_interest', 'DESC')->get();
                } else {
                    $owners = MineralOwner::where('lease_name', $permitValues->lease_name)->groupBy('owner')->orderBy('owner_decimal_interest', 'DESC')->get();
                }

                if ($owners->isEmpty()) {
                    $usingLegalLeases = true;

                    if (!$allRelatedPermits->isEmpty()) {
                        $owners = DB::select(DB::raw('SELECT *, (3959 * acos(cos(radians(' . $allRelatedPermits[0]->SurfaceLatitudeWGS84 . ')) 
                                                    * cos(radians(LatitudeWGS84)) 
                                                    * cos(radians(LongitudeWGS84) - radians(' . $allRelatedPermits[0]->SurfaceLongitudeWGS84 . ')) +
                                                    sin(radians(' . $allRelatedPermits[0]->SurfaceLatitudeWGS84 . ')) *
                                                    sin(radians(LatitudeWGS84 )))
                                                    ) AS distance 
                                                    FROM mineral_owners 
                                                    HAVING distance < 1.5
                                                    ORDER BY distance LIMIT 0, 1000'));
                    } else {
                        $owners = '';
                    }
                }
                $leaseString = implode( '|', $leaseArray);

            } else {
                $usingLegalLeases = true;
                //Its NEW MEXICO OR LOUISIANA SO DO THE DISTANCE THING
                if (!$allRelatedPermits->isEmpty()) {
                    $owners = DB::select(DB::raw('SELECT *, (3959 * acos(cos(radians(' . $allRelatedPermits[0]->SurfaceLatitudeWGS84 . ')) 
                                                    * cos(radians(LatitudeWGS84)) 
                                                    * cos(radians(LongitudeWGS84) - radians(' . $allRelatedPermits[0]->SurfaceLongitudeWGS84 . ')) +
                                                    sin(radians(' . $allRelatedPermits[0]->SurfaceLatitudeWGS84 . ')) *
                                                    sin(radians(LatitudeWGS84 )))
                                                    ) AS distance 
                                                    FROM mineral_owners 
                                                    HAVING distance < 1.8
                                                    ORDER BY distance LIMIT 0, 1000'));
                } else {
                    $owners = '';
                }
            }

            if (!$allRelatedPermits->isEmpty()) {
                $allWells = DB::select(DB::raw('SELECT *, (3959 * acos(cos(radians(' . $allRelatedPermits[0]->SurfaceLatitudeWGS84 . ')) 
                                                    * cos(radians(SurfaceHoleLatitudeWGS84)) 
                                                    * cos(radians(SurfaceHoleLongitudeWGS84) - radians(' . $allRelatedPermits[0]->SurfaceLongitudeWGS84 . ')) +
                                                    sin(radians(' . $allRelatedPermits[0]->SurfaceLatitudeWGS84 . ')) *
                                                    sin(radians(SurfaceHoleLatitudeWGS84 )))
                                                    ) AS distance 
                                                    FROM well_rollups 
                                                    HAVING distance < 300
                                                    ORDER BY distance LIMIT 0, 1000'));
            } else {
                $allWells = '';
            }

            if ($permitValues->selected_well_name == '' || $permitValues->selected_well_name == null) {
                $wells = WellRollUp::select('id', 'CountyParish','OperatorCompanyName','WellStatus','WellName', 'LeaseName', 'WellNumber', 'FirstProdDate', 'LastProdDate', 'CumOil', 'CumGas')->where('LeaseName', $permitValues->lease_name)->where('CountyParish', 'LIKE', '%'.$permitValues->county_parish .'%')->get();
                $selectWells = WellRollUp::where('CountyParish', 'LIKE', '%'.$permitValues->county_parish .'%')->groupBy('LeaseName')->orderBy('LeaseName', 'ASC')->get();

            } else {
                $wells = WellRollUp::select('id', 'CountyParish','OperatorCompanyName','WellStatus','WellName', 'LeaseName', 'WellNumber', 'FirstProdDate', 'LastProdDate', 'CumOil', 'CumGas')->whereIn('LeaseName', $wellArray)->where('CountyParish', 'LIKE', '%'.$permitValues->county_parish .'%')->get();
                $selectWells = WellRollUp::where('CountyParish', 'LIKE', '%'.$permitValues->county_parish .'%')->groupBy('LeaseName')->orderBy('LeaseName', 'ASC')->get();

            }

            $permitNotes = PermitNote::select('notes')->where('lease_name', $leaseName)->orderBy('id', 'DESC')->get();

            foreach ($permitNotes as $permitNote) {
                $notes .= $permitNote->notes;
            }

            $totalGas = 0;
            $totalGasWithComma = 0;
            $totalOil = 0;
            $totalOilWithComma = 0;

            foreach ($wells as $well) {
                if ($well->WellStatus == 'ACTIVE') {
                    $isProducing = 'producing';
                    Permit::where('permit_id', $permitId)->update(['is_producing' => 1]);
                }

                if ($well->FirstProdDate != null)
                    array_push($onProductionArray, $well->FirstProdDate);
                array_push($dateArray, $well->LastProdDate);
                array_push($oilArray, $well->CumOil);
                array_push($gasArray, $well->CumGas);

                if ( count($gasArray) > 0 ) {
                    $totalGas = $totalGas + $well->CumGas;
                }
                if ( count($oilArray) > 0) {
                    $totalOil = $totalOil + $well->CumOil;
                }
            }

            if ( $totalGas > 0 ) {
                $totalGasWithComma = number_format($totalGas);
            }

            if ( $totalOil > 0 ) {
                $totalOilWithComma = number_format($totalOil);
            }

            if ( count($dateArray) > 0 ) {
                $latestDate = max($dateArray);

                if ( count($onProductionArray) > 0) {
                    $oldestDate = min($onProductionArray);
                } else {
                    $oldestDate = min($dateArray);
                }

                $datetime1 = new DateTime($oldestDate);
                $datetime2 = new DateTime($latestDate);
                $interval = $datetime1->diff($datetime2);
                $yearsOfProduction = $interval->y + 1;

                $bbls = $totalOil / $yearsOfProduction;
                $gbbls = $totalGas / $yearsOfProduction;
                $bblsWithComma = number_format($bbls);
                $gbblsWithComma = number_format($gbbls);
            } else {
                $oldestDate = 0;
                $latestDate = 0;
                $bblsWithComma = 0;
                $gbblsWithComma = 0;
                $yearsOfProduction = 0;
            }
            $count = count($wells);



            JavaScript::put(
                [
                    'allWells' => $allWells,
                    'selectedWells' => $wellArray,
                    'allRelatedPermits' => $allRelatedPermits,
                    'leaseName' => $leaseName,
                    'permitId' => $permitId,
                    'usingLegalLeases' => $usingLegalLeases,
                    'interestArea' => $interestArea
                ]);

            return view('leasePage', compact(
                    'owners',
                    'interestArea',
                    'txInterestAreas',
                    'nonTexasInterestAreas',
                    'usingLegalLeases',
                    'permitValues',
                    'mineralOwnerLeases',
                    'leaseName',
                    'leaseString',
                    'leaseArray',
                    'wellArray',
                    'notes',
                    'selectWells',
                    'users',
                    'isProducing',
                    'wells',
                    'wellArray',
                    'count',
                    'oldestDate',
                    'latestDate',
                    'yearsOfProduction',
                    'totalGasWithComma',
                    'totalOilWithComma',
                    'bblsWithComma',
                    'gbblsWithComma')

            );
        } catch( \Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateAcreage(Request $request) {
        try {
           $meh = Permit::where('id', $request->id)
                ->update(['acreage' => $request->acreage]);

            $errorMsg = new ErrorLog();
            $errorMsg->payload = serialize($meh);

            $errorMsg->save();
            return 'success';

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateWellNames(Request $request) {
        try {
            Permit::where('id', $request->permitId)
                ->update(['selected_well_name' => $request->wellNames]);

            return $request->permitId;
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateLeaseNames(Request $request) {
        try {
            Permit::where('id', $request->permitId)
                ->update(['selected_lease_name' => $request->leaseNames]);

            return $request->permitId;
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateAssignee(Request $request) {
        try {
            if ($request->assigneeId != 0) {
                MineralOwner::where('id', $request->ownerId)->update(
                    [
                        'assignee' => $request->assigneeId,
                        'follow_up_date' => date('Y-m-d h:i:s A', strtotime('+1 day +19 hours'))
                    ]);
            } else {
                MineralOwner::where('id', $request->ownerId)->update(
                    [
                        'assignee' => $request->assigneeId
                    ]);
            }

            return 'success';

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();

            return 'error';
        }
    }

    public function updateWellType(Request $request) {
        try {
            if ($request->wellType != 0) {
                MineralOwner::where('id', $request->ownerId)->update(
                    [
                        'wellbore_type' => $request->wellType,
                        'follow_up_date' => date('Y-m-d h:i:s A', strtotime('+1 day +19 hours'))
                    ]);
            } else {
                MineralOwner::where('id', $request->ownerId)->update(
                    [
                        'wellbore_type' => $request->wellType
                    ]);
            }

            return 'success';

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();

            return 'error';
        }
    }

    public function getOwnerInfo (Request $request) {

        try {
            $owner = MineralOwner::where('id', $request->id)->groupBy('owner')->get();

            if ($owner->isEmpty()) {
                $owner = MineralOwner::where('id', $request->id)->groupBy('Grantor')->get();
            }

            $permitData = Permit::where('id', $request->permitId)->first();

            $leaseName = $permitData->lease_name;
            $reportedOperator = $permitData->reported_operator;
            $county = $permitData->county_parish;

            $owner[0]['reported_operator'] = $reportedOperator;
            $owner[0]['county'] = $county;
            $owner[0]['lease'] = $leaseName;

            $oilPrice = GeneralSetting::where('name', 'oil')->value('value');
            $gasPrice = GeneralSetting::where('name', 'gas')->value('value');

            $owner[0]['oilPrice'] = $oilPrice;
            $owner[0]['gasPrice'] = $gasPrice;

            return $owner[0];
        } catch ( \Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return false;
        }
    }


    public function updateOwnerPrice(Request $request) {
        try {

            MineralOwner::where('id', $request->id)
                ->update(['price' => $request->price]);

            return 'success';

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function getWellInfo(Request $request ) {

        try {
            $wellDetails = WellRollUp::where('id', $request->id)->get();

            return $wellDetails;
        } catch ( \Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }



    public function getNotes(Request $request) {
    try {

        $ownerInfo = MineralOwner::where('id', $request->ownerId)->first();

        if (isset($request->page) && $request->page == 'wellbore') {
            $ownerNotes = OwnerNote::where('owner_name', $ownerInfo->owner)->orderBy('id', 'DESC')->get();

            if ($ownerNotes->isEmpty()) {
                $ownerNotes = OwnerNote::where('owner_name', $ownerInfo->Grantor)->orderBy('id', 'DESC')->get();
            }
        } else {
            if (isset($request->leaseNames)) {
                $leaseArray = explode('|', $request->leaseNames);
                $ownerNotes = OwnerNote::where('owner_name', $ownerInfo->owner)->whereIn('lease_name', $leaseArray)->orderBy('id', 'DESC')->get();

                if ($ownerNotes->isEmpty()) {
                    $ownerNotes = OwnerNote::where('owner_name', $ownerInfo->Grantor)->orderBy('id', 'DESC')->get();
                }
            } else {
                $ownerNotes = OwnerNote::where('owner_name', $ownerInfo->owner)->where('lease_name', $request->leaseName)->orderBy('id', 'DESC')->get();

                if ($ownerNotes->isEmpty()) {
                    $ownerNotes = OwnerNote::where('owner_name', $ownerInfo->Grantor)->orderBy('id', 'DESC')->get();
                }
            }
        }

        return $ownerNotes;
    } catch( \Exception $e ) {
        $errorMsg = new ErrorLog();
        $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

        $errorMsg->save();
    }
}

    public function updateNotes(Request $request) {
        try {
            $userName = Auth()->user()->name;
            $userId = Auth()->user()->id;
            $date = date('d/m/Y h:m:s', strtotime('-5 hours'));

            $ownerInfo = MineralOwner::where('id', $request->id)->first();
            if ($ownerInfo->owner == null || $ownerInfo->owner == '' && ($ownerInfo->Grantor != '' && $ownerInfo->Grantor != null)) {

                $owner = $ownerInfo->Grantor;
            } else {
                $owner = $ownerInfo->owner;
            }

            MineralOwner::where('id', $request->id)->update(['assignee' => $userId, 'follow_up_date' => date('Y-m-d h:i:s A', strtotime('+1 day +19 hours'))]);

            $newOwnerLeaseNote = new OwnerNote();

            $newOwnerLeaseNote->lease_name = $request->leaseName;
            $newOwnerLeaseNote->owner_name = $owner;
            $newOwnerLeaseNote->notes = '<div class="owner_note" id="owner_'.$newOwnerLeaseNote->id.'_'.$request->id.'"><p style="font-size:14px; margin-bottom:0;"> '.$userName . ' | '. $date . '<span class="fas fa-trash delete_owner_note" id="delete_owner_note_'.$newOwnerLeaseNote->id.'_'.$request->id.'" style="display:none; cursor:pointer; color:red; float:right;margin-right:5%;"></span></p>' . $request->notes .'<hr></div>';

            $newOwnerLeaseNote->save();

            OwnerNote::where('id', $newOwnerLeaseNote->id)
                ->update(['notes' => '<div class="owner_note" id="owner_'.$newOwnerLeaseNote->id.'_'.$request->id.'"><p style="font-size:14px; margin-bottom:0;">'.$userName . ' | '. $date . '<span class="fas fa-trash delete_owner_note" id="delete_owner_note_'.$newOwnerLeaseNote->id.'_'.$request->id.'" style="display: none; cursor:pointer; color:red; float:right;margin-right:3%;"></span></p>' . $request->notes .'<hr></div>']);

            $updatedOwnerNote = OwnerNote::where('owner_name', $owner)->where('lease_name', $request->leaseName)->orderBy('id', 'DESC')->get();



            return $updatedOwnerNote;

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function deleteNote(Request $request) {
        try {
            $ownerNote = OwnerNote::where('id', $request->id)->first();

            OwnerNote::destroy($request->id);

            $updatedOwnerNote = OwnerNote::where('owner_name', $ownerNote->owner_name)->where('lease_name', $ownerNote->lease_name)->orderBy('id', 'DESC')->get();

            return $updatedOwnerNote;
        } catch( Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateFollowUp(Request $request) {
        try {

            if ($request->date != '') {
                $dateObj = str_replace('/', '-', $request->date);

                $dateArray = explode('-', $dateObj);

                $formattedDate = $dateArray[1] . '-' . $dateArray[0] . '-' . $dateArray[2];

                $date = date('Y-m-d h:i:s A', strtotime($formattedDate));
            } else {
                $date = null;
            }

            MineralOwner::where('id', $request->id)->update(['follow_up_date' => $date]);

            return 'success';

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function getOwnerNumbers(Request $request) {
        try {

            Log::info($request->ownerName);

            $phoneNumbers = OwnerPhoneNumber::where('owner_name', $request->ownerName)->where('soft_delete', 0)->where('is_pushed', 0)->get();

            return $phoneNumbers;

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function addPhone(Request $request) {
        try {
            $newOwnerPhoneNumber = new OwnerPhoneNumber();
            $newOwnerPhoneNumber->owner_id = $request->id;
            $newOwnerPhoneNumber->phone_number = $request->phoneNumber;
            $newOwnerPhoneNumber->owner_name = $request->ownerName;
            $newOwnerPhoneNumber->phone_desc = $request->phoneDesc;
            $newOwnerPhoneNumber->interest_areas = $request->interestArea;
            $newOwnerPhoneNumber->lease_name = $request->leaseName;

            $newOwnerPhoneNumber->save();

            return $newOwnerPhoneNumber;

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function pushPhoneNumber(Request $request) {
        try {

            OwnerPhoneNumber::where('id', $request->id)
                ->update([
                    'is_pushed' => 1,
                    'reason' => $request->reason
                ]);

            return $request->id;

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function softDeletePhone(Request $request) {
        try {
            OwnerPhoneNumber::where('id', $request->id)
                ->update(['soft_delete' => 1]);

            return $request->id;

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }
}
