<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\Jobs\processTMAFileUpload;
use App\MineralOwner;
use App\MineralOwners;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Jobs\ProcessTMA;


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
                $originalName = $request->file('tma')->getClientOriginalName();

                $request->file('tma')->storeAs('public', $originalName);

                processTMAFileUpload::dispatch($originalName);

            }

            return redirect('/upload-tma')->with('message', 'File is prepared for processing! Give it an hour.');

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();

            return back();
        }

    }

}
