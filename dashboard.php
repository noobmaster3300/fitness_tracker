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
    <title>Dashboard - Fitness Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            display: block;
        }
        .logout {
            text-align: center;
            margin-top: 40px;
        }
        .logout a {
            background-color: #dc3545;
            color: white;
            padding: 12px 25px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .logout a:hover {
            background-color: #c82333;
        }
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
    <p class="subtitle">Choose a feature to get started</p>

    <div class="grid">
        <a href="bmi.php" class="card">
            <span class="card-icon">📊</span>
            <strong>BMI Calculator</strong><br>
            Calculate your Body Mass Index
        </a>

        <a href="bodyfat.php" class="card">
            <span class="card-icon">📏</span>
            <strong>Body Fat Calculator</strong><br>
            Estimate your body fat percentage
        </a>

        <a href="blood.php" class="card">
            <span class="card-icon">❤️</span>
            <strong>Blood Donation Eligibility</strong><br>
            Check if you are eligible to donate blood
        </a>

        <a href="water.php" class="card">
            <span class="card-icon">💧</span>
            <strong>Water Intake</strong><br>
            Monitor your daily water consumption
        </a>

        <a href="exercise.php" class="card">
            <span class="card-icon">💪</span>
            <strong>Exercise Guide</strong><br>
            Browse workout routines and exercises
        </a>

        <a href="nutrition.php" class="card">
            <span class="card-icon">🥗</span>
            <strong>Nutrition Guide</strong><br>
            Learn about healthy eating habits
        </a>

        <a href="community.php" class="card">
            <span class="card-icon">👥</span>
            <strong>Community</strong><br>
            Connect with other fitness enthusiasts
        </a>

        <a href="news.php" class="card">
            <span class="card-icon">📰</span>
            <strong>Fitness News</strong><br>
            Stay updated with latest fitness trends
        </a>

        <a href="profile.php" class="card">
            <span class="card-icon">👤</span>
            <strong>Profile</strong><br>
            View and edit your profile information
        </a>

        <a href="life_in_weeks.php" class="card">
            <span class="card-icon">📅</span>
            <strong>Your Life in Weeks</strong><br>
            Visualize your life in weeks
        </a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>
