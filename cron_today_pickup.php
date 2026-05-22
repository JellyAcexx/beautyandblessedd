<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// 1) Kunin lahat ng reservations na today ang pickup at pending
$sql = "
    SELECT reservation_id, register_id
    FROM reservations
    WHERE status = 'pending'
      AND pickup_date = CURDATE()
";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $reservation_id = (int)$row['reservation_id'];
    $register_id    = (int)$row['register_id'];

    echo "Found reservation $reservation_id for user $register_id<br>";

    // 2) Check kung may existing today_pickup notif na para sa reservation na ito
    $check = $conn->prepare("
        SELECT 1 FROM notifcustomer
        WHERE register_id = ?
          AND notif_type   = 'today_pickup'
          AND reference_id = ?
        LIMIT 1
    ");
    $check->bind_param("ii", $register_id, $reservation_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        // 3) Insert notif
        $msg  = "You have a reservation to be picked up today.";
        $link = "/customer_dashboard.php#reservations";

        $ins = $conn->prepare("
            INSERT INTO notifcustomer
                (register_id, notif_message, notif_type, notif_link, reference_id, is_read, created_at)
            VALUES (?, ?, 'today_pickup', ?, ?, 0, NOW())
        ");
        $ins->bind_param("issi", $register_id, $msg, $link, $reservation_id);
        $ins->execute();
        $ins->close();

        // 4) Get customer info from registers_tb
        $custStmt = $conn->prepare("
            SELECT register_fname, register_lname, register_email
            FROM registers_tb
            WHERE register_id = ?
            LIMIT 1
        ");
        $custStmt->bind_param("i", $register_id);
        $custStmt->execute();
        $custResult = $custStmt->get_result();
        $customer = $custResult->fetch_assoc();
        $custStmt->close();

        if ($customer) {
            $customer_fname  = $customer['register_fname'];
            $customer_lname  = $customer['register_lname'];
            $customer_email  = $customer['register_email'];

            // 5) Get reservation items + product names
            $itemsStmt = $conn->prepare("
                SELECT p.product_name, ri.quantity
                FROM reservation_items ri
                JOIN products p ON p.product_id = ri.product_id
                WHERE ri.reservation_id = ?
            ");
            $itemsStmt->bind_param("i", $reservation_id);
            $itemsStmt->execute();
            $itemsResult = $itemsStmt->get_result();

            $itemsText = "";
            while ($item = $itemsResult->fetch_assoc()) {
                $pname = $item['product_name'];
                $qty   = (int)$item['quantity'];
                $itemsText .= "- $pname (x$qty)\n";
            }
            $itemsStmt->close();

            if ($itemsText === "") {
                $itemsText = "- No items found.\n";
            }

            // 6) Build email body
            $subject = "Reminder: Your reservation #$reservation_id is for pickup today";
            $bodyText = "Hi $customer_fname $customer_lname,\n\n"
                      . "This is a friendly reminder that you have a reservation scheduled for pickup today.\n\n"
                      . "Reservation ID: #$reservation_id\n"
                      . "Items:\n"
                      . "$itemsText\n"
                      . "Please visit Beauty and Blessed Shop to pick up your items.\n\n"
                      . "Thank you for reserving at Beauty and Blessed!\n"
                      . "Beauty and Blessed Shop";

            // 7) Send email via PHPMailer
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
                $mail->addAddress($customer_email, $customer_fname . " " . $customer_lname);

                $mail->isHTML(false); // plain text
                $mail->Subject = $subject;
                $mail->Body    = $bodyText;

                $mail->send();
                echo "Email sent to $customer_email for reservation $reservation_id<br>";
            } catch (Exception $e) {
                echo "Email error for reservation $reservation_id: {$mail->ErrorInfo}<br>";
            }
        }
    }

    $check->close();
}

echo "Today's pickup notifications processed.\n";
