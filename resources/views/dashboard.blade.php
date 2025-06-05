@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Dashboard</h1>

        <div class="mb-3">
            <span id="update-status" class="badge bg-primary">Live and Update</span>
            <small class="text-muted ms-2">Last updated: <span id="last-updated">Just now</span></small>
        </div>

        <div class="row g-2">
            <!-- Pending WhatsApp Count -->
            <div class="col-sm-6 col-md-6 col-lg-3 mb-2">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Pending WhatsApp</h5>
                                <h2 class="mb-0" id="pending-whatsapp-count">{{ $pendingWhatsappCount }}</h2>
                            </div>
                            <div>
                                <i class="fas fa-clock fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Successful WhatsApp Count -->
            <div class="col-sm-6 col-md-6 col-lg-3 mb-2">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Successful WhatsApp</h5>
                                <h2 class="mb-0" id="success-whatsapp-count">{{ $successWhatsappCount }}</h2>
                            </div>
                            <div>
                                <i class="fas fa-check-circle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inside Hostel Count -->
            <div class="col-sm-6 col-md-6 col-lg-3 mb-2">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Inside Hostel</h5>
                                <h2 class="mb-0" id="inside-count">{{ $insideCount }}</h2>
                            </div>
                            <div>
                                <i class="fas fa-home fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outside Hostel Count -->
            <div class="col-sm-6 col-md-6 col-lg-3 mb-2">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Outside Hostel</h5>
                                <h2 class="mb-0" id="outside-count">{{ $outsideCount }}</h2>
                            </div>
                            <div>
                                <i class="fas fa-sign-out-alt fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total WhatsApp Messages -->
        <div class="row mb-4 mt-2">
            <div class="col-12">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total WhatsApp Messages</h5>
                                <h2 class="mb-0" id="total-whatsapp-count">{{ $totalWhatsappCount }}</h2>
                            </div>
                            <div>
                                <i class="fas fa-comment-dots fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-2">
            <!-- Last 10 IN Users -->
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Last 10 IN Records</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>User Code</th>
                                        <th>Name</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody id="in-users-table">
                                    @foreach ($lastInUsers as $log)
                                        <tr>
                                            <td>{{ $log->user_code }}</td>
                                            <td>{{ $log->user->first_name ?? '' }} {{ $log->user->last_name ?? '' }}</td>
                                            <td>{{ $log->punch_date_time->format('d-m-Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Last 10 OUT Users -->
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Last 10 OUT Records</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>User Code</th>
                                        <th>Name</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody id="out-users-table">
                                    @foreach ($lastOutUsers as $log)
                                        <tr>
                                            <td>{{ $log->user_code }}</td>
                                            <td>{{ $log->user->first_name ?? '' }} {{ $log->user->last_name ?? '' }}</td>
                                            <td>{{ $log->punch_date_time->format('d-m-Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Log initial values from server
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initial inside count:', {{ $insideCount }});
            console.log('Initial outside count:', {{ $outsideCount }});
        });

        // Function to update dashboard data
        function updateDashboardData() {
            const updateStatus = document.getElementById('update-status');
            updateStatus.textContent = 'Updating...';
            updateStatus.className = 'badge bg-warning';

            axios.get('{{ route('punch-logs.latest') }}')
                .then(function(response) {
                    const data = response.data;

                    // Debug output
                    console.log('Dashboard update data:', data);
                    console.log('Inside count:', data.inside_count);
                    console.log('Outside count:', data.outside_count);

                    // Update counts
                    document.getElementById('pending-whatsapp-count').textContent = data.pending_whatsapp_count;
                    document.getElementById('success-whatsapp-count').textContent = data.success_whatsapp_count;
                    document.getElementById('total-whatsapp-count').textContent = data.total_whatsapp_count;
                    document.getElementById('inside-count').textContent = data.inside_count;
                    document.getElementById('outside-count').textContent = data.outside_count;

                    // Update IN users table
                    const inUsersTable = document.getElementById('in-users-table');
                    inUsersTable.innerHTML = '';
                    data.last_in_users.forEach(function(log) {
                        const row = document.createElement('tr');
                        const date = new Date(log.punch_date_time);
                        const formattedDate = date.toLocaleDateString('en-GB') + ' ' +
                            date.toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });

                        // Handle name display properly
                        const firstName = log.user && log.user.first_name ? log.user.first_name : '';
                        const lastName = log.user && log.user.last_name ? log.user.last_name : '';
                        const fullName = (firstName + ' ' + lastName).trim() || 'N/A';

                        row.innerHTML = `
                        <td>${log.user_code}</td>
                        <td>${fullName}</td>
                        <td>${formattedDate}</td>
                    `;
                        inUsersTable.appendChild(row);
                    });

                    // Update OUT users table
                    const outUsersTable = document.getElementById('out-users-table');
                    outUsersTable.innerHTML = '';
                    data.last_out_users.forEach(function(log) {
                        const row = document.createElement('tr');
                        const date = new Date(log.punch_date_time);
                        const formattedDate = date.toLocaleDateString('en-GB') + ' ' +
                            date.toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });

                        // Handle name display properly
                        const firstName = log.user && log.user.first_name ? log.user.first_name : '';
                        const lastName = log.user && log.user.last_name ? log.user.last_name : '';
                        const fullName = (firstName + ' ' + lastName).trim() || 'N/A';

                        row.innerHTML = `
                        <td>${log.user_code}</td>
                        <td>${fullName}</td>
                        <td>${formattedDate}</td>
                    `;
                        outUsersTable.appendChild(row);
                    });

                    // Update status and last updated time
                    updateStatus.textContent = 'Updated';
                    updateStatus.className = 'badge bg-success';
                    document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();

                    // Reset status after 2 seconds
                    setTimeout(() => {
                        updateStatus.textContent = 'Live and Update';
                        updateStatus.className = 'badge bg-primary';
                    }, 2000);
                })
                .catch(function(error) {
                    console.error('Error fetching dashboard data:', error);
                    updateStatus.textContent = 'Error';
                    updateStatus.className = 'badge bg-danger';
                });
        }

        // Function to check for and process unprocessed logs
        function checkAndProcessLogs() {
            const updateStatus = document.getElementById('update-status');
            updateStatus.textContent = 'Live and Update';
            updateStatus.className = 'badge bg-info';

            axios.get('{{ route('punch-logs.check-and-process') }}')
                .then(function(response) {
                    // If any logs were processed, update the dashboard
                    if (response.data.processed > 0 || response.data.failed > 0) {
                        console.log('Processed logs:', response.data.processed, 'Failed:', response.data.failed);
                        updateStatus.textContent = 'New Data';
                        updateStatus.className = 'badge bg-warning';
                        updateDashboardData();
                    } else {
                        // No new data
                        updateStatus.textContent = 'Live and Update';
                        updateStatus.className = 'badge bg-primary';
                    }
                })
                .catch(function(error) {
                    console.error('Error processing punch logs:', error);
                    updateStatus.textContent = 'Error';
                    updateStatus.className = 'badge bg-danger';
                });
        }

        // Update data every 15 seconds
        setInterval(updateDashboardData, 15000);

        // Check for new punch logs every 10 seconds (changed from 3 seconds)
        setInterval(checkAndProcessLogs, 10000);

        // Initial data load
        document.addEventListener('DOMContentLoaded', function() {
            updateDashboardData();
            checkAndProcessLogs();
        });
    </script>
@endsection
