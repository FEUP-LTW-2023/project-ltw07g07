<?php
// start session
session_start();

// unset session variables
session_unset();

// destroy session
session_destroy();

// redirect to login page
header('Location: login.php');
exit();
?>