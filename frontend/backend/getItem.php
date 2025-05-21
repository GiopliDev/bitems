<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Item ID is required']);
        exit;
    }

    $itemId = $_GET['id'];
    $conn = connection();

    // Get current user ID from session
    session_start();
    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Get item data with seller info and images
    $sql = "SELECT a.*, 
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

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if(!$item) {
        http_response_code(404);
        echo json_encode(['error' => 'Item not found']);
        exit;
    }

    // Convert comma-separated strings to arrays
    $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
    $item['images'] = $item['images'] ? explode(',', $item['images']) : [];
    
    // Add isOwner flag based on user session
    $item['isOwner'] = $currentUserId && $currentUserId == $item['art_ute_id'];

    // Get reviews for the item
    $recensioni = json_decode(getItemRecensioni($itemId), true);
    $item['recensioni'] = $recensioni;

    echo json_encode([
        'success' => true,
        'item' => $item
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function getItemsByUserId($userId){
    $conn = connection();
    $sql = "SELECT articoli.art_id,
    articoli.art_titolo,
    articoli.art_qtaDisp,
    articoli.art_prezzoUnitario,
    articoli.art_descrizione,   
    articoli.art_timestamp,
    articoli.art_status,
    utenti.ute_id,
    utenti.ute_username,
    utenti.ute_rep
    FROM articoli
    INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id
    WHERE articoli.art_ute_id = ? AND articoli.isPrivato = 0";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    return json_encode($items);
}

function getItemRecensioni($id){
    $conn = connection();

    $sql = "SELECT 
                utenti.ute_id,
                utenti.ute_username,
                recensioni.*,
                DATE_FORMAT(recensioni.rec_timestamp, '%Y-%m-%d %H:%i:%s') as rec_timestamp
            FROM recensioni 
            INNER JOIN utenti on recensioni.rec_ute_id = utenti.ute_id 
            WHERE recensioni.rec_art_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recensioni = [];
    while($row = $result->fetch_assoc()) {
        $recensioni[] = $row;
    }

    return json_encode($recensioni);
}
?>