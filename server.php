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
    // Add a new review
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("SELECT * FROM Book WHERE book_id = ?");
    $stmt->execute([$data['book_id']]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        $stmt = $conn->prepare("INSERT INTO Review (user_id, book_id, rating, review_text, date_created, date_updated) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        try {
            $stmt->execute([$data['user_id'], $data['book_id'], $data['rating'], $data['review_text']]);
            echo json_encode(["message" => "Review added successfully!"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Failed to add review: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "Book not found."]);
    }
}

if ($action === 'editReview') {
    // Edit an existing review
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("SELECT user_id FROM Review WHERE review_id = ?");
    $stmt->execute([$data['review_id']]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT role FROM User WHERE user_id = ?");
    $stmt->execute([$data['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($review && ($review['user_id'] == $data['user_id'] || $user['role'] == 'admin')) {
        $stmt = $conn->prepare("UPDATE Review SET rating = ?, review_text = ?, date_updated = CURRENT_TIMESTAMP WHERE review_id = ?");
        try {
            $stmt->execute([$data['rating'], $data['review_text'], $data['review_id']]);
            echo json_encode(["message" => "Review updated successfully!"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Failed to update review: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "You can only edit your own reviews or you must be an admin."]);
    }
}

if ($action === 'deleteReview') {
    // Delete a review
    $review_id = $_GET['review_id'];
    $user_id = $_GET['user_id'];
    $stmt = $conn->prepare("SELECT user_id FROM Review WHERE review_id = ?");
    $stmt->execute([$review_id]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT role FROM User WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($review && ($review['user_id'] == $user_id || $user['role'] == 'admin')) {
        $stmt = $conn->prepare("DELETE FROM Review WHERE review_id = ?");
        try {
            $stmt->execute([$review_id]);
            echo json_encode(["message" => "Review deleted successfully!"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Failed to delete review: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "You can only delete your own reviews or you must be an admin."]);
    }
}

if ($action === 'getReviews') {
    // Get reviews for a specific book
    $book_id = $_GET['book_id'];
    $stmt = $conn->prepare("SELECT Review.*, User.username FROM Review JOIN User ON Review.user_id = User.user_id WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reviews);
}

if ($action === 'filterBooks') {
    // Filter books based on genre, author, and rating
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

if ($action === 'getBookDetails') {
    // Get details of a specific book
    $book_id = $_GET['book_id'];
    $stmt = $conn->prepare("SELECT Book.*, Author.fname, Author.lname FROM Book JOIN Author ON Book.author_id = Author.author_id WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT Review.*, User.username FROM Review JOIN User ON Review.user_id = User.user_id WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['book' => $book, 'reviews' => $reviews]);
}

if ($action === 'getBooks') {
    // Get all books
    $stmt = $conn->query("SELECT Book.*, Author.fname, Author.lname FROM Book JOIN Author ON Book.author_id = Author.author_id");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
}

if ($action === 'getUserReviews') {
    // Get reviews by a specific user
    $user_id = $_GET['user_id'];
    $stmt = $conn->prepare("SELECT * FROM Review WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reviews);
}

if ($action === 'addToReadingList') {
    // Add a book to the reading list
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("INSERT INTO Reading_List (user_id, book_id, list_name, date_added, status) VALUES (?, ?, ?, CURRENT_DATE, ?)");
    try {
        $stmt->execute([$data['user_id'], $data['book_id'], $data['list_name'], $data['status']]);
        echo json_encode(["message" => "Book added to reading list successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Failed to add book to reading list: " . $e->getMessage()]);
    }
}

if ($action === 'searchBooks') {
    // Search books by title
    $searchTerm = $_GET['searchTerm'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM Book WHERE title LIKE ?");
    $stmt->execute(['%' . $searchTerm . '%']);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
}

if ($action === 'getReadingList') {
    // Get the reading list for a specific user
    $user_id = $_GET['user_id'];
    $sort_by = $_GET['sort_by'] ?? 'status';
    $valid_sort_columns = ['status', 'genre', 'author', 'rating'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'status';
    }
    $stmt = $conn->prepare("
        SELECT Reading_List.*, Book.genre, Author.fname, Author.lname, Book.avg_rating
        FROM Reading_List
        JOIN Book ON Reading_List.book_id = Book.book_id
        JOIN Author ON Book.author_id = Author.author_id
        WHERE Reading_List.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $readingList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($readingList);
}

if ($action === 'getUserReadingLists') {
    // Get the reading lists for a specific user
    $user_id = $_GET['user_id'];
    $stmt = $conn->prepare("SELECT DISTINCT list_name FROM Reading_List WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $readingLists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($readingLists);
}
?>
