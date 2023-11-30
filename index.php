<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['delete'])) {
    $userId = $_POST['user_id'];

    $query = "DELETE FROM users WHERE id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
}

if (isset($_POST['update'])) {
    $userId = $_POST['user_id'];
    $newUsername = $_POST['new_username'];
    $newEmail = $_POST['new_email'];

    $query = "UPDATE users SET username = :new_username, email = :new_email WHERE id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':new_username', $newUsername);
    $stmt->bindParam(':new_email', $newEmail);
    $stmt->execute();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo $_SESSION['user']; ?></h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>

        <div class="mt-3">
            <h3>Account List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM users";
                    $stmt = $db->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['email']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='user_id' value='{$row['id']}'>
                                        <button type='submit' name='delete' class='btn btn-danger btn-sm'>Delete</button>
                                    </form>
                                    <button type='button' class='btn btn-success btn-sm' data-toggle='modal' data-target='#updateModal{$row['id']}'>Update</button>
                                </td>
                                </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pencarian Data Akun -->
        <div class="mt-3">
            <h3>Search Account</h3>
            <form method="get" action="">
                <div class="form-group">
                    <label for="search">Search by Username or Email:</label>
                    <input type="text" name="search" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <?php
            if (isset($_GET['search'])) {
                $searchTerm = $_GET['search'];
                $query = "SELECT * FROM users WHERE username LIKE :search OR email LIKE :search";
                $stmt = $db->prepare($query);
                $stmt->bindValue(':search', '%' . $searchTerm . '%');
                $stmt->execute();

                echo "<h3>Search Results</h3>";
                echo "<table class='table'>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td>
                                <form method='post' action=''>
                                    <input type='hidden' name='user_id' value='{$row['id']}'>
                                    <button type='submit' name='delete' class='btn btn-danger btn-sm'>Delete</button>
                                </form>
                                <button type='button' class='btn btn-success btn-sm' data-toggle='modal' data-target='#updateModal{$row['id']}'>Update</button>
                            </td>
                            </tr>";
                }

                echo "</tbody></table>";
            }
            ?>
        </div>

        <!-- Update Data Akun - Modal -->
        <?php
        $queryUsers = "SELECT * FROM users";
        $stmtUsers = $db->prepare($queryUsers);
        $stmtUsers->execute();

        while ($row = $stmtUsers->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateModalLabel<?php echo $row['id']; ?>">Update Account</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <div class="form-group">
                                    <label for="new_username">New Username:</label>
                                    <input type="text" name="new_username" class="form-control" value="<?php echo $row['username']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_email">New Email:</label>
                                    <input type="email" name="new_email" class="form-control" value="<?php echo $row['email']; ?>" required>
                                </div>
                                <button type="submit" name="update" class="btn btn-success">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>

    </div>


    <!-- Bootstrap JS and jQuery (Make sure to include these at the end of the body tag) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
