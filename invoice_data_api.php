<?php
header("Content-Type: application/json");
require_once('include/dbController.php');
$db_handle = new DBController();

$requestMethod = $_SERVER["REQUEST_METHOD"];
parse_str(file_get_contents("php://input"), $inputData);

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['invoice_id'])) {
            $invoiceId = $_GET['invoice_id'];
            fetchInvoiceById($db_handle, $invoiceId);
        } else {
            fetchAllInvoices($db_handle);
        }
        break;

    case "POST":
        createInvoice($db_handle, $inputData);
        break;

    case "PUT":
        if (isset($inputData['invoice_id'])) {
            updateInvoice($db_handle, $inputData);
        } else {
            echo json_encode(["error" => "Invoice ID is required for update."]);
        }
        break;

    case "DELETE":
        if (isset($inputData['invoice_id'])) {
            deleteInvoice($db_handle, $inputData['invoice_id']);
        } else {
            echo json_encode(["error" => "Invoice ID is required for deletion."]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

function fetchAllInvoices($db_handle) {
    $query = "SELECT * FROM invoice_data";
    $invoices = $db_handle->selectQuery($query);
    echo json_encode($invoices ?: []);
}

function fetchInvoiceById($db_handle, $invoiceId) {
    $query = "SELECT * FROM invoice_data WHERE invoice_id = $invoiceId";
    $invoice = $db_handle->selectQuery($query);
    echo json_encode($invoice ?: ["error" => "Invoice not found"]);
}

function createInvoice($db_handle, $data) {
    $query = "INSERT INTO invoice_data (invoice_no, user_id, registratio_promo, tour_promo, items, price, grand_total, 
              inserted_at, updated_at, discount, tour_discount, payment_status) 
              VALUES (
                  '{$data['invoice_no']}', 
                  '{$data['user_id']}', 
                  '{$data['registratio_promo']}', 
                  '{$data['tour_promo']}', 
                  '{$data['items']}', 
                  '{$data['price']}', 
                  '{$data['grand_total']}', 
                  NOW(), 
                  NOW(), 
                  '{$data['discount']}', 
                  '{$data['tour_discount']}', 
                  '{$data['payment_status']}'
              )";
    $result = $db_handle->insertQuery($query);
    if ($result) {
        echo json_encode(["success" => true, "invoice_id" => $db_handle->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to create invoice"]);
    }
}

function updateInvoice($db_handle, $data) {
    $query = "UPDATE invoice_data SET 
              invoice_no = '{$data['invoice_no']}', 
              user_id = '{$data['user_id']}', 
              registratio_promo = '{$data['registratio_promo']}', 
              tour_promo = '{$data['tour_promo']}', 
              items = '{$data['items']}', 
              price = '{$data['price']}', 
              grand_total = '{$data['grand_total']}', 
              updated_at = NOW(), 
              discount = '{$data['discount']}', 
              tour_discount = '{$data['tour_discount']}', 
              payment_status = '{$data['payment_status']}'
              WHERE invoice_id = {$data['invoice_id']}";
    $result = $db_handle->updateQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update invoice"]);
    }
}

function deleteInvoice($db_handle, $invoiceId) {
    $query = "DELETE FROM invoice_data WHERE invoice_id = $invoiceId";
    $result = $db_handle->deleteQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete invoice"]);
    }
}
?>
