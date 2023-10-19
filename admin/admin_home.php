<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get the count of total users
$total_users_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM users");
$total_users_data = mysqli_fetch_assoc($total_users_result);
$total_users = $total_users_data['count'];

// Get the count of total pending users
$total_pending_users_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM users WHERE status='pending'");
$total_pending_users_data = mysqli_fetch_assoc($total_pending_users_result);
$total_pending_users = $total_pending_users_data['count'];

// Get the count of total courses
$total_courses_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM courses");
$total_courses_data = mysqli_fetch_assoc($total_courses_result);
$total_courses = $total_courses_data['count'];

// Get the count of total enrollments
$total_enrollments_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM enrollments");
$total_enrollments_data = mysqli_fetch_assoc($total_enrollments_result);
$total_enrollments = $total_enrollments_data['count'];

// Get the count of total admins
$total_admins_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM admins");
$total_admins_data = mysqli_fetch_assoc($total_admins_result);
$total_admins = $total_admins_data['count'];

// Get the count of total coupons
$total_coupons_result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM coupons");
$total_coupons_data = mysqli_fetch_assoc($total_coupons_result);
$total_coupons = $total_coupons_data['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body{
            background-color: aquamarine;
        }
        .card-title {
            font-size: 18px;
        }

        .card-text {
            font-size: 24px;
            font-weight: bold;
        }

        .total-users-card {
            background-color: #4CAF50;
            color: #fff;
        }

        .total-pending-users-card {
            background-color: #FFC107;
            color: #fff;
        }

        .total-courses-card {
            background-color: #2196F3;
            color: #fff;
        }

        .total-enrollments-card {
            background-color: #E91E63;
            color: #fff;
        }

        .total-admins-card {
            background-color: #673AB7;
            color: #fff;
        }

        .total-coupons-card {
            background-color: #FF5722;
            color: #fff;
        }
    </style>
</head>
<body>
<?php include('admin_navbar.php') ?>

    <div class="container mt-5">
        <h2 class="text-center">Admin Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card total-users-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text"><?php echo $total_users; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card total-pending-users-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Pending Users</h5>
                        <p class="card-text"><?php echo $total_pending_users; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card total-courses-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Courses</h5>
                        <p class="card-text"><?php echo $total_courses; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card total-enrollments-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Enrollments</h5>
                        <p class="card-text"><?php echo $total_enrollments; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card total-admins-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Admins</h5>
                        <p class="card-text"><?php echo $total_admins; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card total-coupons-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Coupons</h5>
                        <p class="card-text"><?php echo $total_coupons; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

