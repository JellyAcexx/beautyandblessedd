<?php
include "database.php";

function generate_notifications($conn, $register_id) {

    /* -----------------------------------------------
       1) WELCOME NOTIFICATION
       (historical creation date)
    ------------------------------------------------ */
    $checkWelcome = $conn->prepare("SELECT * FROM notifications WHERE register_id = ? AND type = 'welcome'");
    $checkWelcome->bind_param("i", $register_id);
    $checkWelcome->execute();
    $resWelcome = $checkWelcome->get_result();

    if ($resWelcome->num_rows == 0) {
        $getUser = $conn->prepare("SELECT created_at FROM registers_tb WHERE register_id = ?");
        $getUser->bind_param("i", $register_id);
        $getUser->execute();
        $userData = $getUser->get_result()->fetch_assoc();

        $insert = $conn->prepare("INSERT INTO notifications 
            (register_id, type, title, message, created_at)
            VALUES (?, 'welcome', 'Welcome to Beauty & Blessed!', 'Thank you for joining our platform.', ?)
        ");
        $insert->bind_param("is", $register_id, $userData['created_at']);
        $insert->execute();
    }

    /* -----------------------------------------------
       2) CART ITEMS 3+ DAYS OLD
       (created_at = detection time)
    ------------------------------------------------ */
    $cartQuery = "
        SELECT ci.cart_items_id, ci.add_date
        FROM cart_items ci
        JOIN cart c ON ci.cart_id = c.cart_id
        JOIN login_tb l ON c.login_id = l.login_id
        WHERE l.register_id = ? AND TIMESTAMPDIFF(DAY, ci.add_date, NOW()) >= 3
    ";
    $stmt = $conn->prepare($cartQuery);
    $stmt->bind_param("i", $register_id);
    $stmt->execute();
    $resCart = $stmt->get_result();

    while ($row = $resCart->fetch_assoc()) {
        $check = $conn->prepare("SELECT * FROM notifications WHERE cart_items_id = ? AND type = 'cart_old'");
        $check->bind_param("i", $row['cart_items_id']);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows == 0) {
            $detectTime = date("Y-m-d H:i:s");
            $message = "You have items in your cart added on ".$row['add_date'].". Please proceed with your reservation.";

            $insert = $conn->prepare("INSERT INTO notifications 
                (register_id, type, title, message, cart_items_id, created_at)
                VALUES (?, 'cart_old', 'Items in Cart for 3 Days', ?, ?, ?)
            ");
            $insert->bind_param("isis", $register_id, $message, $row['cart_items_id'], $detectTime);
            $insert->execute();
        }
    }

    /* -----------------------------------------------
       3) UPCOMING PICKUP REMINDER
       (created_at = detection time)
    ------------------------------------------------ */
    $pickupQuery = "
        SELECT reservation_id, pickup_date
        FROM reservations
        WHERE register_id = ? AND status = 'pending' AND DATEDIFF(pickup_date, CURDATE()) = 1
    ";
    $stmt = $conn->prepare($pickupQuery);
    $stmt->bind_param("i", $register_id);
    $stmt->execute();
    $resPickup = $stmt->get_result();

    while ($row = $resPickup->fetch_assoc()) {
        $check = $conn->prepare("SELECT * FROM notifications WHERE reservation_id = ? AND type = 'pickup_reminder'");
        $check->bind_param("i", $row['reservation_id']);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows == 0) {
            $detectTime = date("Y-m-d H:i:s");
            $message = "Your reservation scheduled for ".$row['pickup_date']." is tomorrow.";

            $insert = $conn->prepare("INSERT INTO notifications 
                (register_id, type, title, message, reservation_id, created_at)
                VALUES (?, 'pickup_reminder', 'Upcoming Pickup Reminder', ?, ?, ?)
            ");
            $insert->bind_param("isis", $register_id, $message, $row['reservation_id'], $detectTime);
            $insert->execute();
        }
    }

    /* -----------------------------------------------
       4) PICKED-UP RESERVATIONS
       (created_at = detection time)
    ------------------------------------------------ */
    $completedQuery = "
        SELECT reservation_id, pickup_date
        FROM reservations
        WHERE register_id = ? AND status = 'picked_up'
    ";
    $stmt = $conn->prepare($completedQuery);
    $stmt->bind_param("i", $register_id);
    $stmt->execute();
    $resCompleted = $stmt->get_result();

    while ($row = $resCompleted->fetch_assoc()) {
        $check = $conn->prepare("SELECT * FROM notifications WHERE reservation_id = ? AND type = 'completed'");
        $check->bind_param("i", $row['reservation_id']);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows == 0) {
            $detectTime = date("Y-m-d H:i:s");
            $message = "Your reservation scheduled for ".$row['pickup_date']." has been successfully picked up. Thank you!";

            $insert = $conn->prepare("INSERT INTO notifications 
                (register_id, type, title, message, reservation_id, created_at)
                VALUES (?, 'completed', 'Reservation Picked Up', ?, ?, ?)
            ");
            $insert->bind_param("isis", $register_id, $message, $row['reservation_id'], $detectTime);
            $insert->execute();
        }
    }

    /* -----------------------------------------------
       5) CANCELLED RESERVATIONS
       (created_at = detection time)
    ------------------------------------------------ */
    $cancelQuery = "
        SELECT reservation_id, cancel_date, reservation_date
        FROM reservations
        WHERE register_id = ? AND status = 'cancelled'
    ";
    $stmt = $conn->prepare($cancelQuery);
    $stmt->bind_param("i", $register_id);
    $stmt->execute();
    $resCancel = $stmt->get_result();

    while ($row = $resCancel->fetch_assoc()) {
        $check = $conn->prepare("SELECT * FROM notifications WHERE reservation_id = ? AND type = 'cancelled'");
        $check->bind_param("i", $row['reservation_id']);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows == 0) {
            $detectTime = date("Y-m-d H:i:s");
            $eventDate = $row['cancel_date'] ?? $row['reservation_date'];
            $message = "Your reservation scheduled for ".$eventDate." has been cancelled.";

            $insert = $conn->prepare("INSERT INTO notifications
                (register_id, type, title, message, reservation_id, created_at)
                VALUES (?, 'cancelled', 'Reservation Cancelled', ?, ?, ?)
            ");
            $insert->bind_param("isis", $register_id, $message, $row['reservation_id'], $detectTime);
            $insert->execute();
        }
    }
}
?>
