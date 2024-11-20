<?php
session_start();
include 'config.php';
include 'classes/Member.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$isAdmin = $_SESSION['user'] === 'admin'; // Pastikan hanya admin yang dapat menambah user

$member = new Member($conn);

// Tambah, Edit, Hapus Anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $member->addMember($name, $email, $phone);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $member->updateMember($id, $name, $email, $phone);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $member->deleteMember($id);
    } elseif ($isAdmin && isset($_POST['add_user'])) { // Tambah User (Admin Saja)
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $query->bind_param('ss', $username, $password);
        $query->execute();
    }
    header('Location: members.php');
    exit;
}

$members = $member->getAllMembers();
$users = $conn->query("SELECT id, username FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Members Management</h2>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <input type="text" name="name" placeholder="Name" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" placeholder="Email" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="text" name="phone" placeholder="Phone" class="form-control" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Add Member</button>
        </form>

        <h3>Member List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $members->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td><?= $row['phone']; ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button type="button" class="btn btn-warning btn-sm" 
                                onclick="editMember(<?= $row['id']; ?>, '<?= $row['name']; ?>', 
                                '<?= $row['email']; ?>', '<?= $row['phone']; ?>')">
                                Edit
                            </button>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
<hr></hr>
<hr></hr>
        <?php if ($isAdmin): ?>
        <h2 class="mt-4">Add User</h2>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <input type="text" name="username" placeholder="Username" class="form-control" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Password" class="form-control" required>
            </div>
            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
        </form>

        <h3>User List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['username']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script>
        function editMember(id, name, email, phone) {
            document.querySelector('[name="name"]').value = name;
            document.querySelector('[name="email"]').value = email;
            document.querySelector('[name="phone"]').value = phone;
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
            submitButton.textContent = 'Update Member';
            submitButton.className = 'btn btn-success';
            form.appendChild(submitButton);
            form.querySelector('button[type="submit"]').remove();
        }
    </script>
</body>
</html>