<?php
// Start session to check login status
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header("Location: login.html"); // Redirect to login page if not logged in
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dental_practice";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get future appointments (ignore time part)
$futureAppointmentsQuery = "
    SELECT Patients.FullName, Patients.Email, Appointments.AppointmentDate, Appointments.AppointmentTime
    FROM Patients
    JOIN Appointments ON Patients.PatientID = Appointments.PatientID
    WHERE DATE(Appointments.AppointmentDate) >= CURDATE()
    ORDER BY Appointments.AppointmentDate, Appointments.AppointmentTime
";

// Get past appointments (ignore time part)
$pastAppointmentsQuery = "
    SELECT Patients.FullName, Patients.Email, Appointments.AppointmentDate, Appointments.AppointmentTime
    FROM Patients
    JOIN Appointments ON Patients.PatientID = Appointments.PatientID
    WHERE DATE(Appointments.AppointmentDate) < CURDATE()
    ORDER BY Appointments.AppointmentDate DESC
";

// Execute queries
$futureAppointments = $conn->query($futureAppointmentsQuery);
$pastAppointments = $conn->query($pastAppointmentsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Reports</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Staff Reports</h2>

<!-- Future Appointments Table -->
<h3>Future Appointments</h3>
<table>
    <thead>
        <tr>
            <th>Patient Name</th>
            <th>Patient Email</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Display future appointments
        if ($futureAppointments->num_rows > 0) {
            while ($row = $futureAppointments->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['FullName']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['AppointmentDate']}</td>
                        <td>{$row['AppointmentTime']}</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No future appointments found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Past Appointments Table -->
<h3>Past Appointments</h3>
<table>
    <thead>
        <tr>
            <th>Patient Name</th>
            <th>Patient Email</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Display past appointments
        if ($pastAppointments->num_rows > 0) {
            while ($row = $pastAppointments->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['FullName']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['AppointmentDate']}</td>
                        <td>{$row['AppointmentTime']}</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No past appointments found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
