<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportCsvRequest;
use App\Http\Resources\ImportedDataAnalysisResource;
use App\Jobs\ProcessCsvImport;
use App\Models\ImportedData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function import(ImportCsvRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('imports');

        ProcessCsvImport::dispatch($path);

        return response()->json(['message' => 'Importação iniciada.']);
    }

    public function analysis()
    {
        $result = ImportedData::selectRaw('
        date,
        AVG(value) as average,
        MIN(value) as min,
        MAX(value) as max,
        SUM(CASE WHEN value > 10 THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as above_10,
        SUM(CASE WHEN value < -10 THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as below_minus_10,
        SUM(CASE WHEN value BETWEEN -10 AND 10 THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as between_minus10_and_10
        ')
        ->groupBy('date')
        ->get()
        ->map(function ($item) {
            $values = ImportedData::where('date', $item->date)->pluck('value')->sort()->values();
            $count = $values->count();
            $median = $count ? ($count % 2
                ? $values->get(intval($count / 2))
                : ($values->get($count / 2 - 1) + $values->get($count / 2)) / 2
            ) : 0;

            return [
                'date' => $item->date,
                'average' => (float) $item->average,
                'min' => (float) $item->min,
                'max' => (float) $item->max,
                'above_10_percent' => round((float) $item->above_10, 2),
                'below_minus10_percent' => round((float) $item->below_minus_10, 2),
                'between_minus10_and_10_percent' => round((float) $item->between_minus10_and_10, 2),
                'median' => $median
            ];
        });


        return response()->json($result);
    }
}
