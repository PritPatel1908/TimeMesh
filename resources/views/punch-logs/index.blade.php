@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Punch Logs</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('punch-logs.index') }}" method="GET" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="user_code" class="form-label">Student</label>
                                <select name="user_code" id="user_code" class="form-select">
                                    <option value="">All Students</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->user_code }}"
                                            {{ request('user_code') == $user->user_code ? 'selected' : '' }}>
                                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->user_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>In (Entry)
                                    </option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Out (Exit)
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('punch-logs.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Date & Time</th>
                                        <th>Student Code</th>
                                        <th>Student Name</th>
                                        <th>Status</th>
                                        <th>Message Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($punchLogs as $log)
                                        <tr>
                                            <td>{{ $log->id }}</td>
                                            <td>{{ $log->punch_date_time->format('d-m-Y h:i A') }}</td>
                                            <td>{{ $log->user_code }}</td>
                                            <td>
                                                @if ($log->user)
                                                    {{ $log->user->first_name }} {{ $log->user->last_name }}
                                                @else
                                                    <span class="text-danger">User not found</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->punch_status)
                                                    <span class="badge bg-success">In (Entry)</span>
                                                @else
                                                    <span class="badge bg-danger">Out (Exit)</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->is_process)
                                                    @if ($log->send_message)
                                                        <span class="badge bg-success">Sent</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Failed</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No punch logs found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $punchLogs->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
