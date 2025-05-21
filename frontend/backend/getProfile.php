<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['id'] ?? null;

    if (!$userId) {
        http_response_code(400);
        echo json_encode(['error' => 'User ID is required']);
        exit();
    }

    $conn = connection();

    // Get profile data with image URL
    $sql = "SELECT u.*, i.img_url as ute_pfpUrl 
            FROM utenti u 
            LEFT JOIN images i ON u.ute_img_id = i.img_id 
            WHERE u.ute_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();

    if (!$profile) {
        http_response_code(404);
        echo json_encode(['error' => 'Profile not found']);
        exit();
    }

    // Get degli oggetti di un utente GROUP_CONCAT unisce i valori di una colonna in una stringa
    $itemsSql = "SELECT a.*, g.gio_nome as game_name, t.tip_nome as category_name,
                        GROUP_CONCAT(DISTINCT tg.tag_nome) as tags,
                        GROUP_CONCAT(DISTINCT i.img_url) as images
                 FROM articoli a
                 LEFT JOIN giochiaffiliati g ON a.art_gio_id = g.gio_id
                 LEFT JOIN tipologie t ON a.art_tip_id = t.tip_id
                 LEFT JOIN tags_articoli ta ON a.art_id = ta.art_id
                 LEFT JOIN tags tg ON ta.tag_id = tg.tag_id
                 LEFT JOIN images_articoli ia ON a.art_id = ia.art_id
                 LEFT JOIN images i ON ia.img_id = i.img_id
                 WHERE a.art_ute_id = ?
                 GROUP BY a.art_id";
    $itemsStmt = $conn->prepare($itemsSql);
    $itemsStmt->bind_param('i', $userId);
    $itemsStmt->execute();
    $itemsResult = $itemsStmt->get_result();
    
    $items = [];
    while ($item = $itemsResult->fetch_assoc()) {
        // Convert comma-separated strings to arrays
        $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
        $item['images'] = $item['images'] ? explode(',', $item['images']) : [];
        $items[] = $item;
    }

    echo json_encode([
        'success' => true,
        'profile' => $profile,
        'items' => $items
    ]);

    $stmt->close();
    $itemsStmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
} 