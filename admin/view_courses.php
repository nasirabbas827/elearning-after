<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the form is submitted for course deletion
if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    $delete_query = "DELETE FROM Courses WHERE Course_ID = $course_id";
    if (mysqli_query($conn, $delete_query)) {
        echo "Course deleted successfully!";
    } else {
        echo "Error deleting course: " . mysqli_error($conn);
    }
}

// Fetch all courses from the database
$query = "SELECT * FROM Courses";
$result = mysqli_query($conn, $query);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Courses</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        /* Add your custom CSS styling here */
        body {
            background-color: aquamarine;

        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
        input[type="submit"] {
            padding: 5px 10px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <?php include('admin_navbar.php') ?>

    <div class="container">
        <h2 class="text-center mt-4 mb-3">View Courses</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Title</th>
                    <th>Course Instructor</th>
                    <th>Course Content</th>
                    <th>Course Outline</th>
                    <th>Course Price</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course) : ?>
                <tr>
                    <td><?php echo $course['Course_ID']; ?></td>
                    <td><?php echo $course['Course_Title']; ?></td>
                    <td><?php echo $course['Course_Instructor']; ?></td>
                    <td><?php echo $course['Course_Content']; ?></td>
                    <td><?php echo $course['Course_Outline']; ?></td>
                    <td><?php echo $course['Course_Price']; ?></td>
                    <td><?php echo $course['Start_Date']; ?></td>
                    <td><?php echo $course['End_Date']; ?></td>
                    <td>
                        <a class="btn btn-success" href="edit_course.php?id=<?php echo $course['Course_ID']; ?>">Edit</a>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this course?');">
                            <input type="hidden" name="course_id" value="<?php echo $course['Course_ID']; ?>">
                            <input type="submit" name="delete_course" value="Delete" class=" mt-2 btn btn-danger">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
