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

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .page-info {
            font-size: 0.9rem;
        }

        .search-container {
            margin-bottom: 20px;
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

        <!-- Search and Per Page Controls -->
        <div class="row search-container">
            <div class="col-md-6 mb-2 mb-md-0">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search users...">
                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="input-group justify-content-md-end">
                    <label class="input-group-text" for="perPageSelect">Show</label>
                    <select class="form-select" id="perPageSelect" style="max-width: 80px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="input-group-text">per page</span>
                </div>
            </div>
        </div>

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

                <!-- Pagination Controls -->
                <div class="pagination-container">
                    <div class="page-info">
                        Showing <span id="fromRecord">0</span> to <span id="toRecord">0</span> of <span id="totalRecords">0</span> entries
                    </div>
                    <nav aria-label="User pagination">
                        <ul class="pagination" id="paginationContainer">
                            <!-- Pagination will be generated here -->
                        </ul>
                    </nav>
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
                        @csrf
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

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                    <input type="hidden" id="delete_user_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="deleteUserBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Global variables for pagination
        let currentPage = 1;
        let lastPage = 1;
        let perPage = 10;
        let searchTerm = '';

        // Set up axios CSRF token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            loadUsers();

            // Event listeners
            document.getElementById('saveUserBtn').addEventListener('click', saveUser);
            document.getElementById('updateUserBtn').addEventListener('click', updateUser);
            document.getElementById('deleteUserBtn').addEventListener('click', function() {
                const userId = document.getElementById('delete_user_id').value;
                deleteUser(userId);
            });
            document.getElementById('importUsersBtn').addEventListener('click', importUsers);
            document.getElementById('downloadTemplateBtn').addEventListener('click', downloadTemplate);
            document.getElementById('file_type').addEventListener('change', updateFileAccept);

            // Initialize file accept attribute
            updateFileAccept();

            // Pagination event listeners
            document.getElementById('searchButton').addEventListener('click', function() {
                searchTerm = document.getElementById('searchInput').value;
                currentPage = 1; // Reset to first page on new search
                loadUsers();
            });

            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchTerm = document.getElementById('searchInput').value;
                    currentPage = 1; // Reset to first page on new search
                    loadUsers();
                }
            });

            document.getElementById('perPageSelect').addEventListener('change', function() {
                perPage = this.value;
                currentPage = 1; // Reset to first page when changing items per page
                loadUsers();
            });
        });

        // Load users from API with pagination
        function loadUsers() {
            const tableBody = document.getElementById('usersTableBody');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Show loading spinner
            tableBody.innerHTML = '';
            loadingSpinner.style.display = 'block';

            // Build query parameters
            const params = new URLSearchParams({
                page: currentPage,
                per_page: perPage
            });

            if (searchTerm) {
                params.append('search', searchTerm);
            }

            axios.get(`/api/users?${params.toString()}`)
                .then(function(response) {
                    loadingSpinner.style.display = 'none';
                    const data = response.data;
                    const users = data.data; // Laravel pagination puts items in data property

                    // Update pagination information
                    lastPage = data.last_page;
                    currentPage = data.current_page;

                    // Update pagination display
                    updatePaginationInfo(data.from, data.to, data.total);
                    generatePaginationLinks(data.current_page, data.last_page);

                    if (users.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
                        return;
                    }

                    users.forEach(function(user) {
                        const fullName = `${user.first_name} ${user.last_name || ''}`;
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${user.user_code}</td>
                        <td>${fullName}</td>
                        <td>${user.room_number || ''}</td>
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

        // Update pagination information display
        function updatePaginationInfo(from, to, total) {
            document.getElementById('fromRecord').textContent = from || 0;
            document.getElementById('toRecord').textContent = to || 0;
            document.getElementById('totalRecords').textContent = total || 0;
        }

        // Generate pagination links
        function generatePaginationLinks(currentPage, lastPage) {
            const paginationContainer = document.getElementById('paginationContainer');
            paginationContainer.innerHTML = '';

            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            const prevLink = document.createElement('a');
            prevLink.className = 'page-link';
            prevLink.href = '#';
            prevLink.innerHTML = '&laquo;';
            prevLink.setAttribute('aria-label', 'Previous');
            if (currentPage > 1) {
                prevLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    goToPage(currentPage - 1);
                });
            }
            prevLi.appendChild(prevLink);
            paginationContainer.appendChild(prevLi);

            // Calculate range of pages to show
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(lastPage, startPage + 4);

            // Adjust if we're near the end
            if (endPage - startPage < 4 && startPage > 1) {
                startPage = Math.max(1, endPage - 4);
            }

            // First page link if not in range
            if (startPage > 1) {
                const firstLi = document.createElement('li');
                firstLi.className = 'page-item';
                const firstLink = document.createElement('a');
                firstLink.className = 'page-link';
                firstLink.href = '#';
                firstLink.textContent = '1';
                firstLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    goToPage(1);
                });
                firstLi.appendChild(firstLink);
                paginationContainer.appendChild(firstLi);

                // Add ellipsis if needed
                if (startPage > 2) {
                    const ellipsisLi = document.createElement('li');
                    ellipsisLi.className = 'page-item disabled';
                    const ellipsisSpan = document.createElement('span');
                    ellipsisSpan.className = 'page-link';
                    ellipsisSpan.innerHTML = '&hellip;';
                    ellipsisLi.appendChild(ellipsisSpan);
                    paginationContainer.appendChild(ellipsisLi);
                }
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                const pageLink = document.createElement('a');
                pageLink.className = 'page-link';
                pageLink.href = '#';
                pageLink.textContent = i;
                if (i !== currentPage) {
                    pageLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        goToPage(i);
                    });
                }
                pageLi.appendChild(pageLink);
                paginationContainer.appendChild(pageLi);
            }

            // Add ellipsis and last page if needed
            if (endPage < lastPage) {
                if (endPage < lastPage - 1) {
                    const ellipsisLi = document.createElement('li');
                    ellipsisLi.className = 'page-item disabled';
                    const ellipsisSpan = document.createElement('span');
                    ellipsisSpan.className = 'page-link';
                    ellipsisSpan.innerHTML = '&hellip;';
                    ellipsisLi.appendChild(ellipsisSpan);
                    paginationContainer.appendChild(ellipsisLi);
                }

                const lastLi = document.createElement('li');
                lastLi.className = 'page-item';
                const lastLink = document.createElement('a');
                lastLink.className = 'page-link';
                lastLink.href = '#';
                lastLink.textContent = lastPage;
                lastLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    goToPage(lastPage);
                });
                lastLi.appendChild(lastLink);
                paginationContainer.appendChild(lastLi);
            }

            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === lastPage ? 'disabled' : ''}`;
            const nextLink = document.createElement('a');
            nextLink.className = 'page-link';
            nextLink.href = '#';
            nextLink.innerHTML = '&raquo;';
            nextLink.setAttribute('aria-label', 'Next');
            if (currentPage < lastPage) {
                nextLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    goToPage(currentPage + 1);
                });
            }
            nextLi.appendChild(nextLink);
            paginationContainer.appendChild(nextLi);
        }

        // Go to specific page
        function goToPage(page) {
            currentPage = page;
            loadUsers();
        }

        // Import users
        function importUsers() {
            const form = document.getElementById('importUserForm');
            const fileInput = document.getElementById('import_file');
            const importBtn = document.getElementById('importUsersBtn');

            // Reset validation errors
            document.getElementById('import_file').classList.remove('is-invalid');
            document.getElementById('file_error').textContent = '';

            // Check if file is selected
            if (!fileInput.files.length) {
                document.getElementById('import_file').classList.add('is-invalid');
                document.getElementById('file_error').textContent = 'Please select a file to import';
                return;
            }

            // Create form data
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            // Disable button and show loading state
            importBtn.disabled = true;
            importBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importing...';

            // First test the import
            testImport(formData);
        }

        // Test import before proceeding
        function testImport(formData) {
            // Proceed with actual import
            proceedWithImport(formData);
        }

        // Proceed with actual import after test
        function proceedWithImport(formData) {
            // Get CSRF token directly
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            axios.post('/api/users/import', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(function(response) {
                    console.log('Success response:', response);
                    // Close modal and reset form
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importUserModal'));
                    modal.hide();
                    document.getElementById('importUserForm').reset();

                    // Show success message and reload users
                    showAlert('Users imported successfully', 'success');

                    // Reset to first page and reload
                    currentPage = 1;
                    loadUsers();
                })
                .catch(function(error) {
                    console.error('Import error:', error);
                    if (error.response) {
                        console.error('Error response data:', error.response.data);
                        console.error('Error response status:', error.response.status);

                        if (error.response.status === 422) {
                            // Validation errors
                            const errors = error.response.data.errors;
                            if (errors && errors.file) {
                                document.getElementById('import_file').classList.add('is-invalid');
                                document.getElementById('file_error').textContent = errors.file[0];
                            }
                        }
                    }

                    let errorMessage = 'Error importing users';
                    if (error.response && error.response.data && error.response.data.error) {
                        errorMessage += ': ' + error.response.data.error;
                    } else if (error.message) {
                        errorMessage += ': ' + error.message;
                    }
                    showAlert(errorMessage, 'danger');
                })
                .finally(function() {
                    // Restore button state
                    const importBtn = document.getElementById('importUsersBtn');
                    importBtn.innerHTML = 'Import';
                    importBtn.disabled = false;
                });
        }

        // Download template file
        function downloadTemplate() {
            const fileType = document.getElementById('file_type').value;
            let templateUrl;

            // Create template file based on selected type
            if (fileType === 'csv') {
                templateUrl = createCSVTemplate();
            } else {
                // For Excel files, we'll use a server endpoint
                templateUrl = `/api/users/template?type=${fileType}`;
                window.location.href = templateUrl;
                return;
            }

            // For CSV, we can create it client-side
            const link = document.createElement('a');
            link.href = templateUrl;
            link.download = `user_import_template.${fileType}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(templateUrl);
        }

        // Create CSV template
        function createCSVTemplate() {
            const headers = [
                'user_code', 'first_name', 'last_name', 'father_name', 'mother_name',
                'address', 'contact_no', 'guardian_name', 'guardian_relation', 'guardian_contact_no',
                'emergency_contact', 'email', 'room_number', 'vehicle_detail', 'occupation',
                'occupation_address', 'medical_detail', 'other_details', 'joining_date', 'password'
            ];

            const csvContent = headers.join(',') + '\n';
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            return URL.createObjectURL(blob);
        }

        // Update file input accept attribute based on selected file type
        function updateFileAccept() {
            const fileType = document.getElementById('file_type').value;
            const fileInput = document.getElementById('import_file');

            switch (fileType) {
                case 'csv':
                    fileInput.accept = '.csv';
                    break;
                case 'xlsx':
                    fileInput.accept = '.xlsx';
                    break;
                case 'xls':
                    fileInput.accept = '.xls';
                    break;
                default:
                    fileInput.accept = '.csv,.xlsx,.xls';
            }
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

                    // Reset to first page and reload
                    currentPage = 1;
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
                    loadUsers(); // Stay on current page
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

                    // If we're on the last page and there's only one user, go to previous page
                    if (currentPage === lastPage && document.querySelectorAll('#usersTableBody tr').length === 1 && currentPage > 1) {
                        currentPage--;
                    }
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
