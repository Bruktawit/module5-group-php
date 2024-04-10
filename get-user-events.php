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
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
if (!$userId) {
    // User not logged in. Handle this case, maybe return an error or empty events.
    echo json_encode([]);
    exit; // Stop script execution if there is no logged-in user
}

// Retrieve year and month from the query parameters
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

try {
    // Preparing SQL query using the PDO instance from db.php
    $sql = "SELECT id, event_name, event_date, event_time, description FROM events WHERE user_id = :userId AND YEAR(event_date) = :year AND MONTH(event_date) = :month";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId, 'year' => $year, 'month' => $month]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sending a JSON response
    header('Content-Type: application/json');
    echo json_encode($events);
} catch (PDOException $e) {
    // Handle error
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
