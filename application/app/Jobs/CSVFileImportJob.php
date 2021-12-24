<?php

namespace App\Jobs;

use App\Models\Rows;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class CSVFileImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $importData;
    private string $uniqueKey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $importData, string $uniqueKey)
    {
        $this->importData = $importData;
        $this->uniqueKey = $uniqueKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $rowCount = count($this->importData);
            Rows::insert($this->importData);
            $this->saveStatus($this->uniqueKey,$rowCount);
        }catch (\Exception $exception){
            Session::flash("Something when wrong");
        }
    }

    /**
     * @param int $count
     * @param string $uniqueKey
     * @return void
     */
    private function saveStatus(string $uniqueKey, int $count): void
    {
        $redis = Redis::connection();
        $oldValue = intval($redis->get($uniqueKey));
        $redis->set($uniqueKey,$oldValue + $count);
    }

}
