<?php

namespace App\Console\Commands;

use App\ErrorLog;
use App\Http\Controllers\APIManager;
use App\WellRollUp;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetWells extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:getWells';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will get the well rollups';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        try {

            $eagleInterestCountiesArray = array('ATASCOSA%20\(TX\)', 'BEE%20\(TX\)', 'DEWITT%20\(TX\)', 'GONZALES%20\(TX\)', 'KARNES%20\(TX\)', 'LIVE%20OAK%20\(TX\)', 'LAVACA%20\(TX\)', 'WILSON%20\(TX\)');
            $wtxInterestCountiesArray = array('DAWSON%20\(TX\)', 'GAINES%20\(TX\)', 'BORDEN%20\(TX\)', 'CRANE%20\(TX\)', 'ECTOR%20\(TX\)', 'STERLING%20\(TX\)', 'MITCHELL%20\(TX\)', 'JEFF%20DAVIS%20\(TX\)', 'ANDREWS%20\(TX\)', 'HOWARD%20\(TX\)', 'MARTIN%20\(TX\)' );
            $wtx2InterestCountiesArray = array('MIDLAND%20\(TX\)', 'GLASSCOCK%20\(TX\)', 'REAGAN%20\(TX\)', 'UPTON%20\(TX\)', 'WARD%20\(TX\)', 'REEVES%20\(TX\)', 'CULBERSON%20\(TX\)', 'WINKLER%20\(TX\)', 'LOVING%20\(TX\)');
            $wtx3InterestCountiesArray = array('PECOS%20\(TX\)', 'MCMULLEN%20\(TX\)', 'WEBB%20\(TX\)', 'DIMMITT%20\(TX\)');
            $nmByApprovedDate = array('LEA%20\(NM\)', 'EDDY%20\(NM\)');
            $etxInterestCountiesArray = array('CASS%20\(TX\)', 'GREGG%20\(TX\)', 'HARRISON%20\(TX\)', 'MARION%20\(TX\)', 'MORRIS%20\(TX\)', 'NACOGDOCHES%20\(TX\)', 'PANOLA%20\(TX\)', 'SAN%20AUGUSTINE%20\(TX\)', 'RUSK%20\(TX\)', 'SHELBY%20\(TX\)', 'UPSHUR%20\(TX\)');
            $laInterestCountiesArray = array('BIENVILLE%20\(LA\)', 'BOSSIER%20\(LA\)', 'CADDO%20\(LA\)', 'DE%20SOTO%20\(LA\)', 'NATCHITOCHES%20\(LA\)', 'RED%20RIVER%20\(LA\)', 'SABINE%20\(LA\)', 'WEBSTER%20\(LA\)');

            //         $this->getCountyWellData($eagleInterestCountiesArray);

            //         $this->getCountyWellData($wtxInterestCountiesArray);

      //      $this->getCountyWellData($wtx2InterestCountiesArray);

            $this->getCountyWellData($wtx3InterestCountiesArray);

//            $this->getCountyWellData($nmByApprovedDate);

//            $this->getCountyWellData($etxInterestCountiesArray);

//            $this->getCountyWellData($laInterestCountiesArray);


            return 'success';
        } catch (Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
            return 'error';
        }
    }

    public function getCountyWellData ($counties) {

        try {

            $apiManager = new APIManager();
            $token = $apiManager->getToken();

            foreach ($counties as $county) {
                $url = '';

                do {
                    $wells = $apiManager->getWellRollUps( $token->access_token, $county, $url );

                    if (isset($wells[1])) {
                        $url = trim($wells[1]);
                    } else {
                        $url = '';
                    }

                    if ($wells[0] != null && $wells[0] != '' && isset($wells[0])) {

                        $decodedWells = json_decode($wells[0]);

                        if (is_array($decodedWells)) {

                            for ($i = 0; $i < count($decodedWells); $i++) {
                                $errorMsg = new ErrorLog();
                                $errorMsg->payload = 'County: ' . $decodedWells[$i]->CountyParish . ' API14: ' . $decodedWells[$i]->API14;
                                $errorMsg->save();

                                WellRollUp::updateOrCreate(['API14' => $decodedWells[$i]->API14],
                                    [
                                        'CountyParish' => $decodedWells[$i]->CountyParish,
                                        'OperatorCompanyName' => $decodedWells[$i]->OperatorCompanyName,
                                        'ReportedOperator' => $decodedWells[$i]->ReportedOperator,
                                        'WellName' => $decodedWells[$i]->WellName,
                                        'WellNumber' => $decodedWells[$i]->WellNumber,
                                        'WellStatus' => $decodedWells[$i]->WellStatus,
                                        'CompletionDate' => $decodedWells[$i]->CompletionDate,
                                        'CreatedDate' => $decodedWells[$i]->CreatedDate,
                                        'CumGas' => $decodedWells[$i]->CumGas,
                                        'CumOil' => $decodedWells[$i]->CumOil,
                                        'DrillType' => $decodedWells[$i]->DrillType,
                                        'LeaseName' => $decodedWells[$i]->LeaseName,
                                        'MeasuredDepth' => $decodedWells[$i]->MeasuredDepth,
                                        'Abstract' => $decodedWells[$i]->Abstract,
                                        'Range' => $decodedWells[$i]->Range,
                                        'District' => $decodedWells[$i]->District,
                                        'Section' => $decodedWells[$i]->Section,
                                        'Township' => $decodedWells[$i]->Township,
                                        'SurfaceHoleLatitudeWGS84' => $decodedWells[$i]->SurfaceHoleLatitudeWGS84,
                                        'SurfaceHoleLongitudeWGS84' => $decodedWells[$i]->SurfaceHoleLongitudeWGS84,
                                        'BottomHoleLatitudeWGS84' => $decodedWells[$i]->BottomHoleLatitudeWGS84,
                                        'BottomHoleLongitudeWGS84' => $decodedWells[$i]->BottomHoleLongitudeWGS84,
                                        'FirstProdDate' => $decodedWells[$i]->FirstProdDate,
                                        'LastProdDate' => $decodedWells[$i]->LastProdDate
                                    ]
                                );
                            }
                        }
                    }
                } while($wells[1] != '');
           }

            return 'success';
        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
            return 'error';
        }
    }
}
