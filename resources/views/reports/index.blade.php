@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Reports</h1>

        <div class="row g-3">
            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Daily Report</h5>
                    </div>
                    <div class="card-body">
                        <p>View detailed punch logs for a specific date.</p>
                        <form action="{{ route('reports.daily') }}" method="GET">
                            <div class="mb-3">
                                <label for="date" class="form-label">Select Date</label>
                                <input type="date" class="form-control" id="date" name="date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Monthly Report</h5>
                    </div>
                    <div class="card-body">
                        <p>View monthly punch logs with additional filters.</p>
                        <form action="{{ route('reports.monthly') }}" method="GET">
                            <div class="mb-3">
                                <label for="month" class="form-label">Select Month</label>
                                <input type="month" class="form-control" id="month" name="month"
                                    value="{{ date('Y-m') }}" required>
                            </div>
                            <div class="row g-2">
                                <div class="col-sm-6 mb-3">
                                    <label for="user_code" class="form-label">User Code (Optional)</label>
                                    <input type="text" class="form-control" id="user_code" name="user_code">
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label for="room_number" class="form-label">Room Number (Optional)</label>
                                    <input type="text" class="form-control" id="room_number" name="room_number">
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        @media (max-width: 575.98px) {
            .card {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .form-control {
                font-size: 0.95rem;
            }

            .btn {
                padding: 0.375rem 0.75rem;
            }
        }
    </style>
@endsection
