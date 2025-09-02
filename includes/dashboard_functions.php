<?php
// Function to get cached or fresh data
function getCachedData($cache, $key, $query, $conn) {
    $data = $cache->get($key);
    if ($data === false) {
        $result = mysqli_query($conn, $query);
        if ($result) {
            $row = $result->fetch_assoc();
            $data = isset($row['count']) ? $row['count'] : 0;
            $cache->set($key, $data);
        } else {
            $data = 0;
        }
    }
    return $data;
}

// Function to get cached or fresh donation data
function getCachedDonationData($query, $cacheKey, $cacheTime = 300) {
    $cacheFile = "cache/{$cacheKey}.txt";
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
        return file_get_contents($cacheFile);
    }
    
    global $conn;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        return 0;
    }
    
    $row = mysqli_fetch_assoc($result);
    $value = isset($row['total']) ? $row['total'] : (isset($row['count']) ? $row['count'] : 0);
    
    if (!is_dir('cache')) {
        mkdir('cache', 0777, true);
    }
    file_put_contents($cacheFile, $value);
    return $value;
}

// Function to get dashboard statistics
function getDashboardStats($conn, $cache) {
    // Fetch counts for dashboard stats with caching
    $stats = [
        'children_count' => getCachedData($cache, 'children_count', "SELECT COUNT(*) as count FROM children", $conn),
        'volunteers_count' => getCachedData($cache, 'volunteers_count', "SELECT COUNT(*) as count FROM volunteers", $conn),
        'pending_requests' => getCachedData($cache, 'pending_requests', "SELECT COUNT(*) as count FROM volunteers WHERE status='pending'", $conn),
        'feedback_count' => getCachedData($cache, 'feedback_count', "SELECT COUNT(*) as count FROM feedback", $conn),
        'total_donations' => getCachedDonationData(
            "SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE status = 'completed'",
            'total_donations'
        ),
        'monthly_donations' => getCachedDonationData(
            "SELECT COALESCE(SUM(amount), 0) as total FROM donations 
            WHERE status = 'completed' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            'monthly_donations'
        ),
        'pending_donations' => getCachedDonationData(
            "SELECT COUNT(*) as count FROM donations WHERE status = 'pending'",
            'pending_donations'
        ),
        'impact_count' => getCachedDonationData(
            "SELECT COUNT(DISTINCT CASE WHEN child_id IS NOT NULL THEN child_id ELSE 0 END) as count 
            FROM donations 
            WHERE status = 'completed'",
            'impact_count'
        )
    ];
    
    return $stats;
} 