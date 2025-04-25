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
                <h2>Ordinance Proposal</h2>
            </div>
        </div>
        <div class="col">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#proposalModal"
                    onclick="formIDChangeAdd()">
                    Add Proposal
                </button>
            </div>
        </div>
    </div>

    <div class="row mb-3 align-items-end">
        <div class="col-md-3">
            <label for="filterCommittee" class="form-label">Filter by Committee</label>
            <select class="form-select" id="filterCommittee">
                <option value="">All Committees</option>
                <?php
                require_once '../../database/database.php';
                $conn = getConnection();
                $query = "SELECT id, name FROM committees ORDER BY name";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="filterFromDate" class="form-label">From Date</label>
            <input type="date" class="form-control" id="filterFromDate">
        </div>
        <div class="col-md-2">
            <label for="filterToDate" class="form-label">To Date</label>
            <input type="date" class="form-control" id="filterToDate">
        </div>
        <div class="col-md-5 d-flex justify-content-end">
            <button class="btn btn-primary me-2" id="applyFilters">Apply Filters</button>
            <button class="btn btn-secondary" id="clearFilters">Clear Filters</button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="ordinanceProposalsTable" class="table table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proposal</th>
                    <th>Date</th>
                    <th>Details</th>
                    <th>File</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will populate this -->
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="proposalModal" tabindex="-1" aria-labelledby="proposalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proposalModalLabel">Add Proposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="proposalForm" class="needs-validation" novalidate>
                        <input type="hidden" id="proposalID" name="proposalID" value="">
                        <div class="mb-3">
                            <label for="proposal" class="form-label">Proposal</label>
                            <input type="text" class="form-control" id="proposal" name="proposal" required>
                            <div class="invalid-feedback">
                                Please enter a proposal title.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="proposalDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="proposalDate" name="proposalDate" required>
                            <div class="invalid-feedback">
                                Please select a date.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="details" class="form-label">Details</label>
                            <textarea class="form-control" id="details" name="details" rows="3" required></textarea>
                            <div class="invalid-feedback">
                                Please provide proposal details.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="committee" class="form-label">Committee</label>
                            <select class="form-select" id="committee" name="committee_id" required>
                                <option value="">Select Committee</option>
                                <?php
                                require_once '../../database/database.php';
                                try {
                                    $conn = getConnection();
                                    if ($conn) {
                                        $query = "SELECT id, name FROM committees ORDER BY name";
                                        $result = mysqli_query($conn, $query);
                                        if ($result) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>Error loading committees</option>";
                                        }
                                    } else {
                                        echo "<option value=''>Database connection failed</option>";
                                    }
                                } catch (Exception $e) {
                                    echo "<option value=''>Error: " . htmlspecialchars($e->getMessage()) . "</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">
                                Please select a committee.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">File</label>
                            <input class="form-control" type="file" id="file" name="file">
                            <div class="invalid-feedback">
                                Please upload a file.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="proposalDeleteModal" tabindex="-1" aria-labelledby="proposalDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="proposalDeleteModalLabel">Delete Proposal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="deleteID" name="deleteID" value="">
                        Are you sure you want to delete this proposal?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger" id="deleteProposalButton">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Replace the proposalViewCard with this modal -->
    <div class="modal fade" id="viewProposalModal" tabindex="-1" aria-labelledby="viewProposalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewProposalModalLabel">View Proposal Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Proposal:</div>
                        <div class="col-md-9">
                            <div class="text-wrap" id="viewProposal"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Date:</div>
                        <div class="col-md-9" id="viewProposalDate"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Details:</div>
                        <div class="col-md-9">
                            <div id="viewDetails" class="text-wrap"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">File:</div>
                        <div class="col-md-9">
                            <div id="viewFile" class="d-flex align-items-center text-wrap">
                                <span class="file-name me-2"></span>
                                <a href="#" class="btn btn-sm btn-primary view-file-btn" style="display:none;">
                                    <i class="fas fa-eye me-1"></i> View File
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Add Status Modal -->
<div class="modal fade" id="proposalStatusModal" tabindex="-1" aria-labelledby="proposalStatusModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proposalStatusModalLabel">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="statusProposalId" name="proposal_id">
                    <div class="mb-3">
                        <label for="action_type" class="form-label">Action Type</label>
                        <select class="form-select" id="action_type" name="action_type" required>
                            <option value="">Select action</option>
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
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fill for Schedule Modal -->
<div class="modal fade" id="fillScheduleModal" tabindex="-1" aria-labelledby="fillScheduleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="fillScheduleForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="fillScheduleModalLabel">Fill Schedule for Proposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="schedule_proposal_id" name="proposal_id">
                    <div class="mb-3">
                        <label for="schedule_current_status" class="form-label">Current Status</label>
                        <input type="text" class="form-control" id="schedule_current_status" name="current_status"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="schedule_hearing_date" class="form-label">Hearing Date</label>
                        <input type="date" class="form-control" id="schedule_hearing_date" name="hearing_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="schedule_hearing_time" class="form-label">Hearing Time</label>
                        <input type="time" class="form-control" id="schedule_hearing_time" name="hearing_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="schedule_session_type" class="form-label">Session Type</label>
                        <select class="form-select" id="schedule_session_type" name="session_type" required>
                            <option value="Regular">Regular</option>
                            <option value="Special">Special</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="schedule_reading_result" class="form-label">Reading Result</label>
                        <select class="form-select" id="schedule_reading_result" name="reading_result" required>
                            <option value="">Select Result</option>
                            <option value="Approved">Approved</option>
                            <option value="Deferred">Deferred</option>
                            <option value="For Amendment">For Amendment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="schedule_remarks" class="form-label">Remarks (optional)</label>
                        <textarea class="form-control" id="schedule_remarks" name="remarks" rows="2"></textarea>
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

<style>
    .modal-body .text-wrap {
        word-wrap: break-word;
        white-space: pre-wrap;
        max-height: 200px;
        overflow-y: auto;
    }

    .table td {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #viewDetails {
        white-space: pre-wrap;
        word-wrap: break-word;
        max-height: 300px;
        overflow-y: auto;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 4px;
    }
</style>

<script>
    $(document).ready(function () {
        const table = $('#ordinanceProposalsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "../../controller/dataTable/ordinanceProposalTable.php",
                "type": "POST",
                "data": function (d) {
                    d.committee = $('#filterCommittee').val();
                    d.fromDate = $('#filterFromDate').val();
                    d.toDate = $('#filterToDate').val();
                },
                "dataSrc": function (json) {
                    // If your backend returns { data: [...] }
                    if (json.data) return json.data;
                    // If your backend returns an array directly
                    return json;
                },
                "error": function (xhr, error, thrown) {
                    console.error('DataTables Ajax Error:', xhr, error, thrown);
                    alert('Error loading ordinance proposal data');
                }
            },
            "columns": [
                { "data": 0, "title": "ID" },
                { "data": 1, "title": "Proposal" },
                { "data": 2, "title": "Date" },
                { "data": 3, "title": "Details" },
                { "data": 4, "title": "File" },
                { "data": 5, "title": "Actions", "orderable": false }
            ],
            "order": [[0, "desc"]],
            "lengthChange": true,
            "searching": true,
            "paging": true,
            "info": true,
            "autoWidth": false
        });

        $('#applyFilters').on('click', function () {
            table.ajax.reload();
        });

        $('#clearFilters').on('click', function () {
            $('#filterCommittee').val('');
            $('#filterFromDate').val('');
            $('#filterToDate').val('');
            table.ajax.reload();
        });

        // Show Fill Schedule Modal and set proposal_id
        $(document).on('click', '.fillScheduleBtn', function () {
            var proposalId = $(this).data('id');
            $('#schedule_proposal_id').val(proposalId);
            $('#fillScheduleForm')[0].reset();
            $('#fillScheduleModal').modal('show');
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
    });

    // Function to view file in new window or tab
    function viewFile(filePath) {
        if (filePath) {
            window.open(filePath, '_blank');
        }
    }

    // Function to change modal title and form action for adding proposal
    function formIDChangeAdd() {
        $("#proposalModalLabel").text('Add Proposal');
        $("#proposalForm")[0].reset();
        $("#proposalID").val(''); // Clear the ID
        $("#file").prop('required', true);
    }

    // Function to create a proposal
    $(document).on('submit', '#proposalForm', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        var isEdit = $('#proposalID').val() !== '';

        formData.append(isEdit ? 'edit_ordinanceProposal' : 'create_ordinanceProposal', true);

        showLoading();

        $.ajax({
            url: '../../controller/store/ordinanceProposal_controller.php',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.status === 'success') {
                        $('#proposalModal').modal('hide');
                        $('#ordinanceProposalsTable').DataTable().draw();
                        $('#proposalForm')[0].reset();
                        $('#proposalID').val(''); // Clear the ID after successful submission
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
                hideLoading();
                console.error('Ajax Error:', error);
                showToast(error, 'error');
            }
        });
    });

    $(document).on('click', '.editButton', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

        // Change modal title
        $("#proposalModalLabel").text('Edit Proposal');
        $("#file").prop('required', false);

        $.ajax({
            type: 'POST',
            url: '../../controller/store/ordinanceProposal_controller.php',
            data: {
                'id': id,
                'fetch_Proposal': true
            },
            success: function (response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.status === 'success') {
                        $('#proposalModal').modal('show');
                        $('#proposalID').val(result.data.id);
                        $('#proposal').val(result.data.proposal);
                        $('#proposalDate').val(result.data.proposal_date);
                        $('#details').val(result.data.details);
                        $('#committee').val(result.data.committee_id);
                    } else {
                        showToast(result.message || 'Failed to fetch proposal details', 'error');
                    }
                } catch (e) {
                    console.error('Response parsing error:', e);
                    showToast('Invalid server response', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
                showToast('Failed to fetch proposal details', 'error');
            }
        });
    });

    $(document).on('click', '.deleteButton', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        console.log("Delete ID: ", id);
        $('#proposalDeleteModal').modal('show');
        $('#deleteID').val(id);
    });

    $(document).on('submit', '#deleteForm', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('delete_ordinanceProposal', true);

        showLoading(); // Show loading animation

        $.ajax({
            type: 'POST',
            url: '../../controller/store/ordinanceProposal_controller.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading(); // Hide loading animation
                try {
                    // Parse the response if it's a string
                    const result = typeof response === 'string' ? JSON.parse(response) : response;

                    if (result.status === 'success') {
                        $('#proposalDeleteModal').modal('hide');
                        $('#ordinanceProposalsTable').DataTable().draw();
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
                console.error('Ajax Error:', status, error);
                showToast(error, 'error');
            }
        });
    });

    // Update the viewButton click handler
    $(document).on('click', '.viewButton', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '../../controller/store/ordinanceProposal_controller.php',
            data: {
                'id': id,
                'fetch_Proposal': true
            },
            success: function (response) {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.status === 'success') {
                    $('#viewProposal').text(result.data.proposal);
                    $('#viewProposalDate').text(new Date(result.data.proposal_date).toLocaleDateString());
                    $('#viewDetails').text(result.data.details);

                    // Update file display with Google Docs viewer
                    if (result.data.file_name && result.data.file_path) {
                        const fileIcon = getFileIconClass(result.data.file_name);
                        const googleDocsUrl = "https://docs.google.com/document/d/" + result.data.file_path + "/preview";
                        $('#viewFile').html(`
                            <div class="d-flex align-items-center">
                                <i class="${fileIcon} me-2"></i>
                                <span class="file-name me-2">${result.data.file_name}</span>
                                <a href="${googleDocsUrl}" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-eye me-1"></i> View in Google Docs
                                </a>
                            </div>
                        `);
                    } else {
                        $('#viewFile').html('<span class="text-muted">No file attached</span>');
                    }

                    $('#viewProposalModal').modal('show');
                } else {
                    showToast(result.message || 'Failed to fetch proposal details', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
                showToast('Failed to fetch proposal details', 'error');
            }
        });
    });

    $(document).on('click', '[data-bs-target="#proposalStatusModal"]', function (e) {
        e.preventDefault(); // Prevent default action
        e.stopPropagation(); // Stop event propagation

        const proposalId = $(this).data('id');

        // Check status before showing modal
        $.ajax({
            url: '../../controller/store/ordinanceStatus_controller.php',
            type: 'POST',
            data: {
                check_status: true,
                proposal_id: proposalId
            },
            success: function (response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (!result.exists) {
                        if (result.status === 'error') {
                            showToast(result.message, 'error');
                            return;
                        }
                        $('#statusProposalId').val(proposalId);
                        $('#proposalStatusModal').modal('show');
                    } else {
                        // Show detailed information about existing status
                        const statusInfo = `Status: ${result.data.action_type}\n` +
                            `Remarks: ${result.data.remarks}\n` +
                            `Added by: ${result.data.added_by}\n` +
                            `Date: ${new Date(result.data.added_on).toLocaleString()}`;

                        Swal.fire({
                            title: 'Status Already Exists',
                            html: statusInfo.replace(/\n/g, '<br>'),
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    showToast('Error checking status', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
                showToast('Error checking status', 'error');
            }
        });
    });

    // Add this new event handler for the status form submission
    $(document).on('submit', '#statusForm', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('add_status', true);

        $.ajax({
            url: '../../controller/store/ordinanceStatus_controller.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                $('#proposalStatusModal').modal('hide');
                showToast(result.message, result.status);
                if (result.status === 'success') {
                    $('#ordinanceProposalsTable').DataTable().draw();
                }
            },
            error: function (xhr, status, error) {
                showToast('Error updating status', 'error');
            }
        });
    });

    // Add this helper function for file icons
    function getFileIconClass(fileName) {
        const ext = fileName.split('.').pop().toLowerCase();
        switch (ext) {
            case 'doc':
            case 'docx': return 'fas fa-file-word text-primary';
            default: return 'fas fa-file text-secondary';
        }
    }

    function getStatusColor(status) {
        switch (status) {
            case 'Draft': return 'secondary';
            case 'Under Review': return 'info';
            case 'Pending Approval': return 'warning';
            case 'Initial Planning': return 'primary';
            case 'Public Comment Period': return 'dark';
            case 'Approved': return 'success';
            case 'Rejected': return 'danger';
            case 'Implemented': return 'success';
            default: return 'secondary';
        }
    }
</script>

<?php include '../includes/main/footer.php'; ?>

