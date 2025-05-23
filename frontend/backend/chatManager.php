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
    // Get all chats for the current user
    $sql = "SELECT c.cht_id, 
            CASE 
                WHEN c.cht_ute_id1 = ? THEN u2.ute_username
                ELSE u1.ute_username
            END as chat_name,
            CASE 
                WHEN c.cht_ute_id1 = ? THEN u2.ute_img_id
                ELSE u1.ute_img_id
            END as chat_avatar,
            (SELECT mes_content 
             FROM messaggi 
             WHERE mes_chat_id = c.cht_id 
             ORDER BY mes_timestamp DESC 
             LIMIT 1) as last_message,
            (SELECT mes_timestamp 
             FROM messaggi 
             WHERE mes_chat_id = c.cht_id 
             ORDER BY mes_timestamp DESC 
             LIMIT 1) as last_message_time
            FROM chats c
            JOIN utenti u1 ON c.cht_ute_id1 = u1.ute_id
            JOIN utenti u2 ON c.cht_ute_id2 = u2.ute_id
            WHERE c.cht_ute_id1 = ? OR c.cht_ute_id2 = ?
            ORDER BY last_message_time DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $userId, $userId, $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $chats = [];
    while($row = $result->fetch_assoc()) {
        $chats[] = [
            'id' => $row['cht_id'],
            'name' => $row['chat_name'],
            'avatar' => 'http://localhost:80/bitems/frontend/src/assets/images/' . $row['chat_avatar'],
            'lastMessage' => $row['last_message'] ?? 'Nessun messaggio',
            'lastMessageTime' => $row['last_message_time']
        ];
    }
    
    echo json_encode(['success' => true, 'chats' => $chats]);
}

else if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if(!isset($data['sellerId']) || !isset($data['itemId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    $sellerId = $data['sellerId'];
    $itemId = $data['itemId'];
    
    // Check if chat already exists
    $sql = "SELECT cht_id FROM chats 
            WHERE (cht_ute_id1 = ? AND cht_ute_id2 = ?) 
            OR (cht_ute_id1 = ? AND cht_ute_id2 = ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $userId, $sellerId, $sellerId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $chat = $result->fetch_assoc();
        $chatId = $chat['cht_id'];
    } else {
        // Create new chat
        $sql = "INSERT INTO chats (cht_ute_id1, cht_ute_id2) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $userId, $sellerId);
        $stmt->execute();
        $chatId = $conn->insert_id;
    }
    
    // Add initial message about the item
    $sql = "INSERT INTO messaggi (mes_content, mes_ute_id, mes_chat_id) 
            SELECT CONCAT('Ho acquistato: ', art_titolo), ?, ? 
            FROM articoli WHERE art_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $userId, $chatId, $itemId);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'chatId' => $chatId,
        'message' => 'Chat created successfully'
    ]);
}

else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 