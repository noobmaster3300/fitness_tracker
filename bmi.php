<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$bmi = "";
$category = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $height = $_POST['height'];
    $weight = $_POST['weight'];

    // Validate practical ranges
    if ($height < 50 || $height > 300 || $weight < 10 || $weight > 300) {
        $error = "Please enter a realistic height (50-300 cm) and weight (10-300 kg).";
    } elseif ($height > 0 && $weight > 0) {
        $height_m = $height / 100;
        $bmi = round($weight / ($height_m * $height_m), 1);

        if ($bmi < 18.5) {
            $category = "Underweight";
        } elseif ($bmi < 25) {
            $category = "Healthy";
        } elseif ($bmi < 30) {
            $category = "Overweight";
        } else {
            $category = "Obese";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BMI Calculator - Fitness Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/shared.css">
</head>
<body>

<div class="container">
    <div class="page-header">
        <a href="dashboard.php" class="back-btn" title="Back to Dashboard">&#8592;</a>
        <div class="title-section">
            <h1>BMI Calculator</h1>
            <div class="subtitle">Calculate your Body Mass Index</div>
        </div>
        <div></div> <!-- Empty div for flex spacing -->
    </div>
    <form method="POST">
        <div class="form-group">
            <label for="height">Height (cm)</label>
            <input type="number" id="height" name="height" required>
        </div>
        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" id="weight" name="weight" required>
        </div>
        <button type="submit" class="btn btn-primary">Calculate BMI</button>
    </form>
    <?php if ($error): ?>
    <div class="alert alert-error">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>
    <?php if ($bmi): ?>
    <div class="result">
        Your BMI is <strong><?php echo $bmi; ?></strong><br>
        Category: <?php echo $category; ?>
    </div>
    <div class="card advice">
        <?php
        $height_m = $height / 100;
        if ($category == "Underweight") {
            echo "<strong>Goal:</strong> <span style='color:#007bff; font-weight:bold;'>Reach a BMI of at least 18.5</span> (about <b>" . round(18.5 * $height_m * $height_m, 1) . " kg</b>).<br>";
            echo "<strong>Tips:</strong> <ul>
                <li><span style='color:#28a745; font-weight:bold;'>Eat more calories</span> with healthy foods like <span style='color:#007bff;'>nuts, dairy, and whole grains</span>.</li>
                <li><span style='color:#28a745;'>Have regular meals and snacks</span>. Don't skip meals.</li>
                <li><span style='color:#007bff;'>Do strength exercises</span> to build muscle.</li>
                <li>Make sure you get enough <span style='color:#007bff;'>calcium and vitamin D</span> for bone health.</li>
                <li>If you need help, <span style='color:#e53935; font-weight:bold;'>talk to a doctor or dietitian</span>.</li>
            </ul>";
            echo "<strong>Why it matters:</strong> Being underweight can affect your <span style='color:#e53935;'>bones</span> and <span style='color:#e53935;'>immune system</span>.";
        } elseif ($category == "Healthy") {
            $target_weight = round(22 * $height_m * $height_m, 1);
            echo "<strong>Goal:</strong> <span style='color:#28a745; font-weight:bold;'>Keep your weight steady</span> (around <b>$target_weight kg</b> for BMI 22).<br>";
            echo "<strong>Tips:</strong> <ul>
                <li><span style='color:#28a745;'>Eat a balanced diet</span> with lots of variety.</li>
                <li><span style='color:#007bff;'>Stay active</span> with cardio, strength, and stretching.</li>
                <li><span style='color:#ff9800;'>Check your weight</span> and body regularly.</li>
                <li><span style='color:#007bff;'>Build muscle</span> and keep fit.</li>
                <li>See your doctor for <span style='color:#007bff;'>regular checkups</span>.</li>
            </ul>";
            echo "<strong>Why it matters:</strong> A healthy BMI lowers your risk of <span style='color:#28a745;'>chronic diseases</span>.";
        } elseif ($category == "Overweight") {
            $target_weight = round(24.9 * $height_m * $height_m, 1);
            echo "<strong>Goal:</strong> <span style='color:#007bff; font-weight:bold;'>Reach a BMI below 25</span> (target: <b>$target_weight kg</b>).<br>";
            echo "<strong>Tips:</strong> <ul>
                <li><span style='color:#28a745; font-weight:bold;'>Lose weight slowly</span> – aim for 5% of your weight (<b>" . round($weight * 0.05, 1) . " kg</b>).</li>
                <li><span style='color:#007bff;'>Eat more veggies</span> and <span style='color:#007bff;'>cut down on sugar</span>.</li>
                <li><span style='color:#28a745;'>Move more</span> – try for 150 minutes of activity a week.</li>
                <li><span style='color:#ff9800;'>Track your progress</span> and celebrate small wins.</li>
                <li>If you need help, <span style='color:#e53935; font-weight:bold;'>talk to a doctor or dietitian</span>.</li>
            </ul>";
            echo "<strong>Why it matters:</strong> Lowering your weight can help prevent <span style='color:#e53935;'>diabetes</span> and <span style='color:#e53935;'>heart disease</span>.";
        } else { // Obese
            $target_weight = round(29.9 * $height_m * $height_m, 1);
            echo "<strong>Goal:</strong> <span style='color:#007bff; font-weight:bold;'>First, aim for a BMI below 30</span> (target: <b>$target_weight kg</b>), then below 25.<br>";
            echo "<strong>Tips:</strong> <ul>
                <li><span style='color:#e53935; font-weight:bold;'>See a doctor</span> for a safe weight loss plan.</li>
                <li><span style='color:#28a745;'>Set small goals</span> – lose 5–10% of your weight (<b>" . round($weight * 0.05, 1) . "–" . round($weight * 0.10, 1) . " kg</b>).</li>
                <li><span style='color:#007bff;'>Eat fewer calories</span> and choose healthy foods.</li>
                <li><span style='color:#28a745;'>Be more active</span> – start slow and find activities you enjoy.</li>
                <li><span style='color:#ff9800;'>Check your blood pressure, sugar, and cholesterol</span> regularly.</li>
                <li><span style='color:#e53935;'>Get support</span> from family, friends, or groups.</li>
            </ul>";
            echo "<strong>Why it matters:</strong> Obesity raises your risk of <span style='color:#e53935;'>diabetes</span>, <span style='color:#e53935;'>heart disease</span>, and <span style='color:#e53935;'>joint problems</span>. Every step counts!";
        }
        ?>
    </div>
    <?php endif; ?>
    <table>
        <tr><th>Category</th><th>BMI Range</th></tr>
        <tr style="<?php echo $category == 'Underweight' ? 'background: #28a745; color: white;' : ''; ?>"><td>Underweight</td><td>&lt; 18.5</td></tr>
        <tr style="<?php echo $category == 'Healthy' ? 'background: #28a745; color: white;' : ''; ?>"><td>Healthy Weight</td><td>18.5 – 24.9</td></tr>
        <tr style="<?php echo $category == 'Overweight' ? 'background: #28a745; color: white;' : ''; ?>"><td>Overweight</td><td>25.0 – 29.9</td></tr>
        <tr style="<?php echo $category == 'Obese' ? 'background: #28a745; color: white;' : ''; ?>"><td>Obese</td><td>≥ 30.0</td></tr>
    </table>
</div>
<div class="card bmi-info">
    <h2 style="margin-top:0; color:#4f8cff;">About BMI</h2>
    <p><strong>What is BMI?</strong><br>
    <span style="color:#007bff; font-weight:bold;">Body Mass Index (BMI)</span> is a simple number calculated from your height and weight. It helps estimate if you are underweight, at a healthy weight, overweight, or obese.</p>
    <p><strong>How is BMI calculated?</strong><br>
    <span style="color:#007bff;">BMI = weight (kg) / [height (m)]<sup>2</sup></span><br>
    <span style="font-size:0.97em;">Example: If you weigh 70 kg and are 170 cm tall: BMI = 70 / (1.7 × 1.7) = 24.2</span></p>
    <p><strong>Why is BMI important?</strong><br>
    BMI is a quick screening tool to help identify if you are at a healthy weight for your height. Maintaining a healthy BMI can lower your risk of <span style="color:#e53935;">heart disease</span>, <span style="color:#e53935;">diabetes</span>, and other health problems. However, BMI does not measure body fat directly and may not reflect health for everyone (such as athletes).</p>
</div>
</body>
</html>
