<?php
require_once '../config/connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $sql = "SELECT id_acc, nom, supplement_prix FROM Accompagnement ORDER BY nom";
    $stmt = $bdd->query($sql);
    $accompagnements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($accompagnements as &$acc) {
        $acc['id_accompagnement'] = (int)$acc['id_acc'];
        $acc['supplement_prix'] = (float)$acc['supplement_prix'];
        unset($acc['id_acc']);
    }
    
    echo json_encode($accompagnements);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>