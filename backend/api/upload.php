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

// Sur Vercel, le filesystem est en lecture seule (sauf /tmp, qui n'est ni
// public ni persistant) : il n'y a pas de stockage de fichiers durable.
// On refuse explicitement plutôt que d'écrire un fichier qui disparaîtra
// silencieusement. Brancher un stockage externe (Vercel Blob, Cloudinary,
// S3...) pour activer l'upload en production.
if (getenv('VERCEL')) {
    http_response_code(501);
    echo json_encode(['erreur' => "Upload d'image indisponible sur cet environnement : aucun stockage de fichiers persistant n'est configuré."]);
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

// APP_BASE_PATH permet de préfixer les URLs si le site n'est pas servi à la
// racine du domaine (ex: alias Apache local "/GRTR"). Vide par défaut (Vercel).
$basePath = rtrim(getenv('APP_BASE_PATH') ?: '', '/');
$webPath = $basePath . '/backend/uploads/' . $filename;

echo json_encode([
    'succes' => true,
    'chemin' => $webPath,
    'url' => $webPath
]);
?>