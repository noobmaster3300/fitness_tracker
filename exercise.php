<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
require_once 'includes/db.php';

// Get user id from session (assuming username is stored)
$username = $_SESSION['user']['username'];
$user_id = null;
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
if (!$user_id) $user_id = 0; // fallback if not found

// Reset routine handler
if (isset($_GET['reset_routine']) && $_GET['reset_routine'] === '1') {
    unset($_SESSION['routine']);
    header("Location: exercise.php");
    exit;
}

// PHP handler for removing custom exercise (must be before any output)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_custom_exercise_id'])) {
    require_once 'includes/db.php';
    $username = $_SESSION['user']['username'];
    $user_id = null;
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    if (!$user_id) $user_id = 0;
    $remove_id = intval($_POST['remove_custom_exercise_id']);
    $stmt = $conn->prepare("DELETE FROM exercises WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $remove_id, $user_id);
    $stmt->execute();
    $stmt->close();
    // Remove from session routine if present
    if (isset($_SESSION['routine'])) {
        $_SESSION['routine'] = array_values(array_filter($_SESSION['routine'], function($item) use ($remove_id) {
            return $item['exercise_id'] != $remove_id;
        }));
    }
    header("Location: exercise.php");
    exit;
}

// Add custom exercise
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exercise'])) {
    $name = trim($_POST['exercise_name']);
    $type = trim($_POST['exercise_type']);
    $desc = trim($_POST['exercise_desc']);
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO exercises (user_id, name, type, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $name, $type, $desc);
        $stmt->execute();
        $stmt->close();
    }
}

// Add exercise to routine (session)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_routine'])) {
    $ex_id = intval($_POST['routine_exercise_id']);
    $sets = intval($_POST['routine_sets']);
    $reps = intval($_POST['routine_reps']);
    if (!isset($_SESSION['routine'])) $_SESSION['routine'] = [];
    $_SESSION['routine'][] = [
        'exercise_id' => $ex_id,
        'sets' => $sets,
        'reps' => $reps
    ];
}
// Remove from routine
if (isset($_GET['remove']) && isset($_SESSION['routine'][$_GET['remove']])) {
    unset($_SESSION['routine'][$_GET['remove']]);
    $_SESSION['routine'] = array_values($_SESSION['routine']);
}

// Fetch all exercises (predefined and user-created)
$exercises = [];
$stmt = $conn->prepare("SELECT * FROM exercises WHERE user_id IS NULL OR user_id = ? ORDER BY name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $exercises[$row['id']] = $row;
}
$stmt->close();
// Large static pool of exercises (id => [name, type, description])
$static_exercises = [
    1001 => ['name'=>'Push-ups','type'=>'Strength','description'=>'Classic upper body exercise.'],
    1002 => ['name'=>'Squats','type'=>'Strength','description'=>'Lower body exercise.'],
    1003 => ['name'=>'Jumping Jacks','type'=>'Cardio','description'=>'Full body warm-up.'],
    1004 => ['name'=>'Plank','type'=>'Core','description'=>'Hold a straight body position.'],
    1005 => ['name'=>'Lunges','type'=>'Strength','description'=>'Leg and glute exercise.'],
    1006 => ['name'=>'Burpees','type'=>'Cardio','description'=>'Full body explosive movement.'],
    1007 => ['name'=>'Mountain Climbers','type'=>'Cardio','description'=>'Core and cardio move.'],
    1008 => ['name'=>'Sit-ups','type'=>'Core','description'=>'Abdominal exercise.'],
    1009 => ['name'=>'Crunches','type'=>'Core','description'=>'Abdominal exercise.'],
    1010 => ['name'=>'Bicycle Crunches','type'=>'Core','description'=>'Oblique and ab exercise.'],
    1011 => ['name'=>'Tricep Dips','type'=>'Strength','description'=>'Triceps and chest.'],
    1012 => ['name'=>'Pull-ups','type'=>'Strength','description'=>'Back and biceps.'],
    1013 => ['name'=>'Chin-ups','type'=>'Strength','description'=>'Biceps and back.'],
    1014 => ['name'=>'Bench Press','type'=>'Strength','description'=>'Chest and triceps.'],
    1015 => ['name'=>'Deadlift','type'=>'Strength','description'=>'Full body strength.'],
    1016 => ['name'=>'Bicep Curls','type'=>'Strength','description'=>'Biceps isolation.'],
    1017 => ['name'=>'Shoulder Press','type'=>'Strength','description'=>'Shoulders and triceps.'],
    1018 => ['name'=>'Leg Raises','type'=>'Core','description'=>'Lower abs.'],
    1019 => ['name'=>'Russian Twists','type'=>'Core','description'=>'Obliques.'],
    1020 => ['name'=>'High Knees','type'=>'Cardio','description'=>'Legs and cardio.'],
    1021 => ['name'=>'Jump Rope','type'=>'Cardio','description'=>'Cardio and coordination.'],
    1022 => ['name'=>'Step-ups','type'=>'Strength','description'=>'Legs and glutes.'],
    1023 => ['name'=>'Wall Sit','type'=>'Strength','description'=>'Isometric leg hold.'],
    1024 => ['name'=>'Calf Raises','type'=>'Strength','description'=>'Calves.'],
    1025 => ['name'=>'Superman','type'=>'Core','description'=>'Lower back.'],
    1026 => ['name'=>'Glute Bridge','type'=>'Strength','description'=>'Glutes and hamstrings.'],
    1027 => ['name'=>'Side Plank','type'=>'Core','description'=>'Obliques and core.'],
    1028 => ['name'=>'Flutter Kicks','type'=>'Core','description'=>'Lower abs.'],
    1029 => ['name'=>'Reverse Crunch','type'=>'Core','description'=>'Lower abs.'],
    1030 => ['name'=>'Box Jumps','type'=>'Cardio','description'=>'Explosive leg power.'],
];
// Merge static and DB exercises, avoiding duplicates by name (case-insensitive)
$all_exercises = $exercises;
foreach ($static_exercises as $sid => $sex) {
    $found = false;
    foreach ($exercises as $ex) {
        if (strcasecmp($ex['name'], $sex['name']) === 0) {
            $found = true; break;
        }
    }
    if (!$found) {
        $all_exercises[$sid] = [
            'id' => $sid,
            'name' => $sex['name'],
            'type' => $sex['type'],
            'description' => $sex['description']
        ];
    }
}
// Helper for safe output
function safe($v) { return htmlspecialchars($v ?? '', ENT_QUOTES); }

// --- Save Routine Handler ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_routine']) && !empty($_SESSION['routine'])) {
    $routine_name = trim($_POST['routine_name']);
    if ($routine_name !== '') {
        // Insert into workout_routines
        $stmt = $conn->prepare("INSERT INTO workout_routines (user_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $routine_name);
        $stmt->execute();
        $routine_id = $stmt->insert_id;
        $stmt->close();
        // Insert exercises
        $pos = 0;
        foreach ($_SESSION['routine'] as $item) {
            $stmt = $conn->prepare("INSERT INTO workout_routine_exercises (routine_id, exercise_id, sets, reps, position) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiii", $routine_id, $item['exercise_id'], $item['sets'], $item['reps'], $pos);
            $stmt->execute();
            $stmt->close();
            $pos++;
        }
        $save_msg = "Routine saved!";
    }
}
// --- Load Routine Handler ---
if (isset($_GET['load_routine'])) {
    $routine_id = intval($_GET['load_routine']);
    // Fetch exercises for this routine
    $stmt = $conn->prepare("SELECT exercise_id, sets, reps FROM workout_routine_exercises WHERE routine_id = ? ORDER BY position ASC, id ASC");
    $stmt->bind_param("i", $routine_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $routine = [];
    while ($row = $res->fetch_assoc()) {
        $routine[] = [
            'exercise_id' => $row['exercise_id'],
            'sets' => $row['sets'],
            'reps' => $row['reps']
        ];
    }
    $stmt->close();
    $_SESSION['routine'] = $routine;
    header("Location: exercise.php");
    exit;
}
// --- Delete Routine Handler ---
if (isset($_GET['delete_routine'])) {
    $routine_id = intval($_GET['delete_routine']);
    // Delete exercises first
    $stmt = $conn->prepare("DELETE FROM workout_routine_exercises WHERE routine_id = ?");
    $stmt->bind_param("i", $routine_id);
    $stmt->execute();
    $stmt->close();
    // Delete routine
    $stmt = $conn->prepare("DELETE FROM workout_routines WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $routine_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: exercise.php");
    exit;
}
// --- Fetch User's Saved Routines ---
$saved_routines = [];
$stmt = $conn->prepare("SELECT id, name, created_at FROM workout_routines WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $saved_routines[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build Your Workout Routine</title>
    <link rel="stylesheet" href="css/shared.css">
    <style>
        .section {
            margin-bottom: 36px;
        }
        .exercise-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 32px;
            margin-bottom: 24px;
        }
        .exercise-card {
            background: #fafdff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 22px 16px 16px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 240px;
            position: relative;
            transition: box-shadow 0.2s, transform 0.18s;
            max-width: 260px;
            margin: 0 auto;
            border: none;
        }
        .exercise-card:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.13);
            transform: translateY(-4px) scale(1.03);
        }
        .exercise-img-placeholder {
            width: 72px;
            height: 72px;
            background: #e3e9f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #b0b8c1;
            font-size: 1.1em;
            margin-bottom: 14px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .exercise-info {
            text-align: center;
            margin-bottom: 10px;
        }
        .exercise-name {
            font-weight: 600;
            font-size: 1.08em;
            color: #1a1a1a;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.5px;
        }
        .exercise-type {
            font-size: 0.97em;
            color: #3b82f6;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .exercise-desc {
            font-size: 0.97em;
            color: #6b7280;
            margin-bottom: 6px;
            min-height: 32px;
            max-height: 32px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .add-to-routine-form {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 8px;
            background: #f3f6fa;
            border-radius: 8px;
            padding: 6px 0 2px 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .add-to-routine-form input[type=number] {
            width: 44px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #d1d5db;
            font-size: 0.97em;
            background: #fff;
            color: #222;
            outline: none;
            transition: border 0.15s;
        }
        .add-to-routine-form input[type=number]:focus {
            border: 1.5px solid #3b82f6;
        }
        .add-to-routine-form .btn {
            padding: 5px 14px;
            font-size: 0.97em;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }
        .add-to-routine-form .btn:hover {
            background: #2563eb;
        }
        @media (max-width: 900px) {
            .exercise-grid { grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); }
            .exercise-card { min-height: 170px; max-width: 170px; }
        }
        @media (max-width: 600px) {
            .exercise-grid { grid-template-columns: 1fr 1fr; }
            .exercise-card { min-height: 120px; max-width: 100%; }
        }
        .form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            background: #f3f6fa;
            border-radius: 8px;
            padding: 10px 0 2px 0;
        }
        .form-row input, .form-row select {
            flex: 1;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            background: #fff;
            color: #222;
            font-size: 1em;
        }
        .form-row textarea {
            flex: 2;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            background: #fff;
            color: #222;
            font-size: 1em;
        }
        .form-row .btn {
            background: #3b82f6;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.15s;
        }
        .form-row .btn:hover {
            background: #2563eb;
        }
        .custom-badge-small {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 1px 6px 1px 2px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            font-weight: 500;
            z-index: 2;
            margin-right: 4px;
        }
        .exercise-card form[method='post'] button[title='Remove'] {
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255,255,255,0.9);
            transition: background 0.15s;
        }
        .exercise-card form[method='post'] button[title='Remove']:hover {
            background: #ffeaea;
        }
        .remove-btn {
            background: #dc3545 !important;
            color: #fff !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            text-decoration: none !important;
            font-size: 0.9em !important;
        }
        .remove-btn:hover {
            background: #c82333 !important;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f3f6fa;
            font-weight: 600;
            color: #374151;
        }
        tr:hover {
            background: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <a href="dashboard.php" class="back-btn" title="Back to Dashboard">&#8592;</a>
            <div class="title-section">
                <h1>Build Your Workout Routine</h1>
                <div class="subtitle">Create and manage your exercise routines</div>
            </div>
            <div></div> <!-- Empty div for flex spacing -->
        </div>
        
        <div class="section">
            <h3>Exercise Pool</h3>
            <div class="exercise-grid">
            <?php foreach ($all_exercises as $ex): ?>
                <div class="exercise-card" style="position:relative;">
                    <?php if (!empty($ex['user_id'])): ?>
                        <form method="post" style="position:absolute;top:10px;right:10px;z-index:3;" onsubmit="return confirm('Remove this custom exercise?');">
                            <input type="hidden" name="remove_custom_exercise_id" value="<?php echo $ex['id']; ?>">
                            <button type="submit" style="background:none;border:none;color:#f44336;font-size:1.3em;cursor:pointer;line-height:1;" title="Remove">&times;</button>
                        </form>
                        <div class="custom-badge-small">
                            <svg width="18" height="18" viewBox="0 0 38 38" fill="none" style="vertical-align:middle;">
                              <circle cx="19" cy="19" r="19" fill="#f59e42"/>
                              <path d="M19 11l2.47 5.01 5.53.8-4 3.9.94 5.5L19 23.27l-4.94 2.58.94-5.5-4-3.9 5.53-.8L19 11z" fill="#fff"/>
                            </svg>
                            <span style="font-size:0.75em; color:#f59e42; margin-left:2px;">Custom</span>
                        </div>
                    <?php endif; ?>
                    <div class="exercise-img-placeholder">
                        <?php if (strcasecmp($ex['name'], 'Push-ups') === 0): ?>
                            <img src="exercise images/push up.png" alt="Push-ups" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        <?php else: ?>
                            Image<br>Here
                        <?php endif; ?>
                    </div>
                    <div class="exercise-info">
                        <div class="exercise-name"><?php echo safe($ex['name']); ?></div>
                        <div class="exercise-type"><?php echo safe($ex['type']); ?></div>
                        <div class="exercise-desc"><?php echo safe($ex['description']); ?></div>
                    </div>
                    <form method="post" class="add-to-routine-form">
                        <input type="hidden" name="routine_exercise_id" value="<?php echo $ex['id']; ?>">
                        <input type="number" name="routine_sets" min="1" max="10" placeholder="Sets" required>
                        <input type="number" name="routine_reps" min="1" max="50" placeholder="Reps">
                        <button type="submit" name="add_to_routine" class="btn">Add to Routine</button>
                    </form>
                </div>
            <?php endforeach; ?>
            </div>
            <div style="font-size:0.95em; color:#666; margin-bottom:10px; margin-top:20px;">Can't find your exercise? Add a custom one below!</div>
        </div>
        
        <div class="section">
            <h3>Add Custom Exercise</h3>
            <form method="post" class="form-row">
                <input type="text" name="exercise_name" placeholder="Exercise Name" required>
                <select name="exercise_type">
                    <option value="Strength">Strength</option>
                    <option value="Cardio">Cardio</option>
                    <option value="Core">Core</option>
                    <option value="Flexibility">Flexibility</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" name="exercise_desc" placeholder="Description">
                <button type="submit" name="add_exercise" class="btn">Add Exercise</button>
            </form>
        </div>
        
        <div class="section" style="margin-bottom:0;">
            <button id="export-routine-btn" class="btn" style="margin-bottom:16px; float:right;">Export as Image</button>
            <?php if (!empty($_SESSION['routine'])): ?>
            <a href="?reset_routine=1" class="btn" style="margin-bottom:16px; float:right; margin-right:10px; background:#dc3545;" onclick="return confirm('Are you sure you want to reset your current routine? This will remove all exercises.');">Reset Routine</a>
            <?php endif; ?>
            <div style="clear:both;"></div>
            <h3 style="text-align:center; margin-bottom:18px;">Your Current Routine</h3>
            <?php if (!empty($_SESSION['routine'])): ?>
            <table style="width:100%; background:#fafdff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                <tr style="background:#f3f6fa;"><th>#</th><th>Name</th><th>Type</th><th>Sets</th><th>Reps</th><th>Remove</th></tr>
                <?php foreach ($_SESSION['routine'] as $i => $item):
                    $ex = $all_exercises[$item['exercise_id']] ?? null;
                    if (!$ex) continue;
                ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo safe($ex['name']); ?></td>
                    <td><?php echo safe($ex['type']); ?></td>
                    <td><?php echo $item['sets']; ?></td>
                    <td><?php echo $item['reps']; ?></td>
                    <td><a href="?remove=<?php echo $i; ?>" class="btn remove-btn">Remove</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
            <p>No exercises in your routine yet. Add from the pool above!</p>
            <?php endif; ?>
        </div>
        
        <div id="routine-export" style="display:none; background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.07); padding:24px; position:relative; max-width:700px; margin:0 auto 24px auto;">
            <div style="position:absolute; bottom:10px; right:16px; opacity:0.25; font-size:1.2em; pointer-events:none;">
                <strong>Fitness Tracker</strong> | <?php echo safe($username); ?>
            </div>
            <div style="position:absolute; top:10px; left:16px; opacity:0.7; font-size:0.95em; pointer-events:none;">
                <?php echo date('M d, Y'); ?>
            </div>
            <h3 style="text-align:center; margin-bottom:18px;">Your Current Routine</h3>
            <?php if (!empty($_SESSION['routine'])): ?>
            <table style="width:100%; background:#fafdff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                <tr style="background:#f3f6fa;"><th>#</th><th>Name</th><th>Type</th><th>Sets</th><th>Reps</th></tr>
                <?php foreach ($_SESSION['routine'] as $i => $item):
                    $ex = $all_exercises[$item['exercise_id']] ?? null;
                    if (!$ex) continue;
                ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo safe($ex['name']); ?></td>
                    <td><?php echo safe($ex['type']); ?></td>
                    <td><?php echo $item['sets']; ?></td>
                    <td><?php echo $item['reps']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
            <p>No exercises in your routine yet. Add from the pool above!</p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($saved_routines)): ?>
        <div class="section" style="margin-top:30px;">
            <h3>Your Saved Routines</h3>
            <table style="width:100%; background:#fafdff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                <tr><th>Name</th><th>Created</th><th>Load</th><th>Delete</th></tr>
                <?php foreach ($saved_routines as $r): ?>
                <tr>
                    <td><?php echo safe($r['name']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                    <td><a href="?load_routine=<?php echo $r['id']; ?>" class="btn">Load</a></td>
                    <td><a href="?delete_routine=<?php echo $r['id']; ?>" class="btn remove-btn" onclick="return confirm('Delete this routine?');">Delete</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="section" style="margin-top:20px;">
            <h3>Save Current Routine</h3>
            <?php if (!empty($save_msg)) echo '<div style="color:green; margin-bottom:10px;">' . safe($save_msg) . '</div>'; ?>
            <form method="post" style="display:flex; gap:10px; align-items:center; max-width:400px;">
                <input type="text" name="routine_name" placeholder="Routine Name" required style="flex:2; padding:8px; border-radius:5px; border:1px solid #ccc;">
                <button type="submit" name="save_routine" class="btn" style="flex:1;">Save Routine</button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
    document.getElementById('export-routine-btn').addEventListener('click', function() {
        var exportDiv = document.getElementById('routine-export');
        exportDiv.style.display = 'block';
        html2canvas(exportDiv, {backgroundColor: '#fff'}).then(function(canvas) {
            var link = document.createElement('a');
            link.download = 'routine_<?php echo date('Ymd_His'); ?>.png';
            link.href = canvas.toDataURL();
            link.click();
            exportDiv.style.display = 'none';
        });
    });
    </script>
</body>
</html>
