<?php
// Initialize the session
session_start();

// Unset all session variables
$_SESSION = array();

// Delete the session cookie from the browser if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session data on the server
session_destroy();

// Redirect user to the login page
header("Location: login.php?status=logged_out");
exit();
?>