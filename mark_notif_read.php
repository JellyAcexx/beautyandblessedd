<?php
// Optional: errors habang nagde-debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['notif_id'])) {
        http_response_code(400);
        echo 'missing_id';
        exit;
    }

    $notifId = (int) $_POST['notif_id'];

    // Prepared statement para safe ang UPDATE
    $stmt = $conn->prepare("UPDATE notifadmin SET is_read = 1 WHERE notif_id = ?");
    $stmt->bind_param("i", $notifId);

    if ($stmt->execute()) {
        echo 'ok';
    } else {
        http_response_code(500);
        echo 'error';
    }

    $stmt->close();
    $conn->close();
    exit;

} else {
    http_response_code(405);
    echo 'method_not_allowed';
    exit;
}
?>
