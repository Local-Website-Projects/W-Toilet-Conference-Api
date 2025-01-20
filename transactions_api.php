<?php
header("Content-Type: application/json");
require_once('include/dbController.php');
$db_handle = new DBController();

$requestMethod = $_SERVER["REQUEST_METHOD"];
parse_str(file_get_contents("php://input"), $inputData);

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['tran_id'])) {
            $transactionId = $_GET['tran_id'];
            fetchTransactionById($db_handle, $transactionId);
        } else {
            fetchAllTransactions($db_handle);
        }
        break;

    case "POST":
        createTransaction($db_handle, $inputData);
        break;

    case "PUT":
        if (isset($inputData['tran_id'])) {
            updateTransaction($db_handle, $inputData);
        } else {
            echo json_encode(["error" => "Transaction ID is required for update."]);
        }
        break;

    case "DELETE":
        if (isset($inputData['tran_id'])) {
            deleteTransaction($db_handle, $inputData['tran_id']);
        } else {
            echo json_encode(["error" => "Transaction ID is required for deletion."]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

function fetchAllTransactions($db_handle) {
    $query = "SELECT * FROM transactions";
    $transactions = $db_handle->selectQuery($query);
    echo json_encode($transactions ?: []);
}

function fetchTransactionById($db_handle, $transactionId) {
    $query = "SELECT * FROM transactions WHERE tran_id = $transactionId";
    $transaction = $db_handle->selectQuery($query);
    echo json_encode($transaction ?: ["error" => "Transaction not found"]);
}

function createTransaction($db_handle, $data) {
    $query = "INSERT INTO transactions (transaction_id, invoice_id, paid_amount, store_amount, 
              bank_transaction_id, card_type, transaction_date, inserted_at) 
              VALUES (
                  '{$data['transaction_id']}', '{$data['invoice_id']}', '{$data['paid_amount']}', 
                  '{$data['store_amount']}', '{$data['bank_transaction_id']}', '{$data['card_type']}', 
                  '{$data['transaction_date']}', NOW()
              )";
    $result = $db_handle->insertQuery($query);
    if ($result) {
        echo json_encode(["success" => true, "tran_id" => $db_handle->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to create transaction"]);
    }
}

function updateTransaction($db_handle, $data) {
    $query = "UPDATE transactions SET 
              transaction_id = '{$data['transaction_id']}', 
              invoice_id = '{$data['invoice_id']}', 
              paid_amount = '{$data['paid_amount']}', 
              store_amount = '{$data['store_amount']}', 
              bank_transaction_id = '{$data['bank_transaction_id']}', 
              card_type = '{$data['card_type']}', 
              transaction_date = '{$data['transaction_date']}', 
              inserted_at = NOW() 
              WHERE tran_id = {$data['tran_id']}";
    $result = $db_handle->updateQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update transaction"]);
    }
}

function deleteTransaction($db_handle, $transactionId) {
    $query = "DELETE FROM transactions WHERE tran_id = $transactionId";
    $result = $db_handle->deleteQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete transaction"]);
    }
}
?>
