<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ block kung hindi naka-login na admin
if (!isset($_SESSION['admin_email'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Location: log_admin.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'database.php';

// AJAX: MARK AS READ (READ button, walang redirect)
if (isset($_POST['mark_read']) && isset($_POST['notif_id'])) {
    $notifId = (int) $_POST['notif_id'];

    $stmt = $conn->prepare("UPDATE notifadmin SET is_read = 1 WHERE notif_id = ?");
    $stmt->bind_param("i", $notifId);
    $ok = $stmt->execute();
    $stmt->close();

    if ($ok) {
        echo 'ok';
    } else {
        http_response_code(500);
        echo 'error';
    }
    exit();
}

// VIEW button (may redirect) ARE MERON SADYA 
if (isset($_POST['view_notif'])) {
    $notifId = intval($_POST['notif_id']);
    $conn->query("UPDATE notifadmin SET is_read = 1 WHERE notif_id = $notifId");
    $target = htmlspecialchars($_POST['target_page']);
    header("Location: admindashboard.php?page=" . $target);
    exit();
}

$sql = "SELECT * FROM notifadmin ORDER BY created_at DESC";
$result = $conn->query($sql);




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

<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        background: #f7f7f7f7;
        overflow-x: hidden;
    }
    body {
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
    }

    .main-outer {
        width: 100%;
        background: transparent;
        margin: 0;
        padding: 0;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: linear-gradient(135deg, #ffeaf0 0%, #f8d7dc 35%, #ffffff 100%) !important;
        box-shadow: 0 10px 28px rgba(0,0,0,0.12) !important;
        position: sticky;
        top: 0;
        z-index: 100;
        flex-wrap: wrap;
    }
    
    .header-container i {
        color: #6d2e3a;
    }
    
    .heading {
        color: #6d2e3a;
    }

    .notif-scroll-list {
        height: calc(100vh - 90px); /* minus header height */
        overflow-y: auto;
        overflow-x: hidden;
        padding: 12px 0;
    }

    .notif-read {
        background: #fff !important;
    }
    .notif-row {
        display: flex;
        align-items: center;
        background: #f9dde5;
        box-shadow: 0 4px 10px rgba(0,0,0,0.10);
        border-radius: 7px;
        margin: 0 0 11px 0;
        padding: 17px 18px;
        min-height: 48px;
        word-break: break-word;
        width: 100%;
    }
    .notif-icon {
        color: #ab475c;
        background: #fce3ed;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.22rem;
        box-shadow: 0 2px 8px #bebebe19;
        flex-shrink: 0;
    }
    .notif-message {
        flex: 1 1 auto;
        color: #6d2e3a;
        font-weight: 500;
        font-size: 1.14rem;
        padding-left: 3px;
        letter-spacing: 0.01em;
    }
    .notif-time {
        color: #ab475c;
        font-size: 14px;
        font-weight: 500;
        margin-right: 14px;
        min-width: 65px;
        text-align: right;
        flex-shrink: 0;
    }
    .notif-actions {
        display: flex;
        flex-shrink: 0;
    }
    .notif-scroll-list {
        padding: 12px 10px;   /* pareho left/right padding */
    }
    
    .notif-row {
        margin: 0 0 11px 0;   /* walang margin sa kanan at kaliwa */
        width: 100%;          /* siguradong sakop buong lapad */
    }


    .notif-btn {
      border: none;
      background: none;
      color: #ab475c;
      font-weight: bold;
      font-size: 0.90rem;   /* Mas maliit */
      padding: 0 2px 2px 2px; /* Maliit na padding para mas maliit ang button */
      cursor: pointer;
      outline: none;
      text-decoration: underline; /* Guht sa baba, parang link */
      border-radius: 0;           /* No rounded corners */
      box-shadow: none;           /* No shadow */
      margin: 0;
      transition: color 0.18s;
    }
    
    .notif-btn:hover {
      color: #6d2e3a;
      text-decoration: underline;
    }

    @media only screen and (max-width: 900px) {

    /* MAIN WRAPPER */
    .notif-list-outer { 
        max-width: 100vw; 
        width: 100vw; 
        padding: 0 10px; 
    }

    /* VIEW BUTTON to RIGHT sa mobile */
    .notif-actions {
        width: 100%;
        display: flex;
        justify-content: flex-end;  /* ilipat sa kanan */
        margin-top: 10px;
    }

    /* SCROLL AREA */
    .notif-scroll-list { 
        max-height: 100vh; 
        padding-left: 6px; 
        padding-right: 6px; 
    }

    /* LIST ITEMS */
    .notif-row { 
        flex-direction: column; 
        align-items: flex-start;
        padding: 20px;
    }

    .notif-time { 
        margin: 8px 0 5px 0;
        font-size: 14px;
    }

    .notif-message {
        font-size: 1.1rem;
    }

    .notif-actions { 
        margin-top: 12px;
    }
}

/* TABLET VIEW */
@media only screen and (max-width: 600px) {
    .notif-row {
        padding: 18px;
    }
    .notif-message {
        font-size: 1rem;
    }
}

/* Hide scrollbar but keep scrolling */
.notif-scroll-list {
    overflow-y: scroll; /* allow scrolling */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none;  /* IE 10+ */
}

.notif-scroll-list::-webkit-scrollbar {
    width: 0px;  /* Chrome, Safari, Edge */
    background: transparent;  /* optional: just to be safe */
}

@media (max-width: 767px) {
    .header-container {
        flex-direction: column;
        gap: 12px;
        text-align: left;           /* ← Gawing left align ang header container */
        align-items: flex-start;    /* ← Left align content sa container */
    }
    .heading {
        font-size: 25px !important;
        justify-content: flex-start; /* ← Icon + text ay left-justify sa flex */
        text-align: left !important; /* ← Text ng header ay left aligned */
        width: 100%;
    }
}

</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="main-outer">
    <div class="header-container">
        <h1 class="heading" style="display: flex; align-items: center; font-size: 2em; font-weight: bold;">
            <i class="fa fa-bell" style="margin-left: 12px; margin-right: 12px;"></i> Admin Notifications
        </h1>
    </div>

    <div class="notif-scroll-list">
        <?php while ($notif = $result->fetch_assoc()): ?>
                    <?php
                        $targetPage = '';
                        if (
                            $notif['notif_type'] === 'reservation_cancelled' ||
                            $notif['notif_type'] === 'reservation'
                        ) {
                            $targetPage = 'reservations';
                        } elseif (
                            $notif['notif_type'] === 'low_stock' ||
                            $notif['notif_type'] === 'restock' ||
                            $notif['notif_type'] === 'stock_alert'
                        ) {
                            $targetPage = 'addproducts';
                        } elseif ($notif['notif_type'] === 'new_user') {
                            $targetPage = '';
                        }
                
                        // Kung is_read == 1, add notif-read class (for white bg)
                        $rowClasses = 'notif-row' . ($notif['is_read'] == 1 ? ' notif-read' : '');
                    ?>
                
                    <div class="<?= $rowClasses ?>" data-id="<?= $notif['notif_id'] ?>">
                        <span class="notif-icon"><i class="fa fa-check-circle"></i></span>
                        <div class="notif-message"><?= htmlspecialchars($notif['notif_message']) ?></div>
                        <div class="notif-time"><?= time_elapsed_string($notif['created_at']) ?></div>
                        <div class="notif-actions">
                            <?php if ($targetPage !== ''): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="notif_id" value="<?= $notif['notif_id'] ?>">
                                    <input type="hidden" name="target_page" value="<?= $targetPage ?>">
                                    <button class="notif-btn" name="view_notif" type="submit">VIEW</button>
                                </form>
                            <?php else: ?>
                                <button
                                    class="notif-btn"
                                    type="button"
                                    onclick="markNotifRead(<?= $notif['notif_id'] ?>)">
                                    READ
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
            </div>
            
<script>

function markNotifRead(notifId) {
    const formData = new URLSearchParams();
    formData.append('mark_read', '1');
    formData.append('notif_id', notifId);

    fetch('admin_notifications.php', { // ← pangalan ng PHP file ng notifications
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        },
        body: formData.toString()
    })
    .then(response => response.text())
    .then(data => {
        // Optional: console.log(data);
        // 1) update row style para maging white
        const row = document.querySelector('.notif-row[data-id="' + notifId + '"]');
        if (row) {
            row.classList.add('notif-read');
        }
        // 2) update badge count
        updateNotifCount();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

</script>

            
