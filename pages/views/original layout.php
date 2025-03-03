<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGU Ordinance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            overflow-x: hidden;
            padding-top: 70px;
            /* Add padding to account for fixed navbar */
        }

        .sidebar {
            height: 100vh;
            background: #ffffff;
            color: #333;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 999;
            /* Lower than topbar */
            position: fixed;
            top: 50px;
            /* Start below the topbar */
            left: 0;
            width: 250px;
            overflow-y: auto;
            bottom: 0;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 5px 15px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background: #f8f9fa;
            color: #007bff;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: #e9ecef;
            color: #007bff;
            font-weight: bold;
        }

        .topbar {
            background: #ffffff;
            color: #333;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: 50px;
            z-index: 1000;
            /* Higher than sidebar */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
        }

        .logo-container {
            border-bottom: 1px solid #eee;
            padding: 15px;
        }

        .logo-container img {
            width: 80px;
            height: auto;
        }

        .main-content {
            transition: all 0.3s;
            padding: 20px;
            background-color: #f9f9f9;
            min-height: calc(100vh - 70px);
            margin-left: 250px;
            /* Same as sidebar width */
        }

        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 2.5rem;
            color: #007bff;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
                /* Hide sidebar by default on mobile */
                box-shadow: none;
            }

            .sidebar.active {
                margin-left: 0;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            }

            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }
        }

        .toggle-btn {
            cursor: pointer;
            font-size: 1.5rem;
            color: #333;
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <nav class="navbar topbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="toggle-btn d-md-none me-3">
                    <i class="fas fa-bars"></i>
                </div>
                <h2 class="navbar-brand mb-0">LGU Ordinance System</h2>
            </div>
            <div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-decoration-none" type="button" id="userDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container text-center">
            <img src="https://via.placeholder.com/80" alt="Logo" class="mb-3">
            <h5 class="fw-bold">LGU System</h5>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-users me-2"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-file-alt me-2"></i>
                    <span>Documents</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-book me-2"></i>
                    <span>Ordinances</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span>Schedules</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar me-2"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog me-2"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="container-fluid px-4">
            <h1 class="mt-4 mb-4">Welcome to LGU Ordinance System</h1>

            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card border-0 bg-white p-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Ordinances</h6>
                                    <h3 class="mb-0">254</h3>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-book-open"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-success"><i class="fas fa-arrow-up me-1"></i>12%</span>
                                <span class="text-muted ms-2">From last month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card border-0 bg-white p-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Active Users</h6>
                                    <h3 class="mb-0">45</h3>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-success"><i class="fas fa-arrow-up me-1"></i>8%</span>
                                <span class="text-muted ms-2">From last month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card border-0 bg-white p-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Pending Approval</h6>
                                    <h3 class="mb-0">18</h3>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-danger"><i class="fas fa-arrow-down me-1"></i>5%</span>
                                <span class="text-muted ms-2">From last month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card border-0 bg-white p-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Recent Updates</h6>
                                    <h3 class="mb-0">37</h3>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="text-success"><i class="fas fa-arrow-up me-1"></i>15%</span>
                                <span class="text-muted ms-2">From last month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Ordinances</h5>
                            <button class="btn btn-sm btn-outline-primary">View All</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ordinance #</th>
                                            <th>Title</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>ORD-2023-054</td>
                                            <td>Community Health Program</td>
                                            <td>Feb 28, 2023</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                            <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
                                        </tr>
                                        <tr>
                                            <td>ORD-2023-053</td>
                                            <td>Road Improvement Project</td>
                                            <td>Feb 24, 2023</td>
                                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                                            <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
                                        </tr>
                                        <tr>
                                            <td>ORD-2023-052</td>
                                            <td>Water System Upgrade</td>
                                            <td>Feb 20, 2023</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                            <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
                                        </tr>
                                        <tr>
                                            <td>ORD-2023-051</td>
                                            <td>Public Market Renovation</td>
                                            <td>Feb 15, 2023</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                            <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
                                        </tr>
                                        <tr>
                                            <td>ORD-2023-050</td>
                                            <td>Education Support Program</td>
                                            <td>Feb 10, 2023</td>
                                            <td><span class="badge bg-danger">Rejected</span></td>
                                            <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>New
                                    Ordinance</button>
                                <button class="btn btn-outline-primary"><i class="fas fa-user-plus me-2"></i>Add
                                    User</button>
                                <button class="btn btn-outline-primary"><i class="fas fa-file-export me-2"></i>Generate
                                    Report</button>
                                <button class="btn btn-outline-primary"><i class="fas fa-search me-2"></i>Advanced
                                    Search</button>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">System Notifications</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center py-3">
                                    <div class="bg-primary rounded-circle p-2 me-3 text-white">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">New ordinance waiting for approval</p>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center py-3">
                                    <div class="bg-success rounded-circle p-2 me-3 text-white">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">System backup completed successfully</p>
                                        <small class="text-muted">5 hours ago</small>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center py-3">
                                    <div class="bg-warning rounded-circle p-2 me-3 text-white">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">User account password expiring soon</p>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-center py-3">
                                    <div class="bg-info rounded-circle p-2 me-3 text-white">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">System maintenance scheduled</p>
                                        <small class="text-muted">2 days ago</small>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.querySelector('.toggle-btn').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Set active link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function () {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>
