<?php
require_once "config/database.php";

// Fetch children
$children = [];
$sql = "SELECT * FROM children WHERE status = 'active' ORDER BY first_name";
$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $children[] = $row;
    }
}

// Fetch staff members
$staff = [];
$sql = "SELECT * FROM staff WHERE status = 'active' ORDER BY department, position";
$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $staff[] = $row;
    }
}

// Fetch board members
$board = [];
$sql = "SELECT * FROM board_members WHERE status = 'active' ORDER BY position";
$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $board[] = $row;
    }
}

// Group staff by department
$departments = [];
foreach($staff as $member) {
    if(!isset($departments[$member['department']])) {
        $departments[$member['department']] = [];
    }
    $departments[$member['department']][] = $member;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Team - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .team-section {
            padding: 80px 0;
            background: var(--light-bg);
        }
        
        .team-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
            position: relative;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .team-card .card-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .team-card:hover .card-img {
            transform: scale(1.1);
        }
        
        .team-card .card-body {
            padding: 25px;
            position: relative;
            z-index: 1;
        }
        
        .team-card .card-title {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        
        .team-card .card-subtitle {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 15px;
        }
        
        .team-card .card-text {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .team-card .social-links {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        
        .team-card .social-links a {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .team-card .social-links a:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .department-title {
            margin: 50px 0 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
        }
        
        .filter-btn {
            padding: 10px 25px;
            border-radius: 30px;
            background: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
            transition: var(--transition);
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--gradient-primary);
            color: white;
            border-color: transparent;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="page-header text-center">
        <div class="container">
            <h1>Our Team</h1>
            <p class="lead">Meet the amazing people who make our organization possible</p>
        </div>
    </section>

    <section class="team-section">
        <div class="container">
            <!-- Filter Buttons -->
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="children">Children</button>
                <button class="filter-btn" data-filter="staff">Staff</button>
                <button class="filter-btn" data-filter="board">Board</button>
            </div>

            <!-- Board Members Section -->
            <div class="section-title" id="board">
                <h2>Board of Directors</h2>
                <p>Our dedicated board members who provide strategic leadership and governance</p>
            </div>
            <div class="row g-4">
                <?php foreach($board as $member): ?>
                    <div class="col-md-4">
                        <div class="team-card">
                            <img src="<?php echo htmlspecialchars($member['photo_url'] ?? 'assets/images/default-profile.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>" 
                                 class="card-img">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h3>
                                <h4 class="card-subtitle"><?php echo htmlspecialchars($member['position']); ?></h4>
                                <p class="card-text"><?php echo htmlspecialchars($member['bio']); ?></p>
                                <div class="social-links">
                                    <?php if($member['email']): ?>
                                        <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>"><i class="fas fa-envelope"></i></a>
                                    <?php endif; ?>
                                    <?php if($member['phone']): ?>
                                        <a href="tel:<?php echo htmlspecialchars($member['phone']); ?>"><i class="fas fa-phone"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Staff Section -->
            <?php foreach($departments as $department => $members): ?>
                <div class="section-title" id="staff">
                    <h2><?php echo htmlspecialchars($department); ?> Department</h2>
                    <p>Our dedicated staff members who work tirelessly to support our mission</p>
                </div>
                <div class="row g-4">
                    <?php foreach($members as $member): ?>
                        <div class="col-md-4">
                            <div class="team-card">
                                <img src="<?php echo htmlspecialchars($member['photo_url'] ?? 'assets/images/default-profile.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>" 
                                     class="card-img">
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h3>
                                    <h4 class="card-subtitle"><?php echo htmlspecialchars($member['position']); ?></h4>
                                    <p class="card-text"><?php echo htmlspecialchars($member['bio']); ?></p>
                                    <div class="social-links">
                                        <?php if($member['email']): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>"><i class="fas fa-envelope"></i></a>
                                        <?php endif; ?>
                                        <?php if($member['phone']): ?>
                                            <a href="tel:<?php echo htmlspecialchars($member['phone']); ?>"><i class="fas fa-phone"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <!-- Children Section -->
            <div class="section-title" id="children">
                <h2>Our Children</h2>
                <p>Meet the wonderful children in our care who inspire us every day</p>
            </div>
            <div class="row g-4">
                <?php foreach($children as $child): ?>
                    <div class="col-md-4">
                        <div class="team-card">
                            <img src="<?php echo htmlspecialchars($child['photo_url'] ?? 'assets/images/default-child.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?>" 
                                 class="card-img">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?></h3>
                                <h4 class="card-subtitle">Age: <?php echo date_diff(date_create($child['date_of_birth']), date_create('today'))->y; ?> years</h4>
                                <p class="card-text">
                                    <i class="fas fa-venus-mars text-primary"></i> <?php echo htmlspecialchars($child['gender']); ?><br>
                                    <i class="fas fa-calendar-alt text-primary"></i> Joined: <?php echo date('M Y', strtotime($child['admission_date'])); ?>
                                </p>
                                <a href="child-profile.php?id=<?php echo $child['id']; ?>" class="btn btn-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const sections = document.querySelectorAll('.section-title');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    sections.forEach(section => {
                        if(filter === 'all' || section.id === filter) {
                            section.parentElement.style.display = 'block';
                        } else {
                            section.parentElement.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html> 