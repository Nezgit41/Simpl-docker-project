<?php
include_once __DIR__ . '/../../logging.php';

require __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $servername = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    $database = $_ENV['DB_DATABASE'];

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        log_message("Connection failed: " . $conn->connect_error);
        die("Connection failed: " . $conn->connect_error);
    }

    $nameFilter = isset($_GET['name']) ? htmlspecialchars($_GET['name']) . "%" : "%";
    $emailFilter = isset($_GET['email']) ? htmlspecialchars($_GET['email']) . "%" : "%";
    $phoneNumberFilter = isset($_GET['phone_number']) ? htmlspecialchars($_GET['phone_number']) . "%" : "%";
    $isViewedFilter = isset($_GET['is_viewed']) ? (filter_var($_GET['is_viewed'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0) : "%";

    log_message("Filters applied: name=$nameFilter, email=$emailFilter, phone_number=$phoneNumberFilter, is_viewed=$isViewedFilter");

    $sql = $conn->prepare("SELECT name, phone_number, email, text, is_viewed FROM feedback WHERE name LIKE ? AND email LIKE ? AND phone_number LIKE ? AND (is_viewed = ? OR ? = '%')");
    if (!$sql) {
        log_message("SQL prepare failed: " . $conn->error);
        die("SQL prepare failed: " . $conn->error);
    }

    $sql->bind_param("sssii", $nameFilter, $emailFilter, $phoneNumberFilter, $isViewedFilter, $isViewedFilter);
    if (!$sql->execute()) {
        log_message("SQL execute failed: " . $sql->error);
        die("SQL execute failed: " . $sql->error);
    }

    $result = $sql->get_result();
    if (!$result) {
        log_message("Get result failed: " . $sql->error);
        die("Get result failed: " . $sql->error);
    }

    $feedbackData = [];
    while ($row = $result->fetch_assoc()) {
        $feedbackData[] = $row;
    }

    $sql->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($feedbackData);
    log_message("Data retrieved successfully.");
}
?>
