<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🔹 Database connection settings — copy exactly from your InfinityFree MySQL panel
$servername = "sql113.infinityfree.com";
$username = "if0_40263045";
$password = "TejasBhansali";  // replace with the real MySQL password
$dbname = "if0_40263045_contact_db";

// 🔹 Create connection
$conn = new mysqli("sql113.infinityfree.com"," if0_40263045"," TejasBhansali", "if0_40263045_contact_db");

// 🔹 Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🔹 Get form data safely
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';

// 🔹 Check if form is filled
if (empty($name) || empty($email) || empty($message)) {
    die("Please fill all fields.");
}

// 🔹 Insert data into database
$sql = "INSERT INTO contact (name, email, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
    echo "✅ Message sent successfully!";
} else {
    echo "❌ Error inserting record: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
