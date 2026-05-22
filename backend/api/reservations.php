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
    // cree client
    $stmt = $bdd->prepare("INSERT INTO Client (nom_complet, telephone) VALUES (?, ?)");
    $stmt->execute([
        $donnees['nom_complet'],
        $donnees['telephone'] ?? null
    ]);
    $id_client = $bdd->lastInsertId();

    // cree reservation
    $stmt = $bdd->prepare("INSERT INTO Reservation (id_client, date_heure, nb_personnes, commentaire) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $id_client,
        $donnees['date_heure'],
        $donnees['nb_personnes'],
        $donnees['commentaire'] ?? null
    ]);

    echo json_encode(['succes' => true]);

} catch (PDOException $e) {
    echo json_encode(['erreur' => $e->getMessage()]);
}
?>