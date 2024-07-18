// site-php/logging.php
<?php
function log_message($message) {
    $logfile = __DIR__ . '/log.txt';
    $timestamp = date("Y-m-d H:i:s");
    $formatted_message = "[$timestamp] $message\n";
    file_put_contents($logfile, $formatted_message, FILE_APPEND);
}
?>
