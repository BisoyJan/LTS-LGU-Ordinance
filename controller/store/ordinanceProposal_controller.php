<?php
session_start();
require '../../database/database.php';
require_once '../../vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;
use Google\Service\Exception as GoogleServiceException;

function getGoogleDriveClient()
{
    $credentialsPath = '../../api/credentials.json';

    if (!file_exists($credentialsPath)) {
        error_log("Credentials file missing at: " . realpath($credentialsPath));
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
    $client->setScopes([Drive::DRIVE]);

    // Uncomment if using Google Workspace
    // $client->setSubject('admin@your-domain.com');

    return $client;
}

function verifyDriveFolderAccess($driveService, $folderId)
{
    try {
        if (!preg_match('/^[a-zA-Z0-9_-]{15,}$/', $folderId)) {
            throw new Exception('Invalid Google Drive Folder ID format');
        }

        $permissions = $driveService->permissions->listPermissions($folderId, [
            'fields' => 'permissions(emailAddress,role)',
            'supportsAllDrives' => true
        ]);

        $serviceAccountEmail = 'ordinance-access@ordinance-tracking.iam.gserviceaccount.com';
        $hasAccess = false;

        foreach ($permissions as $perm) {
            if (strcasecmp($perm->emailAddress, $serviceAccountEmail) === 0) {
                if (in_array($perm->role, ['writer', 'owner'])) {
                    $hasAccess = true;
                    break;
                }
            }
        }

        if (!$hasAccess) {
            throw new Exception("Service account missing write permissions on folder");
        }

        return true;
    } catch (GoogleServiceException $e) {
        error_log('Drive API Error: ' . $e->getMessage());
        throw new Exception('Folder access verification failed: ' . $e->getMessage());
    }
}

header('Content-Type: application/json');
$conn = getConnection();

if (isset($_POST['fetch_Proposal'])) {
    $proposal_id = mysqli_real_escape_string($conn, $_POST['id']);
    $sql = "SELECT id, proposal, proposal_date, details, committee_id, file_name, file_path 
            FROM ordinance_proposals 
            WHERE id = '$proposal_id'";

    try {
        $query_run = mysqli_query($conn, $sql);
        $data = $query_run->fetch_assoc();
        $res = [
            'status' => $data ? 'success' : 'warning',
            'data' => $data ?? [],
            'message' => $data ? '' : 'User ID not found'
        ];
        echo json_encode($res);
    } catch (mysqli_sql_exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error fetching proposal: ' . $e->getMessage()
        ]);
    }
    exit;
}

if (isset($_POST['create_ordinanceProposal']) || isset($_POST['edit_ordinanceProposal'])) {
    $committeeId = isset($_POST['committee_id']) && !empty($_POST['committee_id']) ? intval($_POST['committee_id']) : null;

    try {
        $userRole = $_SESSION['role'];
        $proposal = isset($_POST['proposal']) ? mysqli_real_escape_string($conn, $_POST['proposal']) : null;
        $proposalDate = isset($_POST['proposalDate']) ? mysqli_real_escape_string($conn, $_POST['proposalDate']) : null;
        $details = isset($_POST['details']) ? mysqli_real_escape_string($conn, $_POST['details']) : null;
        $userId = $_SESSION['user_id'];

        $driveFileId = null;
        $fileName = null;
        $fileType = null;
        $fileSize = 0;

        if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['file'];
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $baseFileName = pathinfo($file['name'], PATHINFO_FILENAME);
            $newFileName = $baseFileName . '.' . $fileExt;

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

            $folderId = '15-c0hmu-lBaEyxhkj1hdcYQRwnAgymoj';
            if (!preg_match('/^[a-zA-Z0-9_-]{15,}$/', $folderId)) {
                throw new Exception('Invalid Google Drive Folder ID format');
            }

            $client = getGoogleDriveClient();
            $driveService = new Drive($client);
            verifyDriveFolderAccess($driveService, $folderId);

            $fileMetadata = new Drive\DriveFile([
                'name' => $newFileName,
                'parents' => [$folderId]
            ]);

            $content = file_get_contents($file['tmp_name']);
            $driveFile = $driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploadType' => 'multipart',
                'fields' => 'id',
                'supportsAllDrives' => true
            ]);

            $driveFileId = $driveFile->id;
            $fileName = $newFileName;
            $fileType = $fileExt;
            $fileSize = $file['size'];
        }

        if (isset($_POST['create_ordinanceProposal'])) {
            $query = "INSERT INTO ordinance_proposals (proposal, proposal_date, details, committee_id, user_id, file_name, file_path) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssisss", $proposal, $proposalDate, $details, $committeeId, $userId, $fileName, $driveFileId);
        } else if (isset($_POST['edit_ordinanceProposal'])) {
            $proposalId = mysqli_real_escape_string($conn, $_POST['proposalID']);

            if ($userRole === 'secretary') {
                // Secretary can only edit the committee_id
                $query = "UPDATE ordinance_proposals SET committee_id = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $committeeId, $proposalId);
            } else {
                // Other roles can edit all fields
                $query = "UPDATE ordinance_proposals SET proposal = ?, proposal_date = ?, details = ?, committee_id = ?, file_name = ?, file_path = ? 
                          WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssissi", $proposal, $proposalDate, $details, $committeeId, $fileName, $driveFileId, $proposalId);
            }
        }

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => isset($_POST['create_ordinanceProposal']) ? 'Ordinance proposal created successfully' : 'Ordinance proposal updated successfully'
            ]);
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }

    } catch (GoogleServiceException $e) {
        error_log('Drive API Error: ' . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Google Drive error: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

if (isset($_POST['delete_ordinanceProposal'])) {
    try {
        if (!isset($_POST['deleteID']) || empty($_POST['deleteID'])) {
            throw new Exception('Proposal ID is required');
        }

        $proposal_id = mysqli_real_escape_string($conn, $_POST['deleteID']);

        $fileQuery = "SELECT file_path FROM ordinance_proposals WHERE id = '$proposal_id'";
        $fileResult = $conn->query($fileQuery);
        $existingFile = $fileResult->fetch_assoc();

        if ($existingFile && !empty($existingFile['file_path'])) {
            $client = getGoogleDriveClient();
            $driveService = new Drive($client);
            try {
                $driveService->files->delete($existingFile['file_path'], [
                    'supportsAllDrives' => true
                ]);
            } catch (GoogleServiceException $e) {
                error_log("Delete error: " . $e->getMessage());
            }
        }

        $query = "DELETE FROM ordinance_proposals WHERE id = '$proposal_id'";
        if ($conn->query($query)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Ordinance proposal deleted successfully'
            ]);
        } else {
            throw new Exception("Database error: " . $conn->error);
        }

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
?>

