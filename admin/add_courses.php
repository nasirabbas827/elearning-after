<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get data from the form
    $course_title = $_POST['course_title'];
    $course_instructor = $_POST['course_instructor'];
    $course_content = $_POST['course_content'];
    $course_outline = $_POST['course_outline'];
    $course_price = $_POST['course_price'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Prepare the SQL statement using a prepared statement
    $query = "INSERT INTO Courses (Course_Title, Course_Instructor, Course_Content, Course_Outline, Course_Price, Start_Date, End_Date)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Create a prepared statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters to the prepared statement
    mysqli_stmt_bind_param($stmt, "ssssdss", $course_title, $course_instructor, $course_content, $course_outline, $course_price, $start_date, $end_date);

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        echo "Course added successfully!";
    } else {
        echo "Error adding course: " . mysqli_stmt_error($stmt);
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
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
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include('admin_navbar.php') ?>

    <div class="container">
        <h2 class="text-center mt-4 mb-3">Add Course</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="course_title">Course Title:</label>
                <input type="text" name="course_title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="course_instructor">Course Instructor:</label>
                <input type="text" name="course_instructor" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="course_content">Course Content:</label>
                <textarea name="course_content" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="course_outline">Course Outline:</label>
                <textarea name="course_outline" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="course_price">Course Price:</label>
                <input type="number" name="course_price" class="form-control" min="0" step="any" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date (if applicable):</label>
                <input type="date" name="start_date" class="form-control">
            </div>

            <div class="form-group">
                <label for="end_date">End Date (if applicable):</label>
                <input type="date" name="end_date" class="form-control">
            </div>

            <div class="form-group text-center">
                <input type="submit" name="submit" value="Add Course" class="btn btn-primary">
            </div>

            <div class="form-group text-center">
                <a class="btn btn-success" href="view_courses.php">View Courses</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
