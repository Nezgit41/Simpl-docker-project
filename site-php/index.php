// site-php/index.php
<?php
include_once __DIR__ . '/logging.php';

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

log_message("Request URI: $request, Method: $method");

switch ($request) {
    case '/':
    case '/index.php':
        if ($method == 'POST') {
            require __DIR__ . '/site/feedback_form/index.php';
        } else if ($method == 'GET') {
            require __DIR__ . '/site/show_sqlBase/index.php';
        }
        break;

    case '/site/feedback_form/':
        if ($method == 'POST') {
            require __DIR__ . '/site/feedback_form/index.php';
        }
        break;

    case '/site/show_sqlBase/':
        if ($method == 'GET') {
            require __DIR__ . '/site/show_sqlBase/index.php';
        }
        break;

    default:
        http_response_code(404);
        log_message("404 Not Found: $request");
        echo "404 Not Found";
        break;
}
?>