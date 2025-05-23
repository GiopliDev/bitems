<?php
//php generale per funzioni comuni e ricorrenti (non messe su getCatalogo per comodita)
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
    if (!$item) return null;
    
    // Convert comma-separated strings to arrays
    $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
    
    // Get all images for this item
    $conn = connection();
    $sql = "SELECT i.img_url 
            FROM images_articoli ia 
            JOIN images i ON ia.img_id = i.img_id 
            WHERE ia.art_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Errore nella preparazione della query: " . $conn->error);
    }
    $stmt->bind_param('i', $item['art_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['img_url'];
    }
    $item['images'] = $images;
    
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
//politica di eliminazione CASCADE !!!!!!!!!
function linkImageToItem($itemId, $imageId, $conn) {
    $sql = "INSERT INTO images_articoli (art_id, img_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $itemId, $imageId);
    return $stmt->execute();
}

function getItemCardData($whereClause = '', $params = [], $types = '', $limit = null) {
    $conn = connection();
    
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
            LEFT JOIN tags tg ON ta.tag_id = tg.tag_id";
    
    if (!empty($whereClause)) {
        $sql .= " WHERE " . $whereClause;
    }
    
    $sql .= " GROUP BY articoli.art_id ORDER BY articoli.art_timestamp DESC";
    
    if ($limit !== null) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return ['error' => 'Failed to prepare statement: ' . $conn->error];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
        $items[] = $row;
    }
    
    $stmt->close();
    return $items;
}
?> 