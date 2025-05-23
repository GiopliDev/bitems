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
        $sql = "SELECT 
                articoli.art_id,
                articoli.art_titolo,
                articoli.art_prezzoUnitario,
                articoli.art_qtaDisp,
                articoli.art_descrizione,
                articoli.art_timestamp,
                articoli.art_isPrivato,
                giochiaffiliati.gio_nome as game_name,
                tipologie.tip_nome as category_name,
                utenti.ute_username as seller_name,
                utenti.ute_rep as seller_rep,
                GROUP_CONCAT(DISTINCT tg.tag_nome) as tags,
                (SELECT img.img_url 
                 FROM images_articoli ia 
                 JOIN images img ON ia.img_id = img.img_id 
                 WHERE ia.art_id = articoli.art_id 
                 ORDER BY RAND() 
                 LIMIT 1) as image
                FROM articoli 
                INNER JOIN giochiaffiliati ON articoli.art_gio_id = giochiaffiliati.gio_id
                INNER JOIN tipologie ON articoli.art_tip_id = tipologie.tip_id
                INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id
                LEFT JOIN tags_articoli ta ON articoli.art_id = ta.art_id
                LEFT JOIN tags tg ON ta.tag_id = tg.tag_id
                WHERE articoli.art_gio_id = ? AND articoli.art_isPrivato = 0
                GROUP BY articoli.art_id
                ORDER BY articoli.art_timestamp DESC
                LIMIT 4";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $game['gio_id']);
        $stmt->execute();
        $items = $stmt->get_result();
        
        $gameItems = [];
        while($item = $items->fetch_assoc()) {
            $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
            $gameItems[] = $item;
        }
        
        if (!empty($gameItems)) {
            $catalogo[$game['gio_nome']] = $gameItems;
        }
    }
    
    return json_encode(['data' => $catalogo]);
}

function getCatalogoFiltrato($gameName, $category, $minPrice, $maxPrice, $onlyAvailable, $tags){
    $conn = connection();
    
    // Base query
    $sql = "SELECT 
            articoli.art_id,
            articoli.art_titolo,
            articoli.art_prezzoUnitario,
            articoli.art_qtaDisp,
            articoli.art_descrizione,
            articoli.art_timestamp,
            articoli.art_isPrivato,
            giochiaffiliati.gio_nome as game_name,
            tipologie.tip_nome as category_name,
            utenti.ute_username as seller_name,
            utenti.ute_rep as seller_rep,
            GROUP_CONCAT(DISTINCT tg.tag_nome) as tags,
            (SELECT img.img_url 
             FROM images_articoli ia 
             JOIN images img ON ia.img_id = img.img_id 
             WHERE ia.art_id = articoli.art_id 
             ORDER BY RAND() 
             LIMIT 1) as image
            FROM articoli 
            INNER JOIN giochiaffiliati ON articoli.art_gio_id = giochiaffiliati.gio_id
            INNER JOIN tipologie ON articoli.art_tip_id = tipologie.tip_id
            INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id
            LEFT JOIN tags_articoli ta ON articoli.art_id = ta.art_id
            LEFT JOIN tags tg ON ta.tag_id = tg.tag_id
            WHERE articoli.art_isPrivato = 0";
    
    $types = "";
    $values = [];
    
    // Game filter
    if (!empty($gameName)) {
        $sql .= " AND giochiaffiliati.gio_nome = ?";
        $types .= "s";
        $values[] = $gameName;
    }
    
    // Category filter
    if (!empty($category)) {
        $sql .= " AND tipologie.tip_nome = ?";
        $types .= "s";
        $values[] = $category;
    }
    
    // Price range filter
    if ($minPrice > 0) {
        $sql .= " AND articoli.art_prezzoUnitario >= ?";
        $types .= "d";
        $values[] = $minPrice;
    }
    
    if ($maxPrice < 20000) {
        $sql .= " AND articoli.art_prezzoUnitario <= ?";
        $types .= "d";
        $values[] = $maxPrice;
    }
    
    // Availability filter
    if ($onlyAvailable) {
        $sql .= " AND articoli.art_qtaDisp > 0";
    }
    
    // Tags filter
    if (!empty($tags)) {
        $tagArray = explode(',', $tags);
        foreach ($tagArray as $tag) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM tags_articoli ta 
                INNER JOIN tags t ON ta.tag_id = t.tag_id 
                WHERE ta.art_id = articoli.art_id 
                AND t.tag_nome = ?
            )";
            $types .= "s";
            $values[] = $tag;
        }
    }
    
    $sql .= " GROUP BY articoli.art_id ORDER BY articoli.art_timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($values)) {
        $stmt->bind_param($types, ...$values);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while($row = $result->fetch_assoc()) {
        $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
        $items[] = $row;
    }
    
    return json_encode(['data' => $items]);
}

function getCatalogoRecenti(){
    $conn = connection();

    $sql = "SELECT * FROM articoli ORDER BY art_timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return json_encode($result);
}

function getCatalogoTrending(){ //calcolo in base al numero di acquisti al giorno controllati su cronologiaacquisti
    $conn = connection();

    $sql = "SELECT * FROM articoli ORDER BY art_valutazione DESC";  
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return json_encode($result);
}

?>