<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit;
}

$email = $_SESSION["email"];
$user_query = "SELECT id FROM Users WHERE Email = '$email'";
$result = mysqli_query($conn, $user_query);
if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION["user_id"] = $user["id"];
}

// Set the user's balance to 10000 if it is not already set
$user_id = $_SESSION["user_id"];
$user_balance_query = "SELECT Balance FROM Users WHERE id = $user_id";
$result = mysqli_query($conn, $user_balance_query);
$user_balance = mysqli_fetch_assoc($result)['Balance'];

if (!$user_balance) {
    $initial_balance = 10000;
    $update_balance_query = "UPDATE Users SET Balance = $initial_balance WHERE id = $user_id";
    if (mysqli_query($conn, $update_balance_query)) {
        $user_balance = $initial_balance;
    } else {
        echo "Error setting initial balance: " . mysqli_error($conn);
        exit;
    }
}

// Function to enroll the user in a course
function enrollCourse($conn, $user_id, $course_id, $discounted_course_price = 0) {
    // Check if the course ID and user ID are valid
    $course_query = "SELECT Course_Price FROM Courses WHERE Course_ID = $course_id";
    $result = mysqli_query($conn, $course_query);
    $course = mysqli_fetch_assoc($result);

    $user_query = "SELECT id FROM Users WHERE id = $user_id";
    $result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($result);

    if (!$course || !$user) {
        echo "Invalid course ID or user ID.";
        return;
    }

    // Check if the course is free or apply the discounted price
    if ($discounted_course_price === 0) {
        $course_price = $course['Course_Price'];
    } else {
        $course_price = $discounted_course_price;
    }

    // Check if the user has enough balance to enroll in the course
    global $user_balance;
    if ($user_balance >= $course_price) {
        // Deduct the course price from the user's balance and enroll the user in the course
        $new_balance = $user_balance - $course_price;

        $enroll_query = "INSERT INTO Enrollments (User_ID, Course_ID) VALUES ($user_id, $course_id)";
        if (mysqli_query($conn, $enroll_query)) {
            $user_balance = $new_balance; // Update the user's balance after successful enrollment

            // Insert the course attempt into the Attempts table
            $attempt_date = date("Y-m-d"); // Get the current date
            $insert_attempt_query = "INSERT INTO Attempts (User_ID, Course_ID, Attempt_Date) VALUES ($user_id, $course_id, '$attempt_date')";
            if (!mysqli_query($conn, $insert_attempt_query)) {
                echo "Error adding course attempt: " . mysqli_error($conn);
            }

            echo "You have successfully enrolled in the course.";
        } else {
            echo "Error enrolling in the course: " . mysqli_error($conn);
        }
    } else {
        echo "Insufficient balance. Please add funds to your account.";
    }
}

// Function to fetch free or paid courses from the database based on the filter
function fetchCoursesFromDatabase($conn, $filter) {
    if ($filter === 'free') {
        $query = "SELECT * FROM Courses WHERE Course_Price = 0";
    } elseif ($filter === 'paid') {
        $query = "SELECT * FROM Courses WHERE Course_Price > 0";
    } else {
        $query = "SELECT * FROM Courses";
    }
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to check if the user is already enrolled in a course
function isEnrolled($conn, $user_id, $course_id) {
    $enrollment_query = "SELECT * FROM Enrollments WHERE User_ID = $user_id AND Course_ID = $course_id";
    $result = mysqli_query($conn, $enrollment_query);
    return mysqli_num_rows($result) > 0;
}

// Fetch all courses from the database based on the filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$courses = fetchCoursesFromDatabase($conn, $filter);

// Handle the enrollment process and applying coupons
if (isset($_POST['enroll'])) {
    $course_id = $_POST['course_id'];
    $user_id = $_SESSION["user_id"];

    if (isEnrolled($conn, $user_id, $course_id)) {
        echo "You are already enrolled in this course.";
    } else {
        // Check if the user applied a coupon
        $coupon_code = $_POST['coupon_code'];
        $discounted_course_price = 0;

        if (!empty($coupon_code)) {
            // Fetch coupon details from the database
            $coupon_query = "SELECT Discount FROM coupons WHERE Coupon_Code = '$coupon_code' AND Course_ID = $course_id AND Expiry_Date >= CURDATE()";
            $result = mysqli_query($conn, $coupon_query);
            $coupon = mysqli_fetch_assoc($result);

            if ($coupon) {
                $discount_percentage = $coupon['Discount'];
                // Fetch the original course price
                $course_query = "SELECT Course_Price FROM Courses WHERE Course_ID = $course_id";
                $result = mysqli_query($conn, $course_query);
                $course = mysqli_fetch_assoc($result);
                $course_price = $course['Course_Price'];

                // Calculate the discounted course price
                $discounted_course_price = $course_price - ($course_price * $discount_percentage / 100);
            } else {
                echo "Invalid coupon or coupon has expired.";
                exit; // Stop the enrollment process if the coupon is invalid or expired.
            }
        }

        // Enroll the user in the course with the appropriate course price (original price or discounted price)
        enrollCourse($conn, $user_id, $course_id, $discounted_course_price);
    }
}

// Handle the cancel enrollment process
if (isset($_POST['cancel_enroll'])) {
    $course_id = $_POST['course_id'];
    $user_id = $_SESSION["user_id"];

    if (isEnrolled($conn, $user_id, $course_id)) {
        // Remove the enrollment record from the database
        $cancel_enroll_query = "DELETE FROM Enrollments WHERE User_ID = $user_id AND Course_ID = $course_id";
        if (mysqli_query($conn, $cancel_enroll_query)) {
            echo "You have successfully canceled your enrollment in the course.";
        } else {
            echo "Error canceling enrollment: " . mysqli_error($conn);
        }
    } else {
        echo "You are not currently enrolled in this course.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Courses</title>
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
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container">
        <h2 class="mb-4 mt-4 text-center">View Courses</h2>
        <div>
            <p>Your Current Balance: <?php echo '$' . $user_balance; ?></p>
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
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <input type="hidden" name="course_id" value="<?php echo $course['Course_ID']; ?>">
                                <input class="form-control mb-2" type="text" name="coupon_code" placeholder="Enter coupon code">
                                <?php if (isEnrolled($conn, $_SESSION["user_id"], $course['Course_ID'])) : ?>
                                    <!-- User is enrolled, show Details and Cancel Enrollment button -->
                                    <a href="course_details.php?id=<?php echo $course['Course_ID']; ?>" class="btn btn-info">Details</a>
                                    <button type="submit" name="cancel_enroll" class="btn btn-danger">Cancel Enrollment</button>
                                <?php else : ?>
                                    <!-- User is not enrolled, show the Enroll button -->
                                    <input type="submit" name="enroll" class="btn btn-primary" value="Enroll">
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Bootstrap JS and jQuery links (at the end of the body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
