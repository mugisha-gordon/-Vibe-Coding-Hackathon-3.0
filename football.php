<?php
session_start();
require_once "config/database.php";

// Fetch team members
$team_members = [];
$sql = "SELECT c.*, ft.position, ft.jersey_number, ft.join_date 
        FROM children c 
        JOIN football_team ft ON c.id = ft.child_id 
        WHERE ft.status = 'Active' 
        ORDER BY ft.position, c.first_name";
$result = mysqli_query($conn, $sql);
if($result){
    while($row = mysqli_fetch_assoc($result)){
        $team_members[] = $row;
    }
}

// Group team members by position
$positions = [
    'Goalkeeper' => [],
    'Defender' => [],
    'Midfielder' => [],
    'Forward' => []
];

foreach($team_members as $member) {
    $positions[$member['position']][] = $member;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Team - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="page-header text-center">
        <div class="container">
            <h1>Football Team</h1>
            <p class="lead">Meet our talented young footballers who are making their mark on and off the field</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <!-- Team Overview -->
            <div class="content-card mb-5">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2>Our Football Program</h2>
                        <p class="lead">Through football, we teach our children valuable life skills such as teamwork, discipline, and perseverance.</p>
                        <p>Our football program provides a safe and supportive environment for children to develop their athletic abilities while building character and confidence.</p>
                        <div class="mt-4">
                            <a href="donate.php" class="btn btn-primary me-3">Support Our Team</a>
                            <a href="volunteer.php" class="btn btn-outline-primary">Volunteer as Coach</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img src="assets/images/football-team.jpg" alt="Football Team" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>

            <!-- Team Members -->
            <?php foreach($positions as $position => $members): ?>
                <div class="mb-5">
                    <h2 class="text-center mb-4"><?php echo $position; ?>s</h2>
                    <div class="card-grid">
                        <?php if(empty($members)): ?>
                            <div class="content-card text-center">
                                <i class="fas fa-user-friends fa-3x mb-3 text-primary"></i>
                                <h3>No <?php echo $position; ?>s</h3>
                                <p>Team positions will be filled soon.</p>
                                        </div>
                        <?php else: ?>
                            <?php foreach($members as $member): ?>
                                <div class="content-card text-center">
                                    <div class="player-number mb-3">
                                        <span class="badge bg-primary">#<?php echo $member['jersey_number']; ?></span>
                                        </div>
                                    <img src="<?php echo htmlspecialchars($member['photo_url'] ?? 'assets/images/default-player.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($member['first_name']); ?>" 
                                         class="rounded-circle mb-3"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    <h3><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h3>
                                    <p class="text-muted"><?php echo $position; ?></p>
                                    <div class="player-stats mt-3">
                                        <p><i class="fas fa-calendar-alt text-primary"></i> Joined: <?php echo date('M Y', strtotime($member['join_date'])); ?></p>
                                        </div>
                                </div>
                                                <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 