<?php
header("Content-Type: application/json");
require_once('include/dbController.php');
$db_handle = new DBController();

$requestMethod = $_SERVER["REQUEST_METHOD"];
parse_str(file_get_contents("php://input"), $inputData);

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['tour_code_id'])) {
            $tourCodeId = $_GET['tour_code_id'];
            fetchTourCodeById($db_handle, $tourCodeId);
        } else {
            fetchAllTourCodes($db_handle);
        }
        break;

    case "POST":
        createTourCode($db_handle, $inputData);
        break;

    case "PUT":
        if (isset($inputData['tour_code_id'])) {
            updateTourCode($db_handle, $inputData);
        } else {
            echo json_encode(["error" => "Tour Code ID is required for update."]);
        }
        break;

    case "DELETE":
        if (isset($inputData['tour_code_id'])) {
            deleteTourCode($db_handle, $inputData['tour_code_id']);
        } else {
            echo json_encode(["error" => "Tour Code ID is required for deletion."]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

function fetchAllTourCodes($db_handle) {
    $query = "SELECT * FROM tour_code";
    $tourCodes = $db_handle->runQuery($query);
    echo json_encode($tourCodes ?: []);
}

function fetchTourCodeById($db_handle, $tourCodeId) {
    $query = "SELECT * FROM tour_code WHERE tour_code_id = $tourCodeId";
    $tourCode = $db_handle->runQuery($query);
    echo json_encode($tourCode ?: ["error" => "Tour Code not found"]);
}

function createTourCode($db_handle, $data) {
    $query = "INSERT INTO tour_code (code, discount, status, inserted_at) 
              VALUES ('{$data['code']}', '{$data['discount']}', '{$data['status']}', NOW())";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true, "tour_code_id" => $db_handle->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to create tour code"]);
    }
}

function updateTourCode($db_handle, $data) {
    $query = "UPDATE tour_code SET 
              code = '{$data['code']}', 
              discount = '{$data['discount']}', 
              status = '{$data['status']}' 
              WHERE tour_code_id = {$data['tour_code_id']}";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update tour code"]);
    }
}

function deleteTourCode($db_handle, $tourCodeId) {
    $query = "DELETE FROM tour_code WHERE tour_code_id = $tourCodeId";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete tour code"]);
    }
}
?>
