<?php

//get oggetto in base all id

include 'connection.php';

$conn = connection();

$id = $_GET['id'];

//da fare join con il venditore per mostrare info e con recensioni per mostrare valutazione

$sql = "SELECT * FROM items WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
?>