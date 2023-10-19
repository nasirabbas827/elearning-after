<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the form is submitted for course update
if (isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $course_title = $_POST['course_title'];
    $course_instructor = $_POST['course_instructor'];
    $course_content = $_POST['course_content'];
    $course_outline = $_POST['course_outline'];
    $course_price = $_POST['course_price'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Prepare the SQL statement using a prepared statement
    $query = "UPDATE Courses 
              SET Course_Title = ?, Course_Instructor = ?, Course_Content = ?, Course_Outline = ?, 
                  Course_Price = ?, Start_Date = ?, End_Date = ? 
              WHERE Course_ID = ?";

    // Create a prepared statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters to the prepared statement
    mysqli_stmt_bind_param($stmt, "ssssdssi", $course_title, $course_instructor, $course_content, $course_outline, $course_price, $start_date, $end_date, $course_id);

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        echo "Course updated successfully!";
        header("Location: view_courses.php");
        exit(); // Make sure to exit after redirection
    } else {
        echo "Error updating course: " . mysqli_stmt_error($stmt);
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
}

// Fetch course details based on the course ID from the query parameter
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    $query = "SELECT * FROM Courses WHERE Course_ID = $course_id";
    $result = mysqli_query($conn, $query);
    $course = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body {
            background-color: aquamarine;

        }
        h2 {
            margin-bottom: 20px;
        }
        form {
            width: 50%;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        textarea,
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        p {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include('admin_navbar.php') ?>

    <div class="container">
        <h2 class="text-center mt-4 mb-3">Edit Course</h2>
        <?php if (isset($course)) : ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="course_id" value="<?php echo $course['Course_ID']; ?>">

                <label for="course_title">Course Title:</label>
                <input type="text" name="course_title" value="<?php echo $course['Course_Title']; ?>" required><br>

                <label for="course_instructor">Course Instructor:</label>
                <input type="text" name="course_instructor" value="<?php echo $course['Course_Instructor']; ?>" required><br>

                <label for="course_content">Course Content:</label>
                <textarea name="course_content" required><?php echo $course['Course_Content']; ?></textarea><br>

                <label for="course_outline">Course Outline:</label>
                <textarea name="course_outline" required><?php echo $course['Course_Outline']; ?></textarea><br>

                <label for="course_price">Course Price:</label>
                <input type="number" name="course_price" min="0" step="any" value="<?php echo $course['Course_Price']; ?>" required><br>

                <label for="start_date">Start Date (if applicable):</label>
                <input type="date" name="start_date" value="<?php echo $course['Start_Date']; ?>"><br>

                <label for="end_date">End Date (if applicable):</label>
                <input type="date" name="end_date" value="<?php echo $course['End_Date']; ?>"><br>

                <input type="submit" name="update_course" value="Update Course" class="mb-4 btn btn-primary">
            </form>
        <?php else : ?>
            <p class="text-center">Invalid course ID. Please select a valid course to edit.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
