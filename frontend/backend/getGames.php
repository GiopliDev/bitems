<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $conn = connection();
    $sql = "SELECT gio_id, gio_nome FROM giochiaffiliati ORDER BY gio_nome";
    $result = $conn->query($sql);
    
    $games = [];
    while($row = $result->fetch_assoc()) {
        $games[] = [
            'id' => $row['gio_id'],
            'name' => $row['gio_nome']
        ];
    }
    
    echo json_encode(['success' => true, 'games' => $games]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 