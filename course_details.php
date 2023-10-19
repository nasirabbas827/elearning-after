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

// Function to fetch course details based on the course ID from the query parameter
function fetchCourseDetails($conn, $course_id) {
    $query = "SELECT * FROM Courses WHERE Course_ID = $course_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Fetch course details if the course ID is provided in the URL query parameter
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    $course_details = fetchCourseDetails($conn, $course_id);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Details</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: aquamarine;

        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <?php if (isset($course_details)) : ?>
            <h2><?php echo $course_details['Course_Title']; ?></h2>
            <p class="mb-2">Instructor: <?php echo $course_details['Course_Instructor']; ?></p>
            <p class="mb-2">Content: <?php echo $course_details['Course_Content']; ?></p>
            <p class="mb-2">Outline: <?php echo $course_details['Course_Outline']; ?></p>
            <p><a class="btn btn-primary" href="enrolled_courses.php?course_id=<?php echo $course_details['Course_ID']; ?>">View Course Content</a></p>
        <?php else : ?>
            <p>No course details available.</p>
        <?php endif; ?>
    </div>

    <!-- Add Bootstrap JS and jQuery links (at the end of the body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

