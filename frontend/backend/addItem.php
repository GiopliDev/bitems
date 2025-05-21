<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $data = json_decode(file_get_contents('php://input'), true);
    
    if(!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data format']);
        exit;
    }

    // Check user session
    session_start();
    if(!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'User not authenticated']);
        exit;
    }
    $userId = $_SESSION['user_id'];

    $conn = connection();
    
    // Get game ID
    $gameSql = "SELECT gio_id FROM giochiaffiliati WHERE gio_nome = ?";
    $gameStmt = $conn->prepare($gameSql);
    $gameStmt->bind_param('s', $data['game']);
    $gameStmt->execute();
    $gameResult = $gameStmt->get_result();
    $gameId = $gameResult->fetch_assoc()['gio_id'];

    // Get category ID
    $catSql = "SELECT tip_id FROM tipologie WHERE tip_nome = ?";
    $catStmt = $conn->prepare($catSql);
    $catStmt->bind_param('s', $data['category']);
    $catStmt->execute();
    $catResult = $catStmt->get_result();
    $catId = $catResult->fetch_assoc()['tip_id'];

    // Insert item
    $sql = "INSERT INTO articoli (
        art_titolo,
        art_descrizione,
        art_prezzoUnitario,
        art_qtaDisp,
        art_timestamp,
        art_isPrivato,
        art_gio_id,
        art_tip_id,
        art_ute_id
    ) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $isPrivato = $data['isPrivato'] ? 1 : 0;
    $stmt->bind_param(
        'ssdiisii',
        $data['title'],
        $data['description'],
        $data['price'],
        $data['qty'],
        $isPrivato,
        $gameId,
        $catId,
        $userId
    );

    if($stmt->execute()) {
        $itemId = $conn->insert_id;

        // Handle tags
        if(!empty($data['tags'])) {
            foreach($data['tags'] as $tag) {
                // Check if tag exists
                $tagSql = "SELECT tag_id FROM tags WHERE tag_nome = ?";
                $tagStmt = $conn->prepare($tagSql);
                $tagStmt->bind_param('s', $tag);
                $tagStmt->execute();
                $tagResult = $tagStmt->get_result();
                
                if($tagResult->num_rows === 0) {
                    // Create new tag
                    $createTagSql = "INSERT INTO tags (tag_nome) VALUES (?)";
                    $createTagStmt = $conn->prepare($createTagSql);
                    $createTagStmt->bind_param('s', $tag);
                    $createTagStmt->execute();
                    $tagId = $conn->insert_id;
                } else {
                    $tagId = $tagResult->fetch_assoc()['tag_id'];
                }

                // Link tag to item
                $linkTagSql = "INSERT INTO tags_articoli (art_id, tag_id) VALUES (?, ?)";
                $linkTagStmt = $conn->prepare($linkTagSql);
                $linkTagStmt->bind_param('ii', $itemId, $tagId);
                $linkTagStmt->execute();
            }
        }

        // Handle images
        if(!empty($data['images'])) {
            foreach($data['images'] as $image) {
                // Insert image record
                $imgSql = "INSERT INTO images (img_url) VALUES (?)";
                $imgStmt = $conn->prepare($imgSql);
                $imgStmt->bind_param('s', $image['url']);
                $imgStmt->execute();
                $imgId = $conn->insert_id;

                // Link image to item
                $linkImgSql = "INSERT INTO images_articoli (art_id, img_id) VALUES (?, ?)";
                $linkImgStmt = $conn->prepare($linkImgSql);
                $linkImgStmt->bind_param('ii', $itemId, $imgId);
                $linkImgStmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Item created successfully',
            'item_id' => $itemId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create item']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>  