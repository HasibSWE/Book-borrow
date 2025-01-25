<?php
session_start();
include 'process.php'; // Include the process logic for form handling
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

        <!-- Admin Login Form -->
        <?php if (!isset($_SESSION['isAdmin']) && !isset($_SESSION['student'])) : ?>
            <h2>Admin Login</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Admin Username" required>
                <input type="password" name="password" placeholder="Admin Password" required>
                <button type="submit" name="adminLogin">Login as Admin</button>
            </form>

            <h2>Student Login</h2>
            <form method="POST">
                <input type="text" name="studentName" placeholder="Student Name" required>
                <input type="text" name="studentID" placeholder="Student ID" required>
                <button type="submit" name="studentLogin">Login as Student</button>
            </form>
        <?php endif; ?>

        <!-- Admin Panel (after login) -->
        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) : ?>
            <h2>Admin Panel</h2>
            <p><?php echo $_SESSION['message'] ?? ''; ?></p>
            <form method="POST">
                <button type="submit" name="adminLogout">Logout</button>
            </form>

            <!-- Add Book Form -->
            <h3>Add a Book</h3>
            <form method="POST">
                <input type="text" name="bookTitle" placeholder="Book Title" required>
                <input type="text" name="bookAuthor" placeholder="Author" required>
                <button type="submit" name="addBook">Add Book</button>
            </form>

            <!-- Current Books -->
            <h3>Manage Books</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($books as $book) : ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="bookId" value="<?= $book['id'] ?>">
                                <button type="submit" name="deleteBook">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <!-- Student Panel (after login) -->
        <?php if (isset($_SESSION['student']) && !isset($_SESSION['isAdmin'])) : ?>
            <h2>Welcome, <?= htmlspecialchars($_SESSION['student']['name']) ?>!</h2>
            <p><?php echo $_SESSION['message'] ?? ''; ?></p>

            <!-- Borrow a Book Form -->
            <h3>Borrow a Book</h3>
            <?php if (isset($_SESSION['borrowed'])) : ?>
                <p>Student: <?= htmlspecialchars($_SESSION['borrowed']['name']) ?><br>
                   Book: <?= htmlspecialchars($_SESSION['borrowed']['book']) ?><br>
                   Token: <?= htmlspecialchars($_SESSION['borrowed']['token']) ?><br>
                   Borrow Date: <?= htmlspecialchars($_SESSION['borrowed']['borrowDate']) ?><br>
                   Return Date: <?= htmlspecialchars($_SESSION['borrowed']['returnDate']) ?></p>

                <form method="POST">
                    <button type="submit" name="studentLogout">Logout</button>
                </form>
            <?php else : ?>
                <form method="POST">
                    <select name="book" required>
                        <?php foreach ($books as $book) : ?>
                            <option value="<?= htmlspecialchars($book['title']) ?>"><?= htmlspecialchars($book['title']) ?> by <?= htmlspecialchars($book['author']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="date" name="borrowDate" required>
                    <input type="date" name="returnDate" required>
                    <button type="submit" name="borrowBook">Borrow Book</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</body>
</html>
