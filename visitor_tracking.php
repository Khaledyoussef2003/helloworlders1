<?php
// visitor_tracking.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "website_projects";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get visitor information
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$page_url = $_SERVER['REQUEST_URI'];
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

// Insert into database
$stmt = $conn->prepare("INSERT INTO site_visits (ip_address, user_agent, page_url, referrer) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $ip_address, $user_agent, $page_url, $referrer);
$stmt->execute();
$stmt->close();
$conn->close();
?>