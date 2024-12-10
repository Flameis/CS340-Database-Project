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
    <title>My Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>My Profile</h1>
        <div>
            <a href="index.php"><button>Back to Main Page</button></a>
        </div>
        <div class="logout-button">
            <form action="logout.php" method="post">
                <button type="submit">Log Out</button>
            </form>
        </div>
    </div>
    <div class="container">
        <div class="main-content">
            <h2>My Reading List</h2>
            <div>
                <label for="sort-reading-list">Sort by:</label>
                <select id="sort-reading-list" onchange="fetchReadingList(<?php echo $_SESSION['user_id']; ?>)">
                    <option value="status">Status</option>
                    <option value="genre">Genre</option>
                    <option value="author">Author</option>
                    <option value="rating">Rating</option>
                </select>
            </div>
            <div id="reading-lists"></div>

            <h2>My Reviews</h2>
            <ul id="user-reviews-list"></ul>
        </div>
    </div>
    <script>
        async function fetchReadingList(userId) {
            const sortBy = document.getElementById("sort-reading-list").value;
            try {
                const response = await fetch(`server.php?action=getReadingList&user_id=${userId}&sort_by=${sortBy}`);
                const readingList = await response.json();
                const readingListsElement = document.getElementById("reading-lists");
                readingListsElement.innerHTML = "";

                const lists = {};
                readingList.forEach((item) => {
                    if (!lists[item.list_name]) {
                        lists[item.list_name] = [];
                    }
                    lists[item.list_name].push(item);
                });

                for (const listName in lists) {
                    lists[listName].sort((a, b) => {
                        if (sortBy === 'author') {
                            return a.lname.localeCompare(b.lname) || a.fname.localeCompare(b.fname);
                        }
                        return a[sortBy].localeCompare(b[sortBy]);
                    });

                    const listDiv = document.createElement("div");
                    listDiv.className = "reading-list-box";
                    listDiv.innerHTML = `<h3>${listName}</h3>`;
                    const ul = document.createElement("ul");
                    lists[listName].forEach((item) => {
                        const li = document.createElement("li");
                        li.innerHTML = `
                            Book ID: ${item.book_id}, Status: ${item.status} <br>
                            Date Added: ${item.date_added}
                        `;
                        ul.appendChild(li);
                    });
                    listDiv.appendChild(ul);
                    readingListsElement.appendChild(listDiv);
                }
            } catch (err) {
                console.error("Error fetching reading list:", err);
            }
        }

        async function fetchUserReviews(userId) {
            try {
                const response = await fetch(`server.php?action=getUserReviews&user_id=${userId}`);
                const reviews = await response.json();
                const userReviewsList = document.getElementById("user-reviews-list");
                userReviewsList.innerHTML = "";
                reviews.forEach((review) => {
                    const li = document.createElement("li");
                    li.innerHTML = `
                        Book ID: ${review.book_id}, Rating: ${review.rating} <br>
                        Review: ${review.review_text}
                        <button onclick="deleteReview(${review.review_id})">Delete</button>
                        <button onclick="editReviewPrompt(${review.review_id}, ${review.rating}, '${review.review_text}')">Edit</button>
                    `;
                    userReviewsList.appendChild(li);
                });
            } catch (err) {
                console.error("Error fetching user reviews:", err);
            }
        }

        async function deleteReview(reviewId) {
            const userId = <?php echo $_SESSION['user_id']; ?>;
            const response = await fetch(`server.php?action=deleteReview&review_id=${reviewId}&user_id=${userId}`, { method: "GET" });
            const result = await response.json();
            if (result.message) {
                alert(result.message);
                fetchUserReviews(userId);
            } else {
                alert(result.error);
            }
        }

        function editReviewPrompt(reviewId, currentRating, currentText) {
            const newRating = prompt("Enter new rating (1-5):", currentRating);
            const newText = prompt("Enter new review text:", currentText);
            if (newRating && newText) {
                editReview(reviewId, newRating, newText);
            }
        }

        async function editReview(reviewId, newRating, newText) {
            const userId = <?php echo $_SESSION['user_id']; ?>;
            const data = { review_id: reviewId, rating: newRating, review_text: newText, user_id: userId };
            const response = await fetch("server.php?action=editReview", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data),
            });
            const result = await response.json();
            if (result.message) {
                alert(result.message);
                fetchUserReviews(userId);
            } else {
                alert(result.error);
            }
        }

        // Load reading list and reviews on page load
        const userId = <?php echo $_SESSION['user_id']; ?>;
        fetchReadingList(userId);
        fetchUserReviews(userId);
    </script>
</body>
</html>
