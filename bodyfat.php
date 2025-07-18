<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$bodyFat = "";
$idealWeight = "";
$category = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gender = $_POST['gender'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $waist = $_POST['waist'];
    $neck = $_POST['neck'];
    $hip = $_POST['hip'] ?? 0;

    if ($gender && $height && $weight && $waist && $neck) {
        if ($gender === "Male") {
            $bodyFat = 495 / (1.0324 - 0.19077 * log10($waist - $neck) + 0.15456 * log10($height)) - 450;
            $idealWeight = 50 + 0.9 * ($height - 152);
        } else {
            $bodyFat = 495 / (1.29579 - 0.35004 * log10($waist + $hip - $neck) + 0.22100 * log10($height)) - 450;
            $idealWeight = 45.5 + 0.9 * ($height - 152);
        }

        $bodyFat = round($bodyFat, 1);
        $idealWeight = round($idealWeight, 1);

        // Category based on body fat %
        if (($gender === "Male" && $bodyFat < 6) || ($gender === "Female" && $bodyFat < 14)) {
            $category = "Essential Fat";
        } elseif (($gender === "Male" && $bodyFat <= 13) || ($gender === "Female" && $bodyFat <= 20)) {
            $category = "Athletes";
        } elseif (($gender === "Male" && $bodyFat <= 17) || ($gender === "Female" && $bodyFat <= 24)) {
            $category = "Fitness";
        } elseif (($gender === "Male" && $bodyFat <= 24) || ($gender === "Female" && $bodyFat <= 31)) {
            $category = "Average";
        } else {
            $category = "Obese";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Body Fat Calculator - Fitness Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 4px;
        }
        .hip-field {
            display: none;
        }
        .hip-field.show {
            display: block;
        }
        .home-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .home-btn:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>

<a href="dashboard.php" class="home-btn">← Back to Dashboard</a>

<div class="container">
    <h2>Body Fat Calculator</h2>

    <form method="POST">
        <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" id="gender" required onchange="toggleHipField()">
                <option value="">Select Gender</option>
                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="height">Height (cm)</label>
            <input type="number" id="height" name="height" required placeholder="Enter height in cm" value="<?php echo isset($_POST['height']) ? $_POST['height'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" id="weight" name="weight" required placeholder="Enter weight in kg" value="<?php echo isset($_POST['weight']) ? $_POST['weight'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="waist">Waist (cm)</label>
            <input type="number" id="waist" name="waist" required placeholder="Enter waist measurement" value="<?php echo isset($_POST['waist']) ? $_POST['waist'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="neck">Neck (cm)</label>
            <input type="number" id="neck" name="neck" required placeholder="Enter neck measurement" value="<?php echo isset($_POST['neck']) ? $_POST['neck'] : ''; ?>">
        </div>

        <div class="form-group hip-field" id="hipField">
            <label for="hip">Hip (cm) - Required for females</label>
            <input type="number" id="hip" name="hip" placeholder="Enter hip measurement" value="<?php echo isset($_POST['hip']) ? $_POST['hip'] : ''; ?>">
        </div>
        
        <button type="submit">Calculate Body Fat</button>
    </form>

    <?php if ($bodyFat): ?>
    <div class="result">
        <h3>Results:</h3>
        <p><strong>Body Fat Percentage:</strong> <?php echo $bodyFat; ?>%</p>
        <p><strong>Category:</strong> <?php echo $category; ?></p>
        <p><strong>Ideal Weight:</strong> <?php echo $idealWeight; ?> kg</p>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleHipField() {
    const genderSelect = document.getElementById('gender');
    const hipField = document.getElementById('hipField');
    const hipInput = document.getElementById('hip');
    
    if (genderSelect.value === 'Female') {
        hipField.classList.add('show');
        hipInput.required = true;
    } else {
        hipField.classList.remove('show');
        hipInput.required = false;
        hipInput.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleHipField();
});
</script>

</body>
</html>
