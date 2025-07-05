<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];

// Handle account deletion
if (isset($_POST['delete_account'])) {
    require_once 'db_conn.php';
    $user_id = $user['id'];

    // Delete from related tables first
    $stmt = $conn->prepare("DELETE FROM water_intake WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    if ($stmt->error) { echo "Error (water_intake): " . $stmt->error; }

    $stmt = $conn->prepare("DELETE FROM water_goal WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    if ($stmt->error) { echo "Error (water_goal): " . $stmt->error; }

    $stmt = $conn->prepare("DELETE FROM exercises WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    if ($stmt->error) { echo "Error (exercises): " . $stmt->error; }

    // Finally, delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    if ($stmt->error) { echo "Error (users): " . $stmt->error; }

    // Log out and redirect to login
    session_destroy();
    header("Location: index.php");
    exit;
}

// Calculate current age from date of birth
$dob_date = new DateTime($user['dob']);
$today = new DateTime();
$current_age = $today->diff($dob_date)->y;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Fitness Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: white;
        }
        .profile-name {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .profile-subtitle {
            color: #666;
            font-size: 1rem;
        }
        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .detail-group {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .detail-group h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
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
        @media (max-width: 768px) {
            .profile-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<a href="dashboard.php" class="home-btn">← Back to Dashboard</a>

<div class="container">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
        </div>
        <h1 class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></h1>
        <p class="profile-subtitle">Fitness Tracker Member</p>
    </div>

    <div class="profile-details">
        <div class="detail-group">
            <h3>Personal Information</h3>
            <div class="detail-item">
                <span class="detail-label">Full Name:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['full_name']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Username:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Gender:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['gender']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Date of Birth:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['dob']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Age:</span>
                <span class="detail-value"><?php echo $current_age; ?> years</span>
            </div>
        </div>

        <div class="detail-group">
            <h3>Health Information</h3>
            <div class="detail-item">
                <span class="detail-label">Weight:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['weight']); ?> kg</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Blood Group:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['blood_group']); ?></span>
            </div>
        </div>

        <div class="detail-group">
            <h3>Contact Information</h3>
            <div class="detail-item">
                <span class="detail-label">Phone:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['phone']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Address:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['address']); ?></span>
            </div>
        </div>

        <div class="detail-group">
            <h3>Account Information</h3>
            <div class="detail-item">
                <span class="detail-label">Member Since:</span>
                <span class="detail-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Account Status:</span>
                <span class="detail-value">Active</span>
            </div>
        </div>
    </div>
    <!-- Delete Account Button -->
    <form method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone!');" style="margin-top:30px;">
        <button type="submit" name="delete_account" style="background:#e57373;color:#fff;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;">Delete Account</button>
    </form>
</div>

</body>
</html>
