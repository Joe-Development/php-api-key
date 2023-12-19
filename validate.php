<?php

include('config.php');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['license']) && isset($_GET['username'])) {
        $licenseKey = $_GET['license'];
        $username = $_GET['username'];
        $result = validateLicenseAndUsername($licenseKey, $username);
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        $response = ['status' => 'error', 'message' => 'License key or username not provided in the URL'];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
    header('Content-Type: application/json');
    echo json_encode($response);
}

function validateLicenseAndUsername($licenseKey, $username) {
    global $conn;

    $selectSql = "SELECT * FROM licenses WHERE license_key = ? AND username = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param('ss', $licenseKey, $username);
    $selectStmt->execute();
    $selectResult = $selectStmt->get_result();

    if ($selectResult->num_rows > 0) {
        $row = $selectResult->fetch_assoc();
        $currentTimestamp = time();
        $expirationTimestamp = strtotime($row['expiration_date']);

        if ($currentTimestamp <= $expirationTimestamp) {
            $timeLeftSeconds = $expirationTimestamp - $currentTimestamp;
            $timeLeftDays = ceil($timeLeftSeconds / (60 * 60 * 24));
            return ['status' => 'success', 'message' => 'License key is valid', 'time_left_days' => $timeLeftDays];
        } else {
            $deleteSql = "DELETE FROM licenses WHERE license_key = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param('s', $licenseKey);
            $deleteStmt->execute();

            return ['status' => 'error', 'message' => 'License key has expired and deleted'];
        }
    } else {
        return ['status' => 'error', 'message' => 'Invalid license key or username'];
    }
}




$conn->close();

?>
