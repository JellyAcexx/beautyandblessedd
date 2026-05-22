<?php
session_start();
include 'database.php';

if (isset($_SESSION['admin_email'])) {
    $email = $_SESSION['admin_email'];

    // ✅ Log lang email + status (walang password)
    $stmt_log = $conn->prepare(
        "INSERT INTO log_history_tb (email, status) VALUES (?, 'Logout')"
    );
    $stmt_log->bind_param("s", $email);
    $stmt_log->execute();
    $stmt_log->close();
}

/* ✅ Linisin ang session nang buo */
$_SESSION = [];
session_unset();
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
session_destroy();

/* ✅ Redirect pabalik sa login */
header("Location: log_admin.php");
exit();
