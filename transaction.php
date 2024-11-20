<?php
session_start();
include 'config.php';
include 'classes/Transaction.php';
include 'classes/Book.php';
include 'classes/Member.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$transaction = new Transaction($conn);
$book = new Book($conn);
$member = new Member($conn);

// Tangani peminjaman dan pengembalian
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['borrow'])) {
        $book_id = $_POST['book_id'];
        $member_id = $_POST['member_id'];
        $borrow_date = date('Y-m-d');
        $transaction->borrowBook($book_id, $member_id, $borrow_date);
    } elseif (isset($_POST['return'])) {
        $transaction_id = $_POST['transaction_id'];
        $return_date = date('Y-m-d');
        $transaction->returnBook($transaction_id, $return_date);
    }
    header('Location: transaction.php');
    exit;
}

$books = $book->getAllBooks();
$members = $member->getAllMembers();
$transactions = $transaction->getAllTransactions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Transactions</h2>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <select name="book_id" class="form-select" required>
                    <option value="">Select Book</option>
                    <?php while ($row = $books->fetch_assoc()): ?>
                        <option value="<?= $row['id']; ?>"><?= $row['title']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <select name="member_id" class="form-select" required>
                    <option value="">Select Member</option>
                    <?php while ($row = $members->fetch_assoc()): ?>
                        <option value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="borrow" class="btn btn-primary">Borrow Book</button>
        </form>

        <h3>Transaction List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book</th>
                    <th>Member</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Fine</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $transactions->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['title']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td><?= $row['borrow_date']; ?></td>
                    <td><?= $row['return_date']; ?></td>
                    <td><?= $row['status']; ?></td>
                    <td><?= $row['fine']; ?></td>
                    <td>
                        <?php if ($row['status'] === 'borrowed'): ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="transaction_id" value="<?= $row['id']; ?>">
                                <button type="submit" name="return" class="btn btn-success btn-sm">Return</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
