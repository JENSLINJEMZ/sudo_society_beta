<?php 
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: http://192.168.1.2/Sudo_society_beta/index.html");