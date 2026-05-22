<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['register_id'])) {
    die("You must be logged in to view notifications.");
}
$customer_id = $_SESSION['register_id'];

include 'database.php';

// --- CORRECT: SELECT statement for fetching notifications ---
$sql = "SELECT * FROM notifcustomer WHERE register_id = ? ORDER BY created_at DESC LIMIT 20";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <style> ... (keep your CSS here) ... </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="notif-frame">
        <div class="notif-header">
            <i class="fa fa-bell"></i>
            <h2 style="margin:0;font-size:2rem;">Notifications</h2>
        </div>
        <div class="notif-list">
            <?php while ($notif = $result->fetch_assoc()): ?>
            <div class="notif<?= !$notif['is_read']?' unread':'' ?>">
                <span class="notif-dot<?= $notif['is_read']?' read':'' ?>"></span>
                <div class="notif-message">
                    <?= htmlspecialchars($notif['notif_message']) ?>
                    <?php if (!empty($notif['notif_link'])): ?>
                        <a href="<?= htmlspecialchars($notif['notif_link']) ?>" style="margin-left:10px;">View</a>
                    <?php endif; ?>
                </div>
                <div class="notif-time"><?= time_elapsed_string($notif['created_at']) ?></div>
            </div>
            <?php endwhile; ?>
            <?php
            function time_elapsed_string($datetime, $full = false) {
                $now = new DateTime;
                $ago = new DateTime($datetime);
                $diff = $now->diff($ago);

                $diff->w = floor($diff->d / 7);
                $diff->d -= $diff->w * 7;

                $string = array(
                    'y' => 'yr',
                    'm' => 'mo',
                    'w' => 'w',
                    'd' => 'd',
                    'h' => 'h',
                    'i' => 'm',
                    's' => 's',
                );
                foreach ($string as $k => &$v) {
                    if ($diff->$k) {
                        $v = $diff->$k . $v;
                    } else {
                        unset($string[$k]);
                    }
                }
                if (!$full) $string = array_slice($string, 0, 1);
                return $string ? implode(', ', $string) . ' ago' : 'just now';
            }
            ?>
        </div>
    </div>
</body>
</html>
