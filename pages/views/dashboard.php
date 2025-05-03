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

<!-- Dashboard/Calendar Carousel -->
<div class="container-fluid">
    <div id="dashboardCarousel" class="carousel slide" data-bs-interval="false">
        <div class="carousel-inner">
            <!-- Dashboard Content FIRST -->
            <div class="carousel-item active" id="dashboardCarouselItem">
                <div class="content-wrapper">
                    <div class="container-fluid">
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
                                    <div>
                                        <button class="btn btn-outline-primary me-2" id="dashboardViewBtn"
                                            style="display:none;">
                                            <i class="fas fa-tachometer-alt"></i> Dashboard
                                        </button>
                                        <button class="btn btn-outline-primary" id="calendarViewBtn">
                                            <i class="fas fa-calendar-alt"></i> View Calendar
                                        </button>
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
                                            <button class="btn btn-link dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
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
                                                            <td><?php echo htmlspecialchars($proposal['committee_name'] ?: 'Not Assigned Yet'); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($proposal['action_type'] ?? 'New'); ?>
                                                            </td>
                                                            <td><?php echo date('M d, Y', strtotime($proposal['created_at'])); ?>
                                                            </td>
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
            </div>
            <!-- Calendar Content SECOND -->
            <div class="carousel-item" id="calendarCarouselItem">
                <div class="content-wrapper">
                    <div class="container-fluid">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 me-3">
                                            Hearing Schedules Calendar
                                        </h3>
                                    </div>
                                    <div>
                                        <button class="btn btn-outline-primary me-2" id="dashboardViewBtn2">
                                            <i class="fas fa-tachometer-alt"></i> Dashboard
                                        </button>
                                        <button class="btn btn-outline-primary" id="calendarViewBtn2"
                                            style="display:none;">
                                            <i class="fas fa-calendar-alt"></i> View Calendar
                                        </button>
                                    </div>
                                </div>
                                <!-- Move date/time below the title -->
                                <div class="d-flex justify-content-start mt-2">
                                    <span class="text-muted" id="calendarCurrentDateTime"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="dashboardCalendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel controls (hidden, we use buttons above) -->
        <button class="carousel-control-prev d-none" type="button" data-bs-target="#dashboardCarousel"
            data-bs-slide="prev"></button>
        <button class="carousel-control-next d-none" type="button" data-bs-target="#dashboardCarousel"
            data-bs-slide="next"></button>
    </div>
</div>

<!-- Proposal Details Modal -->
<div class="modal fade" id="calendarProposalDetailsModal" tabindex="-1"
    aria-labelledby="calendarProposalDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="calendarProposalDetailsModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Proposal Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="calendarModalScheduleId">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Proposal Title:</div>
                        <div class="col-md-8 d-flex align-items-center" style="font-size:1.1rem;font-weight:500;">
                            <span id="calendarModalProposalTitle"></span>
                            <a id="calendarViewProposalDocBtn" class="btn btn-sm btn-primary ms-2" target="_blank"
                                href="#" style="display:none;">
                                <i class="fas fa-eye me-1"></i>View Document
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Current Status:</div>
                        <div class="col-md-8">
                            <span id="calendarModalCurrentStatus" class="badge bg-info text-dark px-3 py-2"
                                style="font-size:1rem;"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Scheduled Date:</div>
                        <div class="col-md-8">
                            <i class="far fa-calendar-alt me-1"></i>
                            <span id="calendarModalHearingDate"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Scheduled Time:</div>
                        <div class="col-md-8">
                            <i class="far fa-clock me-1"></i>
                            <span id="calendarModalHearingTime"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Session Type:</div>
                        <div class="col-md-8">
                            <span id="calendarModalSessionType" class="badge bg-secondary px-3 py-2"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Reading Result:</div>
                        <div class="col-md-8">
                            <span id="calendarModalReadingResult" class="badge bg-success px-3 py-2"></span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold text-secondary">Remarks:</div>
                        <div class="col-md-8">
                            <div id="calendarModalRemarks" class="p-2 rounded bg-light border" style="min-height:40px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
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
        data: (() => {
            // Only include Approved, Rejected, Pending
            const allowed = ['Approved', 'Rejected', 'Pending'];
            const colorMap = {
                'Approved': '#28a745',
                'Rejected': '#dc3545',
                'Pending': '#ffc107'
            };
            const filtered = statusData.filter(item => allowed.includes(item.action_type));
            return {
                labels: filtered.map(item => item.action_type),
                datasets: [{
                    data: filtered.map(item => item.count),
                    backgroundColor: filtered.map(item => colorMap[item.action_type])
                }]
            };
        })()
    });

    // Carousel view switching (sync both sets of buttons)
    function showDashboardView() {
        var carousel = bootstrap.Carousel.getOrCreateInstance(document.getElementById('dashboardCarousel'));
        carousel.to(0);
        document.getElementById('dashboardViewBtn').style.display = 'none';
        document.getElementById('calendarViewBtn').style.display = '';
        document.getElementById('dashboardViewBtn2').style.display = 'none';
        document.getElementById('calendarViewBtn2').style.display = '';
    }
    function showCalendarView() {
        var carousel = bootstrap.Carousel.getOrCreateInstance(document.getElementById('dashboardCarousel'));
        carousel.to(1);
        document.getElementById('dashboardViewBtn').style.display = '';
        document.getElementById('calendarViewBtn').style.display = 'none';
        document.getElementById('dashboardViewBtn2').style.display = '';
        document.getElementById('calendarViewBtn2').style.display = 'none';
    }
    document.getElementById('calendarViewBtn').addEventListener('click', showCalendarView);
    document.getElementById('dashboardViewBtn').addEventListener('click', showDashboardView);
    document.getElementById('calendarViewBtn2').addEventListener('click', showCalendarView);
    document.getElementById('dashboardViewBtn2').addEventListener('click', showDashboardView);

    // Initialize FullCalendar when calendar view is shown
    let calendarInitialized = false;
    document.getElementById('dashboardCarousel').addEventListener('slid.bs.carousel', function (event) {
        if (event.to === 1 && !calendarInitialized) {
            var calendarEl = document.getElementById('dashboardCalendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                    url: '../../controller/dataTable/scheduleEventsTable.php',
                    method: 'GET',
                    failure: function () {
                        alert('There was an error while fetching events!');
                    }
                },
                eventDataTransform: function (eventData) {
                    // Only set start if both date and time are present and not already in ISO format
                    if (
                        eventData.hearing_time &&
                        eventData.start &&
                        !eventData.start.includes('T')
                    ) {
                        eventData.start = eventData.start + 'T' + eventData.hearing_time;
                    }
                    return eventData;
                },
                eventDidMount: function (info) {
                    if (info.event.extendedProps.color) {
                        info.el.style.backgroundColor = info.event.extendedProps.color;
                        info.el.style.borderColor = info.event.extendedProps.color;
                    }
                },
                eventContent: function (arg) {
                    // Use hearing_time from extendedProps (from DB)
                    let timeStr = '';
                    if (arg.event.extendedProps && arg.event.extendedProps.hearing_time) {
                        let t = arg.event.extendedProps.hearing_time.split(':');
                        let hours = parseInt(t[0], 10);
                        let minutes = t[1] ? t[1] : '00';
                        let ampm = hours >= 12 ? 'PM' : 'AM';
                        let h = hours % 12;
                        if (h === 0) h = 12;
                        let m = minutes.length === 1 ? '0' + minutes : minutes;
                        timeStr = h + ':' + m + ' ' + ampm;
                    }
                    let title = arg.event.title || '';
                    let session = arg.event.extendedProps.session_type || '';
                    return { html: `<span style="font-weight:600;">${timeStr}</span> - <span>${title}</span> (${session})` };
                },
                eventClick: function (info) {
                    // Fill modal with event details
                    var event = info.event;
                    document.getElementById('calendarModalScheduleId').value = event.id;
                    document.getElementById('calendarModalProposalTitle').textContent = event.title;
                    document.getElementById('calendarModalCurrentStatus').textContent = event.extendedProps.current_status || '';
                    document.getElementById('calendarModalHearingDate').textContent = event.start ? event.start.toLocaleDateString() : '';
                    document.getElementById('calendarModalHearingTime').textContent = event.extendedProps.hearing_time_formatted || '';
                    document.getElementById('calendarModalSessionType').textContent = event.extendedProps.session_type || '';
                    document.getElementById('calendarModalReadingResult').textContent = event.extendedProps.reading_result || '';
                    document.getElementById('calendarModalRemarks').textContent = event.extendedProps.remarks || '';

                    // Show/hide the view document button beside the proposal title
                    var viewBtn = document.getElementById('calendarViewProposalDocBtn');
                    var fileId = (event.extendedProps && event.extendedProps.file_id) ? event.extendedProps.file_id : (event.file_id || null);

                    if (fileId && fileId !== 'null' && fileId !== '') {
                        viewBtn.href = 'https://docs.google.com/document/d/' + fileId + '/preview';
                        viewBtn.setAttribute('target', '_blank');
                        viewBtn.removeAttribute('tabindex');
                        viewBtn.classList.remove('disabled');
                        viewBtn.style.display = 'inline-block';
                    } else {
                        viewBtn.href = '#';
                        viewBtn.setAttribute('tabindex', '-1');
                        viewBtn.classList.add('disabled');
                        viewBtn.style.display = 'none';
                    }

                    var modal = new bootstrap.Modal(document.getElementById('calendarProposalDetailsModal'));
                    modal.show();
                }
            });
            calendar.render();
            calendarInitialized = true;
        }
        // Sync button visibility on slide
        if (event.to === 1) {
            document.getElementById('dashboardViewBtn').style.display = '';
            document.getElementById('calendarViewBtn').style.display = 'none';
            document.getElementById('dashboardViewBtn2').style.display = '';
            document.getElementById('calendarViewBtn2').style.display = 'none';
            startCalendarDateTimeInterval();
        } else {
            document.getElementById('dashboardViewBtn').style.display = 'none';
            document.getElementById('calendarViewBtn').style.display = '';
            document.getElementById('dashboardViewBtn2').style.display = 'none';
            document.getElementById('calendarViewBtn2').style.display = '';
            stopCalendarDateTimeInterval();
        }
    });

    // Show date and time in the calendar view
    function updateCalendarDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateStr = now.toLocaleDateString(undefined, options);
        const timeStr = now.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', hour12: true });
        document.getElementById('calendarCurrentDateTime').textContent = `${timeStr} | ${dateStr}`;
    }
    // Only update when calendar view is visible
    function startCalendarDateTimeInterval() {
        updateCalendarDateTime();
        if (window.calendarDateTimeInterval) clearInterval(window.calendarDateTimeInterval);
        window.calendarDateTimeInterval = setInterval(updateCalendarDateTime, 10000);
    }
    function stopCalendarDateTimeInterval() {
        if (window.calendarDateTimeInterval) clearInterval(window.calendarDateTimeInterval);
    }

    // If calendar is first, show date/time immediately
    if (document.getElementById('calendarCarouselItem').classList.contains('active')) {
        startCalendarDateTimeInterval();
    }
</script>
<style>
    #dashboardCalendar {
        max-width: 1500px;
        max-height: 700px;
        margin: 20px auto;
        background: #f8fafc;
        padding: 24px;
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(60, 72, 88, 0.10), 0 1.5px 4px rgba(60, 72, 88, 0.06);
        border: none;
        transition: box-shadow 0.2s;
    }

    .fc {
        background: transparent;
        border-radius: 18px;
        font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
        font-size: 1rem;
    }

    .fc-toolbar-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        letter-spacing: 0.01em;
    }

    .fc-button {
        background: #2563eb;
        border: none;
        color: #fff;
        border-radius: 8px;
        padding: 0.5em 1.2em;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(60, 72, 88, 0.08);
        transition: background 0.15s, box-shadow 0.15s;
    }

    .fc-button:hover,
    .fc-button:focus {
        background: #1d4ed8;
        color: #fff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.10);
    }

    .fc-button-primary:not(:disabled).fc-button-active,
    .fc-button-primary:not(:disabled):active {
        background: #1e40af;
        color: #fff;
    }

    /* Only apply background and border-radius to month view days */
    .fc-daygrid-day {
        background: #fff;
        border: none;
        border-radius: 10px;
        transition: background 0.15s;
    }

    .fc-daygrid-day:hover {
        background: #e0e7ef;
    }

    /* Remove background and border-radius from timegrid slots (week/day view) */
    /* Optionally, add a subtle border-bottom for time slots for clarity */
    .fc-timegrid-slot {
        background: transparent !important;
        border-radius: 0 !important;
        border-bottom: 1px solid #f1f5f9;
    }

    .fc-day-today {
        background: #e0e7ff !important;
        border-radius: 10px;
        box-shadow: 0 0 0 2px #2563eb33;
    }

    .fc-event,
    .fc-daygrid-event,
    .fc-timegrid-event {
        background: linear-gradient(90deg, #2563eb 60%, #60a5fa 100%);
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 0.18em 0.7em;
        font-size: 0.92em;
        /* smaller font size */
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.08);
        transition: box-shadow 0.15s, background 0.15s;
        margin-bottom: 4px;
    }

    .fc-event:hover,
    .fc-daygrid-event:hover,
    .fc-timegrid-event:hover {
        background: linear-gradient(90deg, #1d4ed8 60%, #38bdf8 100%);
        color: #fff;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.15);
        cursor: pointer;
    }

    .fc-daygrid-event-dot {
        display: none;
    }

    .fc-scrollgrid,
    .fc-scrollgrid-section {
        border: none !important;
    }

    .fc-col-header-cell-cushion {
        color: #2563eb;
        font-weight: 600;
        font-size: 1.1em;
        padding: 0.5em 0;
    }

    .fc-daygrid-day-number {
        color: #64748b;
        font-weight: 500;
        font-size: 1.05em;
        padding: 0.3em 0.5em;
        border-radius: 6px;
        transition: background 0.15s;
    }

    .fc-daygrid-day-number:hover {
        background: #e0e7ff;
        color: #1d4ed8;
    }

    /* Responsive adjustments */
    @media (max-width: 900px) {
        #dashboardCalendar {
            max-width: 100%;
            padding: 10px;
        }

        .fc-toolbar-title {
            font-size: 1.3rem;
        }
    }

    @media (max-width: 600px) {
        #dashboardCalendar {
            padding: 2px;
        }

        .fc-toolbar-title {
            font-size: 1rem;
        }

        .fc-button {
            padding: 0.3em 0.7em;
            font-size: 0.95em;
        }
    }
</style>

<?php include('../includes/main/footer.php'); ?>

