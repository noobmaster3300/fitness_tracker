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
    <link rel="stylesheet" href="css/shared.css">
    <style>
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
        .danger-zone {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
        }
        .danger-zone h3 {
            color: #721c24;
            margin-bottom: 15px;
        }
        .danger-zone p {
            color: #721c24;
            margin-bottom: 15px;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        @media (max-width: 768px) {
            .profile-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="page-header">
        <a href="dashboard.php" class="back-btn" title="Back to Dashboard">&#8592;</a>
        <div class="title-section">
            <h1>My Profile</h1>
            <div class="subtitle">Manage your account and personal information</div>
        </div>
    </div>

    <div class="card mb-20">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>
            <h2 class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></h2>
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
        </div>
    </div>

    <div class="card">
        <div class="danger-zone">
            <h3>Delete Account</h3>
            <p><strong>Note:</strong> If you delete your account, all your data will be gone forever. This includes your exercise logs, water records, and personal info.</p>
            <form method="post" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                <button type="submit" name="delete_account" class="delete-btn">Delete Account</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
