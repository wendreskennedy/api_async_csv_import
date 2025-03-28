<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvImport;
use App\Models\ImportStatus;
use Exception;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:csv,txt'
            ]);

            $path = $request->file('file')->store('uploads');

            $importStatus = ImportStatus::create([
                'file_path' => $path,
                'status' => 'pending',
            ]);

            ProcessCsvImport::dispatch($importStatus);

            return response()->json(['success' => true, 'message' => 'OK', 'import_id' => $importStatus->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getCode());
        }
    }
}
