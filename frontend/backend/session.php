<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'check';

    if ($action === 'check') {
        session_start();
        
        if (isset($_SESSION['user_id'])) {
            $isAdmin = $_SESSION['user_id'] === 1;
            echo json_encode([
                'isLoggedIn' => true,
                'userId' => $_SESSION['user_id'],
                'isAdmin' => $isAdmin
            ]);
        } else {
            echo json_encode([
                'isLoggedIn' => false,
                'isAdmin' => false
            ]);
        }
    } 
    else if ($action === 'logout') {
        session_start();
        
        // Distruggi la sessione
        session_unset();
        session_destroy();
        
        echo json_encode([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
    else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 