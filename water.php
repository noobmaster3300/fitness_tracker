<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'db_conn.php';
$user = $_SESSION['user'];
$user_id = $user['id'];
$date_today = date('Y-m-d');
$time_now = date('H:i:s');

// --- DB: Fetch or set daily goal ---
$goal_query = $conn->prepare("SELECT daily_goal FROM water_goal WHERE user_id = ?");
$goal_query->bind_param('i', $user_id);
$goal_query->execute();
$goal_result = $goal_query->get_result();
if ($goal_result->num_rows > 0) {
    $goal_row = $goal_result->fetch_assoc();
    $daily_goal = $goal_row['daily_goal'];
} else {
    $daily_goal = 2000;
    $insert_goal = $conn->prepare("INSERT INTO water_goal (user_id, daily_goal) VALUES (?, ?)");
    $insert_goal->bind_param('ii', $user_id, $daily_goal);
    $insert_goal->execute();
}

// --- Handle goal update ---
if (isset($_POST['set_goal'])) {
    $new_goal = intval($_POST['goalInput']);
    if ($new_goal >= 500 && $new_goal <= 5000) {
        $update_goal = $conn->prepare("REPLACE INTO water_goal (user_id, daily_goal) VALUES (?, ?)");
        $update_goal->bind_param('ii', $user_id, $new_goal);
        $update_goal->execute();
        $daily_goal = $new_goal;
    }
}

// --- Handle add water ---
if (isset($_POST['add_water'])) {
    $entry_date = isset($_POST['entry_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['entry_date']) ? $_POST['entry_date'] : $date_today;
    if (isset($_POST['amount']) && intval($_POST['amount']) > 0) {
        $amount = intval($_POST['amount']);
    } else {
        $amount = intval($_POST['add_water']);
    }
    if ($amount > 0) {
        $insert = $conn->prepare("INSERT INTO water_intake (user_id, date, amount, time) VALUES (?, ?, ?, ?)");
        $insert->bind_param('isis', $user_id, $entry_date, $amount, $time_now);
        $insert->execute();
    }
}

// --- Handle reset ---
if (isset($_POST['reset_day'])) {
    $delete = $conn->prepare("DELETE FROM water_intake WHERE user_id = ? AND date = ?");
    $delete->bind_param('is', $user_id, $date_today);
    $delete->execute();
}

// --- Handle reset all ---
if (isset($_POST['reset_all'])) {
    $delete_all = $conn->prepare("DELETE FROM water_intake WHERE user_id = ?");
    $delete_all->bind_param('i', $user_id);
    $delete_all->execute();
}

// --- Handle delete individual entry ---
if (isset($_POST['delete_entry_id'])) {
    $entry_id = intval($_POST['delete_entry_id']);
    $delete_entry = $conn->prepare("DELETE FROM water_intake WHERE id = ? AND user_id = ?");
    $delete_entry->bind_param('ii', $entry_id, $user_id);
    $delete_entry->execute();
}

// --- Date selection logic ---
$selected_date = $date_today;
if (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    $selected_date = $_GET['date'];
}

// --- Fetch selected day's log ---
$log_query = $conn->prepare("SELECT id, amount, time FROM water_intake WHERE user_id = ? AND date = ? ORDER BY time DESC");
$log_query->bind_param('is', $user_id, $selected_date);
$log_query->execute();
$log_result = $log_query->get_result();
$water_log = [];
$current_intake = 0;
while ($row = $log_result->fetch_assoc()) {
    $water_log[] = $row;
    $current_intake += $row['amount'];
}
$percentage = $daily_goal > 0 ? min(round(($current_intake / $daily_goal) * 100), 100) : 0;
$achieved = $current_intake >= $daily_goal;

// --- Weekly stats for chart based on selected date ---
$selected_date_obj = new DateTime($selected_date);
$start_of_week = clone $selected_date_obj;
$start_of_week->modify('-6 days'); // 7 days ending with selected date

$week_dates = [];
$week_totals = [];
for ($i = 0; $i < 7; $i++) {
    $date = $start_of_week->format('Y-m-d');
    $week_dates[] = $date;
    $week_totals[$date] = 0;
    $start_of_week->modify('+1 day');
}
$first_day = $week_dates[0];
$last_day = $week_dates[6];
$week_query = $conn->prepare("SELECT date, SUM(amount) as total FROM water_intake WHERE user_id = ? AND date BETWEEN ? AND ? GROUP BY date");
$week_query->bind_param('iss', $user_id, $first_day, $last_day);
$week_query->execute();
$week_result = $week_query->get_result();
while ($row = $week_result->fetch_assoc()) {
    $week_totals[$row['date']] = (int)$row['total'];
}
$chart_labels = array_map(function($d) {
    return [date('D', strtotime($d)), date('d-m-Y', strtotime($d))];
}, $week_dates);
$chart_data = array_values($week_totals);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .home-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 30px 0 0 30px;
        }
        .home-btn:hover {
            background-color: #545b62;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 24px 18px 18px 18px;
            border: 1px solid #e0e0e0;
        }
        h1 {
            text-align: center;
            color: #222;
            font-size: 1.7em;
            margin-bottom: 12px;
        }
        .chart-area {
            margin-bottom:12px;
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .calendar-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }
        .goal-section {
            margin-bottom: 10px;
        }
        .goal-input {
            display: flex;
            gap: 8px;
            margin-bottom: 6px;
        }
        .goal-input input {
            width: 80px;
            padding: 6px;
            font-size: 1em;
        }
        .goal-input button {
            padding: 6px 14px;
            font-size: 1em;
        }
        .progress-container {
            margin: 10px 0 10px 0;
        }
        .progress-bar {
            width: 100%;
            height: 14px;
            background: #e0e0e0;
            border-radius: 7px;
            overflow: hidden;
            margin: 8px 0 0 0;
        }
        .progress-fill {
            height: 100%;
            background: #2196f3;
            width: <?php echo $percentage; ?>%;
            transition: width 0.3s;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            font-size: 1em;
            margin-bottom: 2px;
        }
        .quick-add {
            display: flex;
            gap: 8px;
            margin: 10px 0 6px 0;
        }
        .quick-btn {
            flex: 1;
            padding: 10px 0;
            background: #90caf9;
            color: #222;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        .quick-btn:hover {
            background: #42a5f5;
            color: #fff;
        }
        .custom-add {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }
        .custom-add input {
            flex: 1;
            padding: 8px;
            font-size: 1em;
        }
        .custom-add button {
            padding: 8px 14px;
            font-size: 1em;
        }
        .achievement {
            background: #43a047;
            color: #fff;
            text-align: center;
            padding: 7px;
            border-radius: 5px;
            margin-bottom: 8px;
            <?php if ($achieved) echo 'display:block;'; else echo 'display:none;'; ?>
        }
        .history {
            margin-top: 8px;
            max-height: 220px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 5px;
            background: #fafbfc;
            padding: 8px 10px;
        }
        .history h3 {
            font-size: 1.1em;
            margin-bottom: 6px;
            color: #333;
        }
        .history-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.98em;
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }
        .history-item:last-child {
            border-bottom: none;
        }
        .reset-btn {
            width: 100%;
            padding: 8px;
            background: #e57373;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            margin-top: 10px;
            cursor: pointer;
        }
        .reset-btn:hover {
            background: #c62828;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <a href="dashboard.php" class="home-btn">← Go to Dashboard</a>
    <div class="container">
        <h1>Water Tracker</h1>
        <div class="chart-area">
            <canvas id="weeklyChart" width="600" height="300"></canvas>
        </div>
        <form method="get" class="calendar-bar">
            <input type="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>" max="<?php echo $date_today; ?>">
            <button type="submit">Go</button>
            <?php if ($selected_date !== $date_today): ?>
                <a href="?date=<?php echo $date_today; ?>" style="margin-left:10px;text-decoration:none;padding:6px 12px;background:#eee;border-radius:4px;">Today</a>
            <?php endif; ?>
        </form>
        <div class="goal-section">
            <form class="goal-input" method="post">
                <input type="number" name="goalInput" placeholder="<?php echo $daily_goal; ?>" min="500" max="5000">
                <button type="submit" name="set_goal">Set Goal</button>
            </form>
            <div style="font-size:0.98em; color:#555;">Daily Goal: <b><?php echo $daily_goal; ?> ml</b></div>
        </div>
        <div class="progress-container">
            <div class="stats">
                <div>Current: <b><?php echo $current_intake; ?> ml</b></div>
                <div>Goal: <b><?php echo $daily_goal; ?> ml</b></div>
                <div><?php echo $percentage; ?>%</div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
        </div>
        <div class="achievement">🎉 Goal reached! Great job!</div>
        <form method="post" style="margin:0;">
            <input type="hidden" name="entry_date" value="<?php echo htmlspecialchars($selected_date); ?>">
            <div class="quick-add">
                <button class="quick-btn" type="submit" name="add_water" value="250">+250ml</button>
                <button class="quick-btn" type="submit" name="add_water" value="500">+500ml</button>
                <button class="quick-btn" type="submit" name="add_water" value="750">+750ml</button>
                <button class="quick-btn" type="submit" name="add_water" value="1000">+1000ml</button>
            </div>
        </form>
        <form class="custom-add" method="post">
            <input type="hidden" name="entry_date" value="<?php echo htmlspecialchars($selected_date); ?>">
            <input type="number" name="amount" placeholder="Custom (ml)" min="1">
            <button type="submit" name="add_water">Add</button>
        </form>
        <div class="history">
            <h3><?php echo ($selected_date === $date_today) ? "Today's Log" : 'Log for ' . htmlspecialchars($selected_date); ?></h3>
            <?php if (count($water_log) === 0): ?>
                <div class="history-item"><span>No water logged<?php echo ($selected_date === $date_today) ? ' yet today' : ' for this day'; ?></span></div>
            <?php else: ?>
                <?php foreach ($water_log as $entry): ?>
                    <div class="history-item">
                        <span><?php echo htmlspecialchars($entry['time']); ?></span>
                        <span><?php echo htmlspecialchars($entry['amount']); ?>ml</span>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_entry_id" value="<?php echo $entry['id']; ?>">
                            <button type="submit" onclick="return confirm('Delete this entry?');" style="background:#e57373;color:#fff;border:none;padding:2px 8px;border-radius:3px;cursor:pointer;">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <form method="post">
            <button class="reset-btn" type="submit" name="reset_day">Reset Today</button>
            <button class="reset-btn" type="submit" name="reset_all" onclick="return confirm('Are you sure you want to delete ALL your water logs? This cannot be undone!');">Reset All</button>
        </form>
    </div>
    <script>
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Daily Intake (ml)',
                data: <?php echo json_encode($chart_data); ?>,
                backgroundColor: '#90caf9',
                borderColor: '#2196f3',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 500 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
    </script>
</body>
</html>


