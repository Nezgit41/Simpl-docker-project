// site-php/site/feedback_form/index.php
<?php
include_once __DIR__ . '/../../logging.php';

require __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputText = htmlspecialchars($_POST['text']);
    $name = htmlspecialchars($_POST['name']);
    $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    $email = htmlspecialchars($_POST['email']);

    if (!preg_match('/^(?:\+7|8)\d{10}$/', $phoneNumber)) {
        log_message("Invalid phone number format: $phoneNumber");
        die("Invalid phone number format.");
    }

    if (strpos($email, '@') === false) {
        log_message("Invalid email address: $email");
        die("Invalid email address.");
    }

    if (empty($inputText)) {
        log_message("Text is empty.");
        die("Text is empty.");
    }

    $servername = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    $database = $_ENV['DB_DATABASE'];

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        log_message("Connection failed: " . $conn->connect_error);
        die("Connection failed: " . $conn->connect_error);
    }

    $isViewed = false;

    $sql = $conn->prepare("INSERT INTO feedback (name, phone_number, email, text, is_viewed) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param("ssssi", $name, $phoneNumber, $email, $inputText, $isViewed);

    if ($sql->execute()) {
        log_message("Data inserted into MySQL successfully.");
        echo "Data inserted into MySQL successfully.";
    } else {
        log_message("Error inserting record: " . $sql->error);
        echo "Error inserting record: " . $sql->error;
    }

    $sql->close();
    $conn->close();
}
?>
