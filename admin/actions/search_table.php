<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$query = isset($_GET['query']) ? $_GET['query'] : '';

if(empty($type) || empty($query)) {
    exit(json_encode(['error' => 'Missing parameters']));
}

$search_results = [];
$html = '';

switch($type) {
    case 'children':
        $sql = "SELECT * FROM children WHERE name LIKE ? OR guardian_name LIKE ? OR school LIKE ?";
        $stmt = mysqli_prepare($conn, $sql);
        $search_term = "%$query%";
        mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
        break;
        
    case 'volunteers':
        $sql = "SELECT * FROM volunteers WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
        $stmt = mysqli_prepare($conn, $sql);
        $search_term = "%$query%";
        mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
        break;
        
    case 'staff':
        $sql = "SELECT * FROM staff WHERE name LIKE ? OR email LIKE ? OR position LIKE ?";
        $stmt = mysqli_prepare($conn, $sql);
        $search_term = "%$query%";
        mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
        break;
        
    case 'events':
        $sql = "SELECT * FROM events WHERE title LIKE ? OR description LIKE ? OR location LIKE ?";
        $stmt = mysqli_prepare($conn, $sql);
        $search_term = "%$query%";
        mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
        break;
        
    case 'donations':
        $sql = "SELECT d.*, c.name as child_name 
                FROM donations d 
                LEFT JOIN children c ON d.child_id = c.id 
                WHERE d.donor_name LIKE ? OR d.donor_email LIKE ? OR c.name LIKE ?";
        $stmt = mysqli_prepare($conn, $sql);
        $search_term = "%$query%";
        mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
        break;
        
    default:
        exit(json_encode(['error' => 'Invalid type']));
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

echo json_encode(['html' => $html]);
?> 