<?php
include 'database.php';

$sql = "SELECT COUNT(*) AS cnt FROM notifadmin WHERE is_read = 0";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
echo (int)$row['cnt'];
