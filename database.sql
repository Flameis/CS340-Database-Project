-- Drop existing tables if they exist
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS Reading_List;
DROP TABLE IF EXISTS Book;
DROP TABLE IF EXISTS Author;
DROP TABLE IF EXISTS User;

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS update_avg_rating;
DROP TRIGGER IF EXISTS update_avg_rating_on_delete;
DROP TRIGGER IF EXISTS update_avg_rating_on_update;

-- Create User table
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Added password column
    date_joined DATE NOT NULL,
    role ENUM('admin', 'user') NOT NULL
);

-- Create Author table
CREATE TABLE Author (
    author_id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL
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

-- Triggers to update avg_rating in Book table
DELIMITER //

CREATE TRIGGER update_avg_rating AFTER INSERT ON Review
FOR EACH ROW
BEGIN
    UPDATE Book
    SET avg_rating = (SELECT COALESCE(AVG(rating), 0) FROM Review WHERE book_id = NEW.book_id)
    WHERE book_id = NEW.book_id;
END;
//

CREATE TRIGGER update_avg_rating_on_delete AFTER DELETE ON Review
FOR EACH ROW
BEGIN
    UPDATE Book
    SET avg_rating = (SELECT COALESCE(AVG(rating), 0) FROM Review WHERE book_id = OLD.book_id)
    WHERE book_id = OLD.book_id;
END;
//

CREATE TRIGGER update_avg_rating_on_update AFTER UPDATE ON Review
FOR EACH ROW
BEGIN
    UPDATE Book
    SET avg_rating = (SELECT COALESCE(AVG(rating), 0) FROM Review WHERE book_id = NEW.book_id)
    WHERE book_id = NEW.book_id;
END;
//

DELIMITER ;

-- Sample data for User table
INSERT INTO User (username, password, date_joined, role) VALUES
('john_doe', 'password1', '2023-01-01', 'user'),
('admin_user', 'password2', '2023-01-02', 'admin'),
('jane_smith', 'password3', '2023-01-03', 'user'),
('emily_bronte', 'password4', '2023-01-04', 'user'),
('mark_twain', 'password5', '2023-01-05', 'user'),
('charles_dickens', 'password6', '2023-01-06', 'user'),
('leo_tolstoy', 'password7', '2023-01-07', 'user'),
('george_orwell', 'password8', '2023-01-08', 'user'),
('jk_rowling', 'password9', '2023-01-09', 'user'),
('agatha_christie', 'password10', '2023-01-10', 'user');

-- Sample data for Author table
INSERT INTO Author (fname, lname) VALUES
('John', 'Grisham'),
('Jane', 'Austen'),
('Emily', 'Dickinson'),
('Mark', 'Twain'),
('Charles', 'Dickens'),
('Leo', 'Tolstoy'),
('George', 'Orwell'),
('J.K.', 'Rowling'),
('Agatha', 'Christie'),
('Ernest', 'Hemingway');

-- Sample data for Book table
INSERT INTO Book (author_id, title, date_published, avg_rating, genre) VALUES
(1, 'The Firm', '1991-02-01', 4.5, 'Legal Thriller'),
(2, 'Pride and Prejudice', '1813-01-28', 4.8, 'Romance'),
(3, 'Collected Poems', '1890-01-01', 4.2, 'Poetry'),
(4, 'Adventures of Huckleberry Finn', '1884-12-10', 4.0, 'Adventure'),
(5, 'A Tale of Two Cities', '1859-04-30', 4.1, 'Historical Fiction'),
(6, 'War and Peace', '1869-01-01', 4.3, 'Historical Fiction'),
(7, '1984', '1949-06-08', 4.6, 'Dystopian'),
(8, 'Harry Potter and the Sorcerers Stone', '1997-06-26', 4.9, 'Fantasy'),
(9, 'Murder on the Orient Express', '1934-01-01', 4.7, 'Mystery'),
(10, 'The Old Man and the Sea', '1952-09-01', 4.4, 'Literary Fiction');

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
(1, 1, 5, 'A thrilling read from start to finish.'),
(1, 2, 4, 'A classic romance with timeless appeal.'),
(2, 3, 4, 'Beautiful and thought-provoking poetry.'),
(2, 4, 3, 'An adventurous tale, but a bit dated.'),
(3, 5, 5, 'A masterpiece of historical fiction.'),
(3, 6, 4, 'A lengthy read, but worth it.'),
(4, 7, 5, 'A chilling dystopian novel.'),
(4, 8, 5, 'Magical and captivating.'),
(5, 9, 4, 'A clever and engaging mystery.'),
(5, 10, 3, 'A simple yet profound story.');



