<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
} else {
    // Redirect to view_quizzes.php or show an error message if quiz_id is not provided.
    header("Location: view_quizzes.php");
    exit;
}

function fetchQuizFromDatabase($conn, $quiz_id) {
    $query = "SELECT Quizzes.*, Courses.Course_Title 
              FROM Quizzes 
              INNER JOIN Courses ON Quizzes.Course_ID = Courses.Course_ID
              WHERE Quiz_ID = $quiz_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Fetch the quiz to be edited from the database
$quiz = fetchQuizFromDatabase($conn, $quiz_id);

// Convert options from JSON to an array
$options = json_decode($quiz['Quiz_Options']);

if (isset($_POST['update'])) {
    // Handle quiz update logic here
    $updated_quiz_title = mysqli_real_escape_string($conn, $_POST['quiz_title']);
    $updated_quiz_question = mysqli_real_escape_string($conn, $_POST['quiz_question']);
    
    // Update options as an array
    $updated_options = [];
    $updated_options[] = mysqli_real_escape_string($conn, $_POST['quiz_option_1']);
    $updated_options[] = mysqli_real_escape_string($conn, $_POST['quiz_option_2']);
    $updated_options[] = mysqli_real_escape_string($conn, $_POST['quiz_option_3']);
    $updated_options[] = mysqli_real_escape_string($conn, $_POST['quiz_option_4']);

    // Encode updated options as JSON
    $updated_options_json = json_encode($updated_options);
    
    $updated_correct_answer = mysqli_real_escape_string($conn, $_POST['quiz_correct_answer']);

    $update_query = "UPDATE Quizzes 
                     SET Quiz_Title = '$updated_quiz_title', Quiz_Question = '$updated_quiz_question', 
                         Quiz_Options = '$updated_options_json', Quiz_Correct_Answer = '$updated_correct_answer'
                     WHERE Quiz_ID = $quiz_id";
    mysqli_query($conn, $update_query);

    // Redirect to view_quizzes.php or show a success message
    header("Location: view_quizzes.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>
    <!-- Add Bootstrap CSS and your CSS styling -->
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
        <h2 class="text-center mt-3 mb-3">Edit Quiz</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'] . "?quiz_id=$quiz_id"; ?>" method="post">
            <div class="form-group">
                <label for="quiz_title">Quiz Title:</label>
                <input type="text" name="quiz_title" class="form-control" value="<?php echo $quiz['Quiz_Title']; ?>">
            </div>

            <div class="form-group">
                <label for="quiz_question">Quiz Question:</label>
                <textarea name="quiz_question" class="form-control"><?php echo $quiz['Quiz_Question']; ?></textarea>
            </div>

            <div class="form-group">
                <label for="quiz_option_1">Option 1:</label>
                <input type="text" name="quiz_option_1" class="form-control" value="<?php echo $options[0]; ?>">
            </div>

            <div class="form-group">
                <label for="quiz_option_2">Option 2:</label>
                <input type="text" name "quiz_option_2" class="form-control" value="<?php echo $options[1]; ?>">
            </div>

            <div class="form-group">
                <label for="quiz_option_3">Option 3:</label>
                <input type="text" name="quiz_option_3" class="form-control" value="<?php echo $options[2]; ?>">
            </div>

            <div class="form-group">
                <label for="quiz_option_4">Option 4:</label>
                <input type="text" name="quiz_option_4" class="form-control" value="<?php echo $options[3]; ?>">
            </div>

            <div class="form-group">
                <label for="quiz_correct_answer">Correct Answer (option number):</label>
                <input type="number" name="quiz_correct_answer" class="form-control" value="<?php echo $quiz['Quiz_Correct_Answer']; ?>">
            </div>

            <button type="submit" name="update" class="btn btn-primary">Update Quiz</button>
        </form>
    </div>

    <!-- Include JavaScript and Bootstrap JS as needed -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
