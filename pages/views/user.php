<?php
include '../includes/main/header.php';

require_once('../../scripts/role_authenticator.php');
restrictAccess('legislator');

include '../includes/main/navigation.php';

// Fetch committees for dropdown
require_once('../../database/database.php');
$conn = getConnection();
$committees = [];
$result = $conn->query("SELECT id, name FROM committees ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $committees[] = $row;
}
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-auto">
            <div class="mb-3">
                <h2>User Management</h2>
            </div>
        </div>
        <div class="col">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal"
                    onclick="formIDChangeAdd()">
                    Add User
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="usersTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Committee</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel"> User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" class="needs-validation" id="userForm" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="hidden" name="id" id="id" value="">
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback">
                            Please choose a username.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">
                            Please provide a name.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Please provide a valid email.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">
                            Please provide a password.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required onchange="toggleCommitteeDropdown()">
                            <option value="">Select Role</option>
                            <option value="mayor">Mayor</option>
                            <option value="legislator">Legislator</option>
                            <option value="admin">Administrator</option>
                            <option value="committee">Committee</option>
                            <option value="secretary">Secretary</option>
                            <option value="user">User</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a role.
                        </div>
                    </div>
                    <div class="mb-3" id="committeeDropdownDiv" style="display:none;">
                        <label for="committee_id" class="form-label">Committee</label>
                        <select class="form-select" id="committee_id" name="committee_id">
                            <option value="">Select Committee</option>
                            <?php foreach ($committees as $committee): ?>
                                <option value="<?= htmlspecialchars($committee['id']) ?>">
                                    <?= htmlspecialchars($committee['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a committee.
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

<div class="modal fade" id="userDeleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="deleteID" id="deleteID" value="">
                    <p>Are you sure you want to delete this user?</p>
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
    $(document).ready(function () {
        $('#usersTable').DataTable({
            "fnCreatedRow": function (nRow, aData, iDataIndex) {
                $(nRow).attr('id', aData[0]);
            },
            "ajax": {
                "url": "../../controller/dataTable/usersTable.php",
                "type": "POST",
                "dataSrc": "data", // Ensure DataTables knows where to look
                "error": function (xhr, error, thrown) {
                    console.error('DataTables Ajax Error:', xhr, error, thrown);
                    alert('Error loading user data');
                }
            },
            "columns": [
                { "title": "ID" },
                { "title": "Username" },
                { "title": "Name" },
                { "title": "Email" },
                { "title": "Role" },
                { "title": "Committee" },
                { "title": "Actions", "orderable": false }
            ],
            "serverSide": true,
            "processing": true,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "language": {
                "processing": '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            }
        });
    });

    function toggleCommitteeDropdown() {
        var role = $('#role').val();
        if (role === 'legislator' || role === 'committee') {
            $('#committeeDropdownDiv').show();
            $('#committee_id').attr('required', true);
        } else {
            $('#committeeDropdownDiv').hide();
            $('#committee_id').val('');
            $('#committee_id').attr('required', false);
        }
    }

    // Ensure dropdown is correct when editing
    function formIDChangeAdd() {
        $("form").attr('id', 'userForm')
        $('#userForm')[0].reset();
        var label = document.getElementById('userModalLabel');
        label.innerHTML = "Add User";
        $('#committeeDropdownDiv').hide();
        $('#committee_id').val('');
        $('#committee_id').attr('required', false);
    };

    function formIDChangeEdit() {
        $("form").attr('id', 'userUpdateForm')
        var label = document.getElementById('userModalLabel');
        label.innerHTML = "Edit User";
    };

    $(document).on('submit', '#userForm', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('create_User', true);

        $.ajax({
            type: 'POST',
            url: '../../controller/store/user_controller.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                try {
                    var res = jQuery.parseJSON(response);
                    if (res.status == 'success') {
                        $('#userModal').modal('hide');
                        mytable = $('#usersTable').DataTable();
                        mytable.draw();
                        showToast(res.message, 'success');
                    } else if (res.status == 'warning') {
                        showToast(res.message, 'warning');
                    } else {
                        showToast(res.message, 'danger');
                    }
                } catch (error) {
                    console.error("Error parsing JSON: ", error, response);
                    showToast("Invalid response from server", "danger");
                }
            }
        });
    });

    $(document).on('click', '.editButton', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '../../controller/store/user_controller.php',
            data: {
                'id': id,
                'fetch_User': true
            },
            success: function (response) {
                try {
                    var res = jQuery.parseJSON(response);
                    console.log("Parsed Response: ", res.data); // Logs parsed JSON response

                    if (res.status === 'success') {

                        $('#userModal').modal('show');

                        $('#id').val(res.data.id);
                        $('#username').val(res.data.username);
                        $('#name').val(res.data.name || '');
                        $('#email').val(res.data.email);
                        $('#role').val(res.data.role);
                        toggleCommitteeDropdown();
                        $('#committee_id').val(res.data.committee_id || '');

                        console.log("After setting - Username field value:", $('#username').val());

                    } else {
                        showToast(res.message, 'danger');
                    }
                } catch (error) {
                    console.error("Error parsing JSON: ", error, response);
                    showToast("Invalid response from server", "danger");
                }
            },
            error: function (xhr, status, error) {
                console.error('Ajax Error:', error);
                showToast('Failed to fetch proposal details', 'danger');
            }
        });
    });

    $(document).on('submit', '#userUpdateForm', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('update_User', true);

        $.ajax({
            type: 'POST',
            url: '../../controller/store/user_controller.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                try {
                    var res = jQuery.parseJSON(response);
                    if (res.status == 'success') {
                        $('#userModal').modal('hide');
                        mytable = $('#usersTable').DataTable();
                        mytable.draw();
                        showToast(res.message, 'success');
                    } else if (res.status == 'warning') {
                        showToast(res.message, 'warning');
                    } else {
                        showToast(res.message, 'danger');
                    }
                } catch (error) {
                    console.error("Error parsing JSON: ", error, response);
                    showToast("Invalid response from server", "danger");
                }
            }
        });
    });

    $(document).on('click', '.deleteButton', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        //console.log("Delete ID: ", id);
        $('#userDeleteModal').modal('show');
        $('#deleteID').val(id);
    })

    $(document).on('submit', '#deleteForm', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('delete_User', true);

        $.ajax({
            type: 'POST',
            url: '../../controller/store/user_controller.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                try {
                    var res = jQuery.parseJSON(response);
                    if (res.status == 'success') {
                        $('#userDeleteModal').modal('hide');
                        mytable = $('#usersTable').DataTable();
                        mytable.draw();
                        showToast(res.message, 'success');
                    } else if (res.status == 'warning') {
                        showToast(res.message, 'warning');
                    } else {
                        showToast(res.message, 'danger');
                    }
                } catch (error) {
                    console.error("Error parsing JSON: ", error, response);
                    showToast("Invalid response from server", "danger");
                }
            }
        });
    });
</script>

<?php include('../includes/main/footer.php'); ?>

