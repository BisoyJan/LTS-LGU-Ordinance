<?php
include '../includes/main/header.php';
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
        <tbody></tbody>
    </table>

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
                            <label for="file" class="form-label">File</label>
                            <input class="form-control" type="file" id="file" name="file" required>
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
                        <div class="col-md-9" id="viewProposal"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Date:</div>
                        <div class="col-md-9" id="viewProposalDate"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Details:</div>
                        <div class="col-md-9" id="viewDetails"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">File:</div>
                        <div class="col-md-9">
                            <div id="viewFile" class="d-flex align-items-center">
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

<script>
    $(document).ready(function () {
        $('#ordinanceProposalsTable').DataTable({
            "fnCreatedRow": function (nRow, aData, iDataIndex) {
                $(nRow).attr('id', aData[0]);
            },
            "ajax": {
                "url": "../../controller/dataTable/ordinanceProposalTable.php",
                "type": "POST",
                "dataSrc": "data", // Ensure DataTables knows where to look
                "error": function (xhr, error, thrown) {
                    console.error('DataTables Ajax Error:', xhr, error, thrown);
                    alert('Error loading ordinance proposal data');
                }
            },
            "columns": [
                { "title": "ID" },
                { "title": "Proposal" },
                { "title": "Date" },
                { "title": "Details" },
                { "title": "File", "orderable": true },
                { "title": "Actions", "orderable": false }
            ],
            "columnDefs": [
                { "width": "5%", "targets": 0 },
                { "width": "15%", "targets": 1 },
                { "width": "10%", "targets": 2 },
                { "width": "25%", "targets": 3 },
                { "width": "15%", "targets": 4 },
                { "width": "20%", "targets": 5 }
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


    // Function to view file in new window or tab
    function viewFile(filePath) {
        if (filePath) {
            window.open(filePath, '_blank');
        }
    }

    // Function to change modal title and form action for adding proposal
    function formIDChangeAdd() {
        $("form").attr('id', 'proposalForm')
        $("#proposalModalLabel").text('Add Proposal');
        $("#proposalForm")[0].reset();
    }

    // Function to change modal title and form action for editing proposal
    function formIDChangeEdit() {
        $("form").attr('id', 'editProposalForm')
        $("#proposalModalLabel").text('Edit Proposal');
    }

    // Function to create a proposal
    $(document).on('submit', '#proposalForm', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('create_ordinanceProposal', true);

        showLoading(); // Show loading animation

        $.ajax({
            url: '../../controller/store/ordinanceProposal_controller.php',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading(); // Hide loading animation
                try {
                    // Parse the response if it's a string
                    const result = typeof response === 'string' ? JSON.parse(response) : response;

                    if (result.status === 'success') {
                        $('#proposalModal').modal('hide');
                        $('#ordinanceProposalsTable').DataTable().draw();
                        $('#proposalForm')[0].reset();
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
                hideLoading(); // Hide loading animation
                console.error('Ajax Error:', status, error);
                showToast(error, 'error');
            }
        });
    });

    $(document).on('click', '.editButton', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '../../controller/store/ordinanceProposal_controller.php',
            data: {
                'id': id,
                'fetch_Proposal': true
            },
            dataType: 'json', // Add this to automatically parse JSON
            success: function (response) {
                // Remove the manual parsing since response is already JSON
                if (response.status === 'success') {
                    $('#proposalModal').modal('show');
                    $('#proposalID').val(response.data.id);
                    $('#proposal').val(response.data.proposal);
                    $('#proposalDate').val(response.data.proposal_date);
                    $('#details').val(response.data.details);
                    $('#file').val(response)
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
                showToast('Failed to fetch proposal details', 'danger');
            }
        });
    });

    $(document).on('submit', '#editProposalForm', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('edit_ordinanceProposal', true);

        showLoading(); // Show loading animation

        $.ajax({
            url: '../../controller/store/ordinanceProposal_controller.php',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading(); // Hide loading animation
                try {
                    // Parse the response if it's a string
                    const result = typeof response === 'string' ? JSON.parse(response) : response;

                    if (result.status === 'success') {
                        $('#proposalModal').modal('hide');
                        $('#ordinanceProposalsTable').DataTable().draw();
                        $('#editProposalForm')[0].reset();
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
                hideLoading(); // Hide loading animation
                console.error('Ajax Error:', status, error);
                showToast(error, 'error');
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

<?php
include '../includes/main/footer.php';
?>

