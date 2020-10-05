<?php

namespace App\Console\Commands;

use App\ErrorLog;
use App\WellRollUp;
use Illuminate\Console\Command;
use App\Permit;

class DetermineProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'determine:production';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will determine what permits have wells and what do not';

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
            $eagleInterestCountiesArray = array('ATASCOSA','BEE','DEWITT','GONZALES','KARNES','LIVE OAK','LAVACA','WILSON');
            $wtxInterestCountiesArray = array('ANDREWS', 'DAWSON', 'GAINES', 'BORDEN', 'CRANE', 'ECTOR', 'STERLING', 'MITCHELL', 'JEFF DAVIS');
            $nmInterestCountiesArray = array('LEA', 'EDDY');
            $etxInterestCountiesArray = array('CASS', 'GREGG', 'HARRISON', 'MARION', 'MORRIS', 'NACOGDOCHES', 'PANOLA', 'SAN%20AUGUSTINE', 'RUSK', 'SHELBY', 'UPSHUR');
            $laInterestCountiesArray = array('BIENVILLE', 'BOSSIER', 'CADDO', 'DE%20SOTO', 'NATCHITOCHES', 'RED%20RIVER', 'SABINE', 'WEBSTER');

            $this->determineProduction($eagleInterestCountiesArray);

            $this->determineProduction($wtxInterestCountiesArray);

            $this->determineProduction($nmInterestCountiesArray);

            $this->determineProduction($etxInterestCountiesArray);

            $this->determineProduction($laInterestCountiesArray);

            return 'success';
        } catch ( \Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine() . ' File: ' . $e->getFile();

            $errorMsg->save();
            return 'error';
        }
    }

    public function determineProduction($countyArray) {


        foreach ($countyArray as $county) {

            $permits = Permit::select('id', 'lease_name', 'selected_lease_name', 'permit_id')->where('county_parish', $county)->get();

            foreach ($permits as $permit) {
                $wells = WellRollUp::select('leaseName')->where('leaseName', $permit->lease_name)->where('WellStatus', 'ACTIVE')->get();

                if ($wells->isEmpty()) {
                    if ($permit->selected_lease_name != '' && $permit->selected_lease_name != null) {
                        $selectedLeaseNameWells = WellRollUp::select('leaseName')->where('leaseName', $permit->selected_lease_name)->where('WellStatus', 'ACTIVE')->get();

                        if ($selectedLeaseNameWells->isEmpty()) {
                            Permit::where('permit_id', $permit->permit_id)->where('id', $permit->id)
                                ->update(['is_producing' => 0]);
                        } else {
                            Permit::where('permit_id', $permit->permit_id)->where('id', $permit->id)
                                ->update(['is_producing' => 1]);
                        }
                    } else {
                        Permit::where('permit_id', $permit->permit_id)->where('id', $permit->id)
                            ->update(['is_producing' => 0]);
                    }
                } else {
                    Permit::where('permit_id', $permit->permit_id)->where('id', $permit->id)
                        ->update(['is_producing' => 1]);
                }
            }
        }
        return 'success';
    }
}
