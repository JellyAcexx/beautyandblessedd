<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['register_id']) || !is_numeric($_SESSION['register_id'])) {
    die('Session variable "register_id" is missing or invalid.');
}
$customer_id = intval($_SESSION['register_id']);

include 'database.php';

// 1. Kapag cliniclick yung VIEW/READ, mark as read + redirect kung kailangan
if (isset($_POST['notif_action']) && isset($_POST['notif_id'])) {
    $notifId = intval($_POST['notif_id']);
    $action  = $_POST['notif_action']; // 'view' or 'read'

    // kunin notif_link ng notif na yun
    $stmtLink = $conn->prepare("SELECT notif_link FROM notifcustomer WHERE notif_id = ? AND register_id = ?");
    $stmtLink->bind_param("ii", $notifId, $customer_id);
    $stmtLink->execute();
    $resLink = $stmtLink->get_result();
    $rowLink = $resLink->fetch_assoc();
    $stmtLink->close();

    // mark as read
    $stmtUpdate = $conn->prepare("UPDATE notifcustomer SET is_read = 1 WHERE notif_id = ? AND register_id = ?");
    $stmtUpdate->bind_param("ii", $notifId, $customer_id);
    $stmtUpdate->execute();
    $stmtUpdate->close();

    // kung VIEW at may notif_link → notify parent frame to changePage
    if ($action === 'view' && !empty($rowLink['notif_link'])) {
        $target = htmlspecialchars($rowLink['notif_link'], ENT_QUOTES, 'UTF-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head><meta charset="utf-8"></head>
        <body>
        <script>
          if (window.parent && window.parent !== window) {
            window.parent.postMessage({ type: "changeFrame", page: "<?php echo $target; ?>" }, "*");
          } else {
            window.location.href = "<?php echo $target; ?>";
          }
        </script>
        </body>
        </html>
        <?php
        exit();
    }

    // kung READ lang o walang link → balik lang sa notifications page
    header("Location: customer_notifications.php");
    exit();
}

// 2. normal fetch ng notifications (pang-display)
$sql = "SELECT * FROM notifcustomer WHERE register_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) { die('Prepare failed: ' . $conn->error); }
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $weeks = floor($diff->days / 7);
    $days = $diff->days - $weeks * 7;
    $result = [];
    if ($diff->y) $result[] = $diff->y . 'yr';
    if ($diff->m) $result[] = $diff->m . 'mo';
    if ($weeks)  $result[] = $weeks . 'w';
    if ($days)   $result[] = $days . 'd';
    if ($diff->h) $result[] = $diff->h . 'h';
    if ($diff->i) $result[] = $diff->i . 'm';
    if ($diff->s && !$result) $result[] = $diff->s . 's';
    if (!$full) $result = array_slice($result, 0, 1);
    return $result ? implode(', ', $result) . ' ago' : 'just now';
}
?>


<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?famil=Poppins:wght@400;500;600;700&display=swap">

  <title>Customer Notifications</title>
  <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background: #fff;
            overflow-x: hidden;
            font-family: 'Poppins', Arial, sans-serif;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none;
        }
        body { min-height: 100vh; }

        .main-outer {
            width: 100%;
            margin: 0 auto;
            padding: 0;
            background: transparent;
        }

        .main-outer { max-width: 100%; padding: 0 15px; box-sizing: border-box; }

        .notif-header-card {
            position: sticky;
            top: 0;
            z-index: 10;
            width: 100%;
            background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%) !important;
            box-shadow: 0 10px 28px rgba(0,0,0,0.12) !important;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            border-radius: 0;
            margin-top: 15px;
            margin: 15px 0px 0 0px;
        }
        .header-icon {
            margin-right: 10px !important;
            font-size: 1.8em;
            color: #6D2E3A;
            display: flex;
            align-items: center;
        }

        .header-text {
            font-weight: 700;
            font-size: 1.8em;
            color: #6D2E3A;
            letter-spacing: 0.5px;
            margin: 0;
        }
        
        .notif-header {
            padding-left: 12px;
        }

        .notif-header .bi-bell-fill {
            color: #6d2e3a;
            font-size: 1.8em;
        }

        .notif-scroll-list {
            height: calc(100vh - 90px);
            overflow-y: auto;
            overflow-x: hidden;
            padding: 12px 0px;
        }

        .notif-scroll-list::-webkit-scrollbar {
            display: none;
        }

        .notif-row {
            display: flex;
            align-items: center;
            background: #f9dde5; /* unread = pink */
            box-shadow: 0 4px 10px rgba(0,0,0,0.10);
            border-radius: 7px;
            margin: 0 0 11px 0;
            padding: 17px 18px;
            min-height: 48px;
            word-break: break-word;
            width: 100%;
            box-sizing: border-box;
            min-width: 0;
        }
        .notif-read {
            background: #ffffff !important; /* read = white */
        }
        .notif-icon i {
            font-size: 1rem;
        }

        .notif-icon {
            color: #ab475c;
            background: #fce3ed;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px #bebebe19;
            flex-shrink: 0;
            margin-right: 14px;
        }

        .notif-message {
            flex: 1 1 auto;
            color: #6d2e3a;
            font-weight: 500;
            font-size: 1.05rem;
            padding-left: 3px;
            letter-spacing: 0.01em;
            min-width: 0;
        }

        .notif-time {
            color: #ab475c;
            font-size: 13px;
            font-weight: 500;
            margin-right: 12px;
            min-width: 65px;
            text-align: right;
            flex-shrink: 0;
        }

        .notif-actions {
            display: flex;
            flex-shrink: 0;
        }

        .notif-btn {
            border: none;
            background: none;
            color: #ab475c;
            font-weight: bold;
            font-size: 0.90rem;
            padding: 0 2px 2px 2px;
            cursor: pointer;
            outline: none;
            text-decoration: underline;
            border-radius: 0;
            box-shadow: none;
            margin: 0;
            transition: color 0.18s;
        }
        .notif-btn:hover {
            color: #6d2e3a;
            text-decoration: underline;
        }

        @media only screen and (max-width: 900px) {
            .notif-scroll-list {
                padding: 12px 10px;
            }
            .notif-row {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 14px;
            }
            .notif-time {
                margin: 8px 0 5px 0;
                font-size: 13px;
            }
            .notif-actions {
                width: 100%;
                display: flex;
                justify-content: flex-end;
                margin-top: 6px;
            }
        }

        @media only screen and (max-width: 575.98px) {
            .header-icon,
            .header-text {
                font-size: 25px;
            }
            .header-icon {
                margin-left: 4px;
            }
            .notif-header-card {
                flex-direction: row !important;
                align-items: flex-start !important;
            }
        }
  </style>
</head>
<body>
<div class="main-outer">
    <div class="notif-header-card">
        <span class="header-icon" style=""><i class="bi bi-calendar-fill"></i></span>
        <span class="header-text"> Notifications</span>
    </div>

    <div class="notif-scroll-list">
        <?php while ($notif = $result->fetch_assoc()): ?>
            <?php
                $rowClasses = 'notif-row' . ($notif['is_read'] == 1 ? ' notif-read' : '');
            ?>
            <div class="<?= $rowClasses ?>">
                <span class="notif-icon"><i class="bi bi-bell-fill"></i></span>

                <div class="notif-message">
                    <?= htmlspecialchars($notif['notif_message']) ?>
                </div>

                <div class="notif-time">
                    <?= time_elapsed_string($notif['created_at']) ?>
                </div>

                <div class="notif-actions">
                    <?php if (!empty($notif['notif_link'])): ?>
                        <!-- May link → VIEW -->
                        <form action="customer_notifications.php" method="POST" style="display:inline;">
                            <input type="hidden" name="notif_id" value="<?= $notif['notif_id'] ?>">
                            <input type="hidden" name="notif_action" value="view">
                            <button class="notif-btn" type="submit">VIEW</button>
                        </form>
                    <?php else: ?>
                        <!-- Walang link → READ lang -->
                        <form action="customer_notifications.php" method="POST" style="display:inline;">
                            <input type="hidden" name="notif_id" value="<?= $notif['notif_id'] ?>">
                            <input type="hidden" name="notif_action" value="read">
                            <button class="notif-btn" type="submit">READ</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
