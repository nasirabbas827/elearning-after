<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}

// Function to fetch attempted courses for the logged-in user
function fetchAttemptedCourses($conn, $user_id) {
    $query = "SELECT c.Course_Title, c.Course_Instructor, c.Course_Price, a.Attempt_Date
              FROM Courses c
              JOIN Attempts a ON c.Course_ID = a.Course_ID
              WHERE a.User_ID = $user_id";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Error fetching attempted courses: " . mysqli_error($conn);
        return array(); // Return an empty array to prevent further errors
    }
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$user_id = $_SESSION["user_id"];
$attempted_courses = fetchAttemptedCourses($conn, $user_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attempted Courses</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom CSS styles */
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
    <?php include('navbar.php'); ?>

    <div class="container mt-4 mb-4">
        <h2 class="text-center">Attempted Courses</h2>
        <?php if (empty($attempted_courses)) : ?>
            <p>No courses have been attempted yet.</p>
        <?php else : ?>
            <table>
                <tr>
                    <th>Course Title</th>
                    <th>Instructor</th>
                    <th>Price</th>
                    <th>Attempt Date</th>
                </tr>
                <?php foreach ($attempted_courses as $course) : ?>
                    <tr>
                        <td><?php echo $course['Course_Title']; ?></td>
                        <td><?php echo $course['Course_Instructor']; ?></td>
                        <td><?php echo '$' . $course['Course_Price']; ?></td>
                        <td><?php echo $course['Attempt_Date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <!-- Add Bootstrap JS and jQuery links (at the end of the body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
