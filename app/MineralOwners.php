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
