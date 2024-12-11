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
    <title>My Book Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <input type="hidden" id="user_id" value="<?php echo $_SESSION['user_id']; ?>">
    <div class="header">
        <h1>My Book Tracker</h1>
        <div class="profile-button">
            <a href="user.php"><button>My Profile</button></a>
        </div>
        <div class="logout-button">
            <form action="logout.php" method="post">
                <button type="submit">Log Out</button>
            </form>
        </div>
    </div>
    <div class="container">
        <div class="sidebar">
            <h2>Book Details</h2>
            <div id="book-details" class="book-details"></div>
        </div>
        <div class="main-content">
            <h2>Search Books</h2>
            <form id="search-form" class="search-bar">
                <input type="text" id="search-input" placeholder="Search by title">
                <button type="submit">Search</button>
            </form>
            <h2>Filter Books</h2>
            <form id="filter-form" class="filter-form">
                <label for="filter-genre">Genre:</label>
                <input type="text" id="filter-genre" name="genre">
                <label for="filter-author">Author:</label>
                <input type="text" id="filter-author" name="author">
                <label for="filter-rating">Rating:</label>
                <input type="number" id="filter-rating" name="rating" min="1" max="5">
                <button type="submit">Filter</button>
            </form>
            <h2>Book List</h2>
            <ul id="book-list" class="book-list"></ul>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>
