<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
        ]);

        $today = date('Y-m-d');
        $lastQueue = Queue::where('queue_date', $today)->max('queue_number');
        $newQueueNumber = ($lastQueue) ? $lastQueue + 1 : 1;

        $queue = Queue::create([
            'employee_id' => $validatedData['employee_id'],
            'queue_number' => $newQueueNumber,
            'queue_date' => $today,
            'status' => 'waiting',
        ]);

        return response()->json(['queue_number' => $queue->queue_number], 201);
    }

    public function index()
    {
        return response()->json(Queue::where('queue_date', date('Y-m-d'))->get(), 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $queue = Queue::find($id);
        if (!$queue) return response()->json(['error' => 'Queue entry not found'], 404);

        $queue->update(['status' => $request->status, 'time_called' => now()]);
        return response()->json($queue, 200);
    }
}
