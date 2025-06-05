@extends('layouts.app')

@section('styles')
    <style>
        .user-actions {
            white-space: nowrap;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        @media (max-width: 767.98px) {
            .user-actions .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                margin-bottom: 0.25rem;
            }

            .user-actions .btn-sm {
                padding: 0.15rem 0.3rem;
                font-size: 0.75rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <h2>User Management</h2>
            </div>
            <div class="col-md-6 text-md-end">
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importUserModal">
                    <i class="fas fa-file-import"></i> Import Users
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus"></i> Add New User
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>User Code</th>
                                <th>Name</th>
                                <th>Room Number</th>
                                <th>Contact</th>
                                <th>Joining Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- User data will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div id="loadingSpinner" class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="user_code" class="form-label">User Code</label>
                                <input type="text" class="form-control" id="user_code" name="user_code" required>
                                <div class="invalid-feedback" id="user_code_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Leave blank for default password">
                                <div class="invalid-feedback" id="password_error"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                                <div class="invalid-feedback" id="first_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                                <div class="invalid-feedback" id="last_name_error"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="contact_no" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_no" name="contact_no" required>
                                <div class="invalid-feedback" id="contact_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="room_number" class="form-label">Room Number</label>
                                <input type="text" class="form-control" id="room_number" name="room_number" required>
                                <div class="invalid-feedback" id="room_number_error"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                            <div class="invalid-feedback" id="address_error"></div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="guardian_name" class="form-label">Guardian Name</label>
                                <input type="text" class="form-control" id="guardian_name" name="guardian_name"
                                    required>
                                <div class="invalid-feedback" id="guardian_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_relation" class="form-label">Guardian Relation</label>
                                <input type="text" class="form-control" id="guardian_relation"
                                    name="guardian_relation" required>
                                <div class="invalid-feedback" id="guardian_relation_error"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="guardian_contact_no" class="form-label">Guardian Contact</label>
                                <input type="text" class="form-control" id="guardian_contact_no"
                                    name="guardian_contact_no" required>
                                <div class="invalid-feedback" id="guardian_contact_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                <input type="text" class="form-control" id="emergency_contact"
                                    name="emergency_contact" required>
                                <div class="invalid-feedback" id="emergency_contact_error"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="occupation" class="form-label">Occupation</label>
                                <input type="text" class="form-control" id="occupation" name="occupation" required>
                                <div class="invalid-feedback" id="occupation_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="joining_date" class="form-label">Joining Date</label>
                                <input type="date" class="form-control" id="joining_date" name="joining_date"
                                    required>
                                <div class="invalid-feedback" id="joining_date_error"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="occupation_address" class="form-label">Occupation Address</label>
                            <textarea class="form-control" id="occupation_address" name="occupation_address" rows="2" required></textarea>
                            <div class="invalid-feedback" id="occupation_address_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="medical_detail" class="form-label">Medical Details</label>
                            <textarea class="form-control" id="medical_detail" name="medical_detail" rows="2" required></textarea>
                            <div class="invalid-feedback" id="medical_detail_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveUserBtn">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_user_code" class="form-label">User Code</label>
                                <input type="text" class="form-control" id="edit_user_code" name="user_code"
                                    required>
                                <div class="invalid-feedback" id="edit_user_code_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="edit_password" name="password"
                                    placeholder="Leave blank to keep current password">
                                <div class="invalid-feedback" id="edit_password_error"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name"
                                    required>
                                <div class="invalid-feedback" id="edit_first_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="edit_last_name" name="last_name"
                                    required>
                                <div class="invalid-feedback" id="edit_last_name_error"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_contact_no" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="edit_contact_no" name="contact_no"
                                    required>
                                <div class="invalid-feedback" id="edit_contact_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_room_number" class="form-label">Room Number</label>
                                <input type="text" class="form-control" id="edit_room_number" name="room_number"
                                    required>
                                <div class="invalid-feedback" id="edit_room_number_error"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Address</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                            <div class="invalid-feedback" id="edit_address_error"></div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_guardian_name" class="form-label">Guardian Name</label>
                                <input type="text" class="form-control" id="edit_guardian_name" name="guardian_name"
                                    required>
                                <div class="invalid-feedback" id="edit_guardian_name_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_guardian_relation" class="form-label">Guardian Relation</label>
                                <input type="text" class="form-control" id="edit_guardian_relation"
                                    name="guardian_relation" required>
                                <div class="invalid-feedback" id="edit_guardian_relation_error"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_guardian_contact_no" class="form-label">Guardian Contact</label>
                                <input type="text" class="form-control" id="edit_guardian_contact_no"
                                    name="guardian_contact_no" required>
                                <div class="invalid-feedback" id="edit_guardian_contact_no_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_emergency_contact" class="form-label">Emergency Contact</label>
                                <input type="text" class="form-control" id="edit_emergency_contact"
                                    name="emergency_contact" required>
                                <div class="invalid-feedback" id="edit_emergency_contact_error"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_occupation" class="form-label">Occupation</label>
                                <input type="text" class="form-control" id="edit_occupation" name="occupation"
                                    required>
                                <div class="invalid-feedback" id="edit_occupation_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_joining_date" class="form-label">Joining Date</label>
                                <input type="date" class="form-control" id="edit_joining_date" name="joining_date"
                                    required>
                                <div class="invalid-feedback" id="edit_joining_date_error"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_occupation_address" class="form-label">Occupation Address</label>
                            <textarea class="form-control" id="edit_occupation_address" name="occupation_address" rows="2" required></textarea>
                            <div class="invalid-feedback" id="edit_occupation_address_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_medical_detail" class="form-label">Medical Details</label>
                            <textarea class="form-control" id="edit_medical_detail" name="medical_detail" rows="2" required></textarea>
                            <div class="invalid-feedback" id="edit_medical_detail_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateUserBtn">Update User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="userDetails">
                        <!-- User details will be loaded here via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Users Modal -->
    <div class="modal fade" id="importUserModal" tabindex="-1" aria-labelledby="importUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importUserModalLabel">Import Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="importUserForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="file_type" class="form-label">File Type</label>
                            <select class="form-select" id="file_type" name="file_type">
                                <option value="csv">CSV</option>
                                <option value="xlsx">Excel (XLSX)</option>
                                <option value="xls">Excel (XLS)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="import_file" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="import_file" name="file"
                                accept=".csv,.xlsx,.xls" required>
                            <div class="invalid-feedback" id="file_error"></div>
                            <div class="form-text mt-2">
                                <a href="#" id="downloadTemplateBtn">Download Template</a>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <strong>Note:</strong> The file should have the following columns: user_code, first_name,
                            last_name, contact_no, guardian_name, etc.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="importUsersBtn">Import</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load users when page loads
            loadUsers();

            // Add user form submission
            document.getElementById('saveUserBtn').addEventListener('click', function() {
                saveUser();
            });

            // Update user form submission
            document.getElementById('updateUserBtn').addEventListener('click', function() {
                updateUser();
            });
        });

        // Load all users
        function loadUsers() {
            const tableBody = document.getElementById('usersTableBody');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Show loading spinner
            tableBody.innerHTML = '';
            loadingSpinner.style.display = 'block';

            axios.get('/api/users')
                .then(function(response) {
                    loadingSpinner.style.display = 'none';
                    const users = response.data.users;

                    if (users.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
                        return;
                    }

                    users.forEach(function(user) {
                        const fullName = `${user.first_name} ${user.last_name}`;
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${user.user_code}</td>
                        <td>${fullName}</td>
                        <td>${user.room_number}</td>
                        <td>${user.contact_no}</td>
                        <td>${formatDate(user.joining_date)}</td>
                        <td class="user-actions">
                            <button class="btn btn-sm btn-primary edit-user" data-id="${user.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-user" data-id="${user.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                        tableBody.appendChild(row);

                        // Add event listeners to the buttons
                        row.querySelector('.edit-user').addEventListener('click', function() {
                            openEditModal(user.id);
                        });

                        row.querySelector('.delete-user').addEventListener('click', function() {
                            openDeleteModal(user.id);
                        });
                    });
                })
                .catch(function(error) {
                    loadingSpinner.style.display = 'none';
                    showAlert('Error loading users: ' + error.message, 'danger');
                });
        }

        // Save new user
        function saveUser() {
            const form = document.getElementById('addUserForm');
            const formData = new FormData(form);
            const formObject = Object.fromEntries(formData.entries());

            // Reset validation errors
            clearValidationErrors();

            axios.post('/api/users', formObject)
                .then(function(response) {
                    // Close modal and reload users
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                    modal.hide();

                    // Clear form
                    form.reset();

                    // Show success message and reload users
                    showAlert('User created successfully', 'success');
                    loadUsers();
                })
                .catch(function(error) {
                    if (error.response && error.response.status === 422) {
                        // Validation errors
                        const errors = error.response.data.errors;
                        displayValidationErrors(errors);
                    } else {
                        showAlert('Error creating user: ' + error.message, 'danger');
                    }
                });
        }

        // Open edit modal and load user data
        function openEditModal(userId) {
            axios.get(`/api/users/${userId}`)
                .then(function(response) {
                    const user = response.data.user;

                    // Set user ID in hidden field
                    document.getElementById('edit_user_id').value = user.id;

                    // Populate form fields
                    document.getElementById('edit_user_code').value = user.user_code;
                    document.getElementById('edit_first_name').value = user.first_name;
                    document.getElementById('edit_last_name').value = user.last_name;
                    document.getElementById('edit_contact_no').value = user.contact_no;
                    document.getElementById('edit_room_number').value = user.room_number;
                    document.getElementById('edit_address').value = user.address;
                    document.getElementById('edit_guardian_name').value = user.guardian_name;
                    document.getElementById('edit_guardian_relation').value = user.guardian_relation;
                    document.getElementById('edit_guardian_contact_no').value = user.guardian_contact_no;
                    document.getElementById('edit_emergency_contact').value = user.emergency_contact;
                    document.getElementById('edit_occupation').value = user.occupation;
                    document.getElementById('edit_joining_date').value = formatDateForInput(user.joining_date);
                    document.getElementById('edit_occupation_address').value = user.occupation_address;
                    document.getElementById('edit_medical_detail').value = user.medical_detail;

                    // Clear password field
                    document.getElementById('edit_password').value = '';

                    // Open modal
                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                })
                .catch(function(error) {
                    showAlert('Error loading user data: ' + error.message, 'danger');
                });
        }

        // Update user
        function updateUser() {
            const form = document.getElementById('editUserForm');
            const formData = new FormData(form);
            const formObject = Object.fromEntries(formData.entries());
            const userId = document.getElementById('edit_user_id').value;

            // Reset validation errors
            clearValidationErrors('edit_');

            axios.put(`/api/users/${userId}`, formObject)
                .then(function(response) {
                    // Close modal and reload users
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                    modal.hide();

                    // Show success message and reload users
                    showAlert('User updated successfully', 'success');
                    loadUsers();
                })
                .catch(function(error) {
                    if (error.response && error.response.status === 422) {
                        // Validation errors
                        const errors = error.response.data.errors;
                        displayValidationErrors(errors, 'edit_');
                    } else {
                        showAlert('Error updating user: ' + error.message, 'danger');
                    }
                });
        }

        // Open delete confirmation modal
        function openDeleteModal(userId) {
            document.getElementById('delete_user_id').value = userId;
            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            modal.show();
        }

        // Delete user
        function deleteUser(userId) {
            axios.delete(`/api/users/${userId}`)
                .then(function(response) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
                    modal.hide();

                    // Show success message and reload users
                    showAlert('User deleted successfully', 'success');
                    loadUsers();
                })
                .catch(function(error) {
                    showAlert('Error deleting user: ' + error.message, 'danger');
                });
        }

        // Display validation errors
        function displayValidationErrors(errors, prefix = '') {
            for (const field in errors) {
                const errorMessage = errors[field][0];
                const errorElement = document.getElementById(`${prefix}${field}_error`);

                if (errorElement) {
                    const inputElement = document.getElementById(`${prefix}${field}`);
                    inputElement.classList.add('is-invalid');
                    errorElement.textContent = errorMessage;
                }
            }
        }

        // Clear validation errors
        function clearValidationErrors(prefix = '') {
            const invalidInputs = document.querySelectorAll('.is-invalid');
            invalidInputs.forEach(function(input) {
                input.classList.remove('is-invalid');
            });

            const errorMessages = document.querySelectorAll('.invalid-feedback');
            errorMessages.forEach(function(element) {
                element.textContent = '';
            });
        }

        // Show alert message
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

            alertContainer.appendChild(alert);

            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                alert.classList.remove('show');
                setTimeout(function() {
                    alertContainer.removeChild(alert);
                }, 150);
            }, 5000);
        }

        // Format date for display (YYYY-MM-DD to DD/MM/YYYY)
        function formatDate(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB');
        }

        // Format date for input field (YYYY-MM-DD)
        function formatDateForInput(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            return date.toISOString().split('T')[0];
        }
    </script>
@endsection
