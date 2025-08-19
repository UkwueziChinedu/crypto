<?php
<?php
// send_mail.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$recipient = $_POST['recipient'] ?? '';
$other_email = $_POST['other_email'] ?? '';
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Determine actual recipient
if ($recipient === 'other') {
    $to = filter_var($other_email, FILTER_VALIDATE_EMAIL) ? $other_email : '';
} else {
    $to = filter_var($recipient, FILTER_VALIDATE_EMAIL) ? $recipient : '';
}

// Basic validation
if (!$to || !$subject || !$message) {
    echo '<div class="alert alert-danger">Please fill all fields and provide a valid email address.</div>';
    exit;
}

// Prepare headers for PHP mail()
$headers = "From: noreply@" . $_SERVER['SERVER_NAME'] . "\r\n";
$headers .= "Reply-To: noreply@" . $_SERVER['SERVER_NAME'] . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";

// Send email using PHP mail()
if (mail($to, $subject, nl2br(htmlspecialchars($message)), $headers)) {
    echo '<div class="alert alert-success">Email sent successfully to ' . htmlspecialchars($to) . '.</div>';
} else {
    echo '<div class="alert alert-danger">Failed to send email. Please check