<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'connection.php';

session_start();
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(!isset($_GET['itemId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing itemId parameter']);
        exit;
    }

    $conn = connection();
    $userId = $_SESSION['user_id'];
    $itemId = $_GET['itemId'];

    // Check if the item has been purchased by the current user
    $sql = "SELECT COUNT(*) as purchased 
            FROM cronologiaacquisti 
            WHERE cro_art_id = ? AND cro_ute_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $itemId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'purchased' => $data['purchased'] > 0
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 