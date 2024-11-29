<?php
session_start();
session_unset();
session_destroy();
header("Location: staff_login.html"); // Redirect to login page after logout
exit;
?>
