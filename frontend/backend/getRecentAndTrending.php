<?php
require_once 'cors.php';
require_once 'getCatalogo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['action'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Azione richiesta']);
        exit;
    }

    switch ($_POST['action']) {
        case 'getRecentItems':
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 4;
            echo getRecentItems($limit);
            break;
        case 'getTrendingItems':
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 4;
            echo getTrendingItems($limit);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Azione non valida']);
            break;
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
}

function getRecentItems($limit = 4) {
    $conn = connection();
    
    // Prima prendiamo gli ID degli articoli più recenti
    $sql = "SELECT art_id 
            FROM articoli 
            WHERE art_isPrivato = 0 
            ORDER BY art_timestamp DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $itemIds = [];
    while ($row = $result->fetch_assoc()) {
        $itemIds[] = $row['art_id'];
    }
    
    if (empty($itemIds)) {
        return json_encode(['success' => true, 'data' => []]);
    }
    
    // Ora usiamo getCatalogoFiltrato per ottenere i dettagli completi
    $items = getCatalogoFiltrato('', '', 0, $limit, false, [], $itemIds);
    
    return $items;
}

function getTrendingItems($limit = 4) {
    $conn = connection();
    
    // Prima prendiamo gli ID degli articoli più venduti nell'ultima settimana
    $sql = "SELECT cro_art_id, COUNT(*) as total_orders 
            FROM cronologiaacquisti 
            WHERE cro_timestamp >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
            GROUP BY cro_art_id 
            ORDER BY total_orders DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $itemIds = [];
    while ($row = $result->fetch_assoc()) {
        $itemIds[] = $row['cro_art_id'];
    }
    
    if (empty($itemIds)) {
        return json_encode(['success' => true, 'data' => []]);
    }
    
    // Ora usiamo getCatalogoFiltrato per ottenere i dettagli completi
    $items = getCatalogoFiltrato('', '', 0, $limit, false, [], $itemIds);
    
    return $items;
} 