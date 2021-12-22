<?php

namespace App\Imports;

use App\Models\CSVFileData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class CSVImport implements ToCollection, WithBatchInserts
{

    /**
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {
        $insertableData = [];
        foreach ($collection as $row) {
            $insertableData [] = [
                'id' => $row[0],
                'name' => $row[1],
                'date' => $row[2],
            ];
        }
        CSVFileData::insert($insertableData);
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }
}
