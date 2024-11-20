<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Dashboard</h2>
        <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>!</p>
        <div class="mb-3">
            <a href="books.php" class="btn btn-primary">Manage Books</a>
            <a href="members.php" class="btn btn-primary">Manage Members</a>
            <a href="transaction.php" class="btn btn-primary">Transactions</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>
