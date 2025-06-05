<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\User;
use App\Models\PunchLog;
use Illuminate\Http\Request;
use App\Exports\PunchLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Show the report page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Generate daily report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function dailyReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $punchLogs = PunchLog::with('user')
            ->whereDate('punch_date_time', $selectedDate)
            ->orderBy('punch_date_time', 'desc')
            ->get();

        return view('reports.daily', compact('punchLogs', 'date'));
    }

    /**
     * Generate monthly report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->input('month', Carbon::today()->format('Y-m'));
        $userCode = $request->input('user_code');
        $roomNumber = $request->input('room_number');

        $query = PunchLog::with('user')
            ->whereYear('punch_date_time', Carbon::parse($month)->year)
            ->whereMonth('punch_date_time', Carbon::parse($month)->month);

        if ($userCode) {
            $query->where('user_code', $userCode);
        }

        if ($roomNumber) {
            $query->whereHas('user', function ($q) use ($roomNumber) {
                $q->where('room_number', $roomNumber);
            });
        }

        $punchLogs = $query->orderBy('punch_date_time', 'desc')->get();

        $users = User::orderBy('user_code')->get();
        $roomNumbers = User::select('room_number')->distinct()->orderBy('room_number')->pluck('room_number');

        return view('reports.monthly', compact('punchLogs', 'month', 'userCode', 'roomNumber', 'users', 'roomNumbers'));
    }

    /**
     * Download daily report in specified format.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    public function downloadDailyReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $format = $request->input('format', 'csv');
        $selectedDate = Carbon::parse($date);

        $punchLogs = PunchLog::with('user')
            ->whereDate('punch_date_time', $selectedDate)
            ->orderBy('punch_date_time', 'desc')
            ->get();

        $filename = 'daily_report_' . $selectedDate->format('Y-m-d');

        return $this->generateResponse($punchLogs, $filename, $format, 'daily', [
            'date' => $selectedDate->format('d-m-Y')
        ]);
    }

    /**
     * Download monthly report in specified format.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    public function downloadMonthlyReport(Request $request)
    {
        $month = $request->input('month', Carbon::today()->format('Y-m'));
        $userCode = $request->input('user_code');
        $roomNumber = $request->input('room_number');
        $format = $request->input('format', 'csv');

        $query = PunchLog::with('user')
            ->whereYear('punch_date_time', Carbon::parse($month)->year)
            ->whereMonth('punch_date_time', Carbon::parse($month)->month);

        if ($userCode) {
            $query->where('user_code', $userCode);
        }

        if ($roomNumber) {
            $query->whereHas('user', function ($q) use ($roomNumber) {
                $q->where('room_number', $roomNumber);
            });
        }

        $punchLogs = $query->orderBy('punch_date_time', 'desc')->get();

        $filename = 'monthly_report_' . Carbon::parse($month)->format('Y-m');
        if ($userCode) {
            $filename = 'monthly_report_' . Carbon::parse($month)->format('Y-m') . '_user_' . $userCode;
        } elseif ($roomNumber) {
            $filename = 'monthly_report_' . Carbon::parse($month)->format('Y-m') . '_room_' . $roomNumber;
        }

        return $this->generateResponse($punchLogs, $filename, $format, 'monthly', [
            'month' => Carbon::parse($month)->format('F Y'),
            'userCode' => $userCode,
            'roomNumber' => $roomNumber
        ]);
    }

    /**
     * Generate response based on format.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $punchLogs
     * @param  string  $filename
     * @param  string  $format
     * @param  string  $reportType
     * @param  array   $extraData
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    private function generateResponse($punchLogs, $filename, $format, $reportType, $extraData = [])
    {
        switch ($format) {
            case 'csv':
                return $this->generateCsvResponse($punchLogs, $filename . '.csv');
            case 'xlsx':
                return Excel::download(new PunchLogsExport($punchLogs), $filename . '.xlsx');
            case 'pdf':
                return $this->generatePdfResponse($punchLogs, $filename . '.pdf', $reportType, $extraData);
            default:
                return $this->generateCsvResponse($punchLogs, $filename . '.csv');
        }
    }

    /**
     * Generate CSV response from punch logs.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $punchLogs
     * @param  string  $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function generateCsvResponse($punchLogs, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($punchLogs) {
            $file = fopen('php://output', 'w');

            // Add CSV header
            fputcsv($file, [
                'User Code',
                'User Name',
                'Room Number',
                'Punch Status',
                'Punch Date & Time',
                'WhatsApp Sent'
            ]);

            // Add data rows
            foreach ($punchLogs as $log) {
                fputcsv($file, [
                    $log->user_code,
                    $log->user->name ?? 'N/A',
                    $log->user->room_number ?? 'N/A',
                    $log->punch_status ? 'IN' : 'OUT',
                    $log->punch_date_time->format('d-m-Y h:i A'),
                    $log->send_message ? 'Yes' : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate PDF response from punch logs.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $punchLogs
     * @param  string  $filename
     * @param  string  $reportType
     * @param  array   $extraData
     * @return \Illuminate\Http\Response
     */
    private function generatePdfResponse($punchLogs, $filename, $reportType, $extraData = [])
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);

        $html = view('reports.pdf.' . $reportType, [
            'punchLogs' => $punchLogs,
            'extraData' => $extraData
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
