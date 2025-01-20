<?php
header("Content-Type: application/json");
require_once('include/dbController.php');
$db_handle = new DBController();

$requestMethod = $_SERVER["REQUEST_METHOD"];
parse_str(file_get_contents("php://input"), $inputData);

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['ticket_data_id'])) {
            $ticketId = $_GET['ticket_data_id'];
            fetchTicketById($db_handle, $ticketId);
        } else {
            fetchAllTickets($db_handle);
        }
        break;

    case "POST":
        createTicket($db_handle, $inputData);
        break;

    case "PUT":
        if (isset($inputData['ticket_data_id'])) {
            updateTicket($db_handle, $inputData);
        } else {
            echo json_encode(["error" => "Ticket ID is required for update."]);
        }
        break;

    case "DELETE":
        if (isset($inputData['ticket_data_id'])) {
            deleteTicket($db_handle, $inputData['ticket_data_id']);
        } else {
            echo json_encode(["error" => "Ticket ID is required for deletion."]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

function fetchAllTickets($db_handle) {
    $query = "SELECT * FROM ticket_data";
    $tickets = $db_handle->runQuery($query);
    echo json_encode($tickets ?: []);
}

function fetchTicketById($db_handle, $ticketId) {
    $query = "SELECT * FROM ticket_data WHERE ticket_data_id = $ticketId";
    $ticket = $db_handle->runQuery($query);
    echo json_encode($ticket ?: ["error" => "Ticket not found"]);
}

function createTicket($db_handle, $data) {
    $query = "INSERT INTO ticket_data (user_id, title, first_name, surname, official_email, phone, city, nationality, gender, 
              birth_year, student, organization, designation, student_file, visa_invitation, passport_no, issue_date, 
              expire_date, passport_file, dietary, accessibility, language, tours, notification, status, inserted_at, 
              updated_at) 
              VALUES (
                  '{$data['user_id']}', '{$data['title']}', '{$data['first_name']}', '{$data['surname']}', 
                  '{$data['official_email']}', '{$data['phone']}', '{$data['city']}', '{$data['nationality']}', 
                  '{$data['gender']}', '{$data['birth_year']}', '{$data['student']}', '{$data['organization']}', 
                  '{$data['designation']}', '{$data['student_file']}', '{$data['visa_invitation']}', 
                  '{$data['passport_no']}', '{$data['issue_date']}', '{$data['expire_date']}', '{$data['passport_file']}', 
                  '{$data['dietary']}', '{$data['accessibility']}', '{$data['language']}', '{$data['tours']}', 
                  '{$data['notification']}', '{$data['status']}', NOW(), NOW()
              )";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true, "ticket_data_id" => $db_handle->lastInsertId()]);
    } else {
        echo json_encode(["error" => "Failed to create ticket"]);
    }
}

function updateTicket($db_handle, $data) {
    $query = "UPDATE ticket_data SET 
              user_id = '{$data['user_id']}', 
              title = '{$data['title']}', 
              first_name = '{$data['first_name']}', 
              surname = '{$data['surname']}', 
              official_email = '{$data['official_email']}', 
              phone = '{$data['phone']}', 
              city = '{$data['city']}', 
              nationality = '{$data['nationality']}', 
              gender = '{$data['gender']}', 
              birth_year = '{$data['birth_year']}', 
              student = '{$data['student']}', 
              organization = '{$data['organization']}', 
              designation = '{$data['designation']}', 
              student_file = '{$data['student_file']}', 
              visa_invitation = '{$data['visa_invitation']}', 
              passport_no = '{$data['passport_no']}', 
              issue_date = '{$data['issue_date']}', 
              expire_date = '{$data['expire_date']}', 
              passport_file = '{$data['passport_file']}', 
              dietary = '{$data['dietary']}', 
              accessibility = '{$data['accessibility']}', 
              language = '{$data['language']}', 
              tours = '{$data['tours']}', 
              notification = '{$data['notification']}', 
              status = '{$data['status']}', 
              updated_at = NOW() 
              WHERE ticket_data_id = {$data['ticket_data_id']}";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to update ticket"]);
    }
}

function deleteTicket($db_handle, $ticketId) {
    $query = "DELETE FROM ticket_data WHERE ticket_data_id = $ticketId";
    $result = $db_handle->executeQuery($query);
    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Failed to delete ticket"]);
    }
}
?>
