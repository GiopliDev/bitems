<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    if(!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if(!isset($data['itemId']) || !isset($data['rating']) || !isset($data['description'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    $conn = connection();
    $userId = $_SESSION['user_id'];
    $itemId = $data['itemId'];
    $rating = $data['rating']; // 0 or 1
    $description = $data['description'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if user has purchased the item and hasn't reviewed it yet
        $checkSql = "SELECT c.*, a.art_ute_id as seller_id 
                    FROM cronologiaacquisti c 
                    JOIN articoli a ON c.cro_art_id = a.art_id 
                    WHERE c.cro_art_id = ? AND c.cro_ute_id = ? AND c.cro_rec_id IS NULL";
        
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param('ii', $itemId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $purchase = $result->fetch_assoc();

        if(!$purchase) {
            throw new Exception('Item not purchased or already reviewed');
        }

        // Insert review
        $insertReview = "INSERT INTO recensioni (rec_art_id, rec_ute_id, rec_voto, rec_dex) 
                        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertReview);
        $stmt->bind_param('iiis', $itemId, $userId, $rating, $description);
        $stmt->execute();
        $reviewId = $conn->insert_id;

        // Update purchase history with review ID
        $updateHistory = "UPDATE cronologiaacquisti SET cro_rec_id = ? 
                         WHERE cro_art_id = ? AND cro_ute_id = ? AND cro_rec_id IS NULL";
        $stmt = $conn->prepare($updateHistory);
        $stmt->bind_param('iii', $reviewId, $itemId, $userId);
        $stmt->execute();

        // Update seller reputation
        $repChange = $rating ? 1 : -1;
        $updateRep = "UPDATE utenti SET ute_rep = ute_rep + ? WHERE ute_id = ?";
        $stmt = $conn->prepare($updateRep);
        $stmt->bind_param('ii', $repChange, $purchase['seller_id']);
        $stmt->execute();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Review added successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 