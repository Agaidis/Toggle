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


            Excel::filter('chunk')->load(database_path('seeds/csv/users.csv'))->chunk(250, function($results) {
                foreach ($results as $row) {
                    $user = User::create([
                        'username' => $row->username,
                        // other fields
                    ]);
                }
            });

            Excel::batch(storage_path('app/public/tma.csv'), function($rows, $file) {

                // Explain the reader how it should interpret each row,
                // for every file inside the batch
                $rows->each(function($row) {

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = serialize($row);
                    $errorMsg->save();

                });

            });

            return redirect('/upload-tma')->with('message', 'Owners are Updated!');

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();

            return back();
        }

    }

}
