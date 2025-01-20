<?php
header("Content-Type: application/json");
require_once('include/dbController.php');
$db_handle = new DBController();

$requestMethod = $_SERVER["REQUEST_METHOD"];
parse_str(file_get_contents("php://input"), $inputData);

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['promo_id'])) {
            $promoId = $_GET['promo_id'];
            fetchPromoById($db_handle, $promoId);
        } else {
            fetchAllPromos($db_handle);
        }
        break;

    case "POST":
        createPromo($db_handle, $inputData);
        break;

    case "PUT":
        if (isset($inputData['promo_id'])) {
            updatePromo($db_handle, $inputData);
        } else {
            echo json_encode(["error" => "Promo ID is required for update."]);
        }
        break;

    case "DELETE":
        if (isset($inputData['promo_id'])) {
            deletePromo($db_handle, $inputData['promo_id']);
        } else {
            echo json_encode(["error" => "Promo ID is required for deletion."]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

function fetchAllPromos($db_handle) {
    $query = "SELECT * FROM promo_code";
    $promos = $db_handle->runQuery($query);
    echo json_encode($promos ?: []);
}

function fetchPromoById($db_handle, $promoId) {
    $query = "SELECT * FROM promo_code WHERE promo_id = $promoId";
    $promo = $db_handle->runQuery($query);
    echo json_encode($promo ?: ["error" => "Promo not found"]);
}

function createPromo($db_handle, $data) {
    $query = "INSERT INTO promo_code (code, discount, status, inserted_at) 
              VALUES (
                  '{$data['code']}', 
                  '{$data['discount']}', 
                  '{$data['status']}', 
                  NOW()
              )";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true, "promo_id" => $db_handle->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to create promo code"]);
    }
}

function updatePromo($db_handle, $data) {
    $query = "UPDATE promo_code SET 
              code = '{$data['code']}', 
              discount = '{$data['discount']}', 
              status = '{$data['status']}' 
              WHERE promo_id = {$data['promo_id']}";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update promo code"]);
    }
}

function deletePromo($db_handle, $promoId) {
    $query = "DELETE FROM promo_code WHERE promo_id = $promoId";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete promo code"]);
    }
}
?>
