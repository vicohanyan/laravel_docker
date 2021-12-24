<?php

namespace App\Services;

use App\Jobs\CSVFileImportJob;
use Illuminate\Support\Carbon;

class CSVImportService
{

    /**
     * @param string $filePath
     * @return void
     */
    public function import(string $filePath): void
    {
        $rowCount = 0;
        $insertData = [];
        $uniqueKey = uniqid('csv_import_');
        $handle = fopen($filePath, 'r');
        if ($file = $handle) {
            while (!feof($file)) {
                $line = fgetcsv($file);
                if (empty($line)) continue;
                if (empty(intval($line[0]))) continue;

                $date = Carbon::createFromFormat('d.m.y', rtrim($line[2], ' '))->format('Y-m-d');
                $insertData[] = [
                    "id" => $line[0],
                    "name" => $line[1],
                    "date" => $date,
                ];
                $rowCount++;
                if ($rowCount == 1000) {
                    CSVFileImportJob::dispatch($insertData, $uniqueKey);
                    $insertData = [];
                    $rowCount = 0;
                }
            }
            CSVFileImportJob::dispatch($insertData, $uniqueKey);
            fclose($file);
            unlink($filePath);
        }
    }
}
