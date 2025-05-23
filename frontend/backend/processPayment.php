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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    if(!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if(!isset($data['itemId']) || !isset($data['quantity'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    $conn = connection();
    $userId = $_SESSION['user_id'];
    $itemId = $data['itemId'];
    $quantity = $data['quantity'];
    $FEE = 1.00; // Tassa fissa di 1 euro

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get item details, user balance and seller info
        $sql = "SELECT a.*, u.ute_saldo, a.art_ute_id as seller_id, s.ute_saldo as seller_balance 
                FROM articoli a 
                JOIN utenti u ON u.ute_id = ? 
                JOIN utenti s ON s.ute_id = a.art_ute_id
                WHERE a.art_id = ? AND a.art_isPrivato = 0";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $userId, $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if(!$data) {
            throw new Exception('Item not found or not available');
        }

        $totalPrice = $data['art_prezzoUnitario'] * $quantity;
        $sellerAmount = $totalPrice - $FEE; // Importo per il venditore (prezzo - tassa)

        // Check if user has enough balance
        if($data['ute_saldo'] < $totalPrice) {
            throw new Exception('Insufficient balance');
        }

        // Check if enough quantity is available
        if($data['art_qtaDisp'] < $quantity) {
            throw new Exception('Not enough items available');
        }

        // Update buyer balance
        $newBuyerBalance = $data['ute_saldo'] - $totalPrice;
        $updateBuyerBalance = "UPDATE utenti SET ute_saldo = ? WHERE ute_id = ?";
        $stmt = $conn->prepare($updateBuyerBalance);
        $stmt->bind_param('di', $newBuyerBalance, $userId);
        $stmt->execute();

        // Update seller balance
        $newSellerBalance = $data['seller_balance'] + $sellerAmount;
        $updateSellerBalance = "UPDATE utenti SET ute_saldo = ? WHERE ute_id = ?";
        $stmt = $conn->prepare($updateSellerBalance);
        $stmt->bind_param('di', $newSellerBalance, $data['seller_id']);
        $stmt->execute();

        // Update item quantity
        $newQty = $data['art_qtaDisp'] - $quantity;
        $updateQty = "UPDATE articoli SET art_qtaDisp = ? WHERE art_id = ?";
        $stmt = $conn->prepare($updateQty);
        $stmt->bind_param('ii', $newQty, $itemId);
        $stmt->execute();

        // Add to purchase history
        $insertHistory = "INSERT INTO cronologiaacquisti (cro_art_id, cro_ute_id, cro_qta, cro_prezzofinale) 
                         VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertHistory);
        $stmt->bind_param('iiid', $itemId, $userId, $quantity, $totalPrice);
        $stmt->execute();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Purchase completed successfully',
            'newBalance' => $newBuyerBalance,
            'fee' => $FEE,
            'sellerAmount' => $sellerAmount
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