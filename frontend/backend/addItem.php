<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = $data['title'];
    $game = $data['game'];
    $otherGame = $data['otherGame'];
    $description = $data['description'];
    $qty = $data['qty'];
    $price = $data['price'];
    $images = $data['images'];
    $category = $data['category'];
    $tags = $data['tags'];
    $status = $data['status'];
}

?>  