<?php
namespace App\Http\Controllers;

use App\Exports\AllResponsesExport;
use App\Exports\MatrixExport;
use Illuminate\Http\Request;
use App\Models\CrossImpactResponse;
use Maatwebsite\Excel\Facades\Excel;

class MatrixController extends Controller
{
    public function index()
    {
        $latestSubmission = CrossImpactResponse::latest()->first();
        return view('matrix.index', compact('latestSubmission'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'job' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'industrial_park' => 'required|string|max:255',
            'key_factor' => 'required|json',
            'key_actor' => 'required|json',
        ]);

        CrossImpactResponse::create([
            'name' => $request->name,
            'job' => $request->job,
            'company' => $request->company,
            'industrial_park' => $request->industrial_park,
            'key_factor' => json_decode($request->key_factor, true),
            'key_actor' => json_decode($request->key_actor, true),
        ]);

        return redirect()->back()->with('success', 'Matrix data successfully saved to the database!');
    }

    public function export()
    {
        return Excel::download(new AllResponsesExport(), 'all_respondents_cross_impact.xlsx');
        // $response = CrossImpactResponse::findOrFail($id);

        // $fileName = "cross_impact_report_id_{$id}.xlsx";

        // // Pass the entire response object, not just the matrix
        // return Excel::download(new MatrixExport($response), $fileName);
    }
}