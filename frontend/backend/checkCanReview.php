<?php

include 'connection.php';
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    session_start();
    if(!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    if(!isset($_GET['itemId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Item ID is required']);
        exit;
    }

    $conn = connection();
    $userId = $_SESSION['user_id'];
    $itemId = $_GET['itemId'];

    // Check if user has purchased the item and hasn't reviewed it yet
    $sql = "SELECT 1 FROM cronologiaacquisti 
            WHERE cro_art_id = ? AND cro_ute_id = ? AND cro_rec_id IS NULL";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $itemId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo json_encode([
        'success' => true,
        'canReview' => $result->num_rows > 0
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 