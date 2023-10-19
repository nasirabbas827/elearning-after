<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// define variables and initialize with empty values
$new_admin_username = $new_admin_password = "";
$username_err = $password_err = "";
$success_message = "";

// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $new_admin_username = trim($_POST["new_admin_username"]);
    $new_admin_password = trim($_POST["new_admin_password"]);

    // Check if the username is not empty
    if (empty($new_admin_username)) {
        $username_err = "Please enter a username.";
    }

    // Check if the password is not empty
    if (empty($new_admin_password)) {
        $password_err = "Please enter a password.";
    }

    // If no errors, add the new admin to the admin table
    if (empty($username_err) && empty($password_err)) {
        $insert_admin_query = "INSERT INTO admins (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insert_admin_query);
        mysqli_stmt_bind_param($stmt, "ss", $new_admin_username, $new_admin_password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success_message = "Admin added successfully.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Admin</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Add your CSS styling here -->
    <style>
                body {
            background-color: aquamarine;

        }
    </style>
</head>
<body>
<?php include('admin_navbar.php') ?>

    <div class="container mt-5">
        <h2 class="text-center">Add New Admin</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="new_admin_username" class="form-control" value="<?php echo $new_admin_username; ?>">
                <span class="text-danger"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="new_admin_password" class="form-control">
                <span class="text-danger"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group text-center">
                <input type="submit" value="Add Admin" name="add_admin" class="btn btn-primary">
            </div>
        </form>

        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
