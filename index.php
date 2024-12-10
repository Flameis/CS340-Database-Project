<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews</title>
</head>
<body>
    <div class="button" style="float: right;">
        <form action="logout.php" method="post">
            <button type="submit">Log Out</button>
        </form>
    </div>
    <h1>My Book Tracker</h1>
    <h2>Add Review</h2>
    <form id="add-review-form">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
        <label for="rating">Rating (1-5):</label>
        <input type="number" id="rating" name="rating" min="1" max="5" required>
        <label for="review_text">Review:</label>
        <textarea id="review_text" name="review_text" required></textarea>
        <button type="submit">Add Review</button>
    </form>

    <h2>Reviews</h2>
    <ul id="reviews-list"></ul>

    <script src="script.js"></script>
</body>
</html>
