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
    <title>Water Tracker - Fitness Tracker</title>
    <link rel="stylesheet" href="css/shared.css">
    <style>
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
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #007bff, #0056b3);
            transition: width 0.3s ease;
        }
        .progress-fill.achieved {
            background: linear-gradient(90deg, #28a745, #1e7e34);
        }
        .progress-text {
            text-align: center;
            margin-top: 5px;
            font-size: 0.9em;
            color: #666;
        }
        .add-water-section {
            margin: 15px 0;
        }
        .quick-add-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .quick-add-btn {
            padding: 8px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .quick-add-btn:hover {
            background: #0056b3;
        }
        .custom-add {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .custom-add input {
            width: 80px;
            padding: 6px;
            font-size: 1em;
        }
        .custom-add button {
            padding: 6px 14px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        .custom-add button:hover {
            background: #1e7e34;
        }
        .log-section {
            margin-top: 20px;
        }
        .log-entry {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .log-entry:last-child {
            border-bottom: none;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.8em;
        }
        .delete-btn:hover {
            background: #c82333;
        }
        .reset-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .reset-btn {
            padding: 8px 16px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .reset-btn:hover {
            background: #545b62;
        }
        .reset-btn.danger {
            background: #dc3545;
        }
        .reset-btn.danger:hover {
            background: #c82333;
        }
        .date-nav {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 15px;
        }
        .date-nav input {
            padding: 6px;
            font-size: 1em;
        }
        .date-nav button {
            padding: 6px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .date-nav button:hover {
            background: #0056b3;
        }
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .stat-card h3 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.1em;
        }
        .stat-card p {
            margin: 0;
            color: #007bff;
            font-size: 1.5em;
            font-weight: bold;
        }
        .achievement-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <a href="dashboard.php" class="back-btn" title="Back to Dashboard">&#8592;</a>
        <div class="title-section">
            <h1>Water Tracker</h1>
            <div class="subtitle">Stay hydrated and track your daily water intake</div>
        </div>
    </div>

    <div class="card mb-20" style="margin-bottom:18px;">
        <h3 style="margin-bottom:10px;">Weekly Overview</h3>
        <div class="chart-area" style="margin-bottom:0; justify-content:center;">
            <canvas id="waterChart" width="650" height="200"></canvas>
        </div>
    </div>

    <div class="card mb-20">
        <div class="stats-summary">
            <div class="stat-card">
                <h3>Today's Intake</h3>
                <p><?php echo $current_intake; ?>ml</p>
            </div>
            <div class="stat-card">
                <h3>Daily Goal</h3>
                <p><?php echo $daily_goal; ?>ml</p>
            </div>
            <div class="stat-card">
                <h3>Progress</h3>
                <p><?php echo $percentage; ?>%</p>
            </div>
        </div>

        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill <?php echo $achieved ? 'achieved' : ''; ?>" style="width: <?php echo $percentage; ?>%"></div>
            </div>
            <div class="progress-text">
                <?php echo $current_intake; ?>ml / <?php echo $daily_goal; ?>ml
                <?php if ($achieved): ?>
                    <span class="achievement-badge">Goal Achieved! 🎉</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card mb-20">
        <h3>Set Daily Goal</h3>
        <div class="goal-section">
            <form method="post" class="goal-input">
                <input type="number" name="goalInput" value="<?php echo $daily_goal; ?>" min="500" max="5000" step="100" required>
                <button type="submit" name="set_goal">Update Goal</button>
            </form>
            <small>Recommended: 2000ml (8 glasses) per day</small>
        </div>
    </div>

    <div class="card mb-20">
        <h3>Add Water Intake</h3>
        <div class="add-water-section">
            <div class="quick-add-buttons">
                <form method="post" style="display: inline;">
                    <button type="submit" name="add_water" value="200" class="quick-add-btn">200ml</button>
                </form>
                <form method="post" style="display: inline;">
                    <button type="submit" name="add_water" value="300" class="quick-add-btn">300ml</button>
                </form>
                <form method="post" style="display: inline;">
                    <button type="submit" name="add_water" value="500" class="quick-add-btn">500ml</button>
                </form>
                <form method="post" style="display: inline;">
                    <button type="submit" name="add_water" value="1000" class="quick-add-btn">1000ml</button>
                </form>
            </div>
            <form method="post" class="custom-add">
                <input type="number" name="amount" placeholder="Custom amount" min="1" max="2000" step="50">
                <button type="submit" name="add_water" value="0">Add</button>
            </form>
        </div>
    </div>

    <div class="card mb-20">
        <h3>Today's Log</h3>
        <div class="date-nav">
            <input type="date" id="dateSelect" value="<?php echo $selected_date; ?>" onchange="changeDate(this.value)">
            <button onclick="goToToday()">Today</button>
        </div>
        
        <div class="log-section">
            <?php if (empty($water_log)): ?>
                <p class="text-center">No water intake recorded for this date.</p>
            <?php else: ?>
                <?php foreach ($water_log as $entry): ?>
                    <div class="log-entry">
                        <span><?php echo $entry['amount']; ?>ml at <?php echo date('H:i', strtotime($entry['time'])); ?></span>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="delete_entry_id" value="<?php echo $entry['id']; ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Delete this entry?')">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="reset-buttons">
            <form method="post" style="display: inline;">
                <button type="submit" name="reset_day" class="reset-btn" onclick="return confirm('Reset today\'s entries?')">Reset Today</button>
            </form>
            <form method="post" style="display: inline;">
                <button type="submit" name="reset_all" class="reset-btn danger" onclick="return confirm('Delete ALL water intake records? This cannot be undone!')">Reset All</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function changeDate(date) {
    window.location.href = '?date=' + date;
}

function goToToday() {
    window.location.href = 'water.php';
}

// Chart.js for weekly overview
const ctx = document.getElementById('waterChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_map(function($label) { return $label[0]; }, $chart_labels)); ?>,
        datasets: [{
            label: 'Water Intake (ml)',
            data: <?php echo json_encode($chart_data); ?>,
            backgroundColor: 'rgba(0, 123, 255, 0.7)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Milliliters'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Day of Week'
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
</body>
</html>


