<?php

namespace App\Http\Controllers;

use App\Models\ImportStatus;

class ImportStatusController extends Controller
{
    public function getStatus($id)
    {
        return response()->json(['success' => true, 'message' => 'Import Status', 'data' => ImportStatus::find($id)], 200);
    }
}
