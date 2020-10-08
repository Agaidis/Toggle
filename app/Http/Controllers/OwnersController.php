<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\MineralOwner;
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
                        ->select('owner_notes.*', 'mineral_owners.*')
                        ->groupBy('mineral_owners.lease_name')
                        ->limit(500)
                        ->get();

                    if ($ownerLeaseData->isEmpty()) {
                        $ownerLeaseData = DB::table('mineral_owners')
                            ->where('Grantor', $ownerName)
                            ->join('owner_notes', 'mineral_owners.Grantor', '=', 'owner_notes.owner_name')
                            ->select('owner_notes.*', 'mineral_owners.*')
                            ->get();
                    }
                } else {
                    $ownerLeaseData = DB::table('mineral_owners')->where('owner', $ownerName)->limit(500)->get();
                    if ($ownerLeaseData->isEmpty()) {
                        $ownerLeaseData = DB::table('mineral_owners')->where('Grantor', $ownerName)->get();
                    }
                }
                $count = 0;

                foreach ($ownerLeaseData as $ownerLease) {
                    $leaseNote = '';

                    $permits = Permit::where('lease_name', $ownerLease->lease_name)->first();
                    $notes = OwnerNote::where('owner_name', $ownerLease->owner)->where('lease_name', $ownerLease->lease_name)->orderBy('created_at', 'DESC')->get();

                    if (is_object(($permits))) {
                        $permitObj[$count]['lease_name'] = $permits->lease_name;
                        $permitObj[$count]['reported_operator'] = $permits->reported_operator;
                        $permitObj[$count]['permit_id'] = $permits->permit_id;
                    } else {
                        $permitObj[$count]['lease_name'] = '';
                        $permitObj[$count]['reported_operator'] = '';
                        $permitObj[$count]['permit_id'] = '';
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

            return view('owner', compact('ownerName', 'ownerNotes', 'interestArea', 'isProducing', 'ownerPhoneNumbers','ownerLeaseData', 'permitObj', 'noteArray', 'email' ));
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
