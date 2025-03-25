<?php
require_once __DIR__ . '/../../database/database.php';

class DashboardController
{
    private $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    public function getDashboardStats()
    {
        try {
            return [
                'total_proposals' => $this->getTotalProposals(),
                'proposals_by_status' => $this->getProposalsByStatus(),
                'recent_proposals' => $this->getRecentProposals(),
                'monthly_proposals' => $this->getMonthlyProposals()
            ];
        } catch (Exception $e) {
            error_log("Dashboard Error: " . $e->getMessage());
            return null;
        }
    }

    private function getTotalProposals()
    {
        // Modified query to get latest status per proposal
        $query = "WITH LatestStatus AS (
            SELECT 
                proposal_id,
                action_type,
                action_date,
                ROW_NUMBER() OVER (PARTITION BY proposal_id ORDER BY action_date DESC) as rn
            FROM ordinance_status
        )
        SELECT 
            COUNT(DISTINCT op.id) as total,
            SUM(CASE WHEN ls.action_type = 'Approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN ls.action_type = 'Pending Approval' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN ls.action_type = 'Rejected' THEN 1 ELSE 0 END) as rejected
        FROM ordinance_proposals op
        LEFT JOIN LatestStatus ls ON op.id = ls.proposal_id AND ls.rn = 1";

        $result = $this->conn->query($query);
        return $result->fetch_assoc();
    }

    private function getProposalsByStatus()
    {
        // Modified query to count only latest status
        $query = "WITH LatestStatus AS (
            SELECT 
                proposal_id,
                action_type,
                ROW_NUMBER() OVER (PARTITION BY proposal_id ORDER BY action_date DESC) as rn
            FROM ordinance_status
        )
        SELECT action_type, COUNT(*) as count
        FROM LatestStatus
        WHERE rn = 1
        GROUP BY action_type";

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getRecentProposals()
    {
        // Modified query to get only latest proposals with their most recent status
        $query = "WITH LatestStatus AS (
            SELECT 
                proposal_id,
                action_type,
                action_date,
                ROW_NUMBER() OVER (PARTITION BY proposal_id ORDER BY action_date DESC) as rn
            FROM ordinance_status
        )
        SELECT 
            op.id,
            op.proposal,
            op.created_at,
            c.name as committee_name,
            ls.action_type
        FROM ordinance_proposals op
        LEFT JOIN committees c ON op.committee_id = c.id
        LEFT JOIN LatestStatus ls ON op.id = ls.proposal_id AND ls.rn = 1
        ORDER BY op.created_at DESC 
        LIMIT 5";

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function getMonthlyProposals()
    {
        // Keep existing monthly proposals query
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
                 COUNT(*) as count
                 FROM ordinance_proposals
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                 GROUP BY month
                 ORDER BY month";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
