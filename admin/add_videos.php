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

    // Insert videos into the database
    $video_titles = $_POST['video_title'];
    $video_files = $_FILES['video_file'];
    $video_durations = $_POST['video_duration'];

    if (!empty($video_titles) && !empty($video_files) && !empty($video_durations)) {
        for ($i = 0; $i < count($video_titles); $i++) {
            // Handle video insertion logic here
            $video_title = mysqli_real_escape_string($conn, $video_titles[$i]);
            $video_tmp_name = $video_files['tmp_name'][$i];
            $video_name = $video_files['name'][$i];
            $video_name = mysqli_real_escape_string($conn, $video_name);
            $video_destination = 'uploads/' . $video_name;
            $video_duration = mysqli_real_escape_string($conn, $video_durations[$i]);

            if (move_uploaded_file($video_tmp_name, $video_destination)) {
                // Include the Video_Duration in the database query
                $video_query = "INSERT INTO Videos (Course_ID, Video_Title, Video_URL, Duration)
                                VALUES ($course_id, '$video_title', '$video_destination', $video_duration)";
                mysqli_query($conn, $video_query);
            }
        }
    }

    echo "Videos added to the course successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Videos</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: aquamarine;

        }
        h2 {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        a {
            display: block;
            margin-top: 10px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include('admin_navbar.php') ?>

    <div class="container mb-4">
        <h2 class="text-center mt-3 mb-3">Add Videos</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
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

            <h3>Videos</h3>
            <div id="videos-container">
                <div class="video-item">
                    <div class="form-group">
                        <label for="video_title">Video Title:</label>
                        <input class="form-control" type="text" name="video_title[]">
                    </div>

                    <div class="form-group">
                        <label for="video_file">Upload Video:</label>
                        <input class="form-control" type="file" name="video_file[]" accept="video/*">
                    </div>

                    <div class="form-group">
                        <label for="video_duration">Duration (in minutes):</label>
                        <input class="form-control" type="number" name="video_duration[]">
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" value="Add Videos" class="btn btn-primary">
            <a href="view_videos.php" class="btn btn-secondary">View Videos</a>
        </form>
    </div>

    <!-- Include JavaScript and Bootstrap JS as needed -->
</body>
</html>
