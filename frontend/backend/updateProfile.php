<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    if(!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'User not authenticated']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $data = json_decode(file_get_contents('php://input'), true); //dati inviati dal frontend come json questo è un test
    
    if(!$data || !isset($data['action'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data format']);
        exit;
    }

    $conn = connection();

    switch($data['action']) {
        case 'updateName':
            if(!isset($data['name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Name is required']);
                exit;
            }
            $sql = "UPDATE utenti SET ute_username = ? WHERE ute_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $data['name'], $userId);
            break;

        case 'updateDescription':
            if(!isset($data['description'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Description is required']);
                exit;
            }
            $sql = "UPDATE utenti SET ute_dex = ? WHERE ute_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $data['description'], $userId);
            break;

        case 'updateImage':
            if(!isset($data['image_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Image ID is required']);
                exit;
            }
            $sql = "UPDATE utenti SET ute_img_id = ? WHERE ute_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $data['image_id'], $userId);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            exit;
    }

    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update profile']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 