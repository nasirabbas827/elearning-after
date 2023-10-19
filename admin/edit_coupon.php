<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the form is submitted for coupon update
if (isset($_POST['update_coupon'])) {
    $coupon_id = $_POST['coupon_id'];
    $course_id = $_POST['course_id'];
    $coupon_code = $_POST['coupon_code'];
    $discount = $_POST['discount'];
    $expiry_date = $_POST['expiry_date'];

    // Validate data (you should perform more robust validation)
    if (empty($coupon_id) || empty($course_id) || empty($coupon_code) || empty($discount) || empty($expiry_date)) {
        echo "Please fill in all the fields.";
    } else {
        // Check if the course exists and has a price greater than zero
        $course_query = "SELECT Course_Price FROM Courses WHERE Course_ID = $course_id";
        $result = mysqli_query($conn, $course_query);
        $course = mysqli_fetch_assoc($result);

        if (!$course || $course['Course_Price'] <= 0) {
            echo "Invalid course ID or course is free. Coupons can only be added to paid courses.";
        } else {
            // Check if the coupon code is unique
            $coupon_check_query = "SELECT Coupon_ID FROM Coupons WHERE Coupon_Code = '$coupon_code' AND Coupon_ID != $coupon_id LIMIT 1";
            $result = mysqli_query($conn, $coupon_check_query);

            if (mysqli_num_rows($result) > 0) {
                echo "Coupon code already exists. Please choose a different code.";
            } else {
                // Update the coupon data in the database
                $query = "UPDATE Coupons 
                          SET Course_ID = $course_id, Coupon_Code = '$coupon_code', 
                              Discount = $discount, Expiry_Date = '$expiry_date' 
                          WHERE Coupon_ID = $coupon_id";

                if (mysqli_query($conn, $query)) {
                    echo "Coupon updated successfully!";
                    header("Location: view_coupons.php");
                    exit(); 
                } else {
                    echo "Error updating coupon: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Fetch coupon details based on the coupon ID from the query parameter
if (isset($_GET['id'])) {
    $coupon_id = $_GET['id'];
    $query = "SELECT * FROM Coupons WHERE Coupon_ID = $coupon_id";
    $result = mysqli_query($conn, $query);
    $coupon = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Coupon</title>
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
        form {
            max-width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
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
        <h2 class="text-center mt-4 mb-4">Edit Coupon</h2>
        <?php if (isset($coupon)) : ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="coupon_id" value="<?php echo $coupon['Coupon_ID']; ?>">

            <label for="course_id">Select Course:</label>
            <select name="course_id" required>
                <option value="" disabled>Select a course</option>
                <?php
                $course_query = "SELECT Course_ID, Course_Title FROM Courses WHERE Course_Price > 0";
                $result = mysqli_query($conn, $course_query);
                while ($course = mysqli_fetch_assoc($result)) {
                    $selected = ($course['Course_ID'] == $coupon['Course_ID']) ? 'selected' : '';
                    echo '<option value="' . $course['Course_ID'] . '" ' . $selected . '>' . $course['Course_Title'] . '</option>';
                }
                ?>
            </select>
            <br>

            <label for="coupon_code">Coupon Code:</label>
            <input type="text" name="coupon_code" value="<?php echo $coupon['Coupon_Code']; ?>" required><br>

            <label for="discount">Discount (%):</label>
            <input type="number" name="discount" min="0" max="100" value="<?php echo $coupon['Discount']; ?>" required><br>

            <label for="expiry_date">Expiry Date:</label>
            <input type="date" name="expiry_date" value="<?php echo $coupon['Expiry_Date']; ?>" required><br>

            <input type="submit" name="update_coupon" value="Update Coupon">
        </form>
        <?php else : ?>
        <p>Invalid coupon ID. Please select a valid coupon to edit.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
