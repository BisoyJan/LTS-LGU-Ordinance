<?php
include '../includes/main/header.php';

require_once('../../scripts/role_authenticator.php');
restrictAccess('legislator');

include '../includes/main/navigation.php';
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-auto">
            <div class="mb-3">
                <h2>Hearing Schedules</h2>
            </div>
        </div>

        <?php if ($_SESSION['role'] !== 'legislator'): ?>
            <div class="col">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                        Add Schedules
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-12">
            <!-- Calendar container -->
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="eventDetailsModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Proposal Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalScheduleId">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Proposal Title:</div>
                        <div class="col-md-8 d-flex align-items-center" style="font-size:1.1rem;font-weight:500;">
                            <span id="modalProposalTitle"></span>
                            <a id="viewProposalDocBtn" class="btn btn-sm btn-primary ms-2" target="_blank" href="#"
                                style="display:none;">
                                <i class="fas fa-eye me-1"></i>View Document
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Scheduled Date:</div>
                        <div class="col-md-8">
                            <i class="far fa-calendar-alt me-1"></i>
                            <span id="modalHearingDate"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Scheduled Time:</div>
                        <div class="col-md-8">
                            <i class="far fa-clock me-1"></i>
                            <span id="modalHearingTime"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Session Type:</div>
                        <div class="col-md-8">
                            <span id="modalSessionType" class="badge bg-secondary px-3 py-2"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Hearing Status:</div>
                        <div class="col-md-8">
                            <span id="modalHearingStatus" class="badge bg-info text-dark px-3 py-2"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold text-secondary">Reading Status:</div>
                        <div class="col-md-8">
                            <span id="modalReadingStatus" class="badge bg-success px-3 py-2"></span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold text-secondary">Remarks:</div>
                        <div class="col-md-8">
                            <div id="modalRemarks" class="p-2 rounded bg-light border" style="min-height:40px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <?php if ($_SESSION['role'] !== 'legislator'): ?>
                    <button type="button" class="btn btn-warning" id="editScheduleBtn">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                    <button type="button" class="btn btn-danger" id="deleteScheduleBtn">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editScheduleForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editScheduleModalLabel">Edit Hearing Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_schedule_id" name="schedule_id">
                    <div class="mb-3">
                        <label for="edit_hearing_date" class="form-label">Hearing Date</label>
                        <input type="date" class="form-control" id="edit_hearing_date" name="hearing_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_hearing_time" class="form-label">Hearing Time</label>
                        <input type="time" class="form-control" id="edit_hearing_time" name="hearing_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_session_type" class="form-label">Session Type</label>
                        <select class="form-select" id="edit_session_type" name="session_type" required>
                            <option value="Regular">Regular</option>
                            <option value="Special">Special</option>
                        </select>
                    </div>
                    <?php if ($_SESSION['role'] !== 'committee'): ?>
                        <div class="mb-3">
                            <label for="reading_status" class="form-label">Reading Status</label>
                            <select class="form-select" id="reading_status" name="reading_status">
                                <option value="">Select Status</option>
                                <option value="Approved">Approved</option>
                                <option value="Deferred">Deferred</option>
                                <option value="Enacted">Enacted</option>
                                <option value="For Amendment">For Amendment</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] !== 'secretary'): ?>
                        <div class="mb-3">
                            <label for="hearing_status" class="form-label">Hearing Status</label>
                            <select class="form-select" id="hearing_status" name="hearing_status">
                                <option value="">Select Hearing Status</option>
                                <option value="1st Hearing">1st Hearing</option>
                                <option value="2nd Hearing">2nd Hearing</option>
                                <option value="3rd Hearing">3rd Hearing</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="edit_remarks" class="form-label">Remarks (optional)</label>
                        <textarea class="form-control" id="edit_remarks" name="remarks" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addScheduleForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Add Hearing Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="proposal_id" class="form-label">Proposal</label>
                        <select class="form-select" id="proposal_id" name="proposal_id" required>
                            <option value="">Select Proposal</option>
                            <?php
                            require_once '../../database/database.php';
                            $conn = getConnection();
                            $sql = "SELECT id, proposal FROM ordinance_proposals ORDER BY proposal";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['proposal']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hearing_date" class="form-label">Hearing Date</label>
                        <input type="date" class="form-control" id="hearing_date" name="hearing_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="hearing_time" class="form-label">Hearing Time</label>
                        <input type="time" class="form-control" id="hearing_time" name="hearing_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="session_type" class="form-label">Session Type</label>
                        <select class="form-select" id="session_type" name="session_type" required>
                            <option value="Regular">Regular</option>
                            <option value="Special">Special</option>
                        </select>
                    </div>
                    <?php if ($_SESSION['role'] !== 'committee'): ?>
                        <div class="mb-3">
                            <label for="reading_status" class="form-label">Reading Status</label>
                            <select class="form-select" id="reading_status" name="reading_status">
                                <option value="">Select Status</option>
                                <option value="Approved">Approved</option>
                                <option value="Deferred">Deferred</option>
                                <option value="Enacted">Enacted</option>
                                <option value="For Amendment">For Amendment</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] !== 'secretary'): ?>
                        <div class="mb-3">
                            <label for="hearing_status" class="form-label">Hearing Status</label>
                            <select class="form-select" id="hearing_status" name="hearing_status">
                                <option value="">Select Hearing Status</option>
                                <option value="1st Hearing">1st Hearing</option>
                                <option value="2nd Hearing">2nd Hearing</option>
                                <option value="3rd Hearing">3rd Hearing</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks (optional)</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Schedule Modal -->
<div class="modal fade" id="deleteScheduleModal" tabindex="-1" aria-labelledby="deleteScheduleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteScheduleForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteScheduleModalLabel">Delete Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delete_schedule_id" name="schedule_id">
                    Are you sure you want to delete this schedule?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
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
                },
                success: function (data) {
                    console.log('FullCalendar events ajax success:', data);
                }
            },
            eventDidMount: function (info) {
                // Set the background color for each event
                if (info.event.extendedProps.color) {
                    info.el.style.backgroundColor = info.event.extendedProps.color;
                    info.el.style.borderColor = info.event.extendedProps.color;
                }
                // Format time with AM/PM in all views (month, week, day)
                var formattedTime = info.event.extendedProps.hearing_time_formatted;
                var titleEl = info.el.querySelector('.fc-event-title');
                if (titleEl && formattedTime) {
                    // Remove any existing time prefix (avoid double time)
                    titleEl.innerHTML = titleEl.innerHTML.replace(/^\s*<span[^>]*>.*?<\/span>\s*-\s*/i, '');
                    titleEl.innerHTML = titleEl.innerHTML;
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
                let hearing = arg.event.extendedProps.hearing_status || '';

                return { html: `<span style="font-weight:600;">${timeStr}</span>-<span>${title}</span> / <span>${hearing}</span>` };
            },
            eventClick: function (info) {
                var event = info.event;
                document.getElementById('modalScheduleId').value = event.id;
                // Use formatted time for modal
                var timeStr = event.extendedProps.hearing_time_formatted || '';
                document.getElementById('modalProposalTitle').textContent = event.title;
                // Set Hearing Status
                document.getElementById('modalHearingStatus').textContent = event.extendedProps.hearing_status || '';
                document.getElementById('modalHearingDate').textContent = event.start ? event.start.toLocaleDateString() : '';
                document.getElementById('modalHearingTime').textContent = timeStr;
                document.getElementById('modalSessionType').textContent = event.extendedProps.session_type || '';
                document.getElementById('modalReadingStatus').textContent = event.extendedProps.reading_status || '';
                document.getElementById('modalRemarks').textContent = event.extendedProps.remarks || '';
                var modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
                modal.show();
            }
        });

        // Log events after they are loaded
        calendar.on('eventsSet', function (events) {
            console.log('FullCalendar eventsSet:', events);
        });

        calendar.render();

        // Store the last selected event globally
        let selectedEvent = null;

        // Set selectedEvent on eventClick and show details modal
        calendar.on('eventClick', function (info) {
            selectedEvent = info.event;
            document.getElementById('modalScheduleId').value = selectedEvent.id;
            document.getElementById('modalProposalTitle').textContent = selectedEvent.title;
            document.getElementById('modalCurrentStatus').textContent = selectedEvent.extendedProps.current_status || '';
            document.getElementById('modalHearingDate').textContent = selectedEvent.start ? selectedEvent.start.toLocaleDateString() : '';
            document.getElementById('modalHearingTime').textContent = selectedEvent.extendedProps.hearing_time_formatted || '';
            document.getElementById('modalSessionType').textContent = selectedEvent.extendedProps.session_type || '';
            document.getElementById('modalReadingStatus').textContent = selectedEvent.extendedProps.reading_status || '';
            document.getElementById('modalRemarks').textContent = selectedEvent.extendedProps.remarks || '';

            // Show/hide the view document button beside the proposal title
            var viewBtn = document.getElementById('viewProposalDocBtn');
            var fileId = (selectedEvent.extendedProps && selectedEvent.extendedProps.file_id) ? selectedEvent.extendedProps.file_id : (selectedEvent.file_id || null);

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

            $('#eventDetailsModal').modal('show');
        });

        // Use jQuery to ensure modals are fully hidden before showing the next
        $('#editScheduleBtn').on('click', function () {
            if (!selectedEvent) return;
            $('#eventDetailsModal').modal('hide');
            $('#eventDetailsModal').one('hidden.bs.modal', function () {
                // Fill edit modal fields
                $('#edit_schedule_id').val(selectedEvent.id);
                $('#edit_current_status').val(selectedEvent.extendedProps.current_status || '');
                $('#edit_hearing_date').val(selectedEvent.start ? selectedEvent.start.toISOString().slice(0, 10) : '');
                if (selectedEvent.extendedProps.hearing_time) {
                    let t = selectedEvent.extendedProps.hearing_time.split(':');
                    $('#edit_hearing_time').val(t[0] + ':' + t[1]);
                }
                $('#edit_session_type').val(selectedEvent.extendedProps.session_type || 'Regular');
                $('#reading_status').val(selectedEvent.extendedProps.reading_status || '');
                $('#hearing_status').val(selectedEvent.extendedProps.hearing_status || '');
                $('#edit_remarks').val(selectedEvent.extendedProps.remarks || '');
                $('#editScheduleModal').modal('show');
                // Remove this handler so it doesn't stack
                $('#eventDetailsModal').off('hidden.bs.modal');
            });
        });

        // Handle add schedule form submit
        document.getElementById('addScheduleForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('add_schedule', true);
            fetch('../../controller/store/schedule_controller.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        $('#scheduleModal').modal('hide');
                        this.reset();
                        calendar.refetchEvents();
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message || 'Failed to add schedule.', 'error');
                    }
                })
                .catch(() => showToast('Error adding schedule.', 'error'));
        });

        // Handle Fill Schedule form submit
        $('#fillScheduleForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('add_schedule', true);

            $.ajax({
                url: '../../controller/store/schedule_controller.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.status === 'success') {
                        $('#ordinanceProposalsTable').DataTable().draw();
                        $('#fillScheduleModal').modal('hide');
                        showToast(result.message, 'success');
                    } else {
                        showToast(result.message || 'Failed to add schedule.', 'error');
                    }
                },
                error: function () {
                    showToast('Error adding schedule.', 'error');
                }
            });
        });

        // Delete button handler (show confirm modal)
        document.getElementById('deleteScheduleBtn').addEventListener('click', function () {
            var scheduleId = document.getElementById('modalScheduleId').value;
            if (!scheduleId) return;
            $('#delete_schedule_id').val(scheduleId);
            $('#deleteScheduleModal').modal('show');
        });

        // Handle delete schedule form submit
        document.getElementById('deleteScheduleForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var scheduleId = document.getElementById('delete_schedule_id').value;
            if (!scheduleId) return;
            fetch('../../controller/store/schedule_controller.php', {
                method: 'POST',
                body: new URLSearchParams({ delete_schedule: true, schedule_id: scheduleId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        $('#deleteScheduleModal').modal('hide');
                        $('#eventDetailsModal').modal('hide');
                        calendar.refetchEvents();
                    } else {
                        showToast(data.message || 'Failed to delete schedule.', 'error');
                    }
                })
                .catch(() => showToast('Error deleting schedule.', 'error'));
        });

        // Edit form submit
        document.getElementById('editScheduleForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('edit_schedule', true);
            fetch('../../controller/store/schedule_controller.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editScheduleModal'));
                        modal.hide();
                        calendar.refetchEvents();
                    } else {
                        showToast(data.message || 'Failed to update schedule.', 'error');
                    }
                }).catch(() => showToast('Error updating schedule.', 'error'));
        });
    });
</script>

<style>
    #calendar {
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
        #calendar {
            max-width: 100%;
            padding: 10px;
        }

        .fc-toolbar-title {
            font-size: 1.3rem;
        }
    }

    @media (max-width: 600px) {
        #calendar {
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

<?php include '../includes/main/footer.php'; ?>

