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
            $wtxInterestCountiesArray = array('DAWSON%20\(TX\)', 'GAINES%20\(TX\)', 'BORDEN%20\(TX\)', 'CRANE%20\(TX\)', 'ECTOR%20\(TX\)', 'STERLING%20\(TX\)', 'MITCHELL%20\(TX\)', 'JEFF%20DAVIS%20\(TX\)', 'ANDREWS%20\(TX\)');
            $nmByApprovedDate = array('LEA%20\(NM\)', 'EDDY%20\(NM\)');
            $etxInterestCountiesArray = array('Cass%20\(TX\)', 'Gregg%20\(TX\)', 'Harrison%20\(TX\)', 'Marion%20\(TX\)', 'Morris%20\(TX\)', 'Nacogdoches%20\(TX\)', 'Panola%20\(TX\)', 'San%20Augustine%20\(TX\)', 'Rusk%20\(TX\)', 'Shelby%20\(TX\)', 'Upshur%20\(TX\)');
            $laInterestCountiesArray = array('Bienville%20\(LA\)', 'Bossier%20\(LA\)', 'Caddo%20\(LA\)', 'De%20Soto%20\(LA\)', 'Natchitoches', 'Red%20River%20\(LA\)', 'Sabine%20\(LA\)', 'Webster%20\(LA\)');

            $this->getCountyWellData($eagleInterestCountiesArray);

            $this->getCountyWellData($wtxInterestCountiesArray);

            $this->getCountyWellData($nmByApprovedDate);

            $this->getCountyWellData($etxInterestCountiesArray);

            $this->getCountyWellData($laInterestCountiesArray);


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
