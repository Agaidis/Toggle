<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\MineralOwner;
use App\Permit;
use Illuminate\Http\Request;

class LeaseCreatorController extends Controller
{
    public function index()
    {
        $counties = Permit::groupBy('county_parish')->get();

        $selectLeases = MineralOwner::groupBy('lease_name')->get();

        return view('createLease', compact('counties', 'selectLeases'));
    }


    public function createLease(Request $request) {
        try {

            $eagleInterestCountiesArray = array('ATASCOSA','BEE','DEWITT','GONZALES','KARNES','LIVE OAK','LAVACA','WILSON');
            $wtxInterestCountiesArray = array('ANDREWS', 'DAWSON', 'GAINES', 'BORDEN', 'CRANE', 'ECTOR', 'STERLING', 'MITCHELL', 'JEFF DAVIS');
            $nmInterestCountiesArray = array('LEA', 'EDDY');

            $permitId = 0000 . rand(7000000, 10000000);
            $state = $request->state;
            $county = $request->county;
            $leaseName = $request->leaseName;

            if (in_array($county, $wtxInterestCountiesArray)) {
                $interestArea = 'wtx';
            } else if (in_array($county, $eagleInterestCountiesArray)) {
                $interestArea = 'eagleford';
            } else if (in_array($county, $nmInterestCountiesArray)) {
                $interestArea = 'nm';
            }

            $newPermit = new Permit();

            $newPermit->permit_id = $permitId;
            $newPermit->state = $state;
            $newPermit->county_parish = $county;
            $newPermit->lease_name = $leaseName;
            $newPermit->interest_area = $interestArea;

            $newPermit->save();

            $request->session()->flash('status', 'Lease was successfully created!');

            return back();
        } catch ( \Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
            return 'error';
        }
    }
}
