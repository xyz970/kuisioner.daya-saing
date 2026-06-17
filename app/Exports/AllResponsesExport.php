<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\CrossImpactResponse;

class AllResponsesExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        
        // Fetch all submissions from the database
        $responses = CrossImpactResponse::all();

        foreach ($responses as $response) {
            // Create a new sheet for every response
            $sheets[] = new MatrixExport($response);
        }

        return $sheets;
    }
}
