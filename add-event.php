<?php
require './db.php';
require './helpers.php';

// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// Allow credentials to be included in the requests
header("Access-Control-Allow-Credentials: true");

// Specify the HTTP methods allowed
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Specify the headers allowed in requests
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

// Set the content type to JSON as we're outputting JSON data
header("Content-Type: application/json");

// Respond to preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Exit early so the preflight request is just the headers above
    exit;
}

//echo json_encode(["message" => "Hello, World from Php!"]);
$data = json_decode(file_get_contents("php://input"), true);
// Check for the existence of required fields, excluding 'description'
if (!empty($data['user_id']) && !empty($data['event_name']) && !empty($data['event_date']) && !empty($data['event_time'])) {
    $userId = $data['user_id'];
    $eventDate = $data['event_date'];
    $eventTime = $data['event_time'];
    $description = !empty($data['description']) ? $data['description'] : ''; // Handle optional description
    $eventName = $data['event_name'];

    $stmt = $pdo->prepare("INSERT INTO events (user_id, event_date, event_time, event_name,description) VALUES (?, ?, ?, ?,?)");

    if ($stmt->execute([$userId, $eventDate, $eventTime,$eventName,$description ])) {
        echo json_encode(['message' => 'Event added successfully']);
    } else {
        echo json_encode(['message' => 'FAILED to add event']);
        //console.log(error);
    }
} else {
    echo json_encode(['message' => 'Incomplete event details; user_id, event_date, and event_time are required']);
}
?>
