<?php

namespace App\Console\Commands;

use App\ErrorLog;
use Exception;
use Illuminate\Console\Command;
use App\Permit;
use App\LegalLease;
use Illuminate\Support\Facades\Log;

class StitchPermitsAndLeases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:stitch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will find the lease that the permit is drilled into';

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
            $permits = Permit::where('interest_area', 'apr')->get();
            $legalLeases = LegalLease::whereIn('CountyParish', ['EDDY (NM)', 'LEA (NM)'])->get();
            $items = array();
            $count = 0;

            foreach ($legalLeases as $legalLease) {
                if ($legalLease->LatitudeWGS84 != null && $legalLease->LongitudeWGS84 != null) {
                    array_push($items, array($count++, $legalLease->LeaseId, $legalLease->LatitudeWGS84, $legalLease->LongitudeWGS84));
                }
            }

            foreach ($permits as $permit) {
                if ($permit->stitch_lease_id == '' || $permit->stitch_lease_id == null) {
                    $permitGeo = str_replace(['"lng": ', ' "lat": ', '{', '}'], ['', '', '', ''], $permit->btm_geometry);
                    $permitLocationArray = explode(',', $permitGeo);

                    $ref = array($permitLocationArray[1], $permitLocationArray[0]);

                    $distances = array_map(function ($item) use ($ref) {
                        $a = array_slice($item, -2);
                        return $this->distance($a, $ref);
                    }, $items);

                    asort($distances);

                    $leaseId = $items[key($distances)][1];

                    $updatePermit = Permit::find($permit->id);
                    $updatePermit->stitch_lease_id = $leaseId;
                    $updatePermit->stitch_distance = $distances[0];

                    $updatePermit->save();
                }
            }
        } catch( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }

    function distance($a, $b)
    {
        list($lat1, $lon1) = $a;
        list($lat2, $lon2) = $b;

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return number_format($miles, 2);
    }
}
