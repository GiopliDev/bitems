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

$conn = connection();
$userId = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(!isset($_GET['chatId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing chatId parameter']);
        exit;
    }
    
    $chatId = $_GET['chatId'];
    
    // Get all messages for the chat
    $sql = "SELECT m.mes_id, m.mes_content, m.mes_timestamp, 
            m.mes_ute_id = ? as is_mine,
            u.ute_username as sender_name
            FROM messaggi m
            JOIN utenti u ON m.mes_ute_id = u.ute_id
            WHERE m.mes_chat_id = ?
            ORDER BY m.mes_timestamp ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $userId, $chatId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while($row = $result->fetch_assoc()) {
        $messages[] = [
            'id' => $row['mes_id'],
            'text' => $row['mes_content'],
            'time' => $row['mes_timestamp'],
            'mine' => (bool)$row['is_mine'],
            'sender' => $row['sender_name']
        ];
    }
    
    echo json_encode(['success' => true, 'messages' => $messages]);
}

else if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if(!isset($data['chatId']) || !isset($data['content'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    $chatId = $data['chatId'];
    $content = $data['content'];
    
    // Insert new message
    $sql = "INSERT INTO messaggi (mes_content, mes_ute_id, mes_chat_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $content, $userId, $chatId);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'messageId' => $conn->insert_id,
        'message' => 'Message sent successfully'
    ]);
}

else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 