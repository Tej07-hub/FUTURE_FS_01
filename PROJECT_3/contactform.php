<?php
// contactform.php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

// Database configuration
$servername = "sql308.infinityfree.com";
$username = "if0_40316441"; // Default XAMPP username
$password = "TejasBhansali3"; // Default XAMPP password is empty
$dbname = "if0_40316441_contact";




// Initialize database connection flag
$db_connected = false;
$db_success = false;

// Create connection
$conn = new mysqli("sql308.infinityfree.com", "if0_40316441", "TejasBhansali3", "if0_40316441_contact");

// Check connection
if ($conn->connect_error) {
    // If database connection fails, we'll still process the form and send email
    $db_connected = false;
} else {
    $db_connected = true;
}

// Get and sanitize form data
$name = isset($_POST['name']) ? filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$phone = isset($_POST['phone']) ? filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING) : '';
$subject = isset($_POST['subject']) ? filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING) : '';
$message = isset($_POST['message']) ? filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING) : '';

// Validation
$errors = [];

// Required fields validation
if (empty($name)) {
    $errors[] = 'Name is required.';
}

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($subject)) {
    $errors[] = 'Subject is required.';
}

if (empty($message)) {
    $errors[] = 'Message is required.';
}

// If there are validation errors, return them
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors)
    ]);
    exit;
}

// Save to database if connected
if ($db_connected) {
    // Create table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS contacts (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        subject VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($createTable) === TRUE) {
        // Insert data
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            $db_success = true;
        } else {
            $db_success = false;
            error_log("Database insert error: " . $stmt->error);
        }
        $stmt->close();
    } else {
        $db_success = false;
        error_log("Table creation error: " . $conn->error);
    }
}

// Send email notification
$to = "info@valtral.com"; // Change this to your actual email address
$email_subject = "Valtral Contact Form: " . $subject;

// Email headers
$headers = [
    'From: ' . $email,
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/html; charset=UTF-8'
];

// Email body
$email_body = "
<html>
<head>
    <title>Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #000; color: #fff; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .field { margin-bottom: 15px; }
        .field-label { font-weight: bold; color: #000; }
        .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Valtral Contact Form Submission</h1>
        </div>
        <div class='content'>
            <div class='field'>
                <span class='field-label'>Name:</span> " . htmlspecialchars($name) . "
            </div>
            <div class='field'>
                <span class='field-label'>Email:</span> " . htmlspecialchars($email) . "
            </div>
            <div class='field'>
                <span class='field-label'>Phone:</span> " . htmlspecialchars($phone) . "
            </div>
            <div class='field'>
                <span class='field-label'>Subject:</span> " . htmlspecialchars($subject) . "
            </div>
            <div class='field'>
                <span class='field-label'>Message:</span><br>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
        </div>
        <div class='footer'>
            <p>This email was sent from the contact form on Valtral website.</p>
        </div>
    </div>
</body>
</html>
";

// Additional headers as string for mail function
$headers_string = implode("\r\n", $headers);

// Send email
$mail_sent = mail($to, $email_subject, $email_body, $headers_string);

// Prepare response
if ($mail_sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you soon.'
    ]);
} else {
    // Log the error for debugging
    error_log('Failed to send email for contact form submission from: ' . $email);
    
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. Please try again later or contact us directly at info@valtral.com.'
    ]);
}

// Close database connection
if ($db_connected) {
    $conn->close();
}
?>