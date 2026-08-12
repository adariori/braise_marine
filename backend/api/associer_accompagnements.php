<?php
require_once '../config/connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$donnees = json_decode(file_get_contents('php://input'), true);

if (!$donnees) {
    echo json_encode(['succes' => false, 'erreur' => 'Données invalides']);
    exit;
}

$action = $donnees['action'];

try {
    if ($action === 'associer') {
        $stmt = $bdd->prepare("INSERT IGNORE INTO PlatAccompagnement (id_plat, id_acc) VALUES (?, ?)");
        $stmt->execute([
            intval($donnees['id_plat']),
            intval($donnees['id_accompagnement'])
        ]);
        echo json_encode(['succes' => true]);
    } 
    elseif ($action === 'dissocier') {
        $stmt = $bdd->prepare("DELETE FROM PlatAccompagnement WHERE id_plat = ? AND id_acc = ?");
        $stmt->execute([
            intval($donnees['id_plat']),
            intval($donnees['id_accompagnement'])
        ]);
        echo json_encode(['succes' => true]);
    } 
    else {
        echo json_encode(['succes' => false, 'erreur' => 'Action inconnue']);
    }
} catch (PDOException $e) {
    echo json_encode(['succes' => false, 'erreur' => $e->getMessage()]);
}
?>