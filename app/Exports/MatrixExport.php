<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MatrixExport implements FromArray, WithStyles, ShouldAutoSize, WithTitle
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function array(): array
    {
        $variables = [
            'E1','E2','E3','E4','E5',
            'K1','K2','K3','K4','K5',
            'S1','S2','S3','S4','S5',
            'L1','L2','L3','L4','L5',
            'I1','I2','I3','I4','I5'
        ];

        $aktorVariables = ['A1','A2','A3','A4','A5','A6','A7','A8','A9','A10','A11','A12','A13','A14'];

        $exportData = [];

        // 1. Add User Details at the top (Now 4 rows)
        $exportData[] = ['Full Name:', $this->response->name];
        $exportData[] = ['Job Title:', $this->response->job];
        $exportData[] = ['Company:', $this->response->company];
        $exportData[] = ['Industrial Park:', $this->response->industrial_park];
        $exportData[] = [];
        $exportData[] = ['Pengisian Kuisoner Faktor Kunci Peningkatan Daya Saing Kawasan Industri' ];

        // 2. Create the Matrix Header Row (Now on Row 6)
        $headerRow = array_merge(['Variabel'], $variables);
        $exportData[] = $headerRow;

        // 3. Loop through the matrix array
        foreach ($this->response->key_factor as $rowIndex => $rowData) {
            $row = [$variables[$rowIndex]]; 

            foreach ($rowData as $colIndex => $value) {
                if ($rowIndex === $colIndex) {
                    $row[] = '-'; // Diagonal
                } elseif ($rowIndex > $colIndex) {
                    $row[] = '';  // Lower triangle
                } else {
                   $row[] = $value !== null || $value === 0 ? (string) $value : ''; // Input value
                }
            }
            $exportData[] = $row;
        }

        $exportData[] = []; 
        $exportData[] = [];
        $exportData[] = ['Pengisian Kuisoner Aktor Kunci Peningkatan Daya Saing Kawasan Industri'];
        $exportData[] = []; 
        $exportData[] = array_merge(['Aktor'], $aktorVariables);
        foreach ($this->response->key_actor as $rowIndex => $rowData) {
            $row = [$aktorVariables[$rowIndex]]; 

            foreach ($rowData as $colIndex => $value) {
                if ($rowIndex === $colIndex) {
                    $row[] = '-'; // Diagonal
                } elseif ($rowIndex > $colIndex) {
                    $row[] = '';  // Lower triangle
                } else {
                   $row[] = $value !== null || $value === 0 ? (string) $value : ''; // Input value
                }
            }
            $exportData[] = $row;
        }

        return $exportData;
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Style the User Info section (Rows 1 to 4)
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);

        // 2. Matrix Headers (Now shifted down to Row 6)
        $sheet->getStyle('A6:Z6')->getFont()->setBold(true);
        $sheet->getStyle('A6:A31')->getFont()->setBold(true);

        $sheet->getStyle('A33:O33')->getFont()->setBold(true);
        $sheet->getStyle('A33:A47')->getFont()->setBold(true);

        // 3. Center-align the data columns (B through Z, Rows 6 to 31)
        $sheet->getStyle('B6:Z31')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 4. Apply Borders to the entire Matrix Grid (A6 to Z31)
        $sheet->getStyle('A6:Z31')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A33:O47')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // 5. Shade the disabled cells Gray
        for ($i = 0; $i < 25; $i++) {
            for ($j = 0; $j < 25; $j++) {
                
                if ($i === $j || $i > $j) {
                    // Data rows now start at row 7 in Excel
                    $excelRow = $i + 7; 
                    $excelCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($j + 2); 
                    
                    $cellAddress = $excelCol . $excelRow;

                    $sheet->getStyle($cellAddress)->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setARGB('FFD4D4D8'); 
                }
            }
        }

        for ($i = 0; $i < 14; $i++) {
            for ($j = 0; $j < 14; $j++) {
                
                if ($i === $j || $i > $j) {
                    // Data rows now start at row 7 in Excel
                    $excelRow = $i + 34; 
                    $excelCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($j + 2); 
                    
                    $cellAddress = $excelCol . $excelRow;

                    $sheet->getStyle($cellAddress)->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setARGB('FFD4D4D8'); 
                }
            }
        }
    }
    public function title(): string
    {
        // Excel prohibits these characters in sheet names
        $safeName = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $this->response->name);
        
        // Excel sheet names are strictly limited to 31 characters.
        // We truncate the name and append the unique DB ID to prevent crashes if two users have the same name.
        return substr($safeName, 0, 25) . '_' . $this->response->id;
    }
}