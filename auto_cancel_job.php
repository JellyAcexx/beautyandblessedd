<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "OK";


include 'database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// hanapin lahat ng pending na lagpas na sa pickup_date
$sql = "
    SELECT reservation_id, register_id, pickup_date
    FROM reservations
    WHERE status = 'pending'
      AND pickup_date < CURDATE()
";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $reservation_id = (int)$row['reservation_id'];
    $register_id    = (int)$row['register_id'];
    $pickup_date    = $row['pickup_date'];

    echo "Auto-cancelling reservation $reservation_id for user $register_id<br>";

    // 1) update reservation to cancelled
    $upd = $conn->prepare("
        UPDATE reservations
        SET status = 'cancelled',
            cancel_date = CURDATE()
        WHERE reservation_id = ?
    ");
    $upd->bind_param("i", $reservation_id);
    $upd->execute();
    $upd->close();

    // 2) get customer info
    $custStmt = $conn->prepare("
        SELECT register_fname, register_lname, register_email
        FROM registers_tb
        WHERE register_id = ?
        LIMIT 1
    ");
    $custStmt->bind_param("i", $register_id);
    $custStmt->execute();
    $custRes  = $custStmt->get_result();
    $customer = $custRes->fetch_assoc();
    $custStmt->close();

    // 3) get reservation items + product names
    $itemsText = "";
    $itemsStmt = $conn->prepare("
        SELECT p.product_name, ri.quantity
        FROM reservation_items ri
        JOIN products p ON p.product_id = ri.product_id
        WHERE ri.reservation_id = ?
    ");
    $itemsStmt->bind_param("i", $reservation_id);
    $itemsStmt->execute();
    $itemsRes = $itemsStmt->get_result();
    while ($item = $itemsRes->fetch_assoc()) {
        $pname = $item['product_name'];
        $qty   = (int)$item['quantity'];
        $itemsText .= "- $pname (x$qty)\n";
    }
    $itemsStmt->close();
    if ($itemsText === "") {
        $itemsText = "- No items found.\n";
    }

    // 4) insert notif sa notifcustomer
    $msg  = "Your reservation #$reservation_id has been cancelled because the pickup date has passed.";
    $link = "/customer_dashboard.php#reservations";

    $ins = $conn->prepare("
        INSERT INTO notifcustomer
            (register_id, notif_message, notif_type, notif_link, reference_id, is_read, created_at)
        VALUES (?, ?, 'auto_cancel', ?, ?, 0, NOW())
    ");
    $ins->bind_param("issi", $register_id, $msg, $link, $reservation_id);
    $ins->execute();
    $ins->close();

        // 5) insert notif sa notifadmin (assume admin = register_id = 1)
    $admin_register_id = 1;
    $admin_msg = "Reservation #$reservation_id for customer ID $register_id has been auto-cancelled.";
    $admin_link = "reservations.php";

    $insAdmin = $conn->prepare("
        INSERT INTO notifadmin
            (register_id, notif_message, notif_type, notif_link, created_at, is_read)
        VALUES (?, ?, 'auto_cancel', ?, NOW(), 0)
    ");
    $insAdmin->bind_param("iss", $admin_register_id, $admin_msg, $admin_link);
    $insAdmin->execute();
    $insAdmin->close();

    // 6) send email to customer (if we have customer info)
    if ($customer) {
        $fname = $customer['register_fname'];
        $lname = $customer['register_lname'];
        $email = $customer['register_email'];

        $subject = "Your reservation #$reservation_id has been cancelled";
        $body = "Hi $fname $lname,\n\n"
              . "Your reservation #$reservation_id has been automatically cancelled "
              . "because the pickup date ($pickup_date) has already passed.\n\n"
              . "Items in this reservation:\n"
              . "$itemsText\n"
              . "If you wish to place a new reservation, you may do so anytime in our system.\n\n"
              . "Thank you,\n"
              . "Beauty and Blessed Shop";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'paulitomapagmahal9@gmail.com';
            $mail->Password   = 'qnkr tpqm vdio kciz';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('paulitomapagmahal9@gmail.com', 'Beauty and Blessed Shop');
            $mail->addAddress($email, $fname . " " . $lname);

            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            echo "Cancel email sent to $email for reservation $reservation_id<br>";
        } catch (Exception $e) {
            echo "Email error for reservation $reservation_id: {$mail->ErrorInfo}<br>";
        }
    }

    // 7) send email to admin
    try {
        $adminMail = new PHPMailer(true);
        $adminMail->isSMTP();
        $adminMail->Host       = 'smtp.gmail.com';
        $adminMail->SMTPAuth   = true;
        $adminMail->Username   = 'paulitomapagmahal9@gmail.com';
        $adminMail->Password   = 'qnkr tpqm vdio kciz';
        $adminMail->SMTPSecure = 'tls';
        $adminMail->Port       = 587;

        $adminMail->setFrom('paulitomapagmahal9@gmail.com', 'Beauty and Blessed Shop');
        $adminMail->addAddress('paulitomapagmahal9@gmail.com', 'Beauty and Blessed Admin');

        $adminSubject = "Auto-cancelled reservation #$reservation_id";
        $adminBody = "Hello Admin,\n\n"
                   . "Reservation #$reservation_id for customer ID $register_id has been automatically cancelled "
                   . "because the pickup date ($pickup_date) has already passed.\n\n"
                   . "Items in this reservation:\n"
                   . "$itemsText\n\n"
                   . "This was done by the nightly auto-cancel job.\n\n"
                   . "Beauty and Blessed System";

        $adminMail->isHTML(false);
        $adminMail->Subject = $adminSubject;
        $adminMail->Body    = $adminBody;

        $adminMail->send();
              echo "Admin cancel email sent for reservation $reservation_id<br>";
    } catch (Exception $e) {
        echo "Admin email error for reservation $reservation_id: {$adminMail->ErrorInfo}<br>";
    }
}

echo "Auto-cancel for past pickup dates processed.\n";
?>
