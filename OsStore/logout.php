<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// If you want to delete the session cookie, you should also do that
if (ini_get("session.use_cookies")) {
    // Get the session parameters
    $params = session_get_cookie_params();
    
    // Delete the session cookie
    setcookie(session_name(), '', time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to the home page
header("Location: /index.php");
exit;
?>
