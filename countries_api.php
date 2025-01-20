<?php
header("Content-Type: application/json");
require_once('include/dbController.php');
$db_handle = new DBController();

$requestMethod = $_SERVER["REQUEST_METHOD"];
parse_str(file_get_contents("php://input"), $inputData);

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['id'])) {
            $countryId = $_GET['id'];
            fetchCountryById($db_handle, $countryId);
        } else {
            fetchAllCountries($db_handle);
        }
        break;

    case "POST":
        createCountry($db_handle, $inputData);
        break;

    case "PUT":
        if (isset($inputData['id'])) {
            updateCountry($db_handle, $inputData);
        } else {
            echo json_encode(["error" => "Country ID is required for update."]);
        }
        break;

    case "DELETE":
        if (isset($inputData['id'])) {
            deleteCountry($db_handle, $inputData['id']);
        } else {
            echo json_encode(["error" => "Country ID is required for deletion."]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

function fetchAllCountries($db_handle) {
    $query = "SELECT * FROM countries";
    $countries = $db_handle->selectQuery($query);
    echo json_encode($countries ?: []);
}

function fetchCountryById($db_handle, $countryId) {
    $query = "SELECT * FROM countries WHERE id = $countryId";
    $country = $db_handle->selectQuery($query);
    echo json_encode($country ?: ["error" => "Country not found"]);
}

function createCountry($db_handle, $data) {
    $query = "INSERT INTO countries (country_name, nationality) 
              VALUES ('{$data['country_name']}', '{$data['nationality']}')";
    $result = $db_handle->insertQuery($query);
    if ($result) {
        echo json_encode(["success" => true, "id" => $db_handle->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to create country"]);
    }
}

function updateCountry($db_handle, $data) {
    $query = "UPDATE countries SET 
              country_name = '{$data['country_name']}', 
              nationality = '{$data['nationality']}' 
              WHERE id = {$data['id']}";
    $result = $db_handle->updateQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update country"]);
    }
}

function deleteCountry($db_handle, $countryId) {
    $query = "DELETE FROM countries WHERE id = $countryId";
    $result = $db_handle->deleteQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete country"]);
    }
}
?>
