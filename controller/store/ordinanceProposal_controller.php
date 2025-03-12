<?php

require '../../database/db.php';

header('Content-Type: application/json');

if (isset($_POST['fetch_Proposal'])) {
    $proposal_id = mysqli_real_escape_string($conn, $_POST['id']);

    $sql = "SELECT id, proposal, proposal_date, details, status, file_name FROM ordinance_proposals WHERE id = '$proposal_id'";

    $query_run = mysqli_query($conn, $sql);

    try {
        $data = $query_run->fetch_assoc();
        $res = [
            'status' => $data ? 'success' : 'warning',
            'data' => $data ?? [],
            'message' => $data ? '' : 'User ID not found'
        ];
    } catch (mysqli_sql_exception $e) {
        $res = [
            'status' => 'error',
            'message' => 'Error fetching proposal: ' . $e->getMessage()
        ];
    }

    echo json_encode($res);
    return;
}

if (isset($_POST['create_ordinanceProposal'])) {
    try {
        $proposal = mysqli_real_escape_string($conn, $_POST['proposal']);
        $proposalDate = mysqli_real_escape_string($conn, $_POST['proposalDate']);
        $details = mysqli_real_escape_string($conn, $_POST['details']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        // Handle file upload if present
        $fileDestination = null;
        $fileNameNew = null;
        $fileTypeEnum = null;
        $fileSizeBytes = 0;

        if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload failed with error code: ' . $file['error']);
            }

            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Check if file with same name exists in database
            $checkQuery = "SELECT file_name FROM ordinance_proposals WHERE file_name = '$fileName'";
            $result = $conn->query($checkQuery);

            if ($result && $result->num_rows > 0) {
                throw new Exception('A file with the same name already exists. Please rename your file.');
            }

            $uploadDir = '../../assets/file/ordinance_proposals/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileDestination = $uploadDir . $fileName;

            if (!move_uploaded_file($fileTmpName, $fileDestination)) {
                throw new Exception('Failed to move uploaded file');
            }

            $fileTypeEnum = $fileExt;
            $fileSizeBytes = $fileSize;
        }

        // Insert query
        $query = "INSERT INTO ordinance_proposals (proposal, proposal_date, details, status, file_name, file_path, file_type, file_size) VALUES ('$proposal', '$proposalDate', '$details', '$status', '$fileName', '$fileDestination', '$fileTypeEnum', $fileSizeBytes)";

        if ($conn->query($query)) {
            $response = array(
                'status' => 'success',
                'message' => 'Ordinance proposal created successfully'
            );
            echo json_encode($response);
        } else {
            throw new Exception("Database error: " . $conn->error);
        }
    } catch (Exception $e) {
        $response = array(
            'status' => 'error',
            'message' => $e->getMessage()
        );
        echo json_encode($response);
    }
}

if (isset($_POST['edit_ordinanceProposal'])) {
    try {
        $proposal_id = mysqli_real_escape_string($conn, $_POST['proposalID']);
        $proposal = mysqli_real_escape_string($conn, $_POST['proposal']);
        $proposalDate = mysqli_real_escape_string($conn, $_POST['proposalDate']);
        $details = mysqli_real_escape_string($conn, $_POST['details']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        // Get existing file information
        $fileQuery = "SELECT file_name, file_path FROM ordinance_proposals WHERE id = '$proposal_id'";
        $fileResult = $conn->query($fileQuery);
        $existingFile = $fileResult->fetch_assoc();

        $fileUpdate = "";

        if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['file'];

            // Check if uploaded file is different from existing file
            if ($existingFile['file_name'] !== $file['name']) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('File upload failed with error code: ' . $file['error']);
                }

                $fileName = $file['name'];
                $fileTmpName = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // Delete existing file if it exists
                if ($existingFile['file_path'] && file_exists($existingFile['file_path'])) {
                    unlink($existingFile['file_path']);
                }

                $uploadDir = '../../assets/file/ordinance_proposals/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileDestination = $uploadDir . $fileName;

                if (!move_uploaded_file($fileTmpName, $fileDestination)) {
                    throw new Exception('Failed to move uploaded file');
                }

                $fileUpdate = ", file_name = '$fileName', file_path = '$fileDestination', 
                              file_type = '$fileExt', file_size = $fileSize";
            }
        }

        // Update query
        $query = "UPDATE ordinance_proposals 
                 SET proposal = '$proposal', 
                     proposal_date = '$proposalDate', 
                     details = '$details', 
                     status = '$status' 
                     $fileUpdate 
                 WHERE id = '$proposal_id'";

        if ($conn->query($query)) {
            $response = array(
                'status' => 'success',
                'message' => 'Ordinance proposal updated successfully'
            );
        } else {
            throw new Exception("Database error: " . $conn->error);
        }

        echo json_encode($response);

    } catch (Exception $e) {
        $response = array(
            'status' => 'error',
            'message' => $e->getMessage()
        );
        echo json_encode($response);
    }
}

if (isset($_POST['delete_ordinanceProposal'])) {
    try {
        if (!isset($_POST['deleteID']) || empty($_POST['deleteID'])) {

            throw new Exception('Proposal ID is required');
        }

        $proposal_id = mysqli_real_escape_string($conn, $_POST['deleteID']);

        // Get existing file information
        $fileQuery = "SELECT file_path FROM ordinance_proposals WHERE id = '$proposal_id'";
        $fileResult = $conn->query($fileQuery);

        if (!$fileResult) {
            throw new Exception("Database error while fetching file info: " . $conn->error);
        }

        $existingFile = $fileResult->fetch_assoc();

        // Only attempt file deletion if we have a valid file path
        if ($existingFile && !empty($existingFile['file_path']) && file_exists($existingFile['file_path'])) {
            if (!unlink($existingFile['file_path'])) {
                error_log("Failed to delete file: " . $existingFile['file_path']);
            }
        }

        $query = "DELETE FROM ordinance_proposals WHERE id = '$proposal_id'";

        if ($conn->query($query)) {
            $response = array(
                'status' => 'success',
                'message' => 'Ordinance proposal deleted successfully'
            );
        } else {
            throw new Exception("Database error: " . $conn->error);
        }

        echo json_encode($response);

    } catch (Exception $e) {
        $response = array(
            'status' => 'error',
            'message' => $e->getMessage()
        );
        echo json_encode($response);
    }
}
