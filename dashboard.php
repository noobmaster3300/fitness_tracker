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
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto 0 auto;
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(60,60,120,0.10);
            padding: 40px 30px 30px 30px;
        }
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .dashboard-header .avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #888;
            margin-right: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .dashboard-header .welcome {
            flex: 1;
        }
        .dashboard-header h2 {
            margin: 0 0 4px 0;
            font-size: 2.1rem;
            color: #222;
            font-weight: 700;
        }
        .dashboard-header .subtitle {
            margin: 0;
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 400;
        }
        .dashboard-header .auth-buttons {
            display: flex;
            gap: 8px;
        }
        .dashboard-header .auth-btn {
            padding: 6px 16px;
            border: 1.5px solid #4f8cff;
            border-radius: 6px;
            background: transparent;
            color: #4f8cff;
            font-size: 0.97rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .dashboard-header .auth-btn:hover {
            background: #4f8cff;
            color: #fff;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 28px;
            margin: 0 0 40px 0;
        }
        .card {
            background: white;
            padding: 32px 20px 28px 20px;
            border-radius: 14px;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 4px 18px rgba(60,60,120,0.10);
            transition: border 0.18s ease;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }
        .card:hover {
            border: 2px solid #4f8cff;
        }
        .card-icon {
            font-size: 2.3rem;
            margin-bottom: 18px;
            display: block;
            color: #4f8cff;
            filter: drop-shadow(0 2px 4px rgba(79,140,255,0.08));
        }
        .card strong {
            font-size: 1.13rem;
            font-weight: 600;
            color: #222;
            display: block;
            margin-bottom: 8px;
        }
        .card br {
            display: none;
        }
        .card .description {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.4;
            margin-top: 0;
        }
        .logout {
            text-align: center;
            margin-top: 30px;
        }
        .logout a {
            background-color: #dc3545;
            color: white;
            padding: 13px 32px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.08rem;
            box-shadow: 0 2px 8px rgba(220,53,69,0.07);
            transition: background 0.18s;
        }
        .logout a:hover {
            background-color: #c82333;
        }
        @media (max-width: 768px) {
            .container {
                padding: 18px 4vw 18px 4vw;
            }
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .dashboard-header .avatar {
                margin-bottom: 10px;
                margin-right: 0;
            }

        }
    </style>
</head>
<body>

<div class="container">
    <div class="dashboard-header">
        <div class="avatar">👤</div>
        <div class="welcome">
            <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
            <div class="subtitle">Choose a feature to get started</div>
        </div>
        <div class="auth-buttons">
            <a href="index.php" class="auth-btn">Login</a>
            <a href="register.php" class="auth-btn">Sign Up</a>
        </div>
    </div>

    <div class="grid">
        <a href="bmi.php" class="card">
            <span class="card-icon">📊</span>
            <strong>BMI Calculator</strong>
            <div class="description">Calculate your Body Mass Index</div>
        </a>

        <a href="bodyfat.php" class="card">
            <span class="card-icon">📏</span>
            <strong>Body Fat Calculator</strong>
            <div class="description">Estimate your body fat percentage</div>
        </a>

        <a href="blood.php" class="card">
            <span class="card-icon">❤️</span>
            <strong>Blood Donation Eligibility</strong>
            <div class="description">Check if you are eligible to donate blood</div>
        </a>

        <a href="water.php" class="card">
            <span class="card-icon">💧</span>
            <strong>Water Intake</strong>
            <div class="description">Monitor your daily water consumption</div>
        </a>

        <a href="exercise.php" class="card">
            <span class="card-icon">💪</span>
            <strong>Exercise Guide</strong>
            <div class="description">Browse workout routines and exercises</div>
        </a>

        <a href="nutrition.php" class="card">
            <span class="card-icon">🥗</span>
            <strong>Nutrition Guide</strong>
            <div class="description">Learn about healthy eating habits</div>
        </a>

        <a href="community.php" class="card">
            <span class="card-icon">👥</span>
            <strong>Community</strong>
            <div class="description">Connect with other fitness enthusiasts</div>
        </a>

        <a href="news.php" class="card">
            <span class="card-icon">📰</span>
            <strong>Fitness News</strong>
            <div class="description">Stay updated with latest fitness trends</div>
        </a>

        <a href="profile.php" class="card">
            <span class="card-icon">👤</span>
            <strong>Profile</strong>
            <div class="description">View and edit your profile information</div>
        </a>

        <a href="life_in_weeks.php" class="card">
            <span class="card-icon">📅</span>
            <strong>Your Life in Weeks</strong>
            <div class="description">Visualize your life in weeks</div>
        </a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>



</body>
</html>
