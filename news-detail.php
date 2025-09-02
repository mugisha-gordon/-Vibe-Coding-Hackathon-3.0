<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
session_start();

$monitor = new PerformanceMonitor();
$monitor->start();

// Check if article ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: news.php");
    exit;
}

$article_id = $_GET['id'];

// Fetch article details
$sql = "SELECT * FROM news_articles WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $article_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $article = mysqli_fetch_assoc($result);
        
        if (!$article) {
            header("location: news.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt);
}

// Fetch related articles
$related_sql = "SELECT * FROM news_articles WHERE category = ? AND id != ? ORDER BY date_published DESC LIMIT 3";
if ($stmt = mysqli_prepare($conn, $related_sql)) {
    mysqli_stmt_bind_param($stmt, "si", $article['category'], $article_id);
    mysqli_stmt_execute($stmt);
    $related_result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
}

$metrics = $monitor->getPerformanceMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - Bumbobi Child Support Uganda</title>
    
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
    .article-header {
        background: linear-gradient(135deg, #1a237e, #0d47a1);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }

    .article-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        border-radius: 15px;
        margin-bottom: 2rem;
    }

    .article-content {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .article-meta {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        color: #6c757d;
    }

    .article-meta > * {
        margin-right: 1.5rem;
    }

    .article-meta i {
        margin-right: 0.5rem;
    }

    .article-category {
        display: inline-block;
        background: #e3f2fd;
        color: #1a237e;
        padding: 0.3rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .article-title {
        color: #1a237e;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        line-height: 1.3;
    }

    .article-text {
        color: #495057;
        font-size: 1.1rem;
        line-height: 1.8;
        margin-bottom: 2rem;
    }

    .article-text p {
        margin-bottom: 1.5rem;
    }

    .article-text h2 {
        color: #1a237e;
        font-size: 1.8rem;
        font-weight: 600;
        margin: 2rem 0 1rem;
    }

    .article-text h3 {
        color: #1a237e;
        font-size: 1.5rem;
        font-weight: 600;
        margin: 1.5rem 0 1rem;
    }

    .article-text ul, .article-text ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }

    .article-text li {
        margin-bottom: 0.5rem;
    }

    .article-text blockquote {
        border-left: 4px solid #1a237e;
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #6c757d;
    }

    .article-text img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin: 1.5rem 0;
    }

    .article-tags {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e3f2fd;
    }

    .article-tag {
        display: inline-block;
        background: #e3f2fd;
        color: #1a237e;
        padding: 0.3rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        margin: 0 0.5rem 0.5rem 0;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .article-tag:hover {
        background: #1a237e;
        color: white;
    }

    .related-articles {
        margin-top: 3rem;
    }

    .related-title {
        color: #1a237e;
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 2rem;
    }

    .related-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .related-card:hover {
        transform: translateY(-5px);
    }

    .related-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 15px 15px 0 0;
    }

    .related-content {
        padding: 1.5rem;
    }

    .related-date {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .related-card-title {
        color: #1a237e;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .btn-read-more {
        background: #1a237e;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-read-more:hover {
        background: #0d47a1;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        color: white;
    }

    @media (max-width: 768px) {
        .article-header {
            padding: 3rem 0;
        }

        .article-image {
            height: 300px;
        }

        .article-title {
            font-size: 2rem;
        }

        .article-text {
            font-size: 1rem;
        }

        .related-title {
            font-size: 1.5rem;
        }
    }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Article Header -->
    <section class="article-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <span class="article-category"><?php echo htmlspecialchars($article['category']); ?></span>
                    <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="article-meta">
                        <span><i class="far fa-calendar-alt"></i><?php echo date('F j, Y', strtotime($article['date_published'])); ?></span>
                        <span><i class="far fa-user"></i>By <?php echo htmlspecialchars($article['author']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Article Content -->
    <section class="article-content-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-image">
                    
                    <div class="article-content">
                        <div class="article-text">
                            <?php echo $article['content']; ?>
                        </div>

                        <?php if (!empty($article['tags'])): ?>
                        <div class="article-tags">
                            <?php
                            $tags = explode(',', $article['tags']);
                            foreach ($tags as $tag):
                            ?>
                            <a href="news.php?tag=<?php echo urlencode(trim($tag)); ?>" class="article-tag">
                                <?php echo htmlspecialchars(trim($tag)); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles -->
    <section class="related-articles py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="related-title">Related Articles</h2>
                </div>
            </div>
            <div class="row">
                <?php while($related = mysqli_fetch_assoc($related_result)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="related-card">
                        <img src="<?php echo htmlspecialchars($related['image_path']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" class="related-image">
                        <div class="related-content">
                            <div class="related-date">
                                <i class="far fa-calendar-alt me-2"></i>
                                <?php echo date('F j, Y', strtotime($related['date_published'])); ?>
                            </div>
                            <h3 class="related-card-title"><?php echo htmlspecialchars($related['title']); ?></h3>
                            <a href="news-detail.php?id=<?php echo $related['id']; ?>" class="btn-read-more">
                                Read More <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/performance.js"></script>
</body>
</html> 