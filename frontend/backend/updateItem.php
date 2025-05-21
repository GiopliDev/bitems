<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);
    
    if(!isset($data['id'])) {
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
    $checkStmt->bind_param('i', $data['id']);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $item = $result->fetch_assoc();

    if(!$item || $item['art_ute_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden - Not item owner']);
        exit;
    }

    // Update item
    $sql = "UPDATE articoli SET 
            art_titolo = ?,
            art_descrizione = ?,
            art_prezzoUnitario = ?,
            art_qtaDisp = ?,
            art_isPrivato = ?
            WHERE art_id = ? AND art_ute_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssdiisi',
        $data['title'],
        $data['description'],
        $data['price'],
        $data['qty'],
        $data['isPrivato'],
        $data['id'],
        $_SESSION['user_id']
    );

    if($stmt->execute()) {
        // Get updated item data
        $getSql = "SELECT a.*, 
                  g.gio_nome as game_name, 
                  t.tip_nome as category_name,
                  u.ute_username as seller_name,
                  u.ute_rep as seller_rep,
                  u.ute_img_id as seller_img_id,
                  i.img_url as seller_img_url,
                  GROUP_CONCAT(DISTINCT tg.tag_nome) as tags,
                  GROUP_CONCAT(DISTINCT img.img_url) as images
                  FROM articoli a
                  LEFT JOIN giochiaffiliati g ON a.art_gio_id = g.gio_id
                  LEFT JOIN tipologie t ON a.art_tip_id = t.tip_id
                  LEFT JOIN utenti u ON a.art_ute_id = u.ute_id
                  LEFT JOIN images i ON u.ute_img_id = i.img_id
                  LEFT JOIN tags_articoli ta ON a.art_id = ta.art_id
                  LEFT JOIN tags tg ON ta.tag_id = tg.tag_id
                  LEFT JOIN images_articoli ia ON a.art_id = ia.art_id
                  LEFT JOIN images img ON ia.img_id = img.img_id
                  WHERE a.art_id = ?
                  GROUP BY a.art_id";

        $getStmt = $conn->prepare($getSql);
        $getStmt->bind_param('i', $data['id']);
        $getStmt->execute();
        $result = $getStmt->get_result();
        $updatedItem = $result->fetch_assoc();

        // Convert comma-separated strings to arrays
        $updatedItem['tags'] = $updatedItem['tags'] ? explode(',', $updatedItem['tags']) : [];
        $updatedItem['images'] = $updatedItem['images'] ? explode(',', $updatedItem['images']) : [];
        $updatedItem['isOwner'] = true;

        echo json_encode([
            'success' => true,
            'item' => $updatedItem
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update item']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 