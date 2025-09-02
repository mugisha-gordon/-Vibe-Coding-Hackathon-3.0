<?php
// Activity images to create
$activities = [
    'education' => ['Education Programs', '#2980b9'],
    'health' => ['Health Care', '#2ecc71'],
    'sports' => ['Sports Activities', '#e67e22'],
    'arts' => ['Arts & Crafts', '#9b59b6'],
    'music' => ['Music Programs', '#e74c3c'],
    'community' => ['Community Outreach', '#3498db'],
    'mentoring' => ['Mentoring', '#f1c40f'],
    'workshops' => ['Skill Workshops', '#1abc9c'],
    'cultural' => ['Cultural Events', '#d35400']
];

// Create HTML files for each activity
foreach ($activities as $activity => $data) {
    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 800px;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: {$data[1]};
            font-family: Arial, sans-serif;
        }
        .text {
            color: white;
            font-size: 40px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="text">{$data[0]}</div>
</body>
</html>
HTML;

    file_put_contents("assets/images/activities/{$activity}.html", $html);
}

echo "Activity image HTML files have been generated successfully!";
?> 