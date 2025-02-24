<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QueueController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
        ]);
        
        $today = date('Y-m-d');
        
        // ✅ Check if employee has already registered today
        $existingQueue = Queue::where('employee_id', $validatedData['employee_id'])
            ->where('queue_date', $today)
            ->first();
        
        if ($existingQueue) {
            return response()->json([
                'already_registered' => true,
                'queue_number' => $existingQueue->queue_number
            ], 200);
        }
        
        // ✅ Get last queue number for today
        $lastQueue = Queue::where('queue_date', $today)->max('queue_number');
        $newQueueNumber = ($lastQueue) ? $lastQueue + 1 : 1;
        
        // ✅ Register new queue entry
        $queue = Queue::create([
            'employee_id' => $validatedData['employee_id'],
            'queue_number' => $newQueueNumber,
            'queue_date' => $today,
            'status' => 'waiting',
        ]);
        
        return response()->json([
            'queue_number' => $queue->queue_number
        ], 201);        
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

    public function getStats()
    {
        $today = Carbon::today()->toDateString(); // Get today's date
    
        $stats = DB::table('queues')
            ->whereDate('queue_date', $today) // Filter only today's patients
            ->selectRaw("
                COUNT(CASE WHEN status = 'waiting' THEN 1 END) as waiting,
                COUNT(CASE WHEN status = 'in_consultation' THEN 1 END) as in_consultation,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                COUNT(*) as new_patients,
                CONCAT(FLOOR(IFNULL(AVG(TIMESTAMPDIFF(MINUTE, time_registered, time_called)), 0)), ' min') as average_waiting_time
            ")
            ->first();
    
        return response()->json($stats);
    }

    public function getWeeklyStats()
    {
        $startDate = Carbon::today()->startOfWeek(); // Monday of current week
        $endDate = Carbon::today()->endOfWeek(); // Sunday of current week
    
        $weeklyData = DB::table('queues')
            ->whereBetween('queue_date', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE(queue_date)")) // ✅ Ensure grouping by date only
            ->orderBy("queue_date")
            ->selectRaw("DATE(queue_date) as queue_date, COUNT(*) as count") // ✅ Extract only date part
            ->get();
    
        // Ensure all 7 days (Mon-Sun) are included
        $daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
        $formattedData = [];
    
        foreach ($daysOfWeek as $index => $day) {
            $date = Carbon::today()->startOfWeek()->addDays($index)->toDateString();
            $formattedData[$date] = ["day" => $day, "count" => 0]; // Default count 0
        }
    
        // Populate actual counts from the database query
        foreach ($weeklyData as $data) {
            if (isset($formattedData[$data->queue_date])) {
                $formattedData[$data->queue_date]["count"] = $data->count;
            }
        }
    
        // Convert to JSON array format
        return response()->json(array_values($formattedData));
    }

    public function getCurrentServing()
    {
            $today = date('Y-m-d');
        
            // ✅ Find the queue number of the patient currently in consultation
            $currentServing = Queue::where('queue_date', $today)
                ->where('status', 'in_consultation')
                ->orderBy('queue_number', 'asc') // Ensure the lowest queue number is shown
                ->first();

                if (!$currentServing) {
                return response()->json([
                    'message' => 'No patient is currently in consultation',
                    'queue_number' => null
                ], 200);
            }
        
            return response()->json([
                'queue_number' => $currentServing->queue_number,
                'employee_id' => $currentServing->employee_id,
                'status' => $currentServing->status
            ], 200);
        }
}
