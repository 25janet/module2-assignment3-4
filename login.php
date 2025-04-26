<?php
session_start(); // Start session for login tracking

// Database connection settings for XAMPP
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the form if submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Check if inputs are not empty
    if (empty($username) || empty($password)) {
        echo "❌ Please fill in all fields.";
        exit();
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    if (!$stmt) {
        die("❌ Query preparation failed: " . $conn->error);
    }

    // Bind and execute
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Regenerate session ID for security
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            header("Location: dashboard.html");
            exit();
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ Username not found.";
    }

    $stmt->close();
}

$conn->close();
?>
