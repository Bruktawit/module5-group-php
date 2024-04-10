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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($data['username']) && !empty($data['password'])) {
    $username = $data['username'];
    $password = $data['password'];

    if (checkUserExist($pdo, $username)) {
        echo json_encode(['message' => 'User already exists']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    
    if ($stmt->execute([$username, $hashedPassword])) {
        echo json_encode(['message' => 'User registered successfully']);
    } else {
        echo json_encode(['message' => 'User registration failed']);
    }
} else {
    echo json_encode(['message' => 'Invalid request']);
}
?>
