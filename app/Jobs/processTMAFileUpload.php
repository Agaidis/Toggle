<?php

namespace App\Jobs;

use App\ErrorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class processTMAFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    /**
     * Create a new job instance.
     *
     * @var $filename
     * @return void
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $handle = fopen(base_path('/storage/app/public/'.$this->fileName), "r");
            $lineNumber = 0;

            while (($raw_string = fgets($handle)) !== false) {

                // Parse the raw csv string: "1, a, b, c"
                $row = str_getcsv($raw_string);

                ProcessTMA::dispatch($row);

                // Increase the current line
                $lineNumber++;
            }

            fclose($handle);
        } catch ( \Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();
        }
    }
}
