<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MeasurementTemplate;

class MeasurementTemplateController extends Controller
{
    public function index()
    {
        return MeasurementTemplate::with(['category', 'itemType'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'item_type_id' => 'nullable|exists:item_types,id',
            'labels' => 'present|array',
        ]);

        return MeasurementTemplate::updateOrCreate(
            ['item_type_id' => $request->item_type_id],
            [
                'category_id' => $request->category_id,
                'labels' => $request->labels
            ]
        );
    }

    public function show(Request $request, $id)
    {
        // Try item_type_id first, fallback strictly to id
        $template = MeasurementTemplate::where('item_type_id', $id)
            ->first();
            
        if (!$template && $request->has('by_id')) {
            $template = MeasurementTemplate::find($id);
        }

        if (!$template) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        return $template;
    }

    public function export($id)
    {
        $template = MeasurementTemplate::findOrFail($id);
        $labels = $template->labels ?? [];
        
        $callback = function() use ($labels) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Measurement Name', 'Default Value', 'Unit']);
            foreach ($labels as $lbl) {
                fputcsv($file, [
                    $lbl['name'] ?? '',
                    $lbl['defaultValue'] ?? '0',
                    $lbl['unit'] ?? 'cm'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_{$id}.csv",
        ]);
    }

    public function import(Request $request, $id)
    {
        $request->validate(['file' => 'required|file']);
        $template = MeasurementTemplate::findOrFail($id);
        
        $file = $request->file('file');
        $data = [];
        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle)) !== FALSE) {
                $data[] = [
                    'name' => $row[0] ?? '',
                    'defaultValue' => $row[1] ?? '0',
                    'unit' => $row[2] ?? 'cm'
                ];
            }
            fclose($handle);
        }
        
        $template->update(['labels' => $data]);
        return $template;
    }

}
