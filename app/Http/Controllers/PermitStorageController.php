<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use Illuminate\Http\Request;
use App\Permit;

class PermitStorageController extends Controller
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
     * Show the application Storage page.
     *
     *
     */
    public function index()
    {
        try {
            $permits = Permit::where('is_stored', 1)->groupBy('lease_name', 'reported_operator')->get();

            return view('permitStorage', compact('permits'));
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();

            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            return back();
        }
    }

    public function sendBack()
    {
        try {
            Permit::where('lease_name', $_GET['leaseName'])->update(['is_stored' => 0]);

            return 'success';
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();

            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();

            return 'error';
        }
    }


}
