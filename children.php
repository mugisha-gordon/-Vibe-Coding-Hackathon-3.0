<?php
session_start();
require_once "config/database.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$program = isset($_GET['program']) ? $_GET['program'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query
$sql = "SELECT c.*, GROUP_CONCAT(p.name) as programs 
        FROM children c 
        LEFT JOIN child_programs cp ON c.id = cp.child_id 
        LEFT JOIN programs p ON cp.program_id = p.id 
        WHERE 1=1";

if ($status) {
    $sql .= " AND c.status = '" . mysqli_real_escape_string($conn, $status) . "'";
}
if ($program) {
    $sql .= " AND p.id = '" . mysqli_real_escape_string($conn, $program) . "'";
}
if ($search) {
    $sql .= " AND (c.first_name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' 
              OR c.last_name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
}

$sql .= " GROUP BY c.id ORDER BY c.first_name, c.last_name";
$result = mysqli_query($conn, $sql);

// Get all programs for filter
$programs_sql = "SELECT * FROM programs ORDER BY name";
$programs_result = mysqli_query($conn, $programs_sql);

// Handle adding new child
if(isset($_POST["add_child"])){
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $admission_date = date("Y-m-d");
    
    $sql = "INSERT INTO children (first_name, last_name, date_of_birth, gender, admission_date) VALUES (?, ?, ?, ?, ?)";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $date_of_birth, $gender, $admission_date);
        if(mysqli_stmt_execute($stmt)){
            header("location: children.php?success=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Children - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .children-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        .child-card {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .child-card:hover {
            transform: translateY(-5px);
        }
        .child-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .child-avatar i {
            font-size: 3rem;
            color: #0d6efd;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: inline-block;
        }
        .status-active {
            background: #d1e7dd;
            color: #0f5132;
        }
        .status-inactive {
            background: #f8d7da;
            color: #842029;
        }
        .program-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .program-tag {
            background: #e9ecef;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 2rem;
        }
        .stats-card i {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .stats-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Children Header -->
    <section class="children-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 fw-bold">Our Children</h1>
                    <p class="lead">Meet the wonderful children we support in their journey to a better future.</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="add_child.php" class="btn btn-light btn-lg">
                        <i class="fas fa-plus-circle"></i> Add New Child
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="Active" <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo $status === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="program">
                        <option value="">All Programs</option>
                        <?php while($program = mysqli_fetch_assoc($programs_result)): ?>
                            <option value="<?php echo $program['id']; ?>" <?php echo isset($_GET['program']) && $_GET['program'] == $program['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($program['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Children Grid -->
    <section class="children-grid py-5">
        <div class="container">
            <div class="row">
                <?php while($child = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card child-card">
                        <div class="card-body text-center">
                            <div class="child-avatar">
                                <i class="fas fa-child"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?></h5>
                            <span class="status-badge <?php echo $child['status'] === 'Active' ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo htmlspecialchars($child['status']); ?>
                            </span>
                            <p class="card-text">
                                <i class="fas fa-calendar"></i> 
                                Age: <?php echo date_diff(date_create($child['date_of_birth']), date_create('today'))->y; ?> years
                            </p>
                            <?php if($child['programs']): ?>
                                <div class="program-tags">
                                    <?php foreach(explode(',', $child['programs']) as $program): ?>
                                        <span class="program-tag"><?php echo htmlspecialchars(trim($program)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <a href="child-profile.php?id=<?php echo $child['id']; ?>" class="btn btn-outline-primary mt-3">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 