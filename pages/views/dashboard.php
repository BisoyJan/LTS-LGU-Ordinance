<?php
require_once '../../controller/store/dashboard_controller.php';
include('../includes/main/header.php');
include('../includes/main/navigation.php');

$dashboard = new DashboardController();
$stats = $dashboard->getDashboardStats();

// Function to get humanized date/time
function getHumanizedDateTime()
{
    $date = new DateTime('now', new DateTimeZone('Asia/Manila'));
    return [
        'time' => $date->format('h:i A'),
        'date' => $date->format('l, F j, Y')
    ];
}

$dateTime = getHumanizedDateTime();
?>

<!-- Dashboard Content -->
<div class="content-wrapper">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Welcome to LGU Ordinance System</h1>
                        <p class="text-muted mb-0">
                            <?php echo $dateTime['time']; ?> | <?php echo $dateTime['date']; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card dashboard-card bg-primary text-white h-100">
                    <div class="card-body stat-card">
                        <h5 class="card-title">Total Proposals</h5>
                        <h2 class="mb-0"><?php echo $stats['total_proposals']['total']; ?></h2>
                        <small class="text-white-50">All time proposals</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Approved</h5>
                        <h2 class="card-text"><?php echo $stats['total_proposals']['approved']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <h2 class="card-text"><?php echo $stats['total_proposals']['pending']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Rejected</h5>
                        <h2 class="card-text"><?php echo $stats['total_proposals']['rejected']; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card dashboard-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Monthly Proposals</h5>
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Export Data</a></li>
                                <li><a class="dropdown-item" href="#">View Details</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 mb-4">
                <div class="card">
                    <div class="card-header">Proposals by Status</div>
                    <div class="card-body">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Proposals Table -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Proposals</h5>
                        <a href="#" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    <div class="card-body table-container">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Proposal</th>
                                        <th>Committee</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['recent_proposals'] as $proposal): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($proposal['proposal']); ?></td>
                                            <td><?php echo htmlspecialchars($proposal['committee_name']); ?></td>
                                            <td><?php echo htmlspecialchars($proposal['action_type'] ?? 'New'); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($proposal['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize Charts
    const monthlyData = <?php echo json_encode($stats['monthly_proposals']); ?>;
    const statusData = <?php echo json_encode($stats['proposals_by_status']); ?>;

    // Monthly Chart
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Proposals',
                data: monthlyData.map(item => item.count),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    });

    // Status Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => item.action_type),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8',
                    '#6c757d'
                ]
            }]
        }
    });
</script>

<?php include('../includes/main/footer.php'); ?>
</body>

</html>
