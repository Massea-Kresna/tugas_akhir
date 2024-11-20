<?php
session_start();
include 'config.php';
include 'classes/Book.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$book = new Book($conn);

// Tambah, Edit, Hapus Buku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $year = (int)$_POST['year'];
        $stock = (int)$_POST['stock'];
        $book->addBook($title, $author, $year, $stock);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $year = (int)$_POST['year'];
        $stock = (int)$_POST['stock'];
        $book->updateBook($id, $title, $author, $year, $stock);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $book->deleteBook($id);
    }
    header('Location: books.php');
    exit;
}

$books = $book->getAllBooks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Books Management</h2>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <input type="text" name="title" placeholder="Title" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="text" name="author" placeholder="Author" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="number" name="year" placeholder="Year" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="number" name="stock" placeholder="Stock" class="form-control" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Add Book</button>
        </form>

        <h3>Book List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $books->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['title']; ?></td>
                    <td><?= $row['author']; ?></td>
                    <td><?= $row['year']; ?></td>
                    <td><?= $row['stock']; ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button type="button" class="btn btn-warning btn-sm" 
                                onclick="editBook(<?= $row['id']; ?>, '<?= $row['title']; ?>', 
                                '<?= $row['author']; ?>', <?= $row['year']; ?>, <?= $row['stock']; ?>)">
                                Edit
                            </button>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script>
        function editBook(id, title, author, year, stock) {
            document.querySelector('[name="title"]').value = title;
            document.querySelector('[name="author"]').value = author;
            document.querySelector('[name="year"]').value = year;
            document.querySelector('[name="stock"]').value = stock;
            const form = document.querySelector('form');
            form.action = '';
            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = 'id';
            hiddenId.value = id;
            form.appendChild(hiddenId);
            const submitButton = document.createElement('button');
            submitButton.type = 'submit';
            submitButton.name = 'edit';
            submitButton.textContent = 'Update Book';
            submitButton.className = 'btn btn-success';
            form.appendChild(submitButton);
            form.querySelector('button[type="submit"]').remove();
        }
    </script>
</body>
</html>
