<?php
require '../../database/database.php';

header('Content-Type: application/json');

$conn = getConnection();

if (isset($_POST['fetch_approved_proposals'])) {
    try {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $itemsPerPage = isset($_POST['itemsPerPage']) ? intval($_POST['itemsPerPage']) : 6;
        $offset = ($page - 1) * $itemsPerPage;

        $fromDate = isset($_POST['fromDate']) && !empty($_POST['fromDate']) ? $_POST['fromDate'] : null;
        $toDate = isset($_POST['toDate']) && !empty($_POST['toDate']) ? $_POST['toDate'] : null;
        $committee = isset($_POST['committee']) && !empty($_POST['committee']) ? intval($_POST['committee']) : null;

        $whereClauses = ["os.action_type = 'Approved'"];
        if ($fromDate) {
            $whereClauses[] = "op.proposal_date >= '$fromDate'";
        }
        if ($toDate) {
            $whereClauses[] = "op.proposal_date <= '$toDate'";
        }
        if ($committee) {
            $whereClauses[] = "op.committee_id = $committee";
        }
        $whereSql = implode(' AND ', $whereClauses);

        $query = "SELECT 
                    op.id, 
                    op.proposal, 
                    op.proposal_date, 
                    op.details, 
                    op.file_name, 
                    op.file_path, 
                    c.name as committee_name, 
                    u.username as created_by 
                  FROM ordinance_proposals op
                  LEFT JOIN committees c ON op.committee_id = c.id
                  LEFT JOIN users u ON op.user_id = u.id
                  LEFT JOIN ordinance_status os ON op.id = os.proposal_id
                  WHERE $whereSql
                  ORDER BY op.proposal_date DESC
                  LIMIT $offset, $itemsPerPage";

        $result = $conn->query($query);

        $proposals = [];
        while ($row = $result->fetch_assoc()) {
            $proposals[] = [
                'id' => $row['id'],
                'proposal' => $row['proposal'],
                'proposal_date' => $row['proposal_date'],
                'details' => $row['details'],
                'file_name' => $row['file_name'],
                'file_path' => $row['file_path'],
                'committee_name' => $row['committee_name'],
                'created_by' => $row['created_by']
            ];
        }

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total 
                       FROM ordinance_proposals op
                       LEFT JOIN ordinance_status os ON op.id = os.proposal_id
                       WHERE $whereSql";
        $countResult = $conn->query($countQuery);
        $totalCount = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCount / $itemsPerPage);

        echo json_encode([
            'status' => 'success',
            'data' => $proposals,
            'totalPages' => $totalPages
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch approved proposals: ' . $e->getMessage()
        ]);
    }
}

if (isset($_POST['fetch_proposal_details'])) {
    try {
        $proposalId = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "SELECT 
                    op.id, 
                    op.proposal, 
                    op.proposal_date, 
                    op.details, 
                    op.file_name, 
                    op.file_path, 
                    c.name as committee_name, 
                    u.username as created_by 
                  FROM ordinance_proposals op
                  LEFT JOIN committees c ON op.committee_id = c.id
                  LEFT JOIN users u ON op.user_id = u.id
                  WHERE op.id = '$proposalId'";

        $result = $conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            echo json_encode([
                'status' => 'success',
                'data' => $row
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Proposal not found.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch proposal details: ' . $e->getMessage()
        ]);
    }
}
?>

