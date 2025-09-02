<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

if(empty($type)) {
    exit(json_encode(['error' => 'Missing type parameter']));
}

$html = '';
$total_records = 0;

// Get total records count
switch($type) {
    case 'children':
        $count_sql = "SELECT COUNT(*) as total FROM children";
        break;
    case 'volunteers':
        $count_sql = "SELECT COUNT(*) as total FROM volunteers";
        break;
    case 'staff':
        $count_sql = "SELECT COUNT(*) as total FROM staff";
        break;
    case 'events':
        $count_sql = "SELECT COUNT(*) as total FROM events";
        break;
    case 'donations':
        $count_sql = "SELECT COUNT(*) as total FROM donations";
        break;
    default:
        exit(json_encode(['error' => 'Invalid type']));
}

$count_result = mysqli_query($conn, $count_sql);
if($count_result) {
    $total_records = mysqli_fetch_assoc($count_result)['total'];
}

// Get paginated data
switch($type) {
    case 'children':
        $sql = "SELECT * FROM children ORDER BY name LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $per_page, $offset);
        break;
        
    case 'volunteers':
        $sql = "SELECT * FROM volunteers ORDER BY name LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $per_page, $offset);
        break;
        
    case 'staff':
        $sql = "SELECT * FROM staff ORDER BY name LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $per_page, $offset);
        break;
        
    case 'events':
        $sql = "SELECT * FROM events ORDER BY event_date DESC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $per_page, $offset);
        break;
        
    case 'donations':
        $sql = "SELECT d.*, c.name as child_name 
                FROM donations d 
                LEFT JOIN children c ON d.child_id = c.id 
                ORDER BY d.created_at DESC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $per_page, $offset);
        break;
}

if(mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    
    while($row = mysqli_fetch_assoc($result)) {
        switch($type) {
            case 'children':
                $html .= "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['age']}</td>
                    <td>{$row['guardian_name']}</td>
                    <td>{$row['school']}</td>
                    <td>
                        <button class='btn btn-sm btn-primary' onclick='editChild({$row['id']})'>Edit</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteChild({$row['id']})'>Delete</button>
                    </td>
                </tr>";
                break;
                
            case 'volunteers':
                $status_class = getStatusBadgeClass($row['status']);
                $html .= "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td><span class='status-badge bg-{$status_class}'>{$row['status']}</span></td>
                    <td>
                        <button class='btn btn-sm btn-primary' onclick='editVolunteer({$row['id']})'>Edit</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteVolunteer({$row['id']})'>Delete</button>
                    </td>
                </tr>";
                break;
                
            case 'staff':
                $status_class = getStatusBadgeClass($row['status']);
                $html .= "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['position']}</td>
                    <td><span class='status-badge bg-{$status_class}'>{$row['status']}</span></td>
                    <td>
                        <button class='btn btn-sm btn-primary' onclick='editStaff({$row['id']})'>Edit</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteStaff({$row['id']})'>Delete</button>
                    </td>
                </tr>";
                break;
                
            case 'events':
                $html .= "<tr>
                    <td>{$row['title']}</td>
                    <td>{$row['event_date']}</td>
                    <td>{$row['location']}</td>
                    <td>
                        <button class='btn btn-sm btn-primary' onclick='editEvent({$row['id']})'>Edit</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteEvent({$row['id']})'>Delete</button>
                    </td>
                </tr>";
                break;
                
            case 'donations':
                $status_class = getStatusBadgeClass($row['status']);
                $html .= "<tr>
                    <td>{$row['donor_name']}</td>
                    <td>$" . number_format($row['amount'], 2) . "</td>
                    <td>{$row['child_name']}</td>
                    <td><span class='status-badge bg-{$status_class}'>{$row['status']}</span></td>
                    <td>
                        <button class='btn btn-sm btn-primary' onclick='editDonation({$row['id']})'>Edit</button>
                        <button class='btn btn-sm btn-danger' onclick='deleteDonation({$row['id']})'>Delete</button>
                    </td>
                </tr>";
                break;
        }
    }
}

mysqli_stmt_close($stmt);

// Calculate pagination
$total_pages = ceil($total_records / $per_page);
$pagination = [
    'current_page' => $page,
    'total_pages' => $total_pages,
    'total_records' => $total_records,
    'type' => $type
];

// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch($status) {
        case 'active':
        case 'completed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'inactive':
        case 'failed':
            return 'danger';
        default:
            return 'secondary';
    }
}

echo json_encode([
    'html' => $html,
    'pagination' => $pagination
]);
?> 