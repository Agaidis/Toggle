<?php

namespace App;

use App\ErrorLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Exception;


class MineralOwners implements ToModel
{
    /**
     * @param array $row
     *
     *
     */
    public function model(array $row)
    {
        try {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = 'Im here';
            $errorMsg->save();

            $lists = $row[0];
            $signupDate = $row[1];
            $email = $row[2];

            $errorMsg = new ErrorLog();
            $errorMsg->payload = $row[1];
            $errorMsg->save();

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
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
