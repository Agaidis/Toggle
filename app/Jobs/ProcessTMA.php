<?php

namespace App\Jobs;

use App\ErrorLog;
use App\MineralOwner;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTMA implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $row;

    /**
     * Create a new job instance.
     *
     * @var $row
     * @return void
     */
    public function __construct($row)
    {
        $this->row = $row;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = serialize($this->row);
            $errorMsg->save();

            $owner = $this->row[0];
            $ownerAddress = $this->row[1];
            $ownerCity = $this->row[2];
            $ownerState = $this->row[3];
            $ownerZip = $this->row[4];
            $ownerDecimalInterest = $this->row[5];
            $ownerInterestType = $this->row[6];
            $appraisalYear = $this->row[7];
            $operatorCompanyName = $this->row[8];
            $leaseName = $this->row[11];
            $rrcLeaseNumber = $this->row[12];
            $county = $this->row[13];
            $stateProvince = $this->row[14];
            $taxValue = $this->row[18];
            $leaseDescription = $this->row[21];
            $firstProdDate = $this->row[23];
            $lastProdDate = $this->row[24];
            $cumProdOil = $this->row[25];
            $cumProdGas = $this->row[26];
            $activeWellCount = $this->row[20];

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
    }
}
