<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
include("db_conn.php");

// Insert new review
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $review = trim($_POST['review']);
    if (!empty($review)) {
        $stmt = $conn->prepare("INSERT INTO reviews (username, review) VALUES (?, ?)");
        $stmt->bind_param("ss", $user['username'], $review);
        $stmt->execute();
    }
}

// Delete review (only user's own review)
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $check = $conn->prepare("SELECT * FROM reviews WHERE id = ? AND username = ?");
    $check->bind_param("is", $delete_id, $user['username']);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
        $delStmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        if ($delStmt) {
            $delStmt->bind_param("i", $delete_id);
            $delStmt->execute();
        }
    }
    header("Location: community.php");
    exit;
}

// Fetch all user reviews
$userReviews = [];
$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
if ($result) {
    $userReviews = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Community Reviews - Fitness Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/shared.css">
</head>
<body>
<div class="container">
    <div class="page-header">
        <a href="dashboard.php" class="back-btn" title="Back to Dashboard">&#8592;</a>
        <div class="title-section">
            <h1>Community Reviews</h1>
            <div class="subtitle">Share your experience and read others' feedback</div>
        </div>
    </div>
    <div class="card mb-20">
        <form method="POST" class="review-form">
            <div class="form-group">
                <label for="review">Write a review</label>
                <textarea id="review" name="review" required placeholder="Share your experience..." rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>
    <div class="card mb-20">
        <h3 class="text-center">User Reviews</h3>
        <?php foreach ($userReviews as $review): ?>
            <div class="mb-20" style="border-bottom:1px solid #e9ecef; padding-bottom:12px; margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight:bold; color:#4f8cff;">@<?php echo htmlspecialchars($review['username']); ?></span>
                    <form method="GET" action="community.php" style="margin:0;">
                        <?php if ($review['username'] === $user['username']): ?>
                            <input type="hidden" name="delete" value="<?php echo $review['id']; ?>">
                            <button type="submit" class="btn btn-danger" style="padding:4px 10px; font-size:0.9em;">Delete</button>
                        <?php endif; ?>
                    </form>
                </div>
                <div style="color:#333; margin-top:6px;">"<?php echo htmlspecialchars($review['review']); ?>"</div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($userReviews)): ?>
            <div class="text-center" style="color:#888;">No user reviews yet. Be the first to share your experience!</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
