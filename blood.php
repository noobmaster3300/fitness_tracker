<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$donateTo = [];
$receiveFrom = [];
$selectedGroup = "";
$age = $weight = $lastDonation = '';
$health = [];
$eligibilityMsg = '';
$canDonate = false;
$nextEligibleDate = '';
$reasons = [];
$personalAdvice = [];
$daysLeft = null;

$compatibility = [
    'A+' => ['donate' => ['A+', 'AB+'], 'receive' => ['A+', 'A-', 'O+', 'O-']],
    'A-' => ['donate' => ['A+', 'A-', 'AB+', 'AB-'], 'receive' => ['A-', 'O-']],
    'B+' => ['donate' => ['B+', 'AB+'], 'receive' => ['B+', 'B-', 'O+', 'O-']],
    'B-' => ['donate' => ['B+', 'B-', 'AB+', 'AB-'], 'receive' => ['B-', 'O-']],
    'AB+' => ['donate' => ['AB+'], 'receive' => ['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-']],
    'AB-' => ['donate' => ['AB+', 'AB-'], 'receive' => ['A-', 'B-', 'AB-', 'O-']],
    'O+' => ['donate' => ['O+', 'A+', 'B+', 'AB+'], 'receive' => ['O+', 'O-']],
    'O-' => ['donate' => ['Everyone'], 'receive' => ['O-']]
];

// Expanded health conditions
$healthOptions = [
    'fever' => 'Fever/cold/flu in past 2 weeks',
    'infection' => 'Current infection',
    'tattoo' => 'Tattoo or piercing in last 6 months',
    'pregnancy' => 'Currently pregnant or recently pregnant',
    'surgery' => 'Major surgery in last 6 months',
    'malaria' => 'Travel to malaria-risk area in last 12 months',
    'medication' => 'Currently on antibiotics or certain medications',
    'chronic' => 'Chronic disease (e.g. diabetes, heart disease)',
    'anemia' => 'History of anemia or low hemoglobin',
    'alcohol' => 'Consumed alcohol in last 24 hours',
];

// Personalized advice for each health condition
$adviceMap = [
    'fever' => 'Wait at least 2 weeks after your symptoms resolve before donating.',
    'infection' => 'Wait until you are fully recovered from your infection.',
    'tattoo' => 'Wait at least 6 months after your last tattoo or piercing.',
    'pregnancy' => 'Wait until at least 6 months after pregnancy and consult your doctor.',
    'surgery' => 'Wait at least 6 months after major surgery and consult your doctor.',
    'malaria' => 'Wait at least 12 months after returning from a malaria-risk area.',
    'medication' => 'Check with your doctor or blood center about your medication.',
    'chronic' => 'Consult your doctor to see if you are eligible to donate.',
    'anemia' => 'You must have normal hemoglobin levels to donate.',
    'alcohol' => 'Wait at least 24 hours after consuming alcohol.',
];

// Deferral periods in days for each health condition
$deferralPeriods = [
    'fever' => 14,
    'infection' => 7, // assume 1 week for infection recovery
    'tattoo' => 180,
    'pregnancy' => 180, // 6 months
    'surgery' => 180,
    'malaria' => 365,
    'medication' => 7, // assume 1 week after antibiotics
    'chronic' => 0, // indefinite, needs doctor clearance
    'anemia' => 0, // indefinite, needs doctor clearance
    'alcohol' => 1,
];

function safe($v) { return htmlspecialchars($v ?? '', ENT_QUOTES); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedGroup = $_POST['blood_group'] ?? '';
    $age = $_POST['age'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $lastDonation = $_POST['last_donation'] ?? '';
    $health = $_POST['health'] ?? [];
    if (isset($compatibility[$selectedGroup])) {
        $donateTo = $compatibility[$selectedGroup]['donate'];
        $receiveFrom = $compatibility[$selectedGroup]['receive'];
    }
    // Eligibility checks
    if ($age === '' || $weight === '' || !$selectedGroup) {
        $eligibilityMsg = 'Please fill in all required fields.';
    } else {
        $age = (int)$age;
        $weight = (float)$weight;
        $now = new DateTime();
        $deferralDates = [];
        // Age
        if ($age < 18 || $age > 65) {
            $reasons[] = 'Age must be between 18 and 65.';
        }
        // Weight
        if ($weight < 50) {
            $reasons[] = 'Weight must be at least 50 kg.';
        }
        // Health conditions
        foreach ($health as $cond) {
            if (isset($healthOptions[$cond])) {
                $reasons[] = $healthOptions[$cond] . '.';
                if (isset($adviceMap[$cond])) {
                    $personalAdvice[] = $adviceMap[$cond];
                }
                // Only add deferral if period > 0 (permanent deferrals handled by advice)
                if (isset($deferralPeriods[$cond]) && $deferralPeriods[$cond] > 0) {
                    $deferralDates[] = (clone $now)->modify("+{$deferralPeriods[$cond]} days");
                }
            }
        }
        // Last donation
        $lastDonationDate = $lastDonation ? DateTime::createFromFormat('Y-m-d', $lastDonation) : false;
        if ($lastDonationDate) {
            $interval = $lastDonationDate->diff($now)->days;
            if ($interval < 90) {
                $reasons[] = 'At least 3 months (90 days) must have passed since your last donation.';
                $deferralDates[] = (clone $lastDonationDate)->modify('+90 days');
            }
        }
        // Next eligible date is the latest of all deferral dates (if any)
        if (!empty($deferralDates)) {
            $maxDate = $deferralDates[0];
            foreach ($deferralDates as $d) {
                if ($d > $maxDate) $maxDate = $d;
            }
            $nextEligibleDate = $maxDate->format('F j, Y');
            $daysLeft = $now->diff($maxDate)->days;
            if ($daysLeft < 0) $daysLeft = 0;
        } else {
            $nextEligibleDate = $now->format('F j, Y');
        }
        // Final eligibility
        if (empty($reasons)) {
            $canDonate = true;
            $eligibilityMsg = '✅ You can donate blood!';
        } else {
            $canDonate = false;
            $eligibilityMsg = '⚠️ You are not eligible to donate blood now:';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blood Donation Eligibility - Fitness Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/shared.css">
    <style>
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        select, input[type=number], input[type=date] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; box-sizing: border-box; }
        .checkbox-group { margin-bottom: 20px; }
        .checkbox-group label { display: block; margin-bottom: 6px; font-weight: normal; }
        button { width: 100%; padding: 12px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #c82333; }
        .result { margin-top: 30px; }
        .result h3 { color: #333; margin-bottom: 20px; text-align: center; }
        .box { background-color: #f8f9fa; padding: 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #dc3545; }
        .box h4 { color: #333; margin-bottom: 10px; }
        .blood-groups { display: flex; flex-wrap: wrap; gap: 10px; }
        .blood-group { background-color: #dc3545; color: white; padding: 8px 12px; border-radius: 4px; font-weight: bold; }
        .not-eligible { color: #dc3545; font-weight: bold; }
        .eligible { color: #28a745; font-weight: bold; }
        ul { margin: 0 0 10px 20px; }
        .advice-list { margin: 0 0 10px 20px; color: #555; }
        .days-left { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <a href="dashboard.php" class="back-btn" title="Back to Dashboard">&#8592;</a>
        <div class="title-section">
            <h1>Blood Donation Eligibility</h1>
            <div class="subtitle">Check if you can donate blood and see your compatibility</div>
        </div>
    </div>

    <div class="card mb-20">
        <form method="POST">
            <label for="blood_group">Blood Group:</label>
            <select name="blood_group" id="blood_group" required>
                <option value="">Choose Blood Group</option>
                <?php foreach ($compatibility as $bg => $v): ?>
                    <option value="<?php echo $bg; ?>" <?php echo ($selectedGroup == $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="age">Age:</label>
            <input type="number" name="age" id="age" min="16" max="100" value="<?php echo safe($age); ?>" required>

            <label for="weight">Weight (kg):</label>
            <input type="number" name="weight" id="weight" min="30" max="200" value="<?php echo safe($weight); ?>" required>

            <label for="last_donation">Last Donation Date (if any):</label>
            <input type="date" name="last_donation" id="last_donation" value="<?php echo safe($lastDonation); ?>">

            <div class="checkbox-group">
                <label>Health Conditions (check all that apply):</label>
                <?php foreach ($healthOptions as $key => $option): ?>
                    <label>
                        <input type="checkbox" name="health[]" value="<?php echo $key; ?>" <?php echo in_array($key, $health) ? 'checked' : ''; ?>>
                        <?php echo $option; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="submit">Check Eligibility</button>
        </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && $selectedGroup): ?>
        <div class="card mb-20">
            <div class="result">
                <h3><?php echo $eligibilityMsg; ?></h3>
                
                <?php if ($canDonate): ?>
                    <div class="box">
                        <h4>✅ You are eligible to donate blood!</h4>
                        <p>Thank you for considering blood donation. Your donation can save up to 3 lives.</p>
                    </div>
                <?php else: ?>
                    <div class="box">
                        <h4>⚠️ Eligibility Issues:</h4>
                        <ul>
                            <?php foreach ($reasons as $reason): ?>
                                <li><?php echo $reason; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <?php if (!empty($personalAdvice)): ?>
                            <h4>Personalized Advice:</h4>
                            <ul class="advice-list">
                                <?php foreach ($personalAdvice as $advice): ?>
                                    <li><?php echo $advice; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        
                        <?php if ($nextEligibleDate && $daysLeft !== null): ?>
                            <p><strong>Next eligible date:</strong> <span class="days-left"><?php echo $nextEligibleDate; ?></span></p>
                            <p><strong>Days remaining:</strong> <span class="days-left"><?php echo $daysLeft; ?> days</span></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="box">
                <h4>Blood Compatibility for <?php echo $selectedGroup; ?>:</h4>
                
                <h5>You can donate to:</h5>
                <div class="blood-groups">
                    <?php foreach ($donateTo as $bg): ?>
                        <span class="blood-group"><?php echo $bg; ?></span>
                    <?php endforeach; ?>
                </div>
                
                <h5>You can receive from:</h5>
                <div class="blood-groups">
                    <?php foreach ($receiveFrom as $bg): ?>
                        <span class="blood-group"><?php echo $bg; ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
