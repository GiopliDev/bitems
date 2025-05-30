<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $newBalance = $data['balance'] ?? null;
    $userId = $data['userId'] ?? null;

    if (!$newBalance || !$userId) {
        http_response_code(400);
        echo json_encode(['error' => 'Balance and user ID are required']);
        exit();
    }

    $conn = connection();

    $sql = "UPDATE utenti SET ute_saldo = ? WHERE ute_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('di', $newBalance, $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Balance updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to update balance'
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
} 