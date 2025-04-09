<?php
function hasAccess($requiredRole)
{
    $userRole = $_SESSION['role'];
    switch ($userRole) {
        case 'legislator':
            return in_array($requiredRole, ['legislator', 'committee', 'viewer', 'admin']);
        case 'committee':
            return in_array($requiredRole, ['committee', 'viewer']);
        case 'admin':
            return in_array($requiredRole, ['admin', 'legislator', 'committee', 'viewer']);
        default:
            return false;
    }
}

?>

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
                    <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <?php if (hasAccess('admin')): ?>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    <?php endif; ?>
                    <li><a class="dropdown-item"
                            href="../../controller/authentication/authentication.php?action=logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo-container text-center">
        <img src="../../assets/img/logo.png" alt="Logo" class="mb-3">
        <h5 class="fw-bold">LGU System</h5>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a href="../views/dashboard.php" class="nav-link 
            <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <?php if (hasAccess('legislator')): ?>
            <li class="nav-item">
                <a href="../views/user.php" class="nav-link
            <?php echo basename($_SERVER['PHP_SELF']) == 'user.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users me-2"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../views/committee.php" class="nav-link
            <?php echo basename($_SERVER['PHP_SELF']) == 'committee.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users-gear me-2"></i>
                    <span>Committees</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../views/ordinanceProposal.php" class="nav-link
            <?php echo basename($_SERVER['PHP_SELF']) == 'ordinanceProposal.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt me-2"></i>
                    <span>Ordinance Proposal</span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (hasAccess('committee')): ?>
            <li class="nav-item">
                <a href="../views/ordinanceStatus.php" class="nav-link
            <?php echo basename($_SERVER['PHP_SELF']) == 'ordinanceStatus.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check me-2"></i>
                    <span>Ordinance Status</span>
                </a>
            </li>
            <!-- <li class="nav-item">
                <a href="../views/schedule.php" class="nav-link
            <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span>Schedules</span>
                </a>
            </li> -->
            <li class="nav-item">
                <a href="../views/report.php" class="nav-link
            <?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar me-2"></i>
                    <span>Reports</span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (hasAccess('admin')): ?>

            <li class="nav-item">
                <a href="../views/setting.php" class="nav-link
            <?php echo basename($_SERVER['PHP_SELF']) == 'setting.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog me-2"></i>
                    <span>Settings</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>


<!-- Main Content Area -->
<div class="main-content">

    <!-- Toast Container (Positioning) -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="toastMessage" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img id="toastIcon" src="" width="20" height="20" class="rounded me-2" alt="Icon">
                <strong id="toastTitle" class="me-auto">Notification</strong>
                <small id="toastTime">Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody">
                <!-- Dynamic Message Here -->
            </div>
        </div>
    </div>
