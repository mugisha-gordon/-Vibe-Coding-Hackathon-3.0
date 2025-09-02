<?php
// Create images directory if it doesn't exist
$imageDir = 'assets/images';
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
}

// Array of image URLs and their corresponding filenames
$images = [
    'about-hero.jpg' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&h=800&fit=crop',
    'support-activities.jpg' => 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?w=1200&h=800&fit=crop',
    'community-work.jpg' => 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=1200&h=800&fit=crop',
    'child-sponsorship.jpg' => 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?w=1200&h=800&fit=crop',
    'care-support.jpg' => 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?w=1200&h=800&fit=crop',
    'economic-empowerment.jpg' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=1200&h=800&fit=crop',
    'building-projects.jpg' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1200&h=800&fit=crop'
];

// Download each image
foreach ($images as $filename => $url) {
    $filepath = $imageDir . '/' . $filename;
    if (!file_exists($filepath)) {
        $imageContent = file_get_contents($url);
        if ($imageContent !== false) {
            file_put_contents($filepath, $imageContent);
            echo "Downloaded: $filename\n";
        } else {
            echo "Failed to download: $filename\n";
        }
    } else {
        echo "File already exists: $filename\n";
    }
}

echo "Image download process completed!\n";
?> 