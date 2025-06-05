<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PunchLog;
use Illuminate\Http\Request;
use App\Models\WhatsappStatus;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class PunchLogController extends Controller
{
    /**
     * Display a listing of the punch logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = PunchLog::with('user');

        // Filter by user_code if provided
        if ($request->has('user_code') && !empty($request->user_code)) {
            $query->where('user_code', $request->user_code);
        }

        // Filter by date range if provided
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('punch_date_time', '>=', $startDate);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('punch_date_time', '<=', $endDate);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== null) {
            $query->where('punch_status', $request->status);
        }

        // Get all users for the filter dropdown
        $users = User::select('id', 'user_code', 'first_name', 'last_name')->get();

        // Order by most recent first
        $punchLogs = $query->orderBy('punch_date_time', 'desc')->paginate(20);

        return view('punch-logs.index', compact('punchLogs', 'users'));
    }

    /**
     * Store a newly created punch log in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_code' => 'required|exists:users,user_code',
            'punch_status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create new punch log
        $punchLog = PunchLog::create([
            'user_code' => $request->user_code,
            'punch_status' => $request->punch_status,
            'punch_date_time' => Carbon::now(),
            'send_message' => false,
            'is_process' => false,
        ]);

        // Get the user
        $user = User::where('user_code', $punchLog->user_code)->first();

        if ($user) {
            // Get WhatsApp status
            $whatsappStatus = WhatsappStatus::first();

            if (!$whatsappStatus) {
                $whatsappStatus = WhatsappStatus::create([
                    'total_message_count' => 0,
                    'success_message_count' => 0,
                ]);
            }

            // Process this log immediately
            $whatsAppService = new WhatsAppService();

            // Prepare message based on punch status
            $status = $punchLog->punch_status ? 'entered' : 'exited';
            $message = "Dear {$user->guardian_name}, your ward {$user->first_name} {$user->last_name} has {$status} the hostel at " .
                $punchLog->punch_date_time->format('d-m-Y h:i A') . ".";

            // Send WhatsApp message
            $messageSent = $whatsAppService->sendMessage($user->guardian_contact_no, $message, $user->first_name, $user->last_name);

            // Update WhatsApp status counts
            $whatsappStatus->total_message_count += 1;

            if ($messageSent) {
                // Message sent successfully
                $whatsappStatus->success_message_count += 1;
                $punchLog->send_message = true;
            }

            $whatsappStatus->save();

            // Mark log as processed
            $punchLog->is_process = true;
            $punchLog->save();
        }

        // Process any other unprocessed logs
        $this->processUnprocessedLogs();

        return response()->json([
            'message' => 'Punch log created successfully',
            'punch_log' => $punchLog
        ], 201);
    }

    /**
     * Process WhatsApp message for the punch log.
     *
     * @param  \App\Models\PunchLog  $punchLog
     * @return void
     */
    private function processWhatsAppMessage(PunchLog $punchLog)
    {
        // Get the user
        $user = User::where('user_code', $punchLog->user_code)->first();

        if (!$user) {
            return;
        }

        // Get WhatsApp status
        $whatsappStatus = WhatsappStatus::first();

        if (!$whatsappStatus) {
            $whatsappStatus = WhatsappStatus::create([
                'total_message_count' => 0,
                'success_message_count' => 0,
            ]);
        }

        // Create WhatsApp service instance
        $whatsAppService = new WhatsAppService();

        // Prepare message based on punch status
        $status = $punchLog->punch_status ? 'entered' : 'exited';
        $message = "Dear {$user->guardian_name}, your ward {$user->first_name} {$user->last_name} has {$status} the hostel at " .
            $punchLog->punch_date_time->format('d-m-Y h:i A') . ".";

        // Send WhatsApp message
        $messageSent = $whatsAppService->sendMessage($user->guardian_contact_no, $message, $user->first_name, $user->last_name);

        // Update WhatsApp status counts
        $whatsappStatus->total_message_count += 1;

        if ($messageSent) {
            // Message sent successfully
            $whatsappStatus->success_message_count += 1;
            $punchLog->send_message = true;
        }

        $whatsappStatus->save();

        // Mark log as processed
        $punchLog->is_process = true;
        $punchLog->save();
    }

    /**
     * Get the latest punch logs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestLogs()
    {
        // Get today's date
        $today = Carbon::today();

        // Get the latest punch log for each user today
        $latestPunchLogs = DB::table('punch_logs as pl1')
            ->select('pl1.user_code', 'pl1.punch_status')
            ->join(DB::raw('(
                SELECT user_code, MAX(punch_date_time) as max_time
                FROM punch_logs
                GROUP BY user_code
            ) as pl2'), function ($join) {
                $join->on('pl1.user_code', '=', 'pl2.user_code')
                    ->on('pl1.punch_date_time', '=', 'pl2.max_time');
            })
            ->get();

        // Get total user count (excluding admin user with ID 1)
        $totalUserCount = User::where('id', '!=', 1)->count();

        // Count users outside based on their latest status
        $outsideCount = $latestPunchLogs->where('punch_status', 0)->count();

        // Count users inside (total users minus those who are outside)
        $insideCount = $totalUserCount - $outsideCount;

        // Get last 10 IN users with eager loading of user relationship
        $lastInUsers = PunchLog::with(['user' => function ($query) {
            $query->select('id', 'user_code', 'first_name', 'last_name');
        }])
            ->where('punch_status', true)
            ->orderBy('punch_date_time', 'desc')
            ->limit(10)
            ->get();

        // Get last 10 OUT users with eager loading of user relationship
        $lastOutUsers = PunchLog::with(['user' => function ($query) {
            $query->select('id', 'user_code', 'first_name', 'last_name');
        }])
            ->where('punch_status', false)
            ->orderBy('punch_date_time', 'desc')
            ->limit(10)
            ->get();

        // Get WhatsApp pending count
        $whatsappStatus = WhatsappStatus::first();
        $successWhatsappCount = $whatsappStatus ? $whatsappStatus->success_message_count : 0;
        $totalWhatsappCount = $whatsappStatus ? $whatsappStatus->total_message_count : 0;
        $pendingWhatsappCount = $totalWhatsappCount - $successWhatsappCount;

        return response()->json([
            'inside_count' => $insideCount,
            'outside_count' => $outsideCount,
            'last_in_users' => $lastInUsers,
            'last_out_users' => $lastOutUsers,
            'pending_whatsapp_count' => $pendingWhatsappCount,
            'success_whatsapp_count' => $successWhatsappCount,
            'total_whatsapp_count' => $totalWhatsappCount,
        ]);
    }

    /**
     * Process unprocessed punch logs and send WhatsApp messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function processUnprocessedLogs()
    {
        // Get unprocessed punch logs
        $unprocessedLogs = PunchLog::where('is_process', false)
            ->orderBy('punch_date_time', 'asc')
            ->get();

        $processed = 0;
        $failed = 0;
        $whatsAppService = new WhatsAppService();

        foreach ($unprocessedLogs as $log) {
            // Get user information
            $user = User::where('user_code', $log->user_code)->first();

            if (!$user) {
                // Mark as processed even if user not found to avoid repeated processing
                $log->is_process = true;
                $log->save();
                $failed++;
                continue;
            }

            // Prepare message based on punch status
            $status = $log->punch_status ? 'entered' : 'exited';
            $message = "Dear {$user->guardian_name}, your ward {$user->first_name} {$user->last_name} has {$status} the hostel at " .
                $log->punch_date_time->format('d-m-Y h:i A') . ".";

            // Get WhatsApp status
            $whatsappStatus = WhatsappStatus::first();

            if (!$whatsappStatus) {
                $whatsappStatus = WhatsappStatus::create([
                    'total_message_count' => 0,
                    'success_message_count' => 0,
                ]);
            }

            // Send WhatsApp message
            $messageSent = $whatsAppService->sendMessage($user->guardian_contact_no, $message, $user->first_name, $user->last_name);

            // Update WhatsApp status counts
            $whatsappStatus->total_message_count += 1;

            if ($messageSent) {
                // Message sent successfully
                $whatsappStatus->success_message_count += 1;
                $log->send_message = true;
                $processed++;
            } else {
                // Message sending failed
                $failed++;
            }

            $whatsappStatus->save();

            // Mark log as processed regardless of message status
            $log->is_process = true;
            $log->save();
        }

        return response()->json([
            'message' => 'Punch logs processed',
            'processed' => $processed,
            'failed' => $failed,
            'total' => $unprocessedLogs->count(),
        ]);
    }

    /**
     * Run the punch-logs:process command via API.
     *
     * @param  string|null  $secretToken
     * @return \Illuminate\Http\JsonResponse
     */
    public function runProcessCommand($secretToken = null)
    {
        // Validate the secret token for public API access
        if ($secretToken !== null && $secretToken !== config('app.cron_secret_token')) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 401);
        }

        // Run the command
        $exitCode = Artisan::call('punch-logs:process');

        // Get the output
        $output = Artisan::output();

        return response()->json([
            'message' => 'Command executed',
            'exit_code' => $exitCode,
            'output' => $output
        ]);
    }
}
