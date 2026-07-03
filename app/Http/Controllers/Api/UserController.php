<?php

namespace App\Http\Controllers\Api;   // ← Yeh line important hai

use App\Http\Controllers\Controller;
use App\Models\DataSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataSheetController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'isDraft' => 'boolean|required',
            ]);

            if ($request->boolean('isDraft')) {
                DataSheet::where('isDraft', true)->delete();
            }

            $dataSheet = DataSheet::create($request->all());

            return response()->json([
                'status' => true,
                'message' => $request->boolean('isDraft') ? 'Draft saved' : 'Record saved',
                'data' => $dataSheet
            ]);

        } catch (\Exception $e) {
            Log::error('DataSheet Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDraft()
    {
        $draft = DataSheet::where('isDraft', true)->first();
        return response()->json([
            'status' => true,
            'data' => $draft
        ]);
    }
}