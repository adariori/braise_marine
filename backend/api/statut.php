<?php
require_once '../config/connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$donnees = json_decode(file_get_contents('php://input'), true);

if (!$donnees) {
    echo json_encode(['erreur' => 'Données invalides']);
    exit;
}

try {
    $table = $donnees['table']; 
    $id    = $donnees['id'];
    $statut = $donnees['statut'];

    $tables_autorisees = ['Commande', 'Reservation'];
    if (!in_array($table, $tables_autorisees)) {
        echo json_encode(['erreur' => 'Table non autorisée']);
        exit;
    }

    $colonne_id = $table === 'Commande' ? 'id_com' : 'id_reser';

    $stmt = $bdd->prepare("UPDATE $table SET statut = ? WHERE $colonne_id = ?");
    $stmt->execute([$statut, $id]);

    echo json_encode(['succes' => true]);

} catch (PDOException $e) {
    echo json_encode(['erreur' => $e->getMessage()]);
}
?>