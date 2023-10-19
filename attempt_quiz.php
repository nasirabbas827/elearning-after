<?php
session_start();
include 'config.php';

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch all quizzes for the course
    $quizzes_query = "SELECT * FROM Quizzes WHERE Course_ID = $course_id";
    $result = mysqli_query($conn, $quizzes_query);
    $quizzes = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// Check if the "retake" parameter is set
if (isset($_GET['retake']) && $_GET['retake'] === '1') {
    // Delete the previous quiz attempts for the specific user and course
    $delete_attempts_query = "DELETE FROM Quiz_Attempts WHERE User_ID = $user_id AND Quiz_ID IN (SELECT Quiz_ID FROM Quizzes WHERE Course_ID = $course_id)";
    mysqli_query($conn, $delete_attempts_query);
    
    // Redirect the user back to the quiz page
    header("Location: attempt_quiz.php?course_id=$course_id");
    exit;
}

// Handle quiz submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['quiz_id']) && isset($_POST['option'])) {
        $quiz_id = $_POST['quiz_id'];
        $selected_option = intval($_POST['option']); // Convert the selected option to an integer

        // Fetch quiz details
        $quiz_query = "SELECT * FROM Quizzes WHERE Quiz_ID = $quiz_id";
        $result = mysqli_query($conn, $quiz_query);
        $quiz = mysqli_fetch_assoc($result);

        if ($quiz) {
            // Check if the selected option is correct
            $correct_answer = intval($quiz['Quiz_Correct_Answer']); // Convert the correct answer to an integer
            $is_correct = ($selected_option === $correct_answer);

            // Insert the quiz attempt into the "Quiz_Attempts" table
            $insert_attempt_query = "INSERT INTO Quiz_Attempts (User_ID, Quiz_ID, Selected_Option, Is_Correct, Attempt_Date) VALUES (?, ?, ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $insert_attempt_query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiii", $user_id, $quiz_id, $selected_option, $is_correct);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Calculate total quizzes count
$total_quizzes = count($quizzes);

// Calculate total quiz attempts and correct attempts for the user
if (isset($quizzes)) {
    $total_attempts = 0;
    $correct_attempts = 0;

    foreach ($quizzes as $quiz) {
        $quiz_id = $quiz['Quiz_ID'];
        $quiz_attempts_query = "SELECT * FROM Quiz_Attempts WHERE User_ID = $user_id AND Quiz_ID = $quiz_id";
        $result = mysqli_query($conn, $quiz_attempts_query);
        $total_attempts += mysqli_num_rows($result);

        $correct_attempts_query = "SELECT * FROM Quiz_Attempts WHERE User_ID = $user_id AND Quiz_ID = $quiz_id AND Is_Correct = 1";
        $result = mysqli_query($conn, $correct_attempts_query);
        $correct_attempts += mysqli_num_rows($result);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attempt All Quizzes</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom CSS styles */
        body {
            background-color: aquamarine;
        }
        .quiz-card {
            margin-bottom: 20px;
            padding: 10px;
            border: 2px solid black;
        }
        .quiz-card h4 {
            margin-bottom: 10px;
        }
        .quiz-card label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <h2 class="text-center mt-4 mb-4">Attempt All Quizzes</h2>
        <?php if (isset($quizzes)) { ?>
            <?php foreach ($quizzes as $quiz) { ?>
                <div class="quiz-card">
                    <h4 class="text-center mt-4 mb-4">Quiz Title: <?php echo $quiz['Quiz_Title']; ?></h4>
                    <p class="text-center mt-4 mb-4">Quiz Question: <?php echo $quiz['Quiz_Question']; ?></p>
                    <form action="attempt_quiz.php?course_id=<?php echo $course_id; ?>" method="post">
                        <input type="hidden" name="quiz_id" value="<?php echo $quiz['Quiz_ID']; ?>">
                        <?php
                        // Convert the quiz options from JSON to an array
                        $quiz_options_json = $quiz['Quiz_Options'];
                        $quiz_options = json_decode($quiz_options_json, true);

                        // Display the quiz options as radio buttons
                        foreach ($quiz_options as $key => $option) {
                            echo '<label>';
                            echo '<input type="radio" name="option" value="' . ($key + 1) . '">';
                            echo $option;
                            echo '</label>';
                        }
                        ?>
                        <button type="submit" class="btn btn-primary mt-2">Submit Answer</button>
                    </form>
                    <?php
                    // Display the result message if available and if the quiz has been attempted
                    if (isset($_POST['quiz_id']) && $_POST['quiz_id'] == $quiz['Quiz_ID']) {
                        $result_message = isset($is_correct) ? ($is_correct ? "Correct! Your answer is right." : "Incorrect! Your answer is wrong.") : "";
                        echo "<p>{$result_message}</p>";
                    }
                    ?>
                </div>
            <?php } ?>

            <?php if (isset($total_attempts) && $total_attempts > 0) { ?>
                <div class="quiz-card">
                    <h3 class="text-center mt-4 mb-4">Overall Result</h3>
                    <p class="text-center mt-4 mb-4">Total Quiz Attempts: <?php echo $total_attempts; ?></p>
                    <p class="text-center mt-4 mb-4">Correct Attempts: <?php echo $correct_attempts; ?></p>
                    <p class="text-center mt-4 mb-4">Percentage: <?php echo round(($correct_attempts / $total_attempts) * 100, 2); ?>%</p>

                    <?php if (($correct_attempts / $total_attempts) < 0.75) { ?>
                        <p>Your score is less than 75% in one or more quizzes. You can retake the quizzes to improve your score.</p>
                        <a class="btn btn-primary" href="attempt_quiz.php?course_id=<?php echo $course_id; ?>&retake=1">Retake Quizzes</a>
                    <?php } else if ($total_attempts === $total_quizzes) { ?>
                        <?php if (isset($course_id)) { ?>
                            <p>Congratulations! You have completed all quizzes with a score greater than 75%. You can now download the course certificate.</p>
                            <a class="mt-3 mb-4 float-right btn btn-success" href="download_certificate.php?course_id=<?php echo $course_id; ?>">Download Course Certificate</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No quizzes found for this course.</p>
        <?php } ?>
    </div>

    <!-- Add Bootstrap JS and jQuery links (at the end of the body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
