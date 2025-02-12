<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(Employee::all(), 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|unique:employees',
            'name' => 'nullable|string',
            'race' => 'nullable|string',
            'nric_number' => 'required|string',
            'passport_number' => 'required|string',
            'nationatility' => 'nullable|string',
            'base' => 'nullable|string',
            'department' => 'nullable|string',
            'company' => 'nullable|string',
        ]);

        $employee = Employee::create($validatedData);
        return response()->json($employee, 201);
    }

    public function show($id)
    {
        $employee = Employee::find($id);
        if (!$employee) return response()->json(['error' => 'Employee not found'], 404);
        return response()->json($employee, 200);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        if (!$employee) return response()->json(['error' => 'Employee not found'], 404);

        $employee->update($request->all());
        return response()->json($employee, 200);
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) return response()->json(['error' => 'Employee not found'], 404);

        $employee->delete();
        return response()->json(['message' => 'Employee deleted'], 200);
    }
}

