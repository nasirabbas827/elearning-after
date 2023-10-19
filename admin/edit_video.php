<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the video_id is provided in the URL
if (!isset($_GET['video_id'])) {
    header("Location: view_videos.php");
    exit;
}

$video_id = $_GET['video_id'];

// Fetch video details from the database
$query = "SELECT V.Video_ID, V.Video_Title, V.Video_URL, V.Duration, C.Course_Title FROM Videos V
          INNER JOIN Courses C ON V.Course_ID = C.Course_ID
          WHERE V.Video_ID = $video_id";
$result = mysqli_query($conn, $query);
$video = mysqli_fetch_assoc($result);

if (!$video) {
    // Video with the provided ID does not exist
    header("Location: view_videos.php");
    exit;
}

// Check if the form is submitted to update the video
if (isset($_POST['update_video'])) {
    $video_title = mysqli_real_escape_string($conn, $_POST['video_title']);
    $video_url = mysqli_real_escape_string($conn, $_POST['video_url']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);

    $update_query = "UPDATE Videos SET Video_Title = '$video_title', Video_URL = '$video_url', Duration = '$duration'
                    WHERE Video_ID = $video_id";
    mysqli_query($conn, $update_query);
    header("Location: view_videos.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Video</title>
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
        <h2 class="text-center mt-3 mb-3">Edit Video</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?video_id=' . $video_id; ?>" method="post">
            <div class="form-group">
                <label for="video_title">Video Title:</label>
                <input type="text" name="video_title" class="form-control" value="<?php echo $video['Video_Title']; ?>">
            </div>
            <div class="form-group">
                <label for="video_url">Video URL:</label>
                <input type="text" name="video_url" class="form-control" value="<?php echo $video['Video_URL']; ?>">
            </div>
            <div class="form-group">
                <label for="duration">Duration (in minutes):</label>
                <input type="number" name="duration" class="form-control" value="<?php echo $video['Duration']; ?>">
            </div>
            <button type="submit" name="update_video" class="btn btn-primary">Update Video</button>
            <a href="view_videos.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <!-- Include JavaScript and Bootstrap JS as needed -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

