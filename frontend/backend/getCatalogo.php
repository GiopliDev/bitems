<?php
require_once 'cors.php';
//get di tutti gli oggeti divisi in sezioni:
//calcolare quelli recenti
//calcolare quelli in trending

//e per ogni gioco affiliato nella tabella giochi creare una sezione con gli oggetti associati (PROCEDURA GET CATALOGO PRINCIPALE) 
//prenderne solo alcuni che poi nell html ci sta "mostra altro" e redirecta al catalogo solo di quella sezione

//get di tutti gli oggetti applicando una query sull url
include 'connection.php';
require_once 'utils.php';

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    //se è solo get è nell url non c'è una query mostro tutte le sezioni
    //se gameName = ? mostro tutti gli oggetti di quella sezione
    if(isset($_GET['action'])){
        if($_GET['action'] == 'getCategories'){
            echo getCategories();
        }else if($_GET['action'] == 'getGames'){
            echo getGames();
        }else if($_GET['action'] == 'getCatalogoDivisoInSezioni'){
            echo getCatalogoDivisoInSezioni();
        }else if($_GET['action'] == 'getCatalogoFiltrato'){
            //query con i filtri,se i filtri sono vuoti applicare default
            $gameName = isset($_GET['gameName']) ? $_GET['gameName'] : '';
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
            $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 20000;
            $onlyAvailable = isset($_GET['onlyAvailable']) ? $_GET['onlyAvailable'] : false;
            $tags = isset($_GET['tags']) ? $_GET['tags'] : [];
            echo getCatalogoFiltrato($gameName, $category, $minPrice, $maxPrice, $onlyAvailable, $tags);
        }else if($_GET['action'] == 'searchTags'){
            $str = isset($_GET['query']) ? $_GET['query'] : '';
            echo findTags($str);
        }else if($_GET['action'] == 'createTag'){
            $tag = isset($_GET['tag']) ? $_GET['tag'] : '';
            createTag($tag);
        }else if($_GET['action'] == 'getTrendingItems'){
            echo getTrendingItems();
        }else if($_GET['action'] == 'getRecentItems'){
            echo getRecentItems();
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
    
    // Per ogni gioco, prendiamo i primi 4 articoli
    while($game = $result->fetch_assoc()) {
        $whereClause = "articoli.art_gio_id = ? AND articoli.art_isPrivato = 0";
        $items = getItemCardData($whereClause, [$game['gio_id']], 'i', 4);
        
        if (!isset($items['error']) && !empty($items)) {
            $catalogo[$game['gio_nome']] = array_slice($items, 0, 4);
        }
    }
    
    return json_encode(['data' => $catalogo]);
}

function getCatalogoFiltrato($gameName, $category, $minPrice, $maxPrice, $onlyAvailable, $tags){
    $whereClause = [];
    $params = [];
    $types = '';
    
    // Title filter
    if (!empty($_GET['title'])) {
        $whereClause[] = "articoli.art_titolo LIKE ?";
        $params[] = "%" . $_GET['title'] . "%";
        $types .= "s";
    }
    
    // Game filter
    if (!empty($gameName)) {
        $whereClause[] = "giochiaffiliati.gio_nome = ?";
        $params[] = $gameName;
        $types .= "s";
    }
    
    // Category filter
    if (!empty($category)) {
        $whereClause[] = "tipologie.tip_nome = ?";
        $params[] = $category;
        $types .= "s";
    }
    
    // Price range filter
    $whereClause[] = "articoli.art_prezzoUnitario BETWEEN ? AND ?";
    $params[] = $minPrice;
    $params[] = $maxPrice;
    $types .= "dd";
    
    // Availability filter
    if ($onlyAvailable) {
        $whereClause[] = "articoli.art_qtaDisp > 0";
    }
    
    // Tags filter
    if (!empty($tags)) {
        $placeholders = str_repeat('?,', count($tags) - 1) . '?';
        $whereClause[] = "EXISTS (
            SELECT 1 FROM tags_articoli ta2 
            JOIN tags tg2 ON ta2.tag_id = tg2.tag_id 
            WHERE ta2.art_id = articoli.art_id 
            AND tg2.tag_nome IN ($placeholders)
        )";
        $params = array_merge($params, $tags);
        $types .= str_repeat('s', count($tags));
    }
    
    $whereClauseStr = implode(' AND ', $whereClause);
    $items = getItemCardData($whereClauseStr, $params, $types);
    
    if (isset($items['error'])) {
        return json_encode(['error' => $items['error']]);
    }
    
    return json_encode(['data' => $items]);
}

function getCatalogoRecenti(){
    $conn = connection();
    
    // Query per ottenere i 4 articoli più recenti
    $whereClause = "articoli.art_isPrivato = 0";
    $items = getItemCardData($whereClause, [], '', 4);
    
    if (isset($items['error'])) {
        return json_encode(['error' => $items['error']]);
    }
    
    // Assicuriamoci che il prezzo sia un numero
    foreach ($items as &$item) {
        $item['art_prezzoUnitario'] = floatval($item['art_prezzoUnitario']);
    }
    
    return json_encode($items);
}

function getCatalogoTrending(){
    $conn = connection();
    
    // Query per ottenere gli articoli più recensiti positivamente
    $whereClause = "articoli.art_isPrivato = 0 AND articoli.art_id IN (
        SELECT rec_art_id 
        FROM recensioni 
        WHERE rec_voto = '1'
        GROUP BY rec_art_id
        ORDER BY COUNT(*) DESC
        LIMIT 4
    )";
    
    $items = getItemCardData($whereClause, [], '', 4);
    
    if (isset($items['error'])) {
        return json_encode(['error' => $items['error']]);
    }
    
    // Assicuriamoci che il prezzo sia un numero
    foreach ($items as &$item) {
        $item['art_prezzoUnitario'] = floatval($item['art_prezzoUnitario']);
    }
    
    return json_encode($items);
}

function getTrendingItems() {
    $conn = connection();
    
    // Query per ottenere gli articoli con più ordini nell'ultima settimana
    $whereClause = "articoli.art_isPrivato = 0 AND articoli.art_id IN (
        SELECT cro_art_id 
        FROM cronologiaacquisti 
        WHERE cro_timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY cro_art_id
        ORDER BY COUNT(*) DESC
        LIMIT 4
    )";
    
    $items = getItemCardData($whereClause, [], '', 4);
    
    if (isset($items['error'])) {
        return json_encode(['error' => $items['error']]);
    }
    
    // Assicuriamoci che il prezzo sia un numero
    foreach ($items as &$item) {
        $item['art_prezzoUnitario'] = floatval($item['art_prezzoUnitario']);
    }
    
    return json_encode(['success' => true, 'items' => $items]);
}

function getRecentItems() {
    $conn = connection();
    
    // Query per ottenere i 4 articoli più recenti
    $whereClause = "articoli.art_isPrivato = 0";
    $items = getItemCardData($whereClause, [], '', 4);
    
    if (isset($items['error'])) {
        return json_encode(['error' => $items['error']]);
    }
    
    // Assicuriamoci che il prezzo sia un numero
    foreach ($items as &$item) {
        $item['art_prezzoUnitario'] = floatval($item['art_prezzoUnitario']);
    }
    
    return json_encode(['success' => true, 'items' => $items]);
}

?>