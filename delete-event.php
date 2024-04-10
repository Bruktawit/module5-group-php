<?php
require 'db.php'; // Ensure this path is correct

// Set necessary headers for CORS
header("Access-Control-Allow-Origin: *"); // Adjust this in production for security
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Include OPTIONS
header("Access-Control-Allow-Headers: Content-Type"); // Include any other headers you're sending with your request

// Respond to preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Just send back a 200 OK response with the headers
    http_response_code(200);
    exit;
}

// Proceed with your existing logic only if the request method is not OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id'])) {
        http_response_code(400); // Bad request
        echo json_encode(['error' => 'Missing event ID']);
        exit;
    }

    $eventId = $input['id'];
    $userId = $_GET['user_id']; // Placeholder for authenticated user ID
    
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id AND user_id = :userId");
        $stmt->execute(['id' => $eventId, 'userId' => $userId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404); // Not found
            echo json_encode(['error' => 'Event not found or permission denied']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
