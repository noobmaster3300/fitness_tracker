<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$bmi = "";
$category = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $height = $_POST['height'];
    $weight = $_POST['weight'];

    if ($height > 0 && $weight > 0) {
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
    <title>BMI Calculator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f0f0; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; }
        h2 { text-align: center; }
        .form-group { margin-bottom: 10px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 3px; }
        .result { text-align: center; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background: #007bff; color: #fff; }
        .highlight { background: #28a745; color: #fff; }
        .home-btn { display: inline-block; margin-bottom: 10px; background: #6c757d; color: #fff; padding: 6px 12px; border-radius: 3px; text-decoration: none; }
    </style>
</head>
<body>
<a href="dashboard.php" class="home-btn">← Back to Dashboard</a>
<div class="container">
    <h2>BMI Calculator</h2>
    <form method="POST">
        <div class="form-group">
            <label for="height">Height (cm)</label>
            <input type="number" id="height" name="height" required>
        </div>
        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" id="weight" name="weight" required>
        </div>
        <button type="submit">Calculate BMI</button>
    </form>
    <?php if ($bmi): ?>
    <div class="result">
        Your BMI is <strong><?php echo $bmi; ?></strong><br>
        Category: <?php echo $category; ?>
    </div>
    <?php endif; ?>
    <table>
        <tr><th>Category</th><th>BMI Range</th></tr>
        <tr class="<?php echo $category == 'Underweight' ? 'highlight' : ''; ?>"><td>Underweight</td><td>&lt; 18.5</td></tr>
        <tr class="<?php echo $category == 'Healthy' ? 'highlight' : ''; ?>"><td>Healthy Weight</td><td>18.5 – 24.9</td></tr>
        <tr class="<?php echo $category == 'Overweight' ? 'highlight' : ''; ?>"><td>Overweight</td><td>25.0 – 29.9</td></tr>
        <tr class="<?php echo $category == 'Obese' ? 'highlight' : ''; ?>"><td>Obese</td><td>≥ 30.0</td></tr>
    </table>
</div>
</body>
</html>
