<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
session_start();

$monitor = new PerformanceMonitor();
$monitor->start();

// Fetch all news articles
$sql = "SELECT * FROM news_articles ORDER BY date_published DESC";
$result = mysqli_query($conn, $sql);

$metrics = $monitor->getPerformanceMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News - Bumbobi Child Support Uganda</title>
    
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
    .news-header {
        background: linear-gradient(135deg, #1a237e, #0d47a1);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }

    .news-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .news-card:hover {
        transform: translateY(-5px);
    }

    .news-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 15px 15px 0 0;
    }

    .news-content {
        padding: 1.5rem;
    }

    .news-date {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .news-title {
        color: #1a237e;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .news-excerpt {
        color: #6c757d;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .news-category {
        display: inline-block;
        background: #e3f2fd;
        color: #1a237e;
        padding: 0.3rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .btn-read-more {
        background: #1a237e;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-read-more:hover {
        background: #0d47a1;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .news-sidebar {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        padding: 1.5rem;
    }

    .sidebar-title {
        color: #1a237e;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e3f2fd;
    }

    .recent-news-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e3f2fd;
    }

    .recent-news-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .recent-news-image {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        margin-right: 1rem;
    }

    .recent-news-content h6 {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .recent-news-content small {
        color: #6c757d;
        font-size: 0.8rem;
    }

    .news-categories {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .news-categories li {
        margin-bottom: 0.5rem;
    }

    .news-categories a {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .news-categories a:hover {
        color: #1a237e;
    }

    .news-categories span {
        background: #e3f2fd;
        color: #1a237e;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .news-header {
            padding: 3rem 0;
        }

        .news-image {
            height: 200px;
        }

        .news-title {
            font-size: 1.3rem;
        }

        .news-sidebar {
            margin-top: 2rem;
        }
    }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- News Header -->
    <section class="news-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1>Latest News</h1>
                    <p>Stay updated with our latest activities and achievements</p>
                </div>
            </div>
        </div>
    </section>

    <!-- News Content -->
    <section class="news-content-section py-5">
        <div class="container">
            <div class="row">
                <!-- Main News Content -->
                <div class="col-lg-8">
                    <?php while($article = mysqli_fetch_assoc($result)): ?>
                    <div class="news-card">
                        <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="news-image">
                        <div class="news-content">
                            <span class="news-category"><?php echo htmlspecialchars($article['category']); ?></span>
                            <div class="news-date">
                                <i class="far fa-calendar-alt me-2"></i>
                                <?php echo date('F j, Y', strtotime($article['date_published'])); ?>
                            </div>
                            <h3 class="news-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="news-excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
                            <a href="news-detail.php?id=<?php echo $article['id']; ?>" class="btn btn-read-more">
                                Read More <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Recent News -->
                    <div class="news-sidebar mb-4">
                        <h4 class="sidebar-title">Recent News</h4>
                        <?php
                        $recent_sql = "SELECT * FROM news_articles ORDER BY date_published DESC LIMIT 3";
                        $recent_result = mysqli_query($conn, $recent_sql);
                        while($recent = mysqli_fetch_assoc($recent_result)):
                        ?>
                        <div class="recent-news-item">
                            <img src="<?php echo htmlspecialchars($recent['image_path']); ?>" alt="<?php echo htmlspecialchars($recent['title']); ?>" class="recent-news-image">
                            <div class="recent-news-content">
                                <h6><?php echo htmlspecialchars($recent['title']); ?></h6>
                                <small><?php echo date('M j, Y', strtotime($recent['date_published'])); ?></small>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Categories -->
                    <div class="news-sidebar">
                        <h4 class="sidebar-title">Categories</h4>
                        <ul class="news-categories">
                            <?php
                            $categories_sql = "SELECT category, COUNT(*) as count FROM news_articles GROUP BY category";
                            $categories_result = mysqli_query($conn, $categories_sql);
                            while($category = mysqli_fetch_assoc($categories_result)):
                            ?>
                            <li>
                                <a href="news.php?category=<?php echo urlencode($category['category']); ?>">
                                    <?php echo htmlspecialchars($category['category']); ?>
                                    <span><?php echo $category['count']; ?></span>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/performance.js"></script>
</body>
</html> 