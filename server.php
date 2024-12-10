<?php
// Database connection variables
$host = "classmysql.engr.oregonstate.edu"; // OSU MySQL server
$db = "cs340_username"; // Your database name
$user = "cs340_username"; // Your MySQL username
$pass = "password"; // Your MySQL password

// Create connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle incoming requests
$action = $_GET['action'] ?? '';

if ($action === 'addReview') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("INSERT INTO Review (user_id, book_id, rating, review_text, date_created, date_updated) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
    try {
        $stmt->execute([$data['user_id'], $data['book_id'], $data['rating'], $data['review_text']]);
        echo json_encode(["message" => "Review added successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to add review: " . $e->getMessage()]);
    }
}

if ($action === 'editReview') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("UPDATE Review SET rating = ?, review_text = ?, date_updated = CURRENT_TIMESTAMP WHERE review_id = ?");
    try {
        $stmt->execute([$data['rating'], $data['review_text'], $data['review_id']]);
        echo json_encode(["message" => "Review updated successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to update review: " . $e->getMessage()]);
    }
}

if ($action === 'deleteReview') {
    $review_id = $_GET['review_id'];
    $stmt = $conn->prepare("DELETE FROM Review WHERE review_id = ?");
    try {
        $stmt->execute([$review_id]);
        echo json_encode(["message" => "Review deleted successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to delete review: " . $e->getMessage()]);
    }
}

if ($action === 'getReviews') {
    $stmt = $conn->query("SELECT * FROM Review");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reviews);
}

if ($action === 'filterBooks') {
    $genre = $_GET['genre'] ?? '';
    $author = $_GET['author'] ?? '';
    $rating = $_GET['rating'] ?? '';

    $query = "SELECT * FROM Book WHERE 1=1";
    $params = [];
    if ($genre) {
        $query .= " AND genre = ?";
        $params[] = $genre;
    }
    if ($author) {
        $query .= " AND author_id = ?";
        $params[] = $author;
    }
    if ($rating) {
        $query .= " AND avg_rating >= ?";
        $params[] = $rating;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
}
?>
