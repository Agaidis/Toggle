<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\MineralOwner;
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

            // Read a CSV file
            $handle = fopen("../storage/app/public/tma.csv", "r");
            $lineNumber = 0;

            while (($raw_string = fgets($handle)) !== false) {

                // Parse the raw csv string: "1, a, b, c"
                $row = str_getcsv($raw_string);

                $errorMsg = new ErrorLog();
                $errorMsg->payload = serialize($row);
                $errorMsg->save();

                try {
                    $owner = $row[0];
                    $ownerAddress = $row[1];
                    $ownerCity = $row[2];
                    $ownerState = $row[3];
                    $ownerZip = $row[4];
                    $ownerDecimalInterest = $row[5];
                    $ownerInterestType = $row[6];
                    $appraisalYear = $row[7];
                    $operatorCompanyName = $row[8];
                    $reportedOperatorName = $row[9];
                    $operatorTicket = $row[10];
                    $leaseName = $row[11];
                    $rrcLeaseNumber = $row[12];
                    $county = $row[13];
                    $stateProvince = $row[14];
                    $diBasin = $row[15];
                    $diPlay = $row[16];
                    $diSubplay = $row[17];
                    $taxValue = $row[18];
                    $api10 = $row[19];
                    $leaseDescription = $row[21];
                    $rrcDistrict = $row[21];
                    $firstProdDate = $row[23];
                    $lastProdDate = $row[24];
                    $cumProdOil = $row[25];
                    $cumProdGas = $row[26];
                    $activeWellCount = $row[20];

                    $isOwnerExist = MineralOwner::where('owner', $owner)->where('lease_name', $leaseName)->get();

                    if ($isOwnerExist->isEmpty()) {

                        $newOwner = new MineralOwner();
                        $newOwner->owner = $owner;
                        $newOwner->lease_name = $leaseName;
                        $newOwner->operator_company_name = $operatorCompanyName;
                        $newOwner->owner_address = $ownerAddress;
                        $newOwner->owner_city = $ownerCity;
                        $newOwner->owner_state = $ownerState;
                        $newOwner->owner_zip = $ownerZip;
                        $newOwner->owner_decimal_interest = $ownerDecimalInterest;
                        $newOwner->owner_interest_type = $ownerInterestType;
                        $newOwner->appraisal_year = $appraisalYear;
                        $newOwner->rrc_lease_number = $rrcLeaseNumber;
                        $newOwner->county = $county;
                        $newOwner->state = $stateProvince;
                        $newOwner->tax_value = $taxValue;
                        $newOwner->lease_description = $leaseDescription;
                        $newOwner->first_prod_date = $firstProdDate;
                        $newOwner->last_prod_date = $lastProdDate;
                        $newOwner->cum_prod_oil = $cumProdOil;
                        $newOwner->cum_prod_gas = $cumProdGas;
                        $newOwner->active_well_count = $activeWellCount;

                        $newOwner->save();

                    } else {
                        MineralOwner::where('id', $isOwnerExist[0]->id)->update([
                            'tax_value' => $taxValue,
                            'first_prod_date' => $firstProdDate,
                            'last_prod_date' => $lastProdDate,
                            'cum_prod_oil' => $cumProdOil,
                            'cum_prod_gas' => $cumProdGas,
                            'lease_description' => $leaseDescription,
                            'appraisal_year' => $appraisalYear,
                            'owner_decimal_interest' => $ownerDecimalInterest,
                            'active_well_count' => $activeWellCount
                        ]);
                    }

                } catch ( Exception $e ) {
                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
                    $errorMsg->save();
                }

                // Increase the current line
                $lineNumber++;
            }

            fclose($handle);

       //     Excel::import(new MineralOwners(), storage_path('app/public/tma.csv'));

            return redirect('/upload-tma')->with('message', 'Owners are Updated!');

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();

            return back();
        }

    }

}
