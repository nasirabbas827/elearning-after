<?php
session_start();
include 'config.php';

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

// Function to fetch enrolled course details including videos and quizzes
function fetchEnrolledCourseDetails($conn, $user_id, $course_id) {
    $course_query = "SELECT * FROM Courses WHERE Course_ID = $course_id";
    $result = mysqli_query($conn, $course_query);
    $course = mysqli_fetch_assoc($result);

    if ($course) {
        $video_query = "SELECT * FROM Videos WHERE Course_ID = $course_id";
        $result = mysqli_query($conn, $video_query);
        $videos = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $quiz_query = "SELECT * FROM Quizzes WHERE Course_ID = $course_id";
        $result = mysqli_query($conn, $quiz_query);
        $quizzes = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return array(
            'course' => $course,
            'videos' => $videos,
            'quizzes' => $quizzes
        );
    }

    return null;
}

// Handle marking video as completed
if (isset($_POST['mark_completed'])) {
    $video_id = $_POST['video_id'];
    $user_id = $_SESSION["user_id"];
    
    // Get video duration
    $video_duration_query = "SELECT Duration FROM Videos WHERE Video_ID = $video_id";
    $result = mysqli_query($conn, $video_duration_query);
    $video_duration = mysqli_fetch_assoc($result)['duration'];

    // Get the total watch time of the video for the user
    $total_watch_time_query = "SELECT IFNULL(SUM(Watch_Time), 0) AS total_watch_time FROM User_Video_Completion WHERE User_ID = $user_id AND Video_ID = $video_id";
    $result = mysqli_query($conn, $total_watch_time_query);
    $total_watch_time = mysqli_fetch_assoc($result)['total_watch_time'];

    // Check if the user has completed watching the video
    if ($total_watch_time >= 0.8 * $video_duration) {
        // Check if the user already has a completion record for this video
        $existing_completion_query = "SELECT * FROM User_Video_Completion WHERE User_ID = $user_id AND Video_ID = $video_id";
        $result = mysqli_query($conn, $existing_completion_query);
        
        if (mysqli_num_rows($result) > 0) {
            // Update the existing completion record
            $update_completion_query = "UPDATE User_Video_Completion SET Completed = 1 WHERE User_ID = $user_id AND Video_ID = $video_id";
            mysqli_query($conn, $update_completion_query);
        } else {
            // Insert a new completion record
            $insert_completion_query = "INSERT INTO User_Video_Completion (User_ID, Video_ID, Watch_Time, Completed) VALUES ($user_id, $video_id, $total_watch_time, 1)";
            mysqli_query($conn, $insert_completion_query);
        }
    }
}

// Function to get the total watch time of a user for a video
function getVideoWatchTime($conn, $user_id, $video_id) {
    $total_watch_time_query = "SELECT IFNULL(SUM(Watch_Time), 0) AS total_watch_time FROM User_Video_Completion WHERE User_ID = $user_id AND Video_ID = $video_id";
    $result = mysqli_query($conn, $total_watch_time_query);
    $total_watch_time = mysqli_fetch_assoc($result)['total_watch_time'];
    return intval($total_watch_time);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Enrolled Course Details</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: aquamarine;

        }
        .video-card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <h2>Enrolled Course Details</h2>
        <?php
        if (isset($_GET['course_id'])) {
            $user_id = $_SESSION["user_id"];
            $course_id = $_GET['course_id'];
            $enrolled_course = fetchEnrolledCourseDetails($conn, $user_id, $course_id);

            if ($enrolled_course) {
                $course = $enrolled_course['course'];
                $videos = $enrolled_course['videos'];
                $quizzes = $enrolled_course['quizzes'];
        ?>
                <h3>Course Title: <?php echo $course['Course_Title']; ?></h3>
                <h4>Course Instructor: <?php echo $course['Course_Instructor']; ?></h4>
                <p>Course Content: <?php echo $course['Course_Content']; ?></p>
                <p>Course Outline: <?php echo $course['Course_Outline']; ?></p>

                <h3>Videos</h3>
                <?php foreach ($videos as $video) : ?>
                    <div class="card video-card">
                        <div class="card-body">
                            <h4 class="card-title">Video Title: <?php echo $video['Video_Title']; ?></h4>
                            <div>
                                <!-- Display the video using the uploaded video URL -->
                                <video width="320" height="240" controls>
                                    <source src="./admin/<?php echo $video['Video_URL']; ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <?php
                            // Check if the user has watched at least 80% of the video
                            $video_id = $video['Video_ID'];
                            $video_completion_query = "SELECT * FROM User_Video_Completion WHERE User_ID = $user_id AND Video_ID = $video_id";
                            $result = mysqli_query($conn, $video_completion_query);
                            $video_completion_status = mysqli_fetch_assoc($result)['Completed'];

                            if ($video_completion_status) {
                                echo "<p>Completed</p>";
                            } else {
                            ?>
                                <form action="<?php echo $_SERVER['PHP_SELF'] . "?course_id=$course_id"; ?>" method="post">
                                    <input type="hidden" name="video_id" value="<?php echo $video_id; ?>">
                                    <button type="submit" name="mark_completed" class="btn btn-primary">Mark as Completed</button>
                                </form>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php
                // Check if all videos are marked as completed
                $all_videos_completed = true;
                foreach ($videos as $video) {
                    $video_id = $video['Video_ID'];
                    $video_completion_query = "SELECT * FROM User_Video_Completion WHERE User_ID = $user_id AND Video_ID = $video_id";
                    $result = mysqli_query($conn, $video_completion_query);
                    $video_completion_status = mysqli_fetch_assoc($result)['Completed'];

                    if (!$video_completion_status) {
                        $all_videos_completed = false;
                        break;
                    }
                }

                if ($all_videos_completed) {
                    // Display a link to the quiz attempt page for each quiz
                    echo "<h3 class='mt-5 text-center'>Quizzes</h3>";      
                    echo "<p><a class='text-center mb-5 btn btn-primary' href='attempt_quiz.php?course_id=$course_id'>Attempt All Quizzes</a></p>";
                }

                // Check if the user has completed all quizzes
                $completed_all_quizzes_query = "SELECT COUNT(*) AS num_quizzes FROM Quizzes WHERE Course_ID = $course_id";
                $result = mysqli_query($conn, $completed_all_quizzes_query);
                $num_quizzes = mysqli_fetch_assoc($result)['num_quizzes'];

                $completed_quizzes_query = "SELECT COUNT(*) AS num_completed FROM Quiz_Attempts WHERE User_ID = $user_id AND Quiz_ID IN (SELECT Quiz_ID FROM Quizzes WHERE Course_ID = $course_id) AND Is_Correct = 1";
                $result = mysqli_query($conn, $completed_quizzes_query);
                $num_completed_quizzes = mysqli_fetch_assoc($result)['num_completed'];

            }
        }
        ?>
    </div>

    <!-- Add Bootstrap JS and jQuery links (at the end of the body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
