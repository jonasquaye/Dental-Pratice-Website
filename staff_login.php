<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dental_practice";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch staff details from the database
    $sql = "SELECT * FROM Staff WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $staff = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $staff['PasswordHash'])) {
            $_SESSION['staff_logged_in'] = true; // Set session variable
            $_SESSION['staff_username'] = $username; // Optional: Store username
            header("Location: staff.php"); // Redirect to staff area
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
    }
}
?>
