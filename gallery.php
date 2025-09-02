<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
session_start();

$monitor = new PerformanceMonitor();
$monitor->start();

// Fetch all gallery images
$sql = "SELECT * FROM gallery_images ORDER BY date_uploaded DESC";
$result = mysqli_query($conn, $sql);

$metrics = $monitor->getPerformanceMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Bumbobi Child Support Uganda</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style">
    
    <!-- Main CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
    .gallery-header {
        background: linear-gradient(135deg, #1a237e, #0d47a1);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }

    .gallery-item {
        position: relative;
        margin-bottom: 30px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .gallery-item:hover {
        transform: translateY(-5px);
    }

    .gallery-item img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
    }

    .gallery-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        color: white;
        padding: 20px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .gallery-item:hover .gallery-caption {
        transform: translateY(0);
    }

    .gallery-caption h5 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .gallery-caption p {
        margin: 5px 0 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .gallery-filters {
        margin-bottom: 2rem;
    }

    .filter-btn {
        background: white;
        color: #1a237e;
        border: 1px solid #1a237e;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        margin: 0 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #1a237e;
        color: white;
    }

    .gallery-load-more {
        text-align: center;
        margin-top: 2rem;
    }

    .btn-load-more {
        background: #1a237e;
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-load-more:hover {
        background: #0d47a1;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
        .gallery-header {
            padding: 3rem 0;
        }

        .gallery-item img {
            height: 250px;
        }

        .filter-btn {
            margin: 0 0.25rem 0.5rem;
            padding: 0.4rem 1rem;
            font-size: 0.9rem;
        }
    }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Gallery Header -->
    <section class="gallery-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1>Our Gallery</h1>
                    <p>Explore moments of joy, learning, and growth in our community</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Filters -->
    <section class="gallery-filters">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="education">Education</button>
                    <button class="filter-btn" data-filter="healthcare">Healthcare</button>
                    <button class="filter-btn" data-filter="events">Events</button>
                    <button class="filter-btn" data-filter="community">Community</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="gallery-grid py-5">
        <div class="container">
            <div class="row">
                <?php while($image = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6 gallery-item" data-category="<?php echo htmlspecialchars($image['category']); ?>">
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" class="img-fluid">
                    <div class="gallery-caption">
                        <h5><?php echo htmlspecialchars($image['title']); ?></h5>
                        <p><?php echo htmlspecialchars($image['description']); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Load More Button -->
    <section class="gallery-load-more">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <button class="btn btn-load-more">
                        <i class="fas fa-sync-alt me-2"></i>Load More
                    </button>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/performance.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gallery filtering
        const filterButtons = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                button.classList.add('active');

                const filter = button.getAttribute('data-filter');

                galleryItems.forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Load more functionality
        const loadMoreBtn = document.querySelector('.btn-load-more');
        let currentPage = 1;

        loadMoreBtn.addEventListener('click', () => {
            currentPage++;
            // Here you would typically make an AJAX call to load more images
            // For now, we'll just show a message
            loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
            setTimeout(() => {
                loadMoreBtn.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Load More';
                alert('More images would be loaded here in a real implementation.');
            }, 1000);
        });
    });
    </script>
</body>
</html> 