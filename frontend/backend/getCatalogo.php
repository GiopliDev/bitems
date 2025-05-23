<?php
require_once 'cors.php';
//get di tutti gli oggeti divisi in sezioni:
//calcolare quelli recenti
//calcolare quelli in trending

//e per ogni gioco affiliato nella tabella giochi creare una sezione con gli oggetti associati (PROCEDURA GET CATALOGO PRINCIPALE) 
//prenderne solo alcuni che poi nell html ci sta "mostra altro" e redirecta al catalogo solo di quella sezione

//get di tutti gli oggetti applicando una query sull url
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['action'])){
        switch($_GET['action']) {
            case 'getCategories':
                echo getCategories();
                break;
            case 'getGames':
                echo getGames();
                break;
            case 'getCatalogoDivisoInSezioni':
                echo getCatalogoDivisoInSezioni();
                break;
            case 'getCatalogoFiltrato':
                $gameName = $_GET['gameName'] ?? '';
                $category = $_GET['category'] ?? '';
                $minPrice = floatval($_GET['minPrice'] ?? 0);
                $maxPrice = floatval($_GET['maxPrice'] ?? 20000);
                $onlyAvailable = filter_var($_GET['onlyAvailable'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $tags = $_GET['tags'] ?? [];
                echo getCatalogoFiltrato($gameName, $category, $minPrice, $maxPrice, $onlyAvailable, $tags);
                break;
            case 'searchTags':
                $str = $_GET['query'] ?? '';
                echo findTags($str);
                break;
            case 'createTag':
                $tag = $_GET['tag'] ?? '';
                createTag($tag);
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
                break;
        }
    }
}

function findTags($str){
    $conn = connection();
    $sql = "SELECT tag_nome FROM tags WHERE tag_nome LIKE ? ORDER BY tag_nome LIMIT 10";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$str%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tags = [];
    while($row = $result->fetch_assoc()) {
        $tags[] = $row['tag_nome'];
    }

    //se non esiste ritornare un json con messaggio: "nessun tag trovato" e codice 0
    if(empty($tags)){
        return json_encode(['error' => 'nessun tag trovato', 'code' => 0]);
    }
    return json_encode($tags); //da ottimizzare e da aggiungere la possibilita di creare un nuovo tag se non esiste
}

function createTag($tag){
    $conn = connection();
    $sql = "INSERT INTO tags (tag_nome) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tag);
    $stmt->execute();
}

function getCategories(){ //prende tutte le categorie
    $conn = connection();
    $sql = "SELECT tip_id, tip_nome FROM tipologie ORDER BY tip_nome";
    $result = $conn->query($sql);
    $categories = [];
    while($row = $result->fetch_assoc()) {
        $categories[] = $row['tip_nome'];
    }
    return json_encode($categories);
}

function getGames(){ //prende tutti i giochi
    $conn = connection();
    $sql = "SELECT gio_id, gio_nome FROM giochiaffiliati ORDER BY gio_nome";
    $result = $conn->query($sql);
    $games = [];
    while($row = $result->fetch_assoc()) {
        $games[] = $row['gio_nome'];
    }
    return json_encode($games);
}

function getCatalogoDivisoInSezioni(){
    $conn = connection();
    $catalogo = [];

    // Prima prendiamo tutti i giochi
    $sql = "SELECT gio_id, gio_nome FROM giochiaffiliati ORDER BY gio_nome";
    $result = $conn->query($sql);
    
    // Per ogni gioco, prendiamo i primi 5 articoli
    while($game = $result->fetch_assoc()) {
        $sql = "SELECT 
                a.art_id,
                a.art_titolo,
                a.art_prezzoUnitario,
                a.art_qtaDisp,
                a.art_descrizione,
                a.art_timestamp,
                a.art_isPrivato,
                g.gio_nome as game_name,
                t.tip_nome as category_name,
                u.ute_username as seller_name,
                u.ute_rep as seller_rep,
                GROUP_CONCAT(DISTINCT tg.tag_nome) as tags,
                (SELECT img.img_url 
                 FROM images_articoli ia 
                 JOIN images img ON ia.img_id = img.img_id 
                 WHERE ia.art_id = a.art_id 
                 LIMIT 1) as image
                FROM articoli a
                JOIN giochiaffiliati g ON a.art_gio_id = g.gio_id
                JOIN tipologie t ON a.art_tip_id = t.tip_id
                JOIN utenti u ON a.art_ute_id = u.ute_id
                LEFT JOIN tags_articoli ta ON a.art_id = ta.art_id
                LEFT JOIN tags tg ON ta.tag_id = tg.tag_id
                WHERE a.art_gio_id = ? 
                AND a.art_isPrivato = 0
                GROUP BY a.art_id
                ORDER BY a.art_timestamp DESC
                LIMIT 5";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $game['gio_id']);
        $stmt->execute();
        $items = $stmt->get_result();
        
        $gameItems = [];
        while($item = $items->fetch_assoc()) {
            $formattedItem = [
                'art_id' => (int)$item['art_id'],
                'art_titolo' => $item['art_titolo'],
                'art_prezzoUnitario' => (float)$item['art_prezzoUnitario'],
                'art_qtaDisp' => (int)$item['art_qtaDisp'],
                'art_descrizione' => $item['art_descrizione'],
                'art_timestamp' => $item['art_timestamp'],
                'art_isPrivato' => (bool)$item['art_isPrivato'],
                'game_name' => $item['game_name'],
                'category_name' => $item['category_name'],
                'seller_name' => $item['seller_name'],
                'seller_rep' => (int)$item['seller_rep'],
                'tags' => $item['tags'] ? explode(',', $item['tags']) : [],
                'image' => $item['image'] ?: 'default.jpg'
            ];
            $gameItems[] = $formattedItem;
        }
        
        if (!empty($gameItems)) {
            $catalogo[$game['gio_nome']] = $gameItems;
        }
    }
    
    // Debug log
    error_log('Catalogo data: ' . json_encode($catalogo));
    
    return json_encode(['success' => true, 'data' => $catalogo]);
}

function getCatalogoFiltrato($gameName, $category, $minPrice, $maxPrice, $onlyAvailable, $tags){
    $conn = connection();
    
    $sql = "SELECT 
            a.art_id,
            a.art_titolo,
            a.art_prezzoUnitario,
            a.art_qtaDisp,
            a.art_descrizione,
            a.art_timestamp,
            a.art_isPrivato,
            g.gio_nome as game_name,
            t.tip_nome as category_name,
            u.ute_username as seller_name,
            u.ute_rep as seller_rep,
            GROUP_CONCAT(DISTINCT tg.tag_nome) as tags,
            (SELECT img.img_url 
             FROM images_articoli ia 
             JOIN images img ON ia.img_id = img.img_id 
             WHERE ia.art_id = a.art_id 
             LIMIT 1) as image
            FROM articoli a
            JOIN giochiaffiliati g ON a.art_gio_id = g.gio_id
            JOIN tipologie t ON a.art_tip_id = t.tip_id
            JOIN utenti u ON a.art_ute_id = u.ute_id
            LEFT JOIN tags_articoli ta ON a.art_id = ta.art_id
            LEFT JOIN tags tg ON ta.tag_id = tg.tag_id
            WHERE a.art_isPrivato = 0";
    
    $types = "";
    $values = [];
    
    if (!empty($gameName)) {
        $sql .= " AND g.gio_nome = ?";
        $types .= "s";
        $values[] = $gameName;
    }
    
    if (!empty($category)) {
        $sql .= " AND t.tip_nome = ?";
        $types .= "s";
        $values[] = $category;
    }
    
    if ($minPrice > 0) {
        $sql .= " AND a.art_prezzoUnitario >= ?";
        $types .= "d";
        $values[] = $minPrice;
    }
    
    if ($maxPrice < 20000) {
        $sql .= " AND a.art_prezzoUnitario <= ?";
        $types .= "d";
        $values[] = $maxPrice;
    }
    
    // Solo se onlyAvailable è true, filtriamo per articoli disponibili
    if ($onlyAvailable) {
        $sql .= " AND a.art_qtaDisp > 0";
    }
    
    if (!empty($tags)) {
        $tagArray = is_array($tags) ? $tags : explode(',', $tags);
        foreach ($tagArray as $tag) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM tags_articoli ta 
                INNER JOIN tags t ON ta.tag_id = t.tag_id 
                WHERE ta.art_id = a.art_id 
                AND t.tag_nome = ?
            )";
            $types .= "s";
            $values[] = $tag;
        }
    }
    
    $sql .= " GROUP BY a.art_id ORDER BY a.art_timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($values)) {
        $stmt->bind_param($types, ...$values);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while($row = $result->fetch_assoc()) {
        $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
        $row['image'] = $row['image'] ?: 'default.jpg';
        $items[] = $row;
    }
    
    return json_encode(['success' => true, 'data' => $items]);
}
?>