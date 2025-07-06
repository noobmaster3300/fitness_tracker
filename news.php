<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fitness News - Fitness Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/shared.css">
    <style>
        .news-item {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .news-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .news-body {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .news-meta {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }
        .news-category {
            background-color: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-right: 15px;
        }
        .news-date {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <a href="dashboard.php" class="back-btn" title="Back to Dashboard">&#8592;</a>
        <div class="title-section">
            <h1>Fitness News & Updates</h1>
            <div class="subtitle">Stay updated with the latest fitness trends and health tips</div>
        </div>
    </div>

    <div class="card">
        <div class="news-item">
            <div class="news-title">New Study Shows Benefits of Morning Exercise</div>
            <div class="news-body">
                Recent research indicates that exercising in the morning can boost metabolism throughout the day and improve sleep quality. The study, conducted with over 1,000 participants, found that morning workouts led to 20% better fat burning compared to evening sessions.
            </div>
            <div class="news-meta">
                <span class="news-category">Research</span>
                <span class="news-date">December 15, 2024</span>
            </div>
        </div>

        <div class="news-item">
            <div class="news-title">10 Simple Ways to Stay Active at Home</div>
            <div class="news-body">
                With more people working from home, finding ways to stay active has become crucial. From desk exercises to home workout routines, discover simple yet effective ways to maintain your fitness without leaving your house.
            </div>
            <div class="news-meta">
                <span class="news-category">Tips</span>
                <span class="news-date">December 12, 2024</span>
            </div>
        </div>

        <div class="news-item">
            <div class="news-title">The Importance of Hydration in Fitness</div>
            <div class="news-body">
                Proper hydration is essential for optimal performance during workouts. Learn about the signs of dehydration, how much water you should drink, and the best times to hydrate for maximum fitness benefits.
            </div>
            <div class="news-meta">
                <span class="news-category">Health</span>
                <span class="news-date">December 10, 2024</span>
            </div>
        </div>

        <div class="news-item">
            <div class="news-title">Mental Health Benefits of Regular Exercise</div>
            <div class="news-body">
                Exercise isn't just good for your body—it's also crucial for mental health. Regular physical activity has been shown to reduce stress, anxiety, and depression while improving mood and cognitive function.
            </div>
            <div class="news-meta">
                <span class="news-category">Wellness</span>
                <span class="news-date">December 8, 2024</span>
            </div>
        </div>

        <div class="news-item">
            <div class="news-title">Nutrition Myths Debunked</div>
            <div class="news-body">
                There are many misconceptions about nutrition and fitness. We debunk common myths about carbs, protein timing, meal frequency, and more to help you make informed decisions about your diet.
            </div>
            <div class="news-meta">
                <span class="news-category">Nutrition</span>
                <span class="news-date">December 5, 2024</span>
            </div>
        </div>

        <div class="news-item">
            <div class="news-title">Building a Sustainable Fitness Routine</div>
            <div class="news-body">
                Creating a fitness routine that you can maintain long-term is key to achieving your health goals. Learn how to set realistic goals, create balanced workouts, and stay motivated on your fitness journey.
            </div>
            <div class="news-meta">
                <span class="news-category">Fitness</span>
                <span class="news-date">December 3, 2024</span>
            </div>
        </div>
    </div>
</div>

</body>
</html>
