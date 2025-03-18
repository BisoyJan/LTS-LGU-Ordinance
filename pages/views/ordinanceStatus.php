<?php
include('../includes/main/header.php');
include('../includes/main/navigation.php');
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-auto">
            <div class="mb-3">
                <h2>Ordinance Status</h2>
            </div>
        </div>
        <!-- <div class="col">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#ordinanceStatusModal" onclick="formIDChangeAdd()">
                    
                </button>
            </div>
        </div> -->

        <table id="ordinanceStatusTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Ordinance Number</th>
                    <th>Ordinance Title</th>
                    <th>Ordinance Date</th>
                    <th>Ordinance Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>

        </table>
    </div>



</div>

<?php
include('../includes/main/footer.php');
?>

