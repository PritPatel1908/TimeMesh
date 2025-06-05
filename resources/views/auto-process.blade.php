<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Process Punch Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .log-container {
            height: 400px;
            overflow-y: auto;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            margin-top: 20px;
        }

        .log-entry {
            margin-bottom: 5px;
            padding: 5px;
            border-bottom: 1px solid #dee2e6;
        }

        .success {
            color: #198754;
        }

        .error {
            color: #dc3545;
        }

        .info {
            color: #0d6efd;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Auto Process Punch Logs</h1>

        <div class="row mb-3">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span>Status: </span>
                        <span id="status" class="badge bg-secondary">Idle</span>
                    </div>
                    <div>
                        <button id="startBtn" class="btn btn-primary">Start Auto Process</button>
                        <button id="stopBtn" class="btn btn-danger" disabled>Stop</button>
                        <button id="runOnceBtn" class="btn btn-info text-white">Run Once</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class="form-group">
                    <label for="intervalSelect">Process Interval:</label>
                    <select id="intervalSelect" class="form-select">
                        <option value="30">Every 30 seconds</option>
                        <option value="60" selected>Every 1 minute</option>
                        <option value="300">Every 5 minutes</option>
                        <option value="600">Every 10 minutes</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="log-container">
            <div id="logContent"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startBtn = document.getElementById('startBtn');
            const stopBtn = document.getElementById('stopBtn');
            const runOnceBtn = document.getElementById('runOnceBtn');
            const intervalSelect = document.getElementById('intervalSelect');
            const statusEl = document.getElementById('status');
            const logContent = document.getElementById('logContent');

            let processInterval;

            function addLogEntry(message, type = 'info') {
                const entry = document.createElement('div');
                entry.className = `log-entry ${type}`;

                const timestamp = new Date().toLocaleTimeString();
                entry.textContent = `[${timestamp}] ${message}`;

                logContent.prepend(entry);
            }

            function processLogs() {
                statusEl.textContent = 'Processing...';
                statusEl.className = 'badge bg-warning';

                addLogEntry('Starting punch log processing...', 'info');

                fetch('/api/punch-logs/run-process-command')
                    .then(response => response.json())
                    .then(data => {
                        statusEl.textContent = 'Success';
                        statusEl.className = 'badge bg-success';

                        addLogEntry(`Process completed with exit code: ${data.exit_code}`, 'success');

                        if (data.output) {
                            addLogEntry(`Output: ${data.output}`, 'info');
                        }

                        setTimeout(() => {
                            statusEl.textContent = 'Running';
                            statusEl.className = 'badge bg-primary';
                        }, 2000);
                    })
                    .catch(error => {
                        statusEl.textContent = 'Error';
                        statusEl.className = 'badge bg-danger';

                        addLogEntry(`Error: ${error.message}`, 'error');

                        setTimeout(() => {
                            statusEl.textContent = 'Running';
                            statusEl.className = 'badge bg-primary';
                        }, 2000);
                    });
            }

            startBtn.addEventListener('click', function() {
                const interval = parseInt(intervalSelect.value) * 1000;

                startBtn.disabled = true;
                stopBtn.disabled = false;
                intervalSelect.disabled = true;

                statusEl.textContent = 'Running';
                statusEl.className = 'badge bg-primary';

                addLogEntry(`Auto processing started with interval: ${interval/1000} seconds`, 'info');

                // Run immediately
                processLogs();

                // Set interval for future runs
                processInterval = setInterval(processLogs, interval);
            });

            stopBtn.addEventListener('click', function() {
                clearInterval(processInterval);

                startBtn.disabled = false;
                stopBtn.disabled = true;
                intervalSelect.disabled = false;

                statusEl.textContent = 'Stopped';
                statusEl.className = 'badge bg-secondary';

                addLogEntry('Auto processing stopped', 'info');
            });

            runOnceBtn.addEventListener('click', function() {
                processLogs();
            });
        });
    </script>
</body>

</html>
