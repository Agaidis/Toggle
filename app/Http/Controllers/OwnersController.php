<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\MineralOwner;
use App\Models\User;
use App\OwnerEmail;
use App\OwnerNote;
use App\OwnerPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Permit;
use Illuminate\Support\Facades\Log;

class OwnersController extends Controller
{

    public function index(Request $request) {

        try {
            $ownerName = $request->ownerName;
            $interestArea = $request->interestArea;
            $isProducing = $request->isProducing;
            $permitObj = array();
            $noteArray = array();

            $ownerNotes = OwnerNote::where('owner_name', $ownerName)->get();
            $ownerPhoneNumbers = OwnerPhoneNumber::where('owner_name', $ownerName)->orderBy('soft_delete', 'ASC')->get();
            $email = OwnerEmail::where('name', $ownerName)->value('email');




                if (!$ownerNotes->isEmpty()) {
                    $ownerLeaseData = DB::table('mineral_owners')
                        ->where('owner', $ownerName)
                        ->join('owner_notes', 'mineral_owners.owner', '=', 'owner_notes.owner_name')
                        ->leftjoin('permits', function($join){
                            //$join->on('permits.lease_name','=','mineral_owners.lease_name');
                            $join->on('permits.selected_lease_name','=','mineral_owners.lease_name');
                        })
                        ->select('owner_notes.*', 'mineral_owners.*','permits.selected_lease_name', 'permits.lease_name as permitLeaseName', 'permits.permit_id', 'permits.interest_area')
                        ->groupBy('mineral_owners.lease_name')
                        ->limit(500)
                        ->get();


                    if ($ownerLeaseData->isEmpty()) {
                        $ownerLeaseData = DB::table('mineral_owners')
                            ->where('Grantor', $ownerName)
                            ->join('owner_notes', 'mineral_owners.Grantor', '=', 'owner_notes.owner_name')
                            ->leftjoin('permits', function($join){
                                $join->on('permits.lease_name','=','mineral_owners.lease_name');
                                $join->orOn('permits.selected_lease_name','=','mineral_owners.lease_name');
                            })
                            ->select('owner_notes.*', 'mineral_owners.*', 'permits.interest_area', 'permits.lease_name as permitLeaseName', 'permits.permit_id', 'permits.selected_lease_name')
                            ->get();
                    }
                } else {
                    $ownerLeaseData = DB::table('mineral_owners')->where('owner', $ownerName)->limit(500)->get();
                    if ($ownerLeaseData->isEmpty()) {
                        $ownerLeaseData = DB::table('mineral_owners')->where('Grantor', $ownerName)->get();
                    }
                }


                $count = 0;
                $assigneeId = null;
                $followUpDate = null;

                foreach ($ownerLeaseData as $ownerLease) {
                    $leaseNote = '';
                    if ($assigneeId == null) {
                        if ($ownerLease->assignee != NULL) {
                            $assigneeId = $ownerLease->assignee;
                        }
                    }
                    if ($followUpDate == null) {
                        if ($ownerLease->follow_up_date != NULL) {
                            $followUpDate = $ownerLease->follow_up_date;
                        }
                    }

                    $permits = Permit::where('lease_name', $ownerLease->lease_name)->first();
                    $notes = OwnerNote::where('owner_name', $ownerLease->owner)->where('lease_name', $ownerLease->lease_name)->orderBy('created_at', 'DESC')->get();

                    if (is_object(($permits))) {
                        $permitObj[$count]['lease_name'] = $permits->lease_name;
                        $permitObj[$count]['reported_operator'] = $permits->reported_operator;
                        $permitObj[$count]['permit_id'] = $permits->permit_id;
                        $permitObj[$count]['interest_area'] = $permits->interest_area;
                    } else {
                        $permitObj[$count]['lease_name'] = '';
                        $permitObj[$count]['reported_operator'] = '';
                        $permitObj[$count]['permit_id'] = '';
                        $permitObj[$count]['interest_area'] = '';

                    }

                    if ($notes->isEmpty()) {
                        $noteArray[$count]['lease_name'] = '';
                        $noteArray[$count]['notes'] = '';

                    } else {
                        foreach ($notes as $note ) {
                            $leaseNote .= $note->notes;
                        }
                        $noteArray[$count]['lease_name'] = $notes[0]->lease_name;
                        $noteArray[$count]['notes'] = $leaseNote;
                    }
                    $count++;
                }

                if ($assigneeId != null) {
                    $assignee = User::where('id', $assigneeId)->value('name');
                }

            return view('owner', compact('ownerName', 'assignee', 'followUpDate', 'ownerNotes', 'interestArea', 'isProducing', 'ownerPhoneNumbers','ownerLeaseData', 'permitObj', 'noteArray', 'email' ));
        } catch( \Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getTraceAsString() . $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updateEmail (Request $request) {
        try {

            $ownerEmail = OwnerEmail::where('name', $request->name)->get();

            if ($ownerEmail->isEmpty()) {
                $newEmail = new OwnerEmail();

                $newEmail->name = $request->name;
                $newEmail->email = $request->email;

                $newEmail->save();
            } else {
                OwnerEmail::where('id', $ownerEmail[0]->id)->update(['email' => $request->email]);
            }

            return 'success';
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getTraceAsString() . $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }
}
