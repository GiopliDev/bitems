<?php
require_once 'connection.php';

//prende immagine random per l'articolo
function getItemImage($art_id) {
    $conn = connection();
    $sql = "SELECT img.img_url 
            FROM images_articoli ia 
            JOIN images img ON ia.img_id = img.img_id 
            WHERE ia.art_id = ? 
            ORDER BY RAND() 
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $art_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row ? $row['img_url'] : null;
}

//gestishe i tag che sono concatenati su tutte le query
function processItemData($item) {
    // Convert comma-separated strings to arrays
    $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
    
    // Get a random image for the item
    if (!isset($item['image'])) {
        $item['image'] = getItemImage($item['art_id']);
    }
    
    return $item;
}

//fatto interamente con AI - implementazione FTP
function handleImageUpload($file, $conn) {
    if(!isset($file)) {
        throw new Exception('No image uploaded');
    }

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // Get file extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if(!in_array($fileExt, $allowed)) {
        throw new Exception('File type not allowed');
    }

    if($fileError !== 0) {
        throw new Exception('Error uploading file');
    }

    if($fileSize > 5000000) { // 5MB max
        throw new Exception('File too large');
    }

    // Generate unique filename
    $newFileName = uniqid('img_') . '.' . $fileExt;
    $uploadPath = '../uploadedimages/' . $newFileName;

    // Create directory if it doesn't exist
    if(!file_exists('../uploadedimages')) {
        mkdir('../uploadedimages', 0777, true);
    }

    if(move_uploaded_file($fileTmpName, $uploadPath)) {
        // Insert image record in database
        $sql = "INSERT INTO images (img_url) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $newFileName);
        
        if($stmt->execute()) {
            return [
                'image_id' => $conn->insert_id,
                'image_url' => $newFileName
            ];
        } else {
            // If database insert fails, delete the uploaded file
            unlink($uploadPath);
            throw new Exception('Failed to save image record');
        }
    } else {
        throw new Exception('Failed to upload image');
    }
}

//relazione many to many tra images e articoli per comodita
function linkImageToItem($itemId, $imageId, $conn) {
    $sql = "INSERT INTO images_articoli (art_id, img_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $itemId, $imageId);
    return $stmt->execute();
}
?> 