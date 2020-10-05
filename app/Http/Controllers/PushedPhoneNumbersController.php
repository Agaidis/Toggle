<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\MineralOwner;
use App\OwnerPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PushedPhoneNumbersController extends Controller
{
    public function index()
    {
        try {
            $ownerArray = array();

            $pushedPhoneNumbers = DB::table('owner_phone_numbers')
                ->where('is_pushed', 1)
                ->where('LeaseId', null)
                ->join('mineral_owners', 'owner_phone_numbers.owner_name', '=', 'mineral_owners.owner')
                ->select('owner_phone_numbers.*', 'mineral_owners.owner_address', 'mineral_owners.owner_city', 'mineral_owners.owner_state', 'mineral_owners.owner_zip')
                ->groupBy('owner_phone_numbers.phone_number')
                ->orderBy('owner_phone_numbers.owner_name', 'ASC')->get();


//            $pushPhoneNumbersNM = DB::table('owner_phone_numbers')
//                ->where('is_pushed', 1)
//                ->join('legal_leases', 'owner_phone_numbers.LeaseId', '=', 'legal_leases.LeaseId')
//                ->select('owner_phone_numbers.*', 'legal_leases.Grantor', 'legal_leases.GrantorAddress')
//                ->orderBy('owner_phone_numbers.owner_name', 'ASC')->get();
//           $phoneNumbers = OwnerPhoneNumber::where('owner_id', null)->get();
//
//           foreach ( $phoneNumbers as $phoneNumber) {
//               if ($phoneNumber->owner_id === '' || $phoneNumber->owner_id === null) {
//                   $owner = MineralOwner::where('owner', $phoneNumber->owner_name)->first();
//
//                   OwnerPhoneNumber::where('owner_name', $phoneNumber->owner_name)->update(['owner_id' => $owner->id]);
//               }
//           }

            return view('pushedPhoneNumbers', compact('pushedPhoneNumbers', 'ownerArray'));
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    public function insertPhoneNumber(Request $request) {
        try {
            $ownerName = OwnerPhoneNumber::where('id', $request->id)->value('owner_name');

            $newOwnerPhoneNumber = new OwnerPhoneNumber();

            $newOwnerPhoneNumber->phone_number = $request->phoneNumber;
            $newOwnerPhoneNumber->owner_name = $ownerName;
            $newOwnerPhoneNumber->phone_desc = $request->phoneDesc;

            $newOwnerPhoneNumber->save();

            return $newOwnerPhoneNumber;

        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }

    public function updatePhoneNumber(Request $request) {
        try {

            OwnerPhoneNumber::where('id', $request->id)
                ->update([
                    'is_pushed' => 0,
                    'soft_delete' => 0,
                    'phone_number' => $request->phoneNumber,
                    'phone_desc' => $request->phoneDesc,
                    'owner_id' => $request->ownerId
                ]);

            MineralOwner::where('owner', $request->ownerName)->where('lease_name', $request->leaseName)
                ->update([
                    'follow_up_date' => date('Y-m-d h:i:s A')
                ]);

            return 'success';
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }
}
