<?php
require_once 'cors.php';
include 'connection.php';
require_once 'utils.php';

function getItem($id) {
    $conn = connection();
    
    $sql = "SELECT 
            a.*,
            g.gio_nome as game_name,
            t.tip_nome as category_name,
            u.ute_username as seller_name,
            u.ute_rep as seller_rep,
            (SELECT img.img_url 
             FROM images img 
             WHERE img.img_id = u.ute_img_id) as seller_img_url,
            GROUP_CONCAT(DISTINCT tg.tag_nome) as tags,
            GROUP_CONCAT(DISTINCT img.img_url) as images,
            (SELECT COUNT(*) FROM recensioni WHERE rec_art_id = a.art_id AND rec_voto = '1') as likes,
            (SELECT COUNT(*) FROM recensioni WHERE rec_art_id = a.art_id AND rec_voto = '0') as dislikes
            FROM articoli a
            JOIN giochiaffiliati g ON a.art_gio_id = g.gio_id
            JOIN tipologie t ON a.art_tip_id = t.tip_id
            JOIN utenti u ON a.art_ute_id = u.ute_id
            LEFT JOIN tags_articoli ta ON a.art_id = ta.art_id
            LEFT JOIN tags tg ON ta.tag_id = tg.tag_id
            LEFT JOIN images_articoli ia ON a.art_id = ia.art_id
            LEFT JOIN images img ON ia.img_id = img.img_id
            WHERE a.art_id = ?
            GROUP BY a.art_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        
        // Formatta i dati
        $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
        $item['images'] = $item['images'] ? explode(',', $item['images']) : [];
        $item['likes'] = (int)$item['likes'];
        $item['dislikes'] = (int)$item['dislikes'];
        $item['seller_img_url'] = $item['seller_img_url'] ?: 'default.jpg';
        
        // Aggiungi le recensioni
        $item['recensioni'] = json_decode(getItemRecensioni($id), true);
        
        echo json_encode(['success' => true, 'item' => $item]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Articolo non trovato']);
    }
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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID articolo richiesto']);
        exit;
    }

    $itemId = $_GET['id'];
    getItem($itemId);
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['action'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Azione richiesta']);
        exit;
    }

    switch ($_POST['action']) {
        case 'getRecentItems':
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 5;
            echo getRecentItems($limit);
            break;
        case 'getTrendingItems':
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 5;
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
?>