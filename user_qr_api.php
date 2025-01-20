<?php
header("Content-Type: application/json");
require_once('include/dbController.php');
$db_handle = new DBController();

$requestMethod = $_SERVER["REQUEST_METHOD"];
parse_str(file_get_contents("php://input"), $inputData);

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['qr_id'])) {
            $qrId = $_GET['qr_id'];
            fetchUserQRById($db_handle, $qrId);
        } else {
            fetchAllUserQRs($db_handle);
        }
        break;

    case "POST":
        createUserQR($db_handle, $inputData);
        break;

    case "PUT":
        if (isset($inputData['qr_id'])) {
            updateUserQR($db_handle, $inputData);
        } else {
            echo json_encode(["error" => "QR ID is required for update."]);
        }
        break;

    case "DELETE":
        if (isset($inputData['qr_id'])) {
            deleteUserQR($db_handle, $inputData['qr_id']);
        } else {
            echo json_encode(["error" => "QR ID is required for deletion."]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

function fetchAllUserQRs($db_handle) {
    $query = "SELECT * FROM user_qr";
    $userQRs = $db_handle->selectQuery($query);
    echo json_encode($userQRs ?: []);
}

function fetchUserQRById($db_handle, $qrId) {
    $query = "SELECT * FROM user_qr WHERE qr_id = $qrId";
    $userQR = $db_handle->selectQuery($query);
    echo json_encode($userQR ?: ["error" => "QR not found"]);
}

function createUserQR($db_handle, $data) {
    $query = "INSERT INTO user_qr (user_id, unique_id, file, inserted_at, updated_at) 
              VALUES ('{$data['user_id']}', '{$data['unique_id']}', '{$data['file']}', NOW(), NOW())";
    $result = $db_handle->insertQuery($query);
    if ($result) {
        echo json_encode(["success" => true, "qr_id" => $db_handle->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to create QR"]);
    }
}

function updateUserQR($db_handle, $data) {
    $query = "UPDATE user_qr SET 
              user_id = '{$data['user_id']}', 
              unique_id = '{$data['unique_id']}', 
              file = '{$data['file']}', 
              updated_at = NOW() 
              WHERE qr_id = {$data['qr_id']}";
    $result = $db_handle->updateQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update QR"]);
    }
}

function deleteUserQR($db_handle, $qrId) {
    $query = "DELETE FROM user_qr WHERE qr_id = $qrId";
    $result = $db_handle->deleteQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete QR"]);
    }
}
?>
