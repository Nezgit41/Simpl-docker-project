<?php
require 'vendor/autoload.php'; 
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputText = htmlspecialchars($_POST['text']);
    $name = htmlspecialchars($_POST['name']);
    $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    $email = htmlspecialchars($_POST['email']);

    if (preg_match('/^(?:\+7|8)\d{10}$/', $phoneNumber)) {
        if (strpos($email, '@') !== false) {
            if (!empty($inputText)) {
                $servername = $_ENV['DB_HOST'];
                $username = $_ENV['DB_USER'];
                $password = $_ENV['DB_PASSWORD'];
                $database = $_ENV['DB_DATABASE'];

                $conn = new mysqli($servername, $username, $password, $database);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $isViewed = false; 

                $sql = $conn->prepare("INSERT INTO feedback (name, phone_number, email, text, is_viewed) VALUES (?, ?, ?, ?, ?)");
                $sql->bind_param("ssssi", $name, $phoneNumber, $email, $inputText, $isViewed);

                if ($sql->execute()) {
                    echo "Data inserted into MySQL successfully.";
                } else {
                    echo "Error inserting record: " . $sql->error;
                }

                $sql->close();
                $conn->close();
            } else {
                echo "Text is empty.";
            }
        } else {
            echo "Invalid email address.";
        }
    } else {
        echo "Invalid phone number format.";
    }

} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $servername = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    $database = $_ENV['DB_DATABASE'];

    $conn = new mysqli($servername, $username, $password, $database,);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $nameFilter = "%";
    $emailFilter = "%";
    $phoneNumberFilter = "%";
    $isViewedFilter = "%";

    if (isset($_GET['name'])) {
        $nameFilter = htmlspecialchars($_GET['name']) . "%";
    }
    if (isset($_GET['email'])) {
        $emailFilter = htmlspecialchars($_GET['email']) . "%";
    }
    if (isset($_GET['phone_number'])) {
        $phoneNumberFilter = htmlspecialchars($_GET['phone_number']) . "%";
    }
    if (isset($_GET['is_viewed'])) {
        $isViewedFilter = filter_var($_GET['is_viewed'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }

    $sql = $conn->prepare("SELECT name, phone_number, email, text, is_viewed FROM feedback WHERE name LIKE ? AND email LIKE ? AND phone_number LIKE ? AND is_viewed = ?");
    $sql->bind_param("sssi", $nameFilter, $emailFilter, $phoneNumberFilter, $isViewedFilter);
    $sql->execute();
    $result = $sql->get_result();

    $feedbackData = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $feedbackData[] = $row;
        }
    }

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($feedbackData);
}
?>