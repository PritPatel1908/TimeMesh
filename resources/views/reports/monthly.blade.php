@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Monthly Report</h1>
            <div>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Report for {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h5>
                    <div class="d-flex align-items-center">
                        <form action="{{ route('reports.monthly') }}" method="GET" class="d-flex flex-wrap me-2">
                            <div class="d-flex me-2 mb-2 mb-md-0">
                                <input type="month" class="form-control me-md-2" id="month" name="month"
                                    value="{{ $month }}" required>
                            </div>
                            <div class="d-flex me-2 mb-2 mb-md-0">
                                <select class="form-select me-md-2" id="user_code" name="user_code">
                                    <option value="">All Users</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->user_code }}"
                                            {{ $userCode == $user->user_code ? 'selected' : '' }}>
                                            {{ $user->user_code }} - {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex me-2 mb-2 mb-md-0">
                                <select class="form-select me-md-2" id="room_number" name="room_number">
                                    <option value="">All Rooms</option>
                                    @foreach ($roomNumbers as $room)
                                        <option value="{{ $room }}" {{ $roomNumber == $room ? 'selected' : '' }}>
                                            {{ $room }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-light">Filter</button>
                        </form>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" id="downloadDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-1"></i> Download
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                                <li><a class="dropdown-item"
                                        href="{{ route('reports.monthly.download', ['month' => $month, 'user_code' => $userCode, 'room_number' => $roomNumber, 'format' => 'csv']) }}">CSV</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ route('reports.monthly.download', ['month' => $month, 'user_code' => $userCode, 'room_number' => $roomNumber, 'format' => 'xlsx']) }}">Excel
                                        (XLSX)</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('reports.monthly.download', ['month' => $month, 'user_code' => $userCode, 'room_number' => $roomNumber, 'format' => 'pdf']) }}">PDF</a>
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
                                <th>Punch Date & Time</th>
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
                                    <td>{{ $log->punch_date_time->format('d-m-Y h:i A') }}</td>
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
                                    <td colspan="6" class="text-center">No records found for the selected criteria.</td>
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
