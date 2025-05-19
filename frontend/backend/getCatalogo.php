//get di tutti gli oggeti divisi in sezioni:
//calcolare quelli recenti
//calcolare quelli in trending

//e per ogni gioco affiliato nella tabella giochi creare una sezione con gli oggetti associati (PROCEDURA GET CATALOGO PRINCIPALE) 
//prenderne solo alcuni che poi nell html ci sta "mostra altro" e redirecta al catalogo solo di quella sezione

//get di tutti gli oggetti applicando una query sull url
<?php
include 'connection.php';

function getCatalogoDivisoInSezioni(){
$conn = connection();

$sql = "SELECT * FROM items";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
return json_encode($result);
}

function getCatalogoDiUnaSezione($sezione){
    $conn = connection();

    $sql = "SELECT * FROM items WHERE giocoAppartenente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $sezione);
    $stmt->execute();
    $result = $stmt->get_result();
    return json_encode($result);
}
function getCatalogoRecenti(){
    $conn = connection();

    $sql = "SELECT * FROM items ORDER BY dataPubblicazione DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return json_encode($result);
}

function getCatalogoTrending(){ //calcolo in base al numero di acquisti al giorno
    $conn = connection();

    $sql = "SELECT * FROM items ORDER BY valutazione DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return json_encode($result);
}


?>