<?php
include('../includes/main/header.php');
include('../includes/main/navigation.php');
?>



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



<?php
include('../includes/main/footer.php');
?>

