<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\ImportStatus;
use App\Services\CsvValidationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ImportStatus $importStatus)
    {
        $this->importStatus = $importStatus;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CsvValidationService $validationService)
    {
        try {

            DB::beginTransaction();

            $file = Storage::path($this->importStatus->file_path);
            $handle = fopen($file, "r");

            if (!$handle) {
                throw new Exception("Error Reading CSV", 400);
            }

            $this->importStatus->update(['status' => 'processing']);

            fgetcsv($handle);

            while (($data = fgetcsv($handle, 10000, ","))) {

                $validation = $validationService->validateUser($data);

                if (!$validation['success']) {
                    throw new Exception(json_encode($validation['message']), 422);
                }

                try {

                    User::create([
                        'name' => $validation['data']['name'],
                        'email' => $validation['data']['email'],
                        'birthdate' => $validation['data']['birthdate'],
                    ]);
                } catch (\Exception $e) {

                    throw $e;
                }
            }

            fclose($handle);

            $this->importStatus->update([
                'status' => 'completed',
                'description' => 'File successfully imported'
            ]);

            DB::commit();
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            $this->importStatus->update([
                'status' => 'failed',
                'description' => $e->getMessage()
            ]);
        }
    }
}
