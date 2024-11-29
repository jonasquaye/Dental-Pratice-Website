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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientID = intval($_POST['patient_id']);
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];

    // Validate input
    if (empty($patientID) || empty($appointmentDate) || empty($appointmentTime)) {
        echo "All fields are required!";
    } else {
        // Check for existing appointments at the same date and time
        $checkQuery = "SELECT * FROM Appointments WHERE AppointmentDate = ? AND AppointmentTime = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $appointmentDate, $appointmentTime);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "The selected date and time are already booked. Please choose another slot.";
        } else {
            // Insert the appointment into the database
            $insertQuery = "INSERT INTO Appointments (PatientID, AppointmentDate, AppointmentTime) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("iss", $patientID, $appointmentDate, $appointmentTime);

            if ($stmt->execute()) {
                echo "Appointment successfully booked!";
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }
    $stmt->close();
}

$conn->close();
?>
