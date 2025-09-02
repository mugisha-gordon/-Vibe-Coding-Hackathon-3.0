<?php
function getDashboardStats($conn, $cache) {
    // Try to get cached stats first
    $cached_stats = $cache->get('dashboard_stats');
    if ($cached_stats !== false) {
        return $cached_stats;
    }

    // Initialize variables
    $children_count = 0;
    $volunteers_count = 0;
    $pending_requests = 0;
    $feedback_count = 0;
    $total_donations = 0;
    $monthly_donations = 0;
    $pending_donations = 0;
    $impact_count = 0;

    // Get total children count
    $children_sql = "SELECT COUNT(*) as count FROM children";
    $children_result = mysqli_query($conn, $children_sql);
    if ($children_result) {
        $children_count = mysqli_fetch_assoc($children_result)['count'];
    }

    // Get active volunteers count
    $volunteers_sql = "SELECT COUNT(*) as count FROM volunteers WHERE status = 'approved'";
    $volunteers_result = mysqli_query($conn, $volunteers_sql);
    if ($volunteers_result) {
        $volunteers_count = mysqli_fetch_assoc($volunteers_result)['count'];
    }

    // Get pending volunteer requests
    $requests_sql = "SELECT COUNT(*) as count FROM volunteers WHERE status = 'pending'";
    $requests_result = mysqli_query($conn, $requests_sql);
    if ($requests_result) {
        $pending_requests = mysqli_fetch_assoc($requests_result)['count'];
    }

    // Get feedback count
    $feedback_sql = "SELECT COUNT(*) as count FROM feedback";
    $feedback_result = mysqli_query($conn, $feedback_sql);
    if ($feedback_result) {
        $feedback_count = mysqli_fetch_assoc($feedback_result)['count'];
    }

    // Get total donations
    $donations_sql = "SELECT 
        COUNT(*) as count,
        SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount
        FROM donations";
    $donations_result = mysqli_query($conn, $donations_sql);
    if ($donations_result) {
        $donations_data = mysqli_fetch_assoc($donations_result);
        $total_donations = $donations_data['total_amount'] ?? 0;
    }

    // Get monthly donations
    $monthly_sql = "SELECT 
        SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as monthly_amount
        FROM donations 
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
        AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $monthly_result = mysqli_query($conn, $monthly_sql);
    if ($monthly_result) {
        $monthly_donations = mysqli_fetch_assoc($monthly_result)['monthly_amount'] ?? 0;
    }

    // Get pending donations
    $pending_donations_sql = "SELECT COUNT(*) as count FROM donations WHERE status = 'pending'";
    $pending_donations_result = mysqli_query($conn, $pending_donations_sql);
    if ($pending_donations_result) {
        $pending_donations = mysqli_fetch_assoc($pending_donations_result)['count'];
    }

    // Get impact count (total children helped)
    $impact_sql = "SELECT COUNT(DISTINCT child_id) as count FROM donations WHERE status = 'completed'";
    $impact_result = mysqli_query($conn, $impact_sql);
    if ($impact_result) {
        $impact_count = mysqli_fetch_assoc($impact_result)['count'];
    }

    // Compile stats
    $stats = [
        'children_count' => $children_count,
        'volunteers_count' => $volunteers_count,
        'pending_requests' => $pending_requests,
        'feedback_count' => $feedback_count,
        'total_donations' => $total_donations,
        'monthly_donations' => $monthly_donations,
        'pending_donations' => $pending_donations,
        'impact_count' => $impact_count
    ];

    // Cache the stats for 5 minutes
    $cache->set('dashboard_stats', $stats, 300);

    return $stats;
}

function getDateRange($range) {
    $end = date('Y-m-d H:i:s');
    
    switch ($range) {
        case 'today':
            $start = date('Y-m-d 00:00:00');
            break;
        case 'week':
            $start = date('Y-m-d H:i:s', strtotime('-7 days'));
            break;
        case 'month':
            $start = date('Y-m-d H:i:s', strtotime('-30 days'));
            break;
        case 'year':
            $start = date('Y-m-d H:i:s', strtotime('-1 year'));
            break;
        default:
            $start = date('Y-m-d H:i:s', strtotime('-30 days'));
    }

    return [
        'start' => $start,
        'end' => $end
    ];
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'active':
        case 'completed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'inactive':
        case 'rejected':
            return 'danger';
        default:
            return 'secondary';
    }
}
?> 