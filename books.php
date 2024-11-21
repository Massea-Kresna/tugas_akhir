<?php
session_start();
include 'config.php';
include 'classes/Book.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$book = new Book($conn);

// Add, Edit, Delete Book
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 1000px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .card-header {
            background-color: #f8f9fa;
        }
        .modal-content {
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Books Management</h2>

        <!-- Form to Add New Book -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Book</h5>
            </div>
            <div class="card-body">
                <form method="POST">
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
            </div>
        </div>

        <!-- Book List -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Book List</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
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
            </div>
        </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
