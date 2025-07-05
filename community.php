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

// Demo reviews
$demoReviews = [
    ["username" => "fitness_fan", "review" => "This website helped me lose 5kg in 2 months!"],
    ["username" => "raj123", "review" => "Very helpful calculators and exercise tips."],
    ["username" => "sana_k", "review" => "Loved the nutrition section. Please add more recipes!"]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Community Reviews - Fitness Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        h2 {
            font-size: 2rem;
        }
        h3 {
            font-size: 1.5rem;
            margin-top: 40px;
        }
        .review-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            height: 100px;
            resize: vertical;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
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
        .reviews-section {
            margin-top: 30px;
        }
        .review-item {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .review-username {
            font-weight: bold;
            color: #333;
        }
        .review-date {
            color: #666;
            font-size: 0.9rem;
        }
        .review-text {
            color: #333;
            line-height: 1.5;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .delete-btn:hover {
            background-color: #c82333;
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
    <h2>Community Reviews</h2>
    <p style="text-align: center; color: #666; margin-bottom: 30px;">Share your fitness journey and read what others have to say</p>

    <div class="review-form">
        <h3>Add Your Review</h3>
        <form method="POST">
            <div class="form-group">
                <label for="review">Your Review:</label>
                <textarea id="review" name="review" placeholder="Share your experience with our fitness tracker..." required></textarea>
            </div>
            <button type="submit">Submit Review</button>
        </form>
    </div>

    <div class="reviews-section">
        <h3>Recent Reviews</h3>
        
        <?php if (!empty($userReviews)): ?>
            <?php foreach ($userReviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <span class="review-username"><?php echo htmlspecialchars($review['username']); ?></span>
                        <div>
                            <span class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></span>
                            <?php if ($review['username'] === $user['username']): ?>
                                <a href="?delete=<?php echo $review['id']; ?>" class="delete-btn" onclick="return confirm('Delete this review?')">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="review-text"><?php echo htmlspecialchars($review['review']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #666;">No reviews yet. Be the first to share your experience!</p>
        <?php endif; ?>

        <h3>Featured Reviews</h3>
        <?php foreach ($demoReviews as $review): ?>
            <div class="review-item">
                <div class="review-header">
                    <span class="review-username"><?php echo htmlspecialchars($review['username']); ?></span>
                    <span class="review-date">Featured</span>
                </div>
                <div class="review-text"><?php echo htmlspecialchars($review['review']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
