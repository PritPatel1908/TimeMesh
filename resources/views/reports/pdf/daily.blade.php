<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daily Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
            padding: 3px 6px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Hostel Management System</h1>
        <h2>Daily Report for {{ $extraData['date'] }}</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>User Code</th>
                <th>User Name</th>
                <th>Room Number</th>
                <th>Punch Status</th>
                <th>Punch Time</th>
                <th>WhatsApp Sent</th>
            </tr>
        </thead>
        <tbody>
            @forelse($punchLogs as $log)
                <tr>
                    <td>{{ $log->user_code }}</td>
                    <td>{{ $log->user->name ?? 'N/A' }}</td>
                    <td>{{ $log->user->room_number ?? 'N/A' }}</td>
                    <td>
                        @if ($log->punch_status)
                            <span class="badge-success">IN</span>
                        @else
                            <span class="badge-danger">OUT</span>
                        @endif
                    </td>
                    <td>{{ $log->punch_date_time->format('h:i A') }}</td>
                    <td>
                        @if ($log->send_message)
                            <span class="badge-success">Yes</span>
                        @else
                            <span class="badge-warning">No</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No records found for this date.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: right;">
        <p>Generated on: {{ now()->format('d-m-Y h:i A') }}</p>
    </div>
</body>

</html>
