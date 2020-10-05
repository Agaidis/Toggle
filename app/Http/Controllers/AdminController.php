<?php

namespace App\Http\Controllers;

use App\Console\Commands\GetPermits;
use App\ErrorLog;
use App\Jobs\LegalLeases;
use App\LegalLease;
use App\Permit;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin');
    }


    public function updatePermits(Request $request) {

        try {
            $getPermits = new GetPermits();
            $countyArray = array($request->county);
            $getPermits->getCountyPermitData('2020-04-01','admin', $countyArray);

            $countyPermits = DB::table('permits')->where('interest_area', 'admin')->where('county_parish', $request->county)->get();

            return $countyPermits;
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
            return 'error';
        }
    }
}
