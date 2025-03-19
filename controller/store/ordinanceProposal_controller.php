<?php
session_start();
require '../../database/database.php';
require_once '../../vendor/autoload.php'; // Add this for Google API client

use Google\Client;
use Google\Service\Drive;

function getGoogleDriveClient()
{
    $credentialsPath = '../../api/credentials.json';

    if (!file_exists($credentialsPath)) {
        throw new Exception('Credentials file not found');
    }

    $jsonContent = file_get_contents($credentialsPath);
    if ($jsonContent === false) {
        throw new Exception('Failed to read credentials file');
    }

    $credentials = json_decode($jsonContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON in credentials file: ' . json_last_error_msg());
    }

    if (!isset($credentials['client_email']) || !isset($credentials['private_key'])) {
        throw new Exception('Credentials file missing required fields');
    }

    $credentials['type'] = 'service_account';

    $client = new Client();
    $client->setApplicationName('LTS-LGU-Ordinance');
    $client->setAuthConfig($credentials);
    $client->setScopes([Drive::DRIVE_FILE]);

    return $client;
}

header('Content-Type: application/json');

$conn = getConnection();

if (isset($_POST['fetch_Proposal'])) {
    $proposal_id = mysqli_real_escape_string($conn, $_POST['id']);

    $sql = "SELECT id, proposal, proposal_date, details, committee_id, file_name, file_path FROM ordinance_proposals WHERE id = '$proposal_id'";

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
        // Get next ID from database
        $nextIdQuery = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ordinance_proposals'";
        $nextIdResult = $conn->query($nextIdQuery);
        $nextId = $nextIdResult->fetch_assoc()['AUTO_INCREMENT'];

        $proposal = mysqli_real_escape_string($conn, $_POST['proposal']);
        $proposalDate = mysqli_real_escape_string($conn, $_POST['proposalDate']);
        $details = mysqli_real_escape_string($conn, $_POST['details']);
        $committee_id = mysqli_real_escape_string($conn, $_POST['committee_id']);
        $user_id = $_SESSION['user_id']; // Get current user's ID

        // Handle file upload if present
        $driveFileId = null;
        $fileName = null;
        $fileType = null;
        $fileSize = 0;

        if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['file'];

            // Remove ID prefix and just use original filename
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $baseFileName = pathinfo($file['name'], PATHINFO_FILENAME);
            $newFileName = $baseFileName . '.' . $fileExt;

            // Check if file name already exists
            $checkQuery = "SELECT id FROM ordinance_proposals WHERE file_name = '$newFileName'";
            $checkResult = $conn->query($checkQuery);
            if ($checkResult && $checkResult->num_rows > 0) {
                throw new Exception('A file with this name already exists. Please rename your file.');
            }

            if (!in_array($fileExt, ['doc', 'docx'])) {
                throw new Exception('Only .doc and .docx files are allowed');
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload failed with error code: ' . $file['error']);
            }

            // Upload to Google Drive with new filename
            $client = getGoogleDriveClient();
            $driveService = new Drive($client);

            $fileMetadata = new Drive\DriveFile([
                'name' => $newFileName,
                'parents' => ['15-c0hmu-lBaEyxhkj1hdcYQRwnAgymoj'] // Replace with your folder ID
            ]);

            $content = file_get_contents($file['tmp_name']);
            $driveFile = $driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            $driveFileId = $driveFile->id;
            $fileName = $newFileName;
            $fileType = $fileExt;
            $fileSize = $file['size'];
        }

        // Insert query
        $query = "INSERT INTO ordinance_proposals (proposal, proposal_date, details, committee_id, user_id, file_name, file_path, file_type, file_size) 
                  VALUES ('$proposal', '$proposalDate', '$details', '$committee_id', '$user_id', '$fileName', '$driveFileId', '$fileType', $fileSize)";

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
        $conn->rollback();
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
        $committee_id = mysqli_real_escape_string($conn, $_POST['committee_id']);

        // First update the basic information
        $query = "UPDATE ordinance_proposals 
                  SET proposal = '$proposal', 
                      proposal_date = '$proposalDate', 
                      details = '$details',
                      committee_id = '$committee_id'";

        // Get existing file information
        $fileQuery = "SELECT file_name, file_path FROM ordinance_proposals WHERE id = '$proposal_id'";
        $fileResult = $conn->query($fileQuery);
        $existingFile = $fileResult->fetch_assoc();

        $fileUpdate = ""; // Initialize as an empty string

        if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['file'];

            // Remove ID prefix and just use original filename
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $baseFileName = pathinfo($file['name'], PATHINFO_FILENAME);
            $newFileName = $baseFileName . '.' . $fileExt;

            // Check if file name already exists (excluding current proposal)
            $checkQuery = "SELECT id FROM ordinance_proposals WHERE file_name = '$newFileName' AND id != '$proposal_id'";
            $checkResult = $conn->query($checkQuery);
            if ($checkResult && $checkResult->num_rows > 0) {
                throw new Exception('A file with this name already exists. Please rename your file.');
            }

            if (!in_array($fileExt, ['doc', 'docx'])) {
                throw new Exception('Only .doc and .docx files are allowed');
            }

            // Delete existing file from Google Drive if exists
            if ($existingFile['file_path']) {
                $client = getGoogleDriveClient();
                $driveService = new Drive($client);
                try {
                    $driveService->files->get($existingFile['file_path']);  // Check if file exists
                    $driveService->files->delete($existingFile['file_path']);
                } catch (Exception $e) {
                    error_log("Failed to delete file from Google Drive: " . $e->getMessage());
                    // Continue execution even if file doesn't exist in Drive
                }
            }

            // Upload new file to Google Drive with new filename
            $client = getGoogleDriveClient();
            $driveService = new Drive($client);

            $fileMetadata = new Drive\DriveFile([
                'name' => $newFileName,
                'parents' => ['15-c0hmu-lBaEyxhkj1hdcYQRwnAgymoj'] // Replace with your folder ID
            ]);

            $content = file_get_contents($file['tmp_name']);
            $driveFile = $driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            $fileUpdate = ", file_name = '{$newFileName}', file_path = '{$driveFile->id}', 
                           file_type = '$fileExt', file_size = {$file['size']}";
        }

        // Construct the update query
        $query = "UPDATE ordinance_proposals 
                  SET proposal = '$proposal', 
                      proposal_date = '$proposalDate', 
                      details = '$details',
                      committee_id = '$committee_id'";

        // Append file update fields if present
        if (!empty($fileUpdate)) {
            $query .= $fileUpdate;
        }

        $query .= " WHERE id = '$proposal_id'";

        // Execute the query
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
        if ($existingFile && !empty($existingFile['file_path'])) {
            $client = getGoogleDriveClient();
            $driveService = new Drive($client);
            try {
                // Check if file exists in Google Drive
                $driveService->files->get($existingFile['file_path']);
                $driveService->files->delete($existingFile['file_path']);
            } catch (Exception $e) {
                // File doesn't exist in Google Drive, just log it
                error_log("File not found in Google Drive: " . $e->getMessage());
                $response = array(
                    'status' => 'warning',
                    'message' => 'File not found in Google Drive. Database record will be deleted.'
                );
                echo json_encode($response);
                // Continue with database deletion
            }
        }

        $query = "DELETE FROM ordinance_proposals WHERE id = '$proposal_id'";

        if ($conn->query($query)) {
            if (!isset($response)) {  // Only set response if not already set
                $response = array(
                    'status' => 'success',
                    'message' => 'Ordinance proposal deleted successfully'
                );
            }
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
