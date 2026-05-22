<?php
$to = 'your@email.com';
$subject = 'PHP Mail Test';
$message = 'This is a test message sent from PHP.';
$headers = "From: reservationsystem@beautyandblessed.online\r\n";
if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully.";
} else {
    echo "Email delivery failed.";
}
?>
