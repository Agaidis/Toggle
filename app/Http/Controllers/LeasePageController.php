<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\GeneralSetting;
use App\LegalLease;
use App\MineralOwner;
use App\OwnerNote;
use App\OwnerPhoneNumber;
use App\Permit;
use App\PermitNote;
use App\User;
use App\WellRollUp;
use DateTime;
use Illuminate\Http\Request;
use JavaScript;
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
        $txInterestAreas = ['eagleford', 'wtx', 'tx'];
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
            $notes = '';
            $wellArray = explode('|', $permitValues->selected_well_name);

            array_push($wellArray, $permitValues->lease_name);
            $allRelatedPermits = Permit::where('lease_name', $permitValues->lease_name)->where('SurfaceLatitudeWGS84', '!=', null)->get();

            if (in_array($request->interestArea, $this->txInterestAreas)) {
                $mineralOwnerLeases = MineralOwner::select('lease_name')->groupBy('lease_name')->get();
                if ($mineralOwnerLeases->isEmpty()) {
                }
                $leaseArray = explode('|', $permitValues->selected_lease_name);
                array_push($leaseArray, $permitValues->lease_name);
                if ( $permitValues->selected_lease_name != null ) {
                    $owners = MineralOwner::select('id', 'lease_name', 'assignee', 'wellbore_type', 'follow_up_date', 'owner', 'owner_address', 'owner_city', 'owner_zip', 'owner_decimal_interest', 'owner_interest_type')->whereIn('lease_name',  $leaseArray)->groupBy('owner')->orderBy('owner_decimal_interest', 'DESC')->get();
                } else {
                    $owners = MineralOwner::where('lease_name', $permitValues->lease_name)->groupBy('owner')->orderBy('owner_decimal_interest', 'DESC')->get();
                }

                if ($owners->isEmpty()) {
                    $owners = LegalLease::where('CountyParish', 'LIKE', '%'.$permitValues->county_parish .'%')->where('SurfaceHoleLatitudeWGS84', '!=', null)->where('SurfaceHoleLatitudeWGS84', '<', $permitValues->SurfaceLatitudeWGS84 + .1)->where('LatitudeWGS84', '>', $permitValues->SurfaceLatitudeWGS84 - .1)->where('LongitudeWGS84', '<', $permitValues->SurfaceLongitudeWGS84 + .1)->orderBy('LongitudeWGS84', 'ASC')->get();

                }
                $leaseString = implode( '|', $leaseArray);

            } else {

                $owners = LegalLease::where('CountyParish', 'LIKE', '%'.$permitValues->county_parish .'%')->where('SurfaceHoleLatitudeWGS84', '!=', null)->where('SurfaceHoleLatitudeWGS84', '<', $permitValues->SurfaceLatitudeWGS84 + .1)->where('LatitudeWGS84', '>', $permitValues->SurfaceLatitudeWGS84 - .1)->where('LongitudeWGS84', '<', $permitValues->SurfaceLongitudeWGS84 + .1)->orderBy('LongitudeWGS84', 'ASC')->get();
            }

            $allWells = WellRollUp::where('CountyParish', 'LIKE', '%'.$permitValues->county_parish .'%')->where('SurfaceHoleLatitudeWGS84', '!=', null)->where('SurfaceHoleLatitudeWGS84', '<', $permitValues->SurfaceLatitudeWGS84 + .1)->where('SurfaceHoleLatitudeWGS84', '>', $permitValues->SurfaceLatitudeWGS84 - .1)->where('SurfaceHoleLongitudeWGS84', '<', $permitValues->SurfaceLongitudeWGS84 + .1)->orderBy('LeaseName', 'ASC')->get();

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
                    Permit::where('id', $permitId)->update(['is_producing' => 1]);
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
                    'permitId' => $permitId
                ]);

            return view('leasePage', compact(
                    'owners',
                    'interestArea',
                    'txInterestAreas',
                    'nonTexasInterestAreas',
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
            if (in_array($request->interestArea, $this->txInterestAreas)) {
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
            } else if (in_array($request->interestArea, $this->nonTexasInterestAreas)) {
                if ($request->assigneeId != 0) {
                    LegalLease::where('LeaseId', $request->ownerId)->update(
                        [
                            'assignee' => $request->assigneeId,
                            'follow_up_date' => date('Y-m-d h:i:s A', strtotime('+1 day +19 hours'))
                        ]);
                } else {
                    LegalLease::where('LeaseId', $request->ownerId)->update(
                        [
                            'assignee' => $request->assigneeId
                        ]);
                }
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

            if (in_array($request->interestArea, $this->txInterestAreas)) {
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
            } else if (in_array($request->interestArea, $this->nonTexasInterestAreas)) {
                if ($request->wellType != 0) {
                    LegalLease::where('LeaseId', $request->ownerId)->update(
                        [
                            'wellbore' => $request->wellType,
                            'follow_up_date' => date('Y-m-d h:i:s A', strtotime('+1 day +19 hours'))
                        ]);
                } else {
                    LegalLease::where('LeaseId', $request->ownerId)->update(
                        [
                            'wellbore' => $request->wellType
                        ]);
                }
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
            if (in_array($request->interestArea, $this->txInterestAreas )) {
                $owner = MineralOwner::where('id', $request->id)->groupBy('owner')->first();
            } else {
                $owner = LegalLease::where('LeaseId', $request->id)->first();
                $leaseName = Permit::where('permit_id', $owner->permit_stitch_id)->value('lease_name');
                $owner->lease_name = $leaseName;
            }

            $oilPrice = GeneralSetting::where('name', 'oil')->value('value');
            $gasPrice = GeneralSetting::where('name', 'gas')->value('value');

            $owner['oilPrice'] = $oilPrice;
            $owner['gasPrice'] = $gasPrice;

            return $owner;
        } catch ( \Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return false;
        }
    }


    public function updateOwnerPrice(Request $request) {
        try {
            if (in_array($request->interestArea, $this->txInterestAreas )) {
                MineralOwner::where('id', $request->id)
                    ->update(['price' => $request->price]);
            } else {
                LegalLease::where('LeaseId', $request->id)
                    ->update(['price' => $request->price]);
            }

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
        if (in_array($request->interestArea, $this->txInterestAreas )) {
            if (isset($request->leaseNames)) {
                $leaseArray = explode('|', $request->leaseNames);
                $ownerInfo = MineralOwner::where('id', $request->ownerId)->first();
                return OwnerNote::where('owner_name', $ownerInfo->owner)->whereIn('lease_name', $leaseArray)->orderBy('id', 'DESC')->get();
            } else {
                $ownerInfo = MineralOwner::where('id', $request->ownerId)->first();
                return OwnerNote::where('owner_name', $ownerInfo->owner)->where('lease_name', $request->leaseName)->orderBy('id', 'DESC')->get();
            }

        } else if (in_array($request->interestArea, $this->nonTexasInterestAreas )) {
            $ownerInfo = LegalLease::where('LeaseId', $request->ownerId)->first();
            return OwnerNote::where('owner_name', $ownerInfo->Grantor)->where('lease_name', $request->leaseName)->orderBy('id', 'DESC')->get();
        }
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

            if (in_array($request->interestArea, $this->txInterestAreas )) {
                $owner = MineralOwner::where('id', $request->id)->value('owner');
                MineralOwner::where('id', $request->id)->update(['assignee' => $userId, 'follow_up_date' => date('Y-m-d h:i:s A', strtotime('+1 day +19 hours'))]);

            } else if (in_array($request->interestArea, $this->nonTexasInterestAreas )) {
                $owner = LegalLease::where('LeaseId', $request->id)->value('Grantor');
                LegalLease::where('LeaseId', $request->id)->update(['assignee' => $userId, 'follow_up_date' => date('Y-m-d h:i:s A', strtotime('+1 day +19 hours'))]);
            }

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

            if (in_array($request->interestArea, $this->txInterestAreas)) {
                MineralOwner::where('id', $request->id)->update(['follow_up_date' => $date]);
            } else if (in_array($request->interestArea, $this->nonTexasInterestAreas)) {
                LegalLease::where('LeaseId', $request->id)->update(
                    ['follow_up_date' => $date]);
            }


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

            if (in_array($request->interestArea, $this->txInterestAreas)) {
                $ownerName = MineralOwner::where('id', $request->id)->value('owner');
            } else if (in_array($request->interestArea, $this->nonTexasInterestAreas)) {
                $ownerName = LegalLease::where('LeaseId', $request->id)->value('Grantor');
            }

<<<<<<< HEAD
            $phoneNumbers = OwnerPhoneNumber::where('owner_name', $ownerName)->where('soft_delete', 0)->where('is_pushed', 0)->get();
=======
            $phoneNumbers = OwnerPhoneNumber::where('owner_name', $ownerName)->where('soft_delete', 0)->get();
>>>>>>> e4268c60f567da46b5b3713d288a3876bd93bea7

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

            if (in_array($request->interestArea, $this->txInterestAreas)) {
                $ownerName = MineralOwner::where('id', $request->id)->value('owner');
                $newOwnerPhoneNumber->owner_id = $request->id;
            } else if (in_array($request->interestArea, $this->nonTexasInterestAreas)) {
                $ownerName = LegalLease::where('LeaseId', $request->id)->value('Grantor');
                $newOwnerPhoneNumber->LeaseId = $request->id;

            }

            $newOwnerPhoneNumber->phone_number = $request->phoneNumber;
            $newOwnerPhoneNumber->owner_name = $ownerName;
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
