<!-- Top Bar -->
<nav class="navbar topbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="toggle-btn d-md-none me-3">
                <i class="fas fa-bars"></i>
            </div>
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
