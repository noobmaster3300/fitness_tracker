<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dob = $_POST['dob'] ?? '';
    $lifespan = isset($_POST['lifespan']) && is_numeric($_POST['lifespan']) ? (int)$_POST['lifespan'] : 80;
    $_SESSION['life_in_weeks_dob'] = $dob;
    $_SESSION['life_in_weeks_lifespan'] = $lifespan;
} else {
    $dob = $_SESSION['life_in_weeks_dob'] ?? '';
    $lifespan = $_SESSION['life_in_weeks_lifespan'] ?? 80;
}

// Calculate weeks lived and total weeks
$weeks_lived = 0;
if ($dob) {
    $dob_date = DateTime::createFromFormat('Y-m-d', $dob);
    $now = new DateTime();
    if ($dob_date && $dob_date < $now) {
        $interval = $dob_date->diff($now);
        $weeks_lived = (int)floor($interval->days / 7);
    }
}
$total_weeks = $lifespan * 52;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Life in Weeks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .main-card {
            background: #fff;
            max-width: 900px;
            margin: 40px auto 0 auto;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 24px 24px 24px;
        }
        h2 {
            text-align: center;
            color: #222;
            font-size: 2.1rem;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 28px;
            font-size: 1.1rem;
        }
        .life-form {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .life-form label {
            color: #444;
            font-size: 1rem;
        }
        .life-form input[type="date"],
        .life-form input[type="number"] {
            padding: 7px 12px;
            border-radius: 5px;
            border: 1px solid #bbb;
            font-size: 1rem;
            margin-left: 6px;
        }
        .life-form button {
            padding: 8px 22px;
            border-radius: 5px;
            border: none;
            background: #4caf50;
            color: white;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .life-form button:hover {
            background: #388e3c;
        }
        .legend {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-bottom: 18px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.98rem;
            color: #444;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            display: inline-block;
        }
        .legend-color.lived {
            background: #4caf50;
        }
        .legend-color.present {
            background: #ff9800;
        }
        .legend-color.remaining {
            background: #fff;
            border: 1px solid #ccc;
        }
        .weeks-grid {
            display: grid;
            grid-template-columns: repeat(52, 1fr);
            gap: 4px;
            background: #f4f4f4;
            margin-bottom: 30px;
            border-radius: 7px;
            padding: 4px 0 4px 0;
            border: none;
        }
        .week {
            width: 9px;
            height: 6px;
            background: #fff;
            border-radius: 2px;
            transition: background 0.15s;
        }
        .week.lived {
            background: #4caf50;
        }
        .week.present {
            background: #ff9800;
        }
        .week:hover {
            background: #e0e0e0;
        }
        .life-highlights {
            margin-bottom: 30px;
            background: #f8fafc;
            border-radius: 8px;
            padding: 18px 22px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .life-highlights h3 {
            margin-top: 0;
            font-size: 1.2rem;
            color: #333;
        }
        .life-highlights p {
            color: #444;
            margin: 8px 0 0 0;
            font-size: 1.04rem;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
        }
        @media (max-width: 700px) {
            .main-card {
                padding: 12px 2vw 18px 2vw;
            }
            .weeks-grid {
                grid-template-columns: repeat(26, 1fr);
            }
        }
    </style>
</head>
<body>
<div class="main-card">
    <h2>Your Life in Weeks</h2>
    <p class="subtitle">Each square represents a week in your expected lifespan. Green squares are weeks you've already lived.</p>
    <form class="life-form" method="post" action="">
        <label>Date of Birth:
            <input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>" required>
        </label>
        <label>Lifespan (years):
            <input type="number" name="lifespan" min="1" max="120" value="<?php echo htmlspecialchars($lifespan); ?>" required>
        </label>
        <button type="submit">Update</button>
    </form>
    <div class="legend">
        <div class="legend-item"><span class="legend-color lived"></span> Past</div>
        <div class="legend-item"><span class="legend-color present"></span> Present</div>
        <div class="legend-item"><span class="legend-color remaining"></span> Future</div>
    </div>
    <div class="weeks-grid">
        <?php
        for ($i = 1; $i <= $total_weeks; $i++) {
            $class = '';
            if ($i < $weeks_lived) {
                $class = 'lived';
            } elseif ($i == $weeks_lived) {
                $class = 'present';
            }
            echo '<div class="week' . ($class ? ' ' . $class : '') . '"></div>';
        }
        ?>
    </div>
    <div class="life-highlights" style="max-width:600px;margin:0 auto 30px auto;padding:20px;background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin-top:0;">Life highlights</h3>
        <?php
        $percent = $total_weeks > 0 ? round(($weeks_lived / $total_weeks) * 100, 1) : 0;
        $days_lived = $weeks_lived * 7;
        $years_lived = $weeks_lived / 52;
        $years_left = $lifespan - $years_lived;
        $breaths = number_format($days_lived * 23000); // avg 23,000 breaths/day
        $sleeps = number_format($days_lived); // 1 sleep per day
        ?>
        <p>You've lived <strong><?php echo number_format($weeks_lived); ?></strong> weeks, which is <strong><?php echo $percent; ?>%</strong> of your expected life.</p>
        <p>That's <strong><?php echo number_format($days_lived); ?></strong> days of experience and approximately <strong><?php echo round($years_left, 1); ?></strong> years left.</p>
        <p>You've taken about <strong><?php echo $breaths; ?></strong> breaths and slept about <strong><?php echo $sleeps; ?></strong> times.</p>
    </div>
    <div class="back-link">
        <a href="dashboard.php">&larr; Back to Dashboard</a>
    </div>
</div>
</body>
</html> 