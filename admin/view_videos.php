<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the form is submitted to delete a video
if (isset($_POST['delete_video'])) {
    $video_id = $_POST['video_id'];
    $delete_query = "DELETE FROM Videos WHERE Video_ID = $video_id";
    mysqli_query($conn, $delete_query);
}

// Fetch videos from the database
$query = "SELECT V.Video_ID, V.Video_Title, V.Video_URL, V.Duration, C.Course_Title FROM Videos V
          INNER JOIN Courses C ON V.Course_ID = C.Course_ID";
$result = mysqli_query($conn, $query);
$videos = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Videos</title>
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
        <h2 class="text-center mt-3 mb-3">View Videos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Video Title</th>
                    <th>Course Title</th>
                    <th>Duration</th>
                    <th>Video URL</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($videos as $video) : ?>
                    <tr>
                        <td><?php echo $video['Video_Title']; ?></td>
                        <td><?php echo $video['Course_Title']; ?></td>
                        <td><?php echo $video['Duration']; ?></td>
                        <td><?php echo $video['Video_URL']; ?></td>
                        <td>
                            <!-- Add edit link here to update the video -->
                            <a href="edit_video.php?video_id=<?php echo $video['Video_ID']; ?>" class="btn btn-primary">Edit</a>
                        </td>
                        <td>
                            <!-- Form to delete a video -->
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this video?');">
                                <input type="hidden" name="video_id" value="<?php echo $video['Video_ID']; ?>">
                                <button type="submit" name="delete_video" class="btn btn-danger">Delete</button>
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

