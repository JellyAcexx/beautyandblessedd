<?php
session_start();
include 'database.php';

if (!isset($_POST['register_id'])) {
    echo json_encode(["success" => false, "error" => "Missing register_id"]);
    exit;
}

$register_id = intval($_POST['register_id']);
$fname = trim($_POST['fname']);
$lname = trim($_POST['lname']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);

$stmt = $conn->prepare("
    UPDATE registers_tb 
    SET register_fname = ?, register_lname = ?, register_email = ?, phone_number = ?
    WHERE register_id = ?
");
$stmt->bind_param("ssssi", $fname, $lname, $email, $phone, $register_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
