<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

function fetchQuizzesFromDatabase($conn) {
    $query = "SELECT Quizzes.*, Courses.Course_Title 
              FROM Quizzes 
              INNER JOIN Courses ON Quizzes.Course_ID = Courses.Course_ID";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Fetch all quizzes from the database
$quizzes = fetchQuizzesFromDatabase($conn);

if (isset($_POST['delete'])) {
    $quiz_id = $_POST['quiz_id'];
    // Add code here to delete the selected quiz
    $delete_query = "DELETE FROM Quizzes WHERE Quiz_ID = $quiz_id";
    mysqli_query($conn, $delete_query);
    header("Location: view_quizzes.php");
}

?>
 
 <!DOCTYPE html>
<html>
<head>
    <title>View Quizzes</title>
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
        <h2 class="text-center mt-3 mb-3">View Quizzes</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Quiz ID</th>
                    <th>Course Name</th>
                    <th>Quiz Title</th>
                    <th>Quiz Question</th>
                    <th>Options</th>
                    <th>Correct Answer</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz) : ?>
                    <tr>
                        <td><?php echo $quiz['Quiz_ID']; ?></td>
                        <td><?php echo $quiz['Course_Title']; ?></td>
                        <td><?php echo $quiz['Quiz_Title']; ?></td>
                        <td><?php echo $quiz['Quiz_Question']; ?></td>
                        <td><?php 
                            // Parse and display options
                            $options = json_decode($quiz['Quiz_Options']);
                            foreach ($options as $key => $option) {
                                echo $key + 1 . ". " . $option . "<br>";
                            }
                        ?></td>
                        <td><?php echo $quiz['Quiz_Correct_Answer']; ?></td>
                        <td><a href="edit_quiz.php?quiz_id=<?php echo $quiz['Quiz_ID']; ?>" class="btn btn-primary">Edit</a></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="quiz_id" value="<?php echo $quiz['Quiz_ID']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this quiz?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Include JavaScript and Bootstrap JS as needed -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
