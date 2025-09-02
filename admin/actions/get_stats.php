<?php
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';

header('Content-Type: application/json');

$range = $_POST['range'] ?? 'month';
$stats = [];

try {
    // Get date range
    $date_condition = '';
    switch ($range) {
        case 'today':
            $date_condition = 'DATE(created_at) = CURDATE()';
            break;
        case 'week':
            $date_condition = 'YEARWEEK(created_at) = YEARWEEK(CURDATE())';
            break;
        case 'month':
            $date_condition = 'MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())';
            break;
        case 'year':
            $date_condition = 'YEAR(created_at) = YEAR(CURDATE())';
            break;
    }

    // Get children count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM children");
    $stmt->execute();
    $stats['children_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get active volunteers count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM volunteers WHERE status = 'active'");
    $stmt->execute();
    $stats['volunteers_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get pending requests
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM volunteer_applications WHERE status = 'pending'");
    $stmt->execute();
    $stats['pending_requests'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get feedback count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM feedback WHERE $date_condition");
    $stmt->execute();
    $stats['feedback_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get monthly donations
    $stmt = $conn->prepare("
        SELECT MONTH(created_at) as month, SUM(amount) as total
        FROM donations
        WHERE YEAR(created_at) = YEAR(CURDATE())
        GROUP BY MONTH(created_at)
        ORDER BY month
    ");
    $stmt->execute();
    $monthly_donations = array_fill(0, 12, 0); // Initialize array with zeros
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $monthly_donations[$row['month'] - 1] = (float)$row['total'];
    }
    $stats['monthly_donations'] = $monthly_donations;

    // Get volunteer statistics
    $stmt = $conn->prepare("
        SELECT 
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive
        FROM volunteers
    ");
    $stmt->execute();
    $stats['volunteer_stats'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get program impact statistics
    $stmt = $conn->prepare("
        SELECT 
            program_type,
            COUNT(*) as count
        FROM program_participants
        GROUP BY program_type
        ORDER BY count DESC
        LIMIT 5
    ");
    $stmt->execute();
    $stats['program_impact'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 