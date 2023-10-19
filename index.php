<?php
session_start();
include('config.php');

// Check if the user is already logged in
if (isset($_SESSION['email'])) {
  // User is logged in, redirect to another page
  header("Location: home.php"); 
  exit;
}

// Function to fetch free or paid courses from the database based on the filter
function fetchCoursesFromDatabase($conn, $filter) {
    if ($filter === 'free') {
        $query = "SELECT * FROM Courses WHERE Course_Price = 0";
    } else if ($filter === 'paid') {
        $query = "SELECT * FROM Courses WHERE Course_Price > 0";
    } else {
        $query = "SELECT * FROM Courses";
    }
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Fetch all courses from the database based on the filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$courses = fetchCoursesFromDatabase($conn, $filter);


// Query to get the total number of users
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$resultTotalUsers = $conn->query($totalUsersQuery);

// Query to get the total number of active users (you may need to adjust the condition)
$activeUsersQuery = "SELECT COUNT(*) AS active_users FROM users WHERE status = 'approved'";
$resultActiveUsers = $conn->query($activeUsersQuery);

// Query to get the total number of courses
$totalCoursesQuery = "SELECT COUNT(*) AS total_courses FROM courses";
$resultTotalCourses = $conn->query($totalCoursesQuery);

// Check for query errors (add error handling as needed)
if (!$resultTotalUsers || !$resultActiveUsers || !$resultTotalCourses) {
    echo "Error: " . $conn->error;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom CSS styles */
        body {
            background-color: aquamarine;

        }
        .course-card {
            margin-bottom: 20px;
        }
        /* Style for the carousel */
        .carousel-item {
            height: 400px; 
            position: relative;
        }
        .carousel-caption {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div id="carouselExample" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExample" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExample" data-slide-to="1"></li>
            <li data-target="#carouselExample" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./images/Pic1.Png" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption">
                    <h3>Welcome to E-Learning Platform</h3>
                    <p>Explore a wide range of courses for your learning needs.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./images/Pic2.jpg" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption">
                    <h3>Learn Anytime, Anywhere</h3>
                    <p>Access courses from the comfort of your home or on the go.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./images/Pic3.jpg" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption">
                    <h3>Expert Instructors</h3>
                    <p>Learn from industry experts with hands-on experience.</p>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <div class="container">
        <h2 class="mb-4 mt-4 text-center">Available Courses</h2>
        <div>
            <a class="btn btn-sm btn-primary mr-2" href="<?php echo $_SERVER['PHP_SELF']; ?>">All</a>
            <a class="btn btn-sm btn-primary mr-2" href="<?php echo $_SERVER['PHP_SELF']; ?>?filter=free">Free</a>
            <a class="btn btn-sm btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?filter=paid">Paid</a>
        </div>

        <div class="row mt-4">
            <?php foreach ($courses as $course) : ?>
                <div class="col-md-4">
                    <div class="card course-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $course['Course_Title']; ?></h5>
                            <p class="card-text">Instructor: <?php echo $course['Course_Instructor']; ?></p>
                            <p class="card-text">Price: <?php echo '$' . $course['Course_Price']; ?></p>
                            <a href="course_details.php?id=<?php echo $course['Course_ID']; ?>" class="btn btn-info">Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <section>
    <h2 class="text-center">Statistics</h2>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <?php
                        if ($resultTotalUsers) {
                            $rowTotalUsers = $resultTotalUsers->fetch_assoc();
                            echo "<h5 class='card-title'>Total Users</h5>";
                            echo "<p class='card-text'>" . $rowTotalUsers["total_users"] . "</p>";
                        } else {
                            echo "Error fetching data.";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <?php
                        if ($resultActiveUsers) {
                            $rowActiveUsers = $resultActiveUsers->fetch_assoc();
                            echo "<h5 class='card-title'>Active Users</h5>";
                            echo "<p class='card-text'>" . $rowActiveUsers["active_users"] . "</p>";
                        } else {
                            echo "Error fetching data.";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <?php
                        if ($resultTotalCourses) {
                            $rowTotalCourses = $resultTotalCourses->fetch_assoc();
                            echo "<h5 class='card-title'>Total Courses</h5>";
                            echo "<p class='card-text'>" . $rowTotalCourses["total_courses"] . "</p>";
                        } else {
                            echo "Error fetching data.";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<section class="container mt-5">
  <h2 class="text-center">Contact Us</h2>
  <p class="text-center">If you have any questions or queries, please feel free to contact us by filling out the form below.</p>

  <form action="#" method="POST">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="subject">Subject:</label>
          <input type="text" class="form-control" id="subject" name="subject" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="message">Message:</label>
          <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </form>
</section>


<footer class="mt-5 py-3 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <!-- Column 1 Content -->
                <h5>About Us</h5>
                <p>Learn more about our e-learning platform and mission.</p>
                <a href="#">Read More</a>
            </div>
            <div class="col-md-3">
                <!-- Column 2 Content -->
                <h5>Courses</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Browse Courses</a></li>
                    <li><a href="#">Featured Courses</a></li>
                    <li><a href="#">Course Categories</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <!-- Column 3 Content -->
                <h5>Contact Us</h5>
                <p>If you have any questions or feedback, get in touch.</p>
                <a href="#">Contact Form</a>
            </div>
            <div class="col-md-3">
                <!-- Column 4 Content (optional) -->
                <h5>Terms &amp; Privacy</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Terms of Use</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <p>&copy; 2023 E-Learning. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

    <!-- Add Bootstrap JS and jQuery links (at the end of the body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
