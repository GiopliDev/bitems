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
                articoli.art_id id,
                articoli.art_titolo itemName,
                articoli.art_prezzoUnitario price,
                articoli.art_qtaDisp qty,
                articoli.art_descrizione as description,
                articoli.art_timestamp createdAt,
                articoli.art_status as status,
                utenti.ute_username user,
                utenti.ute_rep userRep,
                giochiaffiliati.gio_nome gameName,
                tipologie.tip_nome category
                FROM articoli 
                INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id 
                INNER JOIN giochiaffiliati ON articoli.art_gio_id = giochiaffiliati.gio_id
                INNER JOIN tipologie ON articoli.art_tip_id = tipologie.tip_id
                WHERE articoli.art_gio_id = ? AND articoli.art_status != 'N'
                ORDER BY articoli.art_timestamp DESC
                LIMIT 4";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $game['gio_id']);
        $stmt->execute();
        $items = $stmt->get_result();
        
        $gameItems = [];
        while($item = $items->fetch_assoc()) {
            // Aggiungiamo i tag più utilizzati per questo articolo
            $item['tags'] = getTopTagsForItem($item['id']);
            $gameItems[] = $item;
        }
        
        if (!empty($gameItems)) {
            $catalogo[$game['gio_nome']] = $gameItems;
        }
    }
    
    return json_encode(['data' => $catalogo]);
}

function getCatalogoFiltrato($gameName, $category, $minPrice, $maxPrice, $onlyAvailable, $tags){
    //se onlyAvailable è true, allora art_status = 'D'
    //sull url l'array tags è di formato tags=tag1,tag2,tag3
    //quindi devo fare una query per ogni tag
    //problema: per ogni parametro devo aggiungere la sua parte nella query e poi aggiungere il bind_param
    //https://www.youtube.com/watch?v=dQw4w9WgXcQ&pp=ygULcmljayBhc3RsZXk%3D per boostare il morale 

    $conn = connection();
    
    // Base query
    $sql = "SELECT articoli.art_id id,
            articoli.art_titolo itemName,
            articoli.art_prezzoUnitario price,
            articoli.art_qtaDisp qty,
            articoli.art_descrizione as description,
            articoli.art_timestamp createdAt,
            articoli.art_status as status,
            utenti.ute_username user,
            utenti.ute_rep userRep,
            tipologie.tip_nome category,
            giochiaffiliati.gio_nome gameName
            FROM articoli 
            INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id 
            INNER JOIN giochiaffiliati ON articoli.art_gio_id = giochiaffiliati.gio_id
            INNER JOIN tipologie ON articoli.art_tip_id = tipologie.tip_id
            WHERE articoli.art_status != 'N'";
    
    $types = "";
    $values = [];
    
    // Game filter
    if (!empty($gameName)) {
        $sql .= " AND giochiaffiliati.gio_nome = ?";
        $types .= "s";
        $values[] = $gameName;
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
    
    // Category filter
    if (!empty($category)) {
        $sql .= " AND EXISTS (
            SELECT 1 FROM tipologie_articoli ta 
            INNER JOIN tipologie t ON ta.tip_id = t.tip_id 
            WHERE ta.art_id = articoli.art_id 
            AND t.tip_nome = ?
        )";
        $types .= "s";
        $values[] = $category;
    }
    
    $sql .= " ORDER BY articoli.art_timestamp DESC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($values)) {
        $stmt->bind_param($types, ...$values);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while($row = $result->fetch_assoc()) {
        $row['tags'] = getTopTagsForItem($row['id']);
        $items[] = $row;
    }
    
    return json_encode(['data' => $items]);
}

// da unire alla funzione getCatalogoFiltrato
//function getCatalogoDiUnaSezione($sezione){
//    $conn = connection();
//    $sql = "SELECT 
//            articoli.art_id id,
//            articoli.art_titolo itemName,
//            articoli.art_prezzoUnitario price,
//            articoli.art_qtaDisp qty,
//            articoli.art_descrizione description,
//            articoli.art_timestamp createdAt,
//            articoli.art_status status,
//            utenti.ute_username user,
//            utenti.ute_rep userRep,
//            giochiaffiliati.gio_nome gameName
//            FROM articoli 
//            INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id 
//            INNER JOIN giochiaffiliati ON articoli.art_gio_id = giochiaffiliati.gio_id
//            WHERE articoli.art_gio_id = ? AND articoli.art_status != 'N'
//            ORDER BY articoli.art_timestamp DESC";
//    $stmt = $conn->prepare($sql);
//    $stmt->bind_param('i', $sezione);
//    $stmt->execute();
//    $result = $stmt->get_result();
//    
//    $items = [];
//    while($row = $result->fetch_assoc()) {
//        // Aggiungiamo i tag più utilizzati per questo articolo
//        $row['tags'] = getTopTagsForItem($row['id']);
//        $items[] = $row;
//    }
//    return json_encode(['data' => $items]);
//}

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

function getTopTagsForItem($art_id) {
    $conn = connection();
    $sql = "SELECT t.tag_nome, COUNT(ta2.tag_id) as usage_count
            FROM tags_articoli ta1
            INNER JOIN tags t ON ta1.tag_id = t.tag_id
            INNER JOIN tags_articoli ta2 ON ta1.tag_id = ta2.tag_id
            WHERE ta1.art_id = ?
            GROUP BY t.tag_id, t.tag_nome
            ORDER BY usage_count DESC
            LIMIT 4";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $art_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tags = [];
    while($row = $result->fetch_assoc()) {
        $tags[] = $row['tag_nome'];
    }
    
    return $tags;
}

?>