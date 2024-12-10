document.getElementById("add-review-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = {
        user_id: formData.get("user_id"),
        book_id: formData.get("book_id"),
        rating: formData.get("rating"),
        review_text: formData.get("review_text"),
    };

    try {
        const response = await fetch("server.php?action=addReview", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
        });
        const result = await response.json();
        if (result.message) {
            alert(result.message);
            fetchReviews();
        } else {
            alert(result.error);
        }
    } catch (err) {
        console.error("Error:", err);
    }
});

async function fetchReviews() {
    try {
        const response = await fetch("server.php?action=getReviews");
        const reviews = await response.json();
        const reviewsList = document.getElementById("reviews-list");
        reviewsList.innerHTML = "";
        reviews.forEach((review) => {
            const li = document.createElement("li");
            li.innerHTML = `
                User ID: ${review.user_id}, Book ID: ${review.book_id}, Rating: ${review.rating} <br>
                Review: ${review.review_text}
                <button onclick="deleteReview(${review.review_id})">Delete</button>
                <button onclick="editReviewPrompt(${review.review_id}, ${review.rating}, '${review.review_text}')">Edit</button>
            `;
            reviewsList.appendChild(li);
        });
    } catch (err) {
        console.error("Error fetching reviews:", err);
    }
}

async function deleteReview(reviewId) {
    const response = await fetch(`server.php?action=deleteReview&review_id=${reviewId}`, { method: "GET" });
    const result = await response.json();
    if (result.message) {
        alert(result.message);
        fetchReviews();
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
    const data = { review_id: reviewId, rating: newRating, review_text: newText };
    const response = await fetch("server.php?action=editReview", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
    });
    const result = await response.json();
    if (result.message) {
        alert(result.message);
        fetchReviews();
    } else {
        alert(result.error);
    }
}

// Load reviews on page load
fetchReviews();
