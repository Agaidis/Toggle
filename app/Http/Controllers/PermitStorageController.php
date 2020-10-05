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
<<<<<<< HEAD
        $this->middleware('auth');
=======
        $this->middleware('Auth');
>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
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
