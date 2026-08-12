<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erreur' => 'Méthode non autorisée']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Aucun fichier ou erreur d\'upload']);
    exit;
}

$file = $_FILES['image'];
$maxSize = 10 * 1024 * 1024;
$allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Fichier trop volumineux (max 10MB)']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Format non autorisé']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_', true) . '.' . $extension;
$filepath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Erreur lors de la sauvegarde']);
    exit;
}

$webPath = '/GRTR/backend/uploads/' . $filename;

echo json_encode([
    'succes' => true,
    'chemin' => $webPath,
    'url' => $webPath
]);
?>