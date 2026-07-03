<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataSheetController extends Controller
{

    public function index()
    {
        return response()->json(DataSheet::all());
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'isDraft' => 'boolean|required',
            ]);

            // Purana draft delete if new draft is coming
            if ($request->boolean('isDraft')) {
                DataSheet::where('isDraft', true)->delete();
            }

            $dataSheet = DataSheet::create($request->all());

            return response()->json([
                'status' => true,
                'message' => $request->boolean('isDraft') ? 'Draft Saved' : 'Record Saved',
                'data' => $dataSheet
            ], 201);

        } catch (\Exception $e) {
            Log::error('DataSheet Store Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
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

    public function show($id)
    {
        $sheet = DataSheet::find($id);

        if (!$sheet) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ], 404);
        }

        return response()->json($sheet);
    }

    public function destroy($id)
    {
        $sheet = DataSheet::find($id);

        if (!$sheet) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ], 404);
        }

        $sheet->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record deleted successfully.'
        ]);
    }

    public function exportCSV()
    {
        $fileName = 'data-sheets.csv';
        $data = DataSheet::all();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Ab photos exclude nahi kar rahe
        $excludeColumns = ['id', 'created_at', 'updated_at'];

        $sample = new DataSheet();
        $allColumns = array_diff(
            \Schema::getColumnListing($sample->getTable()),
            $excludeColumns
        );

        $callback = function () use ($data, $allColumns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $allColumns);

            foreach ($data as $row) {
                $rowData = [];
                foreach ($allColumns as $col) {
                    $value = $row->{$col};

                    // photos array/json hai to string mein convert karo
                    if ($col === 'photos' && is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($col === 'photos' && !is_string($value)) {
                        $value = json_encode($value);
                    }

                    $rowData[] = $value;
                }
                fputcsv($file, $rowData);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file'), 'r');
        $header = fgetcsv($file);

        while ($row = fgetcsv($file)) {
            $rowAssoc = array_combine($header, $row);

            $rowAssoc = array_map(function ($val) {
                return $val === '' ? null : $val;
            }, $rowAssoc);

            // photos ko JSON string se wapas array mein decode karo
            if (isset($rowAssoc['photos']) && $rowAssoc['photos']) {
                $decoded = json_decode($rowAssoc['photos'], true);
                $rowAssoc['photos'] = $decoded ?? [];
            }

            if (!isset($rowAssoc['isDraft'])) {
                $rowAssoc['isDraft'] = false;
            }

            DataSheet::create($rowAssoc);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'CSV Imported Successfully'
        ]);
    }

    public function destroyAll()
    {
        try {
            DataSheet::truncate();

            return response()->json([
                'status' => true,
                'message' => 'All records deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('DataSheet Delete All Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}