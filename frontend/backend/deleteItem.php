<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if(!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Item ID is required']);
        exit;
    }

    session_start();
    if(!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $conn = connection();
    
    // Verify item ownership
    $checkSql = "SELECT art_ute_id FROM articoli WHERE art_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('i', $_GET['id']);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $item = $result->fetch_assoc();

    if(!$item || $item['art_ute_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden - Not item owner']);
        exit;
    }

    // Delete item
    $sql = "DELETE FROM articoli WHERE art_id = ? AND art_ute_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $_GET['id'], $_SESSION['user_id']);

    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete item']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 