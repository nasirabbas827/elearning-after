<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}
$email = $_SESSION["email"];
$user_query = "SELECT id FROM Users WHERE Email = '$email'";
$result = mysqli_query($conn, $user_query);
if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION["user_id"] = $user["id"];
}

// Set the user's balance to 10000 if it is not already set
$user_id = $_SESSION["user_id"];

// Function to fetch enrolled courses for the logged-in user
function fetchEnrolledCourses($conn, $user_id) {
    $query = "SELECT c.Course_ID, c.Course_Title, c.Course_Instructor, c.Course_Price
              FROM Courses c
              JOIN Enrollments e ON c.Course_ID = e.Course_ID
              WHERE e.User_ID = $user_id";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to fetch comments and replies for a specific course
function fetchCourseComments($conn, $course_id) {
    $query = "SELECT c.Comment, c.Admin_Reply, c.Timestamp
              FROM Comments c
              WHERE c.Course_ID = $course_id
              ORDER BY c.Timestamp DESC";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to delete enrollment for a course
function deleteEnrollment($conn, $user_id, $course_id) {
    $delete_query = "DELETE FROM Enrollments WHERE User_ID = $user_id AND Course_ID = $course_id";
    if (mysqli_query($conn, $delete_query)) {
        return true;
    } else {
        return false;
    }
}

$user_id = $_SESSION["user_id"];
$enrolled_courses = fetchEnrolledCourses($conn, $user_id);

// Handle enrollment deletion
if (isset($_POST['delete_enrollment'])) {
    $course_id_to_delete = $_POST['course_id'];
    if (deleteEnrollment($conn, $user_id, $course_id_to_delete)) {
        // Refresh the page after successful deletion
        header("Location: user_courses.php");
        exit;
    } else {
        echo "Error deleting enrollment.";
    }
}

// Handle user comment submission
if (isset($_POST['submit_comment'])) {
    $user_comment = $_POST['user_comment'];
    $course_id_for_comment = $_POST['course_id'];

    // Use prepared statement to prevent SQL injection
    $insert_query = "INSERT INTO Comments (User_ID, Course_ID, Comment, Timestamp)
                     VALUES (?, ?, ?, NOW())";

    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $course_id_for_comment, $user_comment);

    if (mysqli_stmt_execute($stmt)) {
        // Refresh the page after successful comment submission
        echo "Comment Added Successfully ";
        header("Location: user_courses.php");
        exit;
    } else {
        echo "Error submitting comment: " . mysqli_error($conn);
    }
}
// Function to fetch comments and replies for a specific course by the logged-in user
function fetchUserCourseComments($conn, $user_id, $course_id) {
    $query = "SELECT Comment, Admin_Reply, Timestamp
              FROM Comments
              WHERE Course_ID = $course_id AND User_ID = $user_id
              ORDER BY Timestamp DESC";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Enrolled Courses</title>
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
        .comments-section {
            margin-top: 20px;
        }
        .comments-section p {
            margin-bottom: 5px;
        }
        .comments-section hr {
            margin: 10px 0;
            border-top: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
    <h2 class="text-center">Enrolled Courses</h2>
    <table>
        <tr>
            <th>Course Title</th>
            <th>Instructor</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach ($enrolled_courses as $course) : ?>
            <tr>
                <td><?php echo $course['Course_Title']; ?></td>
                <td><?php echo $course['Course_Instructor']; ?></td>
                <td><?php echo '$' . $course['Course_Price']; ?></td>
                <td>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <input type="hidden" name="course_id" value="<?php echo $course['Course_ID']; ?>">
                        <button type="submit" class="btn btn-danger" name="delete_enrollment">Delete</button>
                    </form>
                    <button type="button" class="mt-2 btn btn-primary" data-toggle="modal" data-target="#commentModal_<?php echo $course['Course_ID']; ?>">
                        Ask a Question
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Add comment modals for each course -->
<?php foreach ($enrolled_courses as $course) : ?>
    <div class="modal fade" id="commentModal_<?php echo $course['Course_ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel_<?php echo $course['Course_ID']; ?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel_<?php echo $course['Course_ID']; ?>">Ask a Question</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <div class="form-group">
                            <label for="user_comment">Your Question:</label>
                            <textarea class="form-control" name="user_comment" rows="4" required></textarea>
                        </div>
                        <input type="hidden" name="course_id" value="<?php echo $course['Course_ID']; ?>">
                        <button type="submit" class="btn btn-primary" name="submit_comment">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Display user comments and admin replies for the current course -->
    <div class="container mt-4">
        <h4>Course: <?php echo $course['Course_Title']; ?></h4>
        <div class="comments-section">
            <?php
            $course_id = $course['Course_ID'];
            $user_comments = fetchUserCourseComments($conn, $user_id, $course_id);
            foreach ($user_comments as $comment) {
                echo "<h4>Your Comment: " . $comment['Comment'] . "</h4>";
                if (!empty($comment['Admin_Reply'])) {
                    echo "<h5>Admin Reply: " . $comment['Admin_Reply'] . "</h5>";
                }
                echo "<hr>";
            }
            ?>
        </div>
    </div>
<?php endforeach; ?>

    <!-- Add Bootstrap JS and jQuery links (at the end of the body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
