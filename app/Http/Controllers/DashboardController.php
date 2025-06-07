<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PunchLog;
use Illuminate\Http\Request;
use App\Models\WhatsappStatus;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get WhatsApp status
        $whatsappStatus = WhatsappStatus::first();
        $successWhatsappCount = $whatsappStatus ? $whatsappStatus->success_message_count : 0;
        $totalWhatsappCount = $whatsappStatus ? $whatsappStatus->total_message_count : 0;
        $pendingWhatsappCount = $totalWhatsappCount - $successWhatsappCount;

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

        // Get total user count
        $totalUserCount = User::where('id', '!=', 1)->count();

        // Count users outside based on their latest status
        $outsideCount = $latestPunchLogs->where('punch_status', 1)->count();

        // Count users inside (total users minus those who are outside)
        $insideCount = $totalUserCount - $outsideCount;

        // Get last 10 IN users with eager loading of user relationship
        $lastInUsers = PunchLog::with(['user' => function ($query) {
            $query->select('id', 'user_code', 'first_name', 'last_name');
        }])
            ->where('punch_status', false)
            ->orderBy('punch_date_time', 'desc')
            ->limit(10)
            ->get();

        // Get last 10 OUT users with eager loading of user relationship
        $lastOutUsers = PunchLog::with(['user' => function ($query) {
            $query->select('id', 'user_code', 'first_name', 'last_name');
        }])
            ->where('punch_status', true)
            ->orderBy('punch_date_time', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'pendingWhatsappCount',
            'successWhatsappCount',
            'totalWhatsappCount',
            'insideCount',
            'outsideCount',
            'lastInUsers',
            'lastOutUsers'
        ));
    }
}
