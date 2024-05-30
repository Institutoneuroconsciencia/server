<?php
// Sets the log file
$logFile = 'requests.log';

// Function to register a request
function logRequest($logFile, $requestData) {
    if ($requestData['url'] !== "https://testpaylodsnamuuu.000webhostapp.com/server.php") {
        $logEntry = date('Y-m-d H:i:s') . " - URL: " . $requestData['url'] . " - Método: " . $requestData['method'] . "\n";
        if (!empty($requestData['requestBody'])) {
            $logEntry .= "Corpo:\n" . print_r($requestData['requestBody'], true) . "\n";
        }
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}

// Function to display the log
function displayLog($logFile) {
    if (file_exists($logFile)) {
        return file_get_contents($logFile);
    } else {
        return "No logs available.";
    }
}

// Checks if the request is POST (to log a request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestBody = file_get_contents('php://input');

    // Decodes received JSON
    $requestData = json_decode($requestBody, true);

    // Checks if decoding failed, tries to decode as form data or plain text
    if (is_null($requestData)) {
        parse_str($requestBody, $parsedBody);
        $requestData = [
            'url' => $_SERVER['HTTP_REFERER'] ?? 'unknown',
            'method' => $_SERVER['REQUEST_METHOD'],
            'requestBody' => !empty($parsedBody) ? $parsedBody : $requestBody
        ];
    }

    // Register the request
    logRequest($logFile, $requestData);

    // Responds with a success message
    echo json_encode(['status' => 'success']);
} else {
    // Display the request log
    header('Content-Type: text/plain');
    echo displayLog($logFile);
}

?>
