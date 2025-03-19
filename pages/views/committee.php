<?php
include '../includes/main/header.php';
include '../includes/main/navigation.php';
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-auto">
            <div class="mb-3">
                <h2>Committees</h2>
            </div>
        </div>
        <div class="col">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#committeeModal">
                    Add Committee
                </button>
            </div>
        </div>
    </div>

    <table id="committeesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Add/Edit Committee Modal -->
<div class="modal fade" id="committeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Committee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="committeeForm">
                <div class="modal-body">
                    <input type="hidden" id="committeeId" name="committeeId">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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

<!-- Delete Committee Modal -->
<div class="modal fade" id="deleteCommitteeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Committee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this committee?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let table = $('#committeesTable').DataTable({
            "ajax": "../../controller/dataTable/committeeTable.php",
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "description" },
                { "data": "created_at" },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        return `
                        <button class="btn btn-primary btn-sm editBtn" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    }
                }
            ]
        });

        $('#committeeForm').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            let id = $('#committeeId').val();
            formData.append(id ? 'update_committee' : 'create_committee', true);

            $.ajax({
                url: '../../controller/store/committee_controller.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    let result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.status === 'success') {
                        $('#committeeModal').modal('hide');
                        table.ajax.reload();
                        showToast(result.message, 'success');
                    } else {
                        showToast(result.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    showToast('An error occurred', 'error');
                }
            });
        });

        $(document).on('click', '.editBtn', function () {
            let id = $(this).data('id');
            $.ajax({
                url: '../../controller/store/committee_controller.php',
                type: 'POST',
                data: { fetch_committee: true, id: id },
                success: function (response) {
                    let result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.status === 'success') {
                        $('#committeeId').val(result.data.id);
                        $('#name').val(result.data.name);
                        $('#description').val(result.data.description);
                        $('.modal-title').text('Edit Committee');
                        $('#committeeModal').modal('show');
                    }
                }
            });
        });

        let deleteId = null;
        $(document).on('click', '.deleteBtn', function () {
            deleteId = $(this).data('id');
            $('#deleteCommitteeModal').modal('show');
        });

        $('#confirmDelete').click(function () {
            if (deleteId) {
                $.ajax({
                    url: '../../controller/store/committee_controller.php',
                    type: 'POST',
                    data: { delete_committee: true, id: deleteId },
                    success: function (response) {
                        let result = typeof response === 'string' ? JSON.parse(response) : response;
                        $('#deleteCommitteeModal').modal('hide');
                        if (result.status === 'success') {
                            table.ajax.reload();
                            showToast(result.message, 'success');
                        } else {
                            showToast(result.message, 'error');
                        }
                    }
                });
            }
        });

        $('#committeeModal').on('hidden.bs.modal', function () {
            $('#committeeForm')[0].reset();
            $('#committeeId').val('');
            $('.modal-title').text('Add Committee');
        });
    });
</script>

<?php include '../includes/main/footer.php'; ?>

