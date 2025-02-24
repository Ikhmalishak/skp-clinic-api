<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Employee;

class ExcelImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row

            Employee::create([
                'employee_id'    => $row[0],
                'name'           => $row[1],
                'race'           => $row[2],
                'nric_number'    => $row[3],
                'passport_number'=> $row[4],
                'nationatility'  => $row[5],
                'base'           => $row[6],
                'department'     => $row[7],
                'company'        => $row[8],
            ]);
        }

        return response()->json(['message' => 'Employees imported successfully']);
    }
}
