<?php
require_once 'cors.php';
include 'connection.php';
require_once 'utils.php';

//upload per profilo e per articolo TERMINARE LA CONNESSIONE PRIMA  !!!!IMPORTANTE!!!!
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn = connection();
        $result = handleImageUpload($_FILES['image'], $conn);
        
        echo json_encode([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'image_id' => $result['image_id'],
            'image_url' => $result['image_url']
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    $conn->close();
}
?> 