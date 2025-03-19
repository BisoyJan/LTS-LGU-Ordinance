<?php
include('../includes/main/header.php');
?>
<style>
    .btn-google-edit:hover {
        background-color: #1a73e8;
        color: white;
    }
</style>
<?php
include('../includes/main/navigation.php');
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-auto">
            <div class="mb-3">
                <h2>Ordinance Status</h2>
            </div>
        </div>

        <table id="ordinanceStatusTable" class="table table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ordinance Title</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- View Status Modal -->
<div class="modal fade" id="viewStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Status history will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" id="proposal_id" name="proposal_id">
                    <div class="mb-3">
                        <label for="action_type" class="form-label">Status</label>
                        <select class="form-select" id="action_type" name="action_type" required>
                            <option value="Draft">Draft</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Pending Approval">Pending Approval</option>
                            <option value="Initial Planning">Initial Planning</option>
                            <option value="Public Comment Period">Public Comment Period</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Implemented">Implemented</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#ordinanceStatusTable').DataTable({
            "fnCreatedRow": function (nRow, aData, iDataIndex) {
                $(nRow).attr('id', aData[0]);
            },
            "ajax": {
                "url": "../../controller/dataTable/ordinanceStatusTable.php",
                "type": "POST",
                "dataSrc": function (json) {
                    if (json.error) {
                        console.error('Server Error:', json.error);
                        showToast('Error: ' + json.error, 'error');
                        return [];
                    }
                    return json.data;
                },
                "error": function (xhr, error, thrown) {
                    console.error('DataTables Ajax Error:', xhr.responseText);
                    showToast('Error loading data: ' + (xhr.responseText || error), 'error');
                }
            },
            "columns": [
                { "title": "ID" },
                { "title": "Ordinance Title" },
                { "title": "Date" },
                { "title": "Status" },
                { "title": "Actions", "orderable": false }
            ],
            "columnDefs": [
                { "width": "5%", "targets": 0 },
                { "width": "30%", "targets": 1 },
                { "width": "15%", "targets": 2 },
                { "width": "20%", "targets": 3 },
                { "width": "30%", "targets": 4 }
            ],
            "serverSide": true,
            "processing": true,
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "language": {
                "processing": '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            }
        });
    });

    // View Status History
    $(document).on('click', '.viewButton', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '../../controller/store/ordinanceStatus_controller.php',
            data: {
                'id': id,
                'fetch_Status': true
            },
            success: function (response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    console.log('Status History Response:', result); // Debug log

                    if (result.status === 'success' && Array.isArray(result.data)) {
                        let historyHtml = '<div class="timeline">';

                        // Show proposal creator info if available
                        if (result.drive_history) {
                            historyHtml += `
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-file-alt me-2"></i>
                                            Ordinance Document
                                        </h6>
                                        <p class="mb-2">
                                            <small class="text-muted">
                                                Created by: ${result.drive_history.creator || 'Unknown'}<br>
                                                Created on: ${new Date(result.drive_history.created_at).toLocaleString()}
                                            </small>
                                        </p>`;

                            // Only show version history if document has been edited
                            if (result.drive_history.last_updated &&
                                result.drive_history.created_at !== result.drive_history.last_updated) {
                                historyHtml += `
                                    <div class="card mt-2">
                                        <div class="card-body">
                                            <h6 class="card-subtitle">
                                                <i class="fas fa-history me-2"></i>
                                                Latest Edit
                                            </h6>
                                            <p class="mb-2">
                                                <small class="text-muted">
                                                    Last edited: ${new Date(result.drive_history.last_updated).toLocaleString()}<br>
                                                    By: ${result.drive_history.last_editor || 'Unknown'}
                                                </small>
                                            </p>
                                        </div>
                                    </div>`;
                            }

                            // Only show active document buttons if there is status history
                            const hasStatus = result.data && result.data.length > 0;
                            historyHtml += `
                                <div class="btn-group mt-2">
                                    <a href="${result.drive_history.view_url}" target="_blank" 
                                       class="btn btn-sm btn-primary" title="View document">
                                        <i class="fas fa-eye me-1"></i>
                                        View Document
                                    </a>
                                    ${hasStatus ? `
                                        <a href="${result.drive_history.revision_url}" target="_blank" 
                                           class="btn btn-sm btn-warning" title="Suggest edits">
                                            <i class="fas fa-edit me-1"></i>
                                            Suggest Edit
                                        </a>
                                    ` : `
                                        <button class="btn btn-sm btn-warning disabled" title="Status update required to suggest edits">
                                            <i class="fas fa-edit me-1"></i>
                                            Suggest Edit
                                        </button>
                                    `}
                                </div>
                            </div>
                        </div>`;
                        }

                        // Add status history
                        if (result.data.length === 0) {
                            historyHtml += '<div class="alert alert-info">No status updates available yet.</div>';
                        } else {
                            result.data.forEach(function (item) {
                                const statusBadge = `<span class="badge bg-${getStatusColor(item.action_type)}">${item.action_type}</span>`;
                                historyHtml += `
                                    <div class="timeline-item border-left border-4 border-${getStatusColor(item.action_type)} ps-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-1">${statusBadge}</h6>
                                            <small class="text-muted">${new Date(item.action_date).toLocaleString()}</small>
                                        </div>
                                        <p class="mb-0">${item.remarks || 'No remarks'}</p>
                                        <small class="text-muted">Added by: ${item.added_by}</small>
                                    </div>`;
                            });
                        }

                        historyHtml += '</div>';
                        $('#viewStatusModal .modal-body').html(historyHtml);
                        $('#viewStatusModal').modal('show');
                    } else {
                        showToast(result.message || 'Failed to fetch status history', 'error');
                    }
                } catch (e) {
                    console.error('Parse Error:', e);
                    showToast('Error parsing status history', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
                showToast('Failed to fetch status history', 'error');
            }
        });
    });

    // Update Status Button Click
    $(document).on('click', '.updateStatusButton', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $('#proposal_id').val(id);

        // Fetch current status data
        $.ajax({
            type: 'POST',
            url: '../../controller/store/ordinanceStatus_controller.php',
            data: {
                'id': id,
                'get_latest_status': true
            },
            success: function (response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.status === 'success') {
                        // Populate form with current data
                        $('#action_type').val(result.data.action_type);
                        $('#remarks').val(result.data.remarks);
                        $('#updateStatusModal').modal('show');
                    } else {
                        // If no status exists, just show empty form
                        $('#action_type').val('');
                        $('#remarks').val('');
                        $('#updateStatusModal').modal('show');
                    }
                } catch (e) {
                    console.error('Parse Error:', e);
                    showToast('Error fetching status data', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
                showToast('Failed to fetch status data', 'error');
            }
        });
    });

    // Update Status Form Submit
    $('#updateStatusForm').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('update_status', true);

        $.ajax({
            url: '../../controller/store/ordinanceStatus_controller.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.status === 'success') {
                        $('#updateStatusModal').modal('hide');
                        $('#ordinanceStatusTable').DataTable().ajax.reload();
                        showToast(result.message, 'success');
                    } else {
                        showToast(result.message || 'Unknown error occurred', 'error');
                    }
                } catch (e) {
                    console.error('Response parsing error:', e);
                    showToast('Invalid server response', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
                showToast(error, 'error');
            }
        });
    });

    function getStatusColor(status) {
        switch (status) {
            case 'Draft': return 'secondary';
            case 'Under Review': return 'info';
            case 'Pending Approval': return 'warning';
            case 'Initial Planning': return 'primary';
            case 'Public Comment Period': return 'info';
            case 'Approved': return 'success';
            case 'Rejected': return 'danger';
            case 'Implemented': return 'dark';
            default: return 'secondary';
        }
    }

</script>

<?php
include('../includes/main/footer.php');
?>

