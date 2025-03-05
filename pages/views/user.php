<?php
include('../includes/main/header.php');
include('../includes/main/navigation.php');
?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-auto">
            <div class="mb-3">
                <h1>User Management</h1>
            </div>
        </div>
        <div class="col">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                    Add User
                </button>
            </div>
        </div>
    </div>

    <!-- <div class="table-responsive">
        <table id=userTable class="table table-hover text-center">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>admin</td>
                    <td>Admin User</td>
                    <td>qLJ8u@example.com</td>
                    <td>Admin</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div> -->

    <div class="table-responsive">
        <table id="usersTable" class="table table-hover text-center">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="needs-validation" id="userForm"
                    novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback">
                            Please choose a username.
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
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a role.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    // $(document).ready(function () {
    //     $('#usersTable').DataTable({
    //         "fnCreatedRow": function (nRow, aData, iDataIndex) {
    //             $(nRow).attr('id', aData[0]);
    //         },
    //         'serverSide': 'true',
    //         'processing': 'true',
    //         'paging': 'true',
    //         'order': [],
    //         'ajax': {
    //             'url': '../../controller/dataTable/usersTable.php',
    //             'type': 'post',
    //         },
    //         "aoColumnDefs": [{
    //             "bSortable": false,
    //             //"aTargets": [8]
    //         },

    //         ]
    //     });
    // });

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
                { "title": "Email" },
                { "title": "Role" },
                { "title": "Actions", "orderable": false }
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
                var res = jQuery.parseJSON(response);
                if (res.status == 'success') {
                    $('#userModal').modal('hide');
                    alert(res.message);
                } else {
                    alert(res.message);
                }
            }
        });
    });
</script>




<?php
include('../includes/main/footer.php');
?>

