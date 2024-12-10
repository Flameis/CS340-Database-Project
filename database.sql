-- Drop existing tables if they exist
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS Reading_List;
DROP TABLE IF EXISTS Book;
DROP TABLE IF EXISTS Author;
DROP TABLE IF EXISTS User;

-- Create User table
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    date_joined DATE NOT NULL,
    role ENUM('admin', 'user') NOT NULL
);

-- Create Book table
CREATE TABLE Book (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT,
    title VARCHAR(255) NOT NULL,
    date_published DATE NOT NULL,
    avg_rating DECIMAL(3, 2),
    genre VARCHAR(50),
    FOREIGN KEY (author_id) REFERENCES Author(author_id) ON DELETE SET NULL ON UPDATE RESTRICT
);

-- Create Author table
CREATE TABLE Author (
    author_id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL
);

-- Create Reading List table
CREATE TABLE Reading_List (
    list_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    book_id INT,
    list_name VARCHAR(100),
    date_added DATE NOT NULL,
    status ENUM('to read', 'reading', 'read', 'dropped') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE ON UPDATE RESTRICT,
    FOREIGN KEY (book_id) REFERENCES Book(book_id) ON DELETE CASCADE ON UPDATE RESTRICT
);

-- Create Review table
CREATE TABLE Review (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    book_id INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE ON UPDATE RESTRICT,
    FOREIGN KEY (book_id) REFERENCES Book(book_id) ON DELETE SET NULL ON UPDATE RESTRICT
);

-- Sample data for User table
INSERT INTO User (username, date_joined, role) VALUES
('user1', '2023-01-01', 'user'),
('admin1', '2023-01-02', 'admin'),
('user2', '2023-01-03', 'user'),
('user3', '2023-01-04', 'user'),
('user4', '2023-01-05', 'user'),
('user5', '2023-01-06', 'user'),
('user6', '2023-01-07', 'user'),
('user7', '2023-01-08', 'user'),
('user8', '2023-01-09', 'user'),
('user9', '2023-01-10', 'user');

-- Sample data for Author table
INSERT INTO Author (fname, lname) VALUES
('John', 'Doe'),
('Jane', 'Smith'),
('Emily', 'Bronte'),
('Mark', 'Twain'),
('Charles', 'Dickens'),
('Leo', 'Tolstoy'),
('George', 'Orwell'),
('J.K.', 'Rowling'),
('Agatha', 'Christie'),
('Ernest', 'Hemingway');

-- Sample data for Book table
INSERT INTO Book (author_id, title, date_published, avg_rating, genre) VALUES
(1, 'Book One', '2023-01-01', 4.5, 'Fiction'),
(2, 'Book Two', '2023-01-02', 3.8, 'Non-Fiction'),
(3, 'Book Three', '2023-01-03', 4.2, 'Fiction'),
(4, 'Book Four', '2023-01-04', 4.0, 'Fiction'),
(5, 'Book Five', '2023-01-05', 3.9, 'Non-Fiction'),
(6, 'Book Six', '2023-01-06', 4.1, 'Fiction'),
(7, 'Book Seven', '2023-01-07', 4.3, 'Fiction'),
(8, 'Book Eight', '2023-01-08', 4.4, 'Non-Fiction'),
(9, 'Book Nine', '2023-01-09', 4.6, 'Fiction'),
(10, 'Book Ten', '2023-01-10', 4.7, 'Non-Fiction'),
(1, 'Book Eleven', '2023-01-11', 4.8, 'Fiction'),
(2, 'Book Twelve', '2023-01-12', 4.9, 'Non-Fiction'),
(3, 'Book Thirteen', '2023-01-13', 5.0, 'Fiction'),
(4, 'Book Fourteen', '2023-01-14', 3.7, 'Fiction'),
(5, 'Book Fifteen', '2023-01-15', 3.6, 'Non-Fiction'),
(6, 'Book Sixteen', '2023-01-16', 3.5, 'Fiction'),
(7, 'Book Seventeen', '2023-01-17', 3.4, 'Fiction'),
(8, 'Book Eighteen', '2023-01-18', 3.3, 'Non-Fiction'),
(9, 'Book Nineteen', '2023-01-19', 3.2, 'Fiction'),
(10, 'Book Twenty', '2023-01-20', 3.1, 'Non-Fiction');

-- Sample data for Reading List table
INSERT INTO Reading_List (user_id, book_id, list_name, date_added, status) VALUES
(1, 1, 'Favorites', '2023-01-01', 'read'),
(1, 2, 'To Read', '2023-01-02', 'to read'),
(2, 3, 'Favorites', '2023-01-03', 'reading'),
(2, 4, 'To Read', '2023-01-04', 'to read'),
(3, 5, 'Favorites', '2023-01-05', 'read'),
(3, 6, 'To Read', '2023-01-06', 'to read'),
(4, 7, 'Favorites', '2023-01-07', 'reading'),
(4, 8, 'To Read', '2023-01-08', 'to read'),
(5, 9, 'Favorites', '2023-01-09', 'read'),
(5, 10, 'To Read', '2023-01-10', 'to read');

-- Sample data for Review table
INSERT INTO Review (user_id, book_id, rating, review_text) VALUES
(1, 1, 5, 'Great book!'),
(1, 2, 3, 'It was okay.'),
(2, 3, 4, 'Interesting read.'),
(2, 4, 2, 'Not my type.'),
(3, 5, 5, 'Loved it!'),
(3, 6, 4, 'Good book.'),
(4, 7, 3, 'Average.'),
(4, 8, 5, 'Excellent!'),
(5, 9, 4, 'Very good.'),
(5, 10, 2, 'Could be better.'),
(6, 11, 5, 'Amazing!'),
(6, 12, 3, 'Not bad.'),
(7, 13, 4, 'Enjoyed it.'),
(7, 14, 2, 'Disappointing.'),
(8, 15, 5, 'Fantastic!'),
(8, 16, 4, 'Quite good.'),
(9, 17, 3, 'Mediocre.'),
(9, 18, 5, 'Superb!'),
(10, 19, 4, 'Nice read.'),
(10, 20, 2, 'Not great.');

-- Triggers to update avg_rating in Book table
CREATE TRIGGER update_avg_rating AFTER INSERT ON Review
FOR EACH ROW
BEGIN
    UPDATE Book
    SET avg_rating = (SELECT AVG(rating) FROM Review WHERE book_id = NEW.book_id)
    WHERE book_id = NEW.book_id;
END;

CREATE TRIGGER update_avg_rating_on_delete AFTER DELETE ON Review
FOR EACH ROW
BEGIN
    UPDATE Book
    SET avg_rating = (SELECT AVG(rating) FROM Review WHERE book_id = OLD.book_id)
    WHERE book_id = OLD.book_id;
END;

CREATE TRIGGER update_avg_rating_on_update AFTER UPDATE ON Review
FOR EACH ROW
BEGIN
    UPDATE Book
    SET avg_rating = (SELECT AVG(rating) FROM Review WHERE book_id = NEW.book_id)
    WHERE book_id = NEW.book_id;
END;


