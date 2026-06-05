<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


if (isset($_POST['submit'])) {
    // Get data from the form
    $course_id = $_POST['course_id'];
    $coupon_code = $_POST['coupon_code'];
    $discount = $_POST['discount'];
    $expiry_date = $_POST['expiry_date'];

    // Validate data (you should perform more robust validation)
    if (empty($course_id) || empty($coupon_code) || empty($discount) || empty($expiry_date)) {
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
            $coupon_check_query = "SELECT Coupon_ID FROM Coupons WHERE Coupon_Code = '$coupon_code' LIMIT 1";
            $result = mysqli_query($conn, $coupon_check_query);

            if (mysqli_num_rows($result) > 0) {
                echo "Coupon code already exists. Please choose a different code.";
            } else {
                // Insert the coupon data into the database
                $query = "INSERT INTO Coupons (Course_ID, Coupon_Code, Discount, Expiry_Date)
                          VALUES ($course_id, '$coupon_code', $discount, '$expiry_date')";

                if (mysqli_query($conn, $query)) {
                    echo "Coupon added successfully!"."<br>";
                    // Send email to registered user
                    $subject = "New Coupon Available";
                    $message = "A new coupon is available for the course: " . $course_id . "\n";
                    $message .= "Coupon Code: " . $coupon_code . "\n";
                    $message .= "Discount: " . $discount . "%\n";
                    $message .= "Expiry Date: " . $expiry_date . "\n";

                    // Fetch email addresses of registered users
                    $user_query = "SELECT email FROM users WHERE status = 'approved'";
                    $user_result = mysqli_query($conn, $user_query);

                    $mail = new PHPMailer();

                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
                    $mail->SMTPAuth = true;
                    $mail->Username = 'nasiryt.827@gmail.com'; // Replace with your SMTP username
                    $mail->Password = "YOUR_OWN_API_KEY"; // Replace with your SMTP password
                    $mail->Port = 587; // Replace with your SMTP port (usually 587)
        
                    // Email content
                    $mail->setFrom('nasiryt.827@gmail.com', 'NASIR ABBAS'); // Replace with your email and name
                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    while ($user = mysqli_fetch_assoc($user_result)) {
                        $to = $user['email'];
                        $mail->addAddress($to); // Add recipient
                        if (!$mail->send()) {
                            echo "Mailer Error: " . $mail->ErrorInfo;
                        } else {
                            echo "Email sent successfully to: " . $to . "<br>";
                        }
                        $mail->clearAddresses(); // Clear recipients for the next iteration
                    }
                } else {
                    echo "Error adding coupon: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Coupon</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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

    <div class="container">
        <h2 class="text-center mt-4 mb-3">Add Coupon</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="course_id">Select Course:</label>
            <select name="course_id" required>
                <option value="" disabled selected>Select a course</option>
                <?php
                $course_query = "SELECT Course_ID, Course_Title FROM Courses WHERE Course_Price > 0";
                $result = mysqli_query($conn, $course_query);
                while ($course = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $course['Course_ID'] . '">' . $course['Course_Title'] . '</option>';
                }
                ?>
            </select>

            <label for="coupon_code">Coupon Code:</label>
            <input type="text" name="coupon_code" required>

            <label for="discount">Discount (%):</label>
            <input type="number" name="discount" min="0" max="100" required>

            <label for="expiry_date">Expiry Date:</label>
            <input type="date" name="expiry_date" required>

            <input type="submit" name="submit" value="Add Coupon" class="btn btn-primary">
            <a class="mt-2 ml-3 btn btn-success" href="view_coupons.php">View Coupons</a>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
