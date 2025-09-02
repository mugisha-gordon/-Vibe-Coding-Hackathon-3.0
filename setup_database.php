<?php
require_once "config/database.php";

// Function to execute SQL file
function executeSQLFile($conn, $file) {
    if (!file_exists($file)) {
        return ["success" => false, "message" => "File not found: " . $file];
    }

    $sql = file_get_contents($file);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if (!mysqli_query($conn, $statement)) {
                return ["success" => false, "message" => "Error executing " . basename($file) . ": " . mysqli_error($conn)];
            }
        }
    }
    
    return ["success" => true, "message" => basename($file) . " executed successfully!"];
}

// Array of SQL files to execute in order
$sql_files = [
    'sql/create_database.sql',
    'sql/children.sql',
    'sql/child_programs.sql',
    'sql/football_team.sql'
];

$success = true;
$messages = [];

// Execute each SQL file
foreach ($sql_files as $file) {
    $result = executeSQLFile($conn, $file);
    $messages[] = $result["message"];
    if (!$result["success"]) {
        $success = false;
        break; // Stop if there's an error
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Database Setup Results</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h5>All tables created successfully!</h5>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <h5>Some errors occurred during setup.</h5>
                            </div>
                        <?php endif; ?>
                        
                        <ul class="list-group">
                            <?php foreach ($messages as $message): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars($message); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="mt-4">
                            <a href="children.php" class="btn btn-primary">Go to Children Page</a>
                            <a href="add_child.php" class="btn btn-success">Add New Child</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 