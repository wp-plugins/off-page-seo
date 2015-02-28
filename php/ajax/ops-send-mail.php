<?php

require_once('../../../../../wp-load.php');
require_once('../ops.php');
$settings = Off_Page_SEO::ops_get_settings();

if (strlen($settings['guest_posting']['email_content']) < 10) {
    echo "<a href='admin.php?page=ops_settings'>Set up</a> outreach email first.";
} else {

    // send email
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= "From: " . $settings['guest_posting']['email_reply'] . "\r\n";

    $m = mail($_POST['email'], $settings['guest_posting']['email_subject'], nl2br(htmlspecialchars($settings['guest_posting']['email_content'])), $headers);
    if ($m == 1) {
        echo "Sent.";
    } else {
        echo "error";
    }
}