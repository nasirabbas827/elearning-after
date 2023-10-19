<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

function fetchCoursesFromDatabase($conn) {
    $query = "SELECT * FROM Courses";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Fetch all courses from the database
$courses = fetchCoursesFromDatabase($conn);

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the course ID from the form
    $course_id = $_POST['course_id'];

    $quiz_titles = $_POST['quiz_title'];
    $quiz_questions = $_POST['quiz_question'];
    $quiz_options = $_POST['quiz_options'];
    $quiz_correct_answers = $_POST['quiz_correct_answers'];

    if (!empty($quiz_titles) && !empty($quiz_questions) && !empty($quiz_options) && !empty($quiz_correct_answers)) {
        for ($i = 0; $i < count($quiz_titles); $i++) {
            // Handle quiz insertion logic here
            $quiz_title = mysqli_real_escape_string($conn, $quiz_titles[$i]);
            $quiz_question = mysqli_real_escape_string($conn, $quiz_questions[$i]);
            $quiz_options_json = json_encode($quiz_options[$i]);
            $quiz_correct_answer = mysqli_real_escape_string($conn, $quiz_correct_answers[$i]);

            $quiz_query = "INSERT INTO Quizzes (Course_ID, Quiz_Title, Quiz_Question, Quiz_Options, Quiz_Correct_Answer)
                           VALUES ($course_id, '$quiz_title', '$quiz_question', '$quiz_options_json', '$quiz_correct_answer')";
            mysqli_query($conn, $quiz_query);
        }
    }

    echo "Quizzes added to the course successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Quizzes</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: aquamarine;

        }
        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include('admin_navbar.php') ?>

    <div class="container mb-4">
        <h2 class="text-center mt-3 mb-3">Add Quizzes</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="course_id">Select Course:</label>
                <select class="form-control" name="course_id" required>
                    <option value="" disabled selected>Select a course</option>
                    <?php foreach ($courses as $course) : ?>
                        <option value="<?php echo $course['Course_ID']; ?>"><?php echo $course['Course_Title']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <hr>

            <h3 class="mt-2 text-center">Quizzes</h3>
            <div id="quizzes-container">
                <div class="quiz-item">
                    <div class="form-group">
                        <label for="quiz_title">Quiz Title:</label>
                        <input class="form-control" type="text" name="quiz_title[]">
                    </div>

                    <div class="form-group">
                        <label for="quiz_question">Quiz Question:</label>
                        <textarea class="form-control" name="quiz_question[]"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Quiz Options:</label>
                        <input class="form-control" type="text" name="quiz_options[0][]"><br>
                        <input class="form-control" type="text" name="quiz_options[0][]"><br>
                        <input class="form-control" type="text" name="quiz_options[0][]"><br>
                        <input class="form-control" type="text" name="quiz_options[0][]"><br>
                    </div>

                    <div class="form-group">
                        <label for="quiz_correct_answer">Correct Answer (option number):</label>
                        <input class="form-control" type="number" name="quiz_correct_answers[]">
                    </div>
                </div>
            </div>

            <input type="submit" name="submit" value="Add Quizzes" class="btn btn-primary">
            <a href="view_quizzes.php" class="btn btn-secondary">View Quizzes</a>
        </form>
    </div>

    <!-- Include JavaScript and Bootstrap JS as needed -->
</body>
</html>
