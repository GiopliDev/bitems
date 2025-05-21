<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    session_start();
    if(!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $conn = connection();
    $userId = $_SESSION['user_id'];

    $sql = "SELECT ute_saldo FROM utenti WHERE ute_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if(!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'balance' => $user['ute_saldo']
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 