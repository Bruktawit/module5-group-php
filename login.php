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
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Preflight request. Stop here and send 204 response
    http_response_code(204);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['username']) && !empty($data['password'])) {
    $username = $data['username'];
    $password = $data['password'];

    // Assuming $pdo is your PDO connection from db.php
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verifying the password
        if (password_verify($password, $user['password'])) {
            // Start session and set session variables if needed
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            echo json_encode(['message' => 'Login successful', 'user' => ['id' => $user['id'], 'username' => $user['username']]]);
            http_response_code(200);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid username or password']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid username or password']);
    }
} else {
    http_response_code(400);
    echo json_encode(['message' => 'Username and password are required']);
}
?>
