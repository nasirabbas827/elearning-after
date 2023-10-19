<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to fetch all comments and replies
// Function to fetch all comments and replies
function fetchAllComments($conn) {
    $query = "SELECT c.Comment_ID, c.User_ID, c.Course_ID, c.Comment, c.Admin_Reply, c.Timestamp,
    u.username as User_Name, cr.Course_Title
    FROM Comments c
    LEFT JOIN users u ON c.User_ID = u.id
    LEFT JOIN Courses cr ON c.Course_ID = cr.Course_ID
    ORDER BY c.Timestamp DESC";

    $result = mysqli_query($conn, $query);

    // Check for query execution error
    if (!$result) {
        echo "Error fetching comments: " . mysqli_error($conn);
        return []; // Return an empty array to prevent foreach error
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle admin reply submission
if (isset($_POST['submit_reply'])) {
    $comment_id = $_POST['comment_id'];
    $admin_reply = $_POST['admin_reply'];

    // Use prepared statement to prevent SQL injection
    $update_query = "UPDATE Comments SET Admin_Reply = ? WHERE Comment_ID = ?";

    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $admin_reply, $comment_id);

    if (mysqli_stmt_execute($stmt)) {
        // Refresh the page after successful reply submission
        header("Location: comments.php");
        exit;
    } else {
        echo "Error submitting reply: " . mysqli_error($conn);
    }
}

// Function to delete a comment
function deleteComment($conn, $comment_id) {
    $delete_query = "DELETE FROM Comments WHERE Comment_ID = $comment_id";
    if (mysqli_query($conn, $delete_query)) {
        return true;
    } else {
        return false;
    }
}

// Handle comment deletion
if (isset($_POST['delete_comment'])) {
    $comment_id_to_delete = $_POST['comment_id'];
    if (deleteComment($conn, $comment_id_to_delete)) {
        // Refresh the page after successful deletion
        header("Location: comments.php");
        exit;
    } else {
        echo "Error deleting comment.";
    }
}

$comments = fetchAllComments($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Comments</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
body {
            background-color: aquamarine;

        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

</style>
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-4">
        <h2 class="text-center">Admin Comments</h2>
        <table class="table table-bordered">
        <tr>
    <th>Comment ID</th>
    <th>User Name</th>
    <th>Course Title</th>
    <th>Comment</th>
    <th>Admin Reply</th>
    <th>Timestamp</th>
    <th>Reply</th>
    <th>Delete</th> <!-- New column for delete buttons -->
</tr>
<?php foreach ($comments as $comment) : ?>
    <tr>
        <td><?php echo $comment['Comment_ID']; ?></td>
        <td><?php echo $comment['User_Name']; ?></td>
        <td><?php echo $comment['Course_Title']; ?></td>
        <td><?php echo $comment['Comment']; ?></td>
        <td><?php echo $comment['Admin_Reply']; ?></td>
        <td><?php echo $comment['Timestamp']; ?></td>
        <td>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="comment_id" value="<?php echo $comment['Comment_ID']; ?>">
                <div class="form-group">
                    <textarea class="form-control" name="admin_reply" rows="2"><?php echo $comment['Admin_Reply']; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary" name="submit_reply">Reply</button>
            </form>
        </td>
        <td>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="comment_id" value="<?php echo $comment['Comment_ID']; ?>">
                <button type="submit" class="btn btn-danger" name="delete_comment">Delete</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
        </table>
    </div>

    <!-- Add Bootstrap JS and jQuery links (at the end of the body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>