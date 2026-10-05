<?php

session_start();

/*
|--------------------------------------------------------------------------
| Logout Admin
|--------------------------------------------------------------------------
| Session completely destroy karke admin ko login page par bhejta hai.
|--------------------------------------------------------------------------
*/

// Remove all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();

?>