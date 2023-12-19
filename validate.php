<?php

$servername = "127.0.0.1:3308";
$username = "joe";
$password = "joev2";
$dbname = "api";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

function validateLicenseKey($licenseKey) {
    global $conn;

    $sql = "SELECT * FROM licenses WHERE license_key = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $licenseKey);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentTimestamp = time();
        $expirationTimestamp = strtotime($row['expiration_date']);

        if ($currentTimestamp <= $expirationTimestamp) {
            $timeLeftSeconds = $expirationTimestamp - $currentTimestamp;
            $timeLeftDays = ceil($timeLeftSeconds / (60 * 60 * 24));
            return ['status' => 'success', 'message' => 'License key is valid', 'time_left' => $timeLeftSeconds, 'time_left_days' => $timeLeftDays];
        } else {
            return ['status' => 'error', 'message' => 'License key has expired'];
        }
    } else {
        return ['status' => 'error', 'message' => 'Invalid license key'];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['license'])) {
        $licenseKey = $_GET['license'];
        $result = validateLicenseKey($licenseKey);
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        $response = ['status' => 'error', 'message' => 'License key not provided in the URL'];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
    header('Content-Type: application/json');
    echo json_encode($response);
}

$conn->close();

?>
