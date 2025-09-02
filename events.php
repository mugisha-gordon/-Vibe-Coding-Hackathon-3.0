<?php
require_once "config/database.php";
session_start();

$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$isAdmin = $isLoggedIn && isset($_SESSION["role"]) && $_SESSION["role"] === "admin";

// Fetch upcoming events
$upcoming_events = [];
$sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3";
$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $upcoming_events[] = $row;
    }
}

// Fetch past events
$past_events = [];
$sql = "SELECT * FROM events WHERE event_date < CURDATE() ORDER BY event_date DESC LIMIT 6";
$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $past_events[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="page-header text-center">
        <div class="container">
            <h1>Events</h1>
            <p class="lead">Join us in our mission to support and empower children in Uganda</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <!-- Upcoming Events -->
            <div class="mb-5">
                <h2 class="text-center mb-4">Upcoming Events</h2>
                <div class="card-grid">
                    <?php if(empty($upcoming_events)): ?>
                        <div class="content-card text-center">
                            <i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
                            <h3>No Upcoming Events</h3>
                            <p>Check back soon for new events!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($upcoming_events as $event): ?>
                            <div class="content-card">
                                <div class="event-date mb-3">
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                </div>
                                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p><?php echo htmlspecialchars($event['description']); ?></p>
                                <div class="event-details mt-3">
                                    <p><i class="fas fa-map-marker-alt text-primary"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                                    <p><i class="far fa-clock text-primary"></i> <?php echo htmlspecialchars($event['time']); ?></p>
                                </div>
                                <?php if($event['registration_required']): ?>
                                    <a href="event-registration.php?id=<?php echo $event['id']; ?>" class="btn btn-primary mt-3">Register Now</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Past Events -->
            <div>
                <h2 class="text-center mb-4">Past Events</h2>
                <div class="card-grid">
                    <?php if(empty($past_events)): ?>
                        <div class="content-card text-center">
                            <i class="fas fa-history fa-3x mb-3 text-primary"></i>
                            <h3>No Past Events</h3>
                            <p>Our event history will appear here.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($past_events as $event): ?>
                            <div class="content-card">
                                <div class="event-date mb-3">
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                </div>
                                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p><?php echo htmlspecialchars($event['description']); ?></p>
                                <?php if($event['gallery_link']): ?>
                                    <a href="<?php echo htmlspecialchars($event['gallery_link']); ?>" class="btn btn-outline-primary mt-3">View Gallery</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 