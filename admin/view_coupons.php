<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to fetch all coupons from the database
function fetchCouponsFromDatabase($conn) {
    $query = "SELECT * FROM Coupons";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to delete a coupon from the database
function deleteCouponFromDatabase($conn, $coupon_id) {
    $query = "DELETE FROM Coupons WHERE Coupon_ID = $coupon_id";
    mysqli_query($conn, $query);
}

// Fetch all coupons from the database
$coupons = fetchCouponsFromDatabase($conn);

// Check if the form is submitted for coupon deletion
if (isset($_POST['delete_coupon'])) {
    $coupon_id = $_POST['coupon_id'];
    deleteCouponFromDatabase($conn, $coupon_id);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Coupons</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        /* Add your custom CSS styling here */
        body {
            background-color: aquamarine;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        a {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }
        form {
            display: inline-block;
            margin: 0;
        }
        input[type="submit"] {
            padding: 5px 10px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include('admin_navbar.php') ?>

    <div class="container">
        <h2 class="text-center mt-4 mb-3">View Coupons</h2>
        <table>
            <tr>
            <th>Coupon ID</th>
                <th>Course ID</th>
                <th>Course Name</th>
                <th>Coupon Code</th>
                <th>Discount (%)</th>
                <th>Expiry Date</th>
                <th>Action</th>
            </tr>
            <?php foreach ($coupons as $coupon) : ?>
                <?php
                $course_id = $coupon['Course_ID'];
                $course_query = "SELECT Course_Title FROM Courses WHERE Course_ID = $course_id";
                $result = mysqli_query($conn, $course_query);
                $course = mysqli_fetch_assoc($result);
                ?>
                <tr>
                    <td><?php echo $course_id; ?></td>
                    <td><?php echo $coupon['Coupon_ID']; ?></td>
                    <td><?php echo $course['Course_Title']; ?></td>
                    <td><?php echo $coupon['Coupon_Code']; ?></td>
                    <td><?php echo $coupon['Discount']; ?></td>
                    <td><?php echo $coupon['Expiry_Date']; ?></td>
                    <td>
                        <a class="btn btn-success" href="edit_coupon.php?id=<?php echo $coupon['Coupon_ID']; ?>">Edit</a>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="hidden" name="coupon_id" value="<?php echo $coupon['Coupon_ID']; ?>">
                            <input type="submit" name="delete_coupon" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>