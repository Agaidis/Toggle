<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\MineralOwners;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;


class UploadTMAController extends Controller
{
    public function index() {
        try {

            return view('uploadTMA');
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();

            return back();
        }
    }

    public function uploadTMA(Request $request) {

        try {

            if (isset($_FILES['tma'])) {
                $request->file('tma')->storeAs('public','tma.csv');
            } else {
                $file = 'notworking';
            }

            Excel::import(new MineralOwners(), storage_path('app/public/tma.csv'));

            return redirect('/upload-tma')->with('message', 'Owners are Updated!');

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();

            return back();
        }

    }

}
