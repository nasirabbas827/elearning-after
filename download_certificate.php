<?php
session_start();
include 'config.php';

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}

// Function to generate and download the certificate
function generateCertificate($user_id, $course_title, $user_name, $user_email, $quiz_marks) {
    require('FPDF/fpdf.php');

    // Create a new FPDF object
    $pdf = new FPDF();
    $pdf->AddPage('L', 'A4');

    // Set font
    $pdf->SetFont('Arial', 'B', 16);

    // Add content to the certificate
    $pdf->Cell(0, 10, 'Certificate of Completion', 0, 1, 'C');
    $pdf->Ln(20);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "This is to certify that", 0, 1, 'C');
    $pdf->Cell(0, 10, $user_name, 0, 1, 'C');
    $pdf->Cell(0, 10, "with email: $user_email", 0, 1, 'C');
    $pdf->Cell(0, 10, "has successfully completed the course:", 0, 1, 'C');
    $pdf->Cell(0, 10, $course_title, 0, 1, 'C');


    $pdf->Ln(20);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, "Congratulations on successfully completing the course and achieving excellent marks in the quizzes. We commend your dedication and effort in learning the course material.");

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Thank you for your precious time!", 0, 1, 'C');

    // Close and output the PDF
    $pdf->Output('certificate.pdf', 'D');
}

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch course details
    $course_query = "SELECT * FROM Courses WHERE Course_ID = $course_id";
    $result = mysqli_query($conn, $course_query);
    $course = mysqli_fetch_assoc($result);

    if ($course) {
        // Fetch user details
        $user_id = $_SESSION["user_id"];
        $user_query = "SELECT * FROM Users WHERE id = $user_id";
        $result = mysqli_query($conn, $user_query);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // Fetch quiz marks for the course
            $quiz_marks = array();
            $quizzes_query = "SELECT * FROM Quizzes WHERE Course_ID = $course_id";
            $result = mysqli_query($conn, $quizzes_query);

            // Generate and download the certificate
            generateCertificate($user_id, $course['Course_Title'], $user['username'], $user['email'], $quiz_marks);
        }
    }
}
?>
