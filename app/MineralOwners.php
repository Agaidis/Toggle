<?php

namespace App;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Exception;


class MineralOwners implements ToModel, WithChunkReading
{
    use RemembersChunkOffset;
    /**
     * @param array $row
     *
     *
     */
    public function model(array $row)
    {
        try {
            $owner = $row[0];
            $ownerAddress = '';
            $leaseName = $row[11];
            $taxValue = $row[18];
            $lastProdDate = $row[23];
            $cumProdOil = $row[24];
            $cumProdGas = $row[25];

            $isOwnerExist = MineralOwner::where('owner', $owner)->where('lease_name', $leaseName)->get();

            if ($isOwnerExist->isEmpty()) {
//                $newOwner = new MineralOwner();
//                $newOwner->owner = $owner;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//                $newOwner-> ;
//
//                $newOwner->save();

            } else {
                MineralOwner::where('id', $isOwnerExist[0]->id)->update([
                    'tax_value' => $taxValue,
                    'last_prod_date' => $lastProdDate,
                    'cum_prod_oil' => $cumProdOil,
                    'cum_prod_gas' => $cumProdGas,
                ]);
            }

        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();
        }
    }

    public function startRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return 2000;
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}
