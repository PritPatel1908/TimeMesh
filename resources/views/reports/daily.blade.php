@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Daily Report</h1>
            <div>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Report for {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</h5>
                    <div class="d-flex flex-wrap">
                        <form action="{{ route('reports.daily') }}" method="GET" class="d-flex me-2 mb-2 mb-md-0">
                            <input type="date" class="form-control me-md-2" id="date" name="date"
                                value="{{ $date }}" required>
                            <button type="submit" class="btn btn-light">Update</button>
                        </form>
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" id="downloadDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-1"></i> Download
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                                <li><a class="dropdown-item"
                                        href="{{ route('reports.daily.download', ['date' => $date, 'format' => 'csv']) }}">CSV</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ route('reports.daily.download', ['date' => $date, 'format' => 'xlsx']) }}">Excel
                                        (XLSX)</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('reports.daily.download', ['date' => $date, 'format' => 'pdf']) }}">PDF</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
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
                                            <span class="badge bg-success">IN</span>
                                        @else
                                            <span class="badge bg-danger">OUT</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->punch_date_time->format('h:i A') }}</td>
                                    <td>
                                        @if ($log->send_message)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-warning text-dark">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No records found for this date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Adjust table scrolling behavior on small screens
            const adjustTableResponsive = function() {
                const tableContainer = document.querySelector('.table-responsive');
                if (window.innerWidth < 768 && tableContainer) {
                    tableContainer.scrollLeft = 0;
                }
            };

            // Initial call
            adjustTableResponsive();

            // On resize
            window.addEventListener('resize', adjustTableResponsive);
        });
    </script>
@endsection
