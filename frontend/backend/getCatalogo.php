<?php
//get di tutti gli oggeti divisi in sezioni:
//calcolare quelli recenti
//calcolare quelli in trending

//e per ogni gioco affiliato nella tabella giochi creare una sezione con gli oggetti associati (PROCEDURA GET CATALOGO PRINCIPALE) 
//prenderne solo alcuni che poi nell html ci sta "mostra altro" e redirecta al catalogo solo di quella sezione

//get di tutti gli oggetti applicando una query sull url
include 'connection.php';
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    //se è solo get è nell url non c'è una query mostro tutte le sezioni
    //se gameName = ? mostro tutti gli oggetti di quella sezione
    if(!isset($_GET['gameName'])){
        echo getCatalogoDivisoInSezioni();
    }
    else{
        echo getCatalogoDiUnaSezione($_GET['gameName']);
    }
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
                articoli.art_descrizione description,
                articoli.art_timestamp createdAt,
                articoli.art_status status,
                utenti.ute_username user,
                utenti.ute_rep userRep,
                giochiaffiliati.gio_nome gameName
                FROM articoli 
                INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id 
                INNER JOIN giochiaffiliati ON articoli.art_gio_id = giochiaffiliati.gio_id
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

function getCatalogoDiUnaSezione($sezione){
    $conn = connection();
    $sql = "SELECT 
            articoli.art_id id,
            articoli.art_titolo itemName,
            articoli.art_prezzoUnitario price,
            articoli.art_qtaDisp qty,
            articoli.art_descrizione description,
            articoli.art_timestamp createdAt,
            articoli.art_status status,
            utenti.ute_username user,
            utenti.ute_rep userRep,
            giochiaffiliati.gio_nome gameName
            FROM articoli 
            INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id 
            INNER JOIN giochiaffiliati ON articoli.art_gio_id = giochiaffiliati.gio_id
            WHERE articoli.art_gio_id = ? AND articoli.art_status != 'N'
            ORDER BY articoli.art_timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $sezione);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while($row = $result->fetch_assoc()) {
        // Aggiungiamo i tag più utilizzati per questo articolo
        $row['tags'] = getTopTagsForItem($row['id']);
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