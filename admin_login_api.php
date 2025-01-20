<?php
header("Content-Type: application/json");
require_once('include/dbController.php');
$db_handle = new DBController();

$requestMethod = $_SERVER["REQUEST_METHOD"];
parse_str(file_get_contents("php://input"), $inputData);

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['admin_id'])) {
            $adminId = $_GET['admin_id'];
            fetchAdminById($db_handle, $adminId);
        } else {
            fetchAllAdmins($db_handle);
        }
        break;

    case "POST":
        createAdmin($db_handle, $inputData);
        break;

    case "PUT":
        if (isset($inputData['admin_id'])) {
            updateAdmin($db_handle, $inputData);
        } else {
            echo json_encode(["error" => "Admin ID is required for update."]);
        }
        break;

    case "DELETE":
        if (isset($inputData['admin_id'])) {
            deleteAdmin($db_handle, $inputData['admin_id']);
        } else {
            echo json_encode(["error" => "Admin ID is required for deletion."]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

function fetchAllAdmins($db_handle) {
    $query = "SELECT * FROM admin_login";
    $admins = $db_handle->runQuery($query);
    echo json_encode($admins ?: []);
}

function fetchAdminById($db_handle, $adminId) {
    $query = "SELECT * FROM admin_login WHERE admin_id = $adminId";
    $admin = $db_handle->runQuery($query);
    echo json_encode($admin ?: ["error" => "Admin not found"]);
}

function createAdmin($db_handle, $data) {
    $query = "INSERT INTO admin_login (admin_name, admin_email, admin_password) 
              VALUES ('{$data['admin_name']}', '{$data['admin_email']}', '{$data['admin_password']}')";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true, "admin_id" => $db_handle->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to create admin"]);
    }
}

function updateAdmin($db_handle, $data) {
    $query = "UPDATE admin_login SET 
              admin_name = '{$data['admin_name']}', 
              admin_email = '{$data['admin_email']}', 
              admin_password = '{$data['admin_password']}'
              WHERE admin_id = {$data['admin_id']}";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update admin"]);
    }
}

function deleteAdmin($db_handle, $adminId) {
    $query = "DELETE FROM admin_login WHERE admin_id = $adminId";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete admin"]);
    }
}
?>
