<?php
session_start();

$booksFile = "books.json";

// Check if the books file exists, and initialize with data if not
function readBooks($booksFile) {
    return file_exists($booksFile) ? json_decode(file_get_contents($booksFile), true) : [];
}

function writeBooks($booksFile, $books) {
    file_put_contents($booksFile, json_encode($books));
}

$books = readBooks($booksFile);

// Admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'password');

// Admin login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin Login
    if (isset($_POST['adminLogin'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['isAdmin'] = true;
            $_SESSION['message'] = "Logged in as Admin!";
        } else {
            $_SESSION['message'] = "Invalid admin credentials!";
        }
    }

    // Student Login and Borrow Book Logic
    elseif (isset($_POST['studentLogin'])) {
        $studentName = htmlspecialchars($_POST['studentName']);
        $studentID = htmlspecialchars($_POST['studentID']);
        
        // Store student info in session for borrowing
        $_SESSION['student'] = [
            'name' => $studentName,
            'id' => $studentID
        ];
        $_SESSION['message'] = "Welcome, $studentName! Choose a book to borrow.";
    }

    // Admin Logout
    elseif (isset($_POST['adminLogout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // Add Book (Admin only)
    elseif (isset($_POST['addBook']) && ($_SESSION['isAdmin'] ?? false)) {
        $title = htmlspecialchars($_POST['bookTitle']);
        $author = htmlspecialchars($_POST['bookAuthor']);
        $newId = $books ? end($books)['id'] + 1 : 1;
        $books[] = ["id" => $newId, "title" => $title, "author" => $author, "status" => "available"];
        writeBooks($booksFile, $books);
        $_SESSION['message'] = "Book added successfully!";
    }

    // Remove Book (Admin only)
    elseif (isset($_POST['deleteBook']) && ($_SESSION['isAdmin'] ?? false)) {
        $bookId = intval($_POST['bookId']);
        $books = array_filter($books, fn($book) => $book['id'] !== $bookId);
        writeBooks($booksFile, $books);
        $_SESSION['message'] = "Book removed successfully!";
    }

    // Borrow Book (Student)
    elseif (isset($_POST['borrowBook'])) {
        $bookTitle = htmlspecialchars($_POST['book']);
        $borrowDate = htmlspecialchars($_POST['borrowDate']);
        $returnDate = htmlspecialchars($_POST['returnDate']);
        $tokenNo = strtoupper(uniqid("TOKEN_")); // Generate a unique token

        // Store borrowed book and dates
        $_SESSION['borrowed'] = [
            "name" => $_SESSION['student']['name'],
            "id" => $_SESSION['student']['id'],
            "book" => $bookTitle,
            "borrowDate" => $borrowDate,
            "returnDate" => $returnDate,
            "token" => $tokenNo
        ];
        $_SESSION['message'] = "Thank you, {$_SESSION['student']['name']}! Your token number is $tokenNo for the book '$bookTitle'.";
    }

    // Logout Student
    elseif (isset($_POST['studentLogout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }

    header("Location: index.php");
    exit;
}
?>
