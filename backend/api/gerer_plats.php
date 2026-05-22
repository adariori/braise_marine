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

$action = $donnees['action'];

try {
    //ajout de plat
    if ($action === 'ajouter') {
        $stmt = $bdd->prepare("INSERT INTO Plat (id_categorie, nom, description, prix, image_url, est_disponible) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([
            $donnees['id_categorie'],
            $donnees['nom'],
            $donnees['description'],
            $donnees['prix'],
            $donnees['image_url']
        ]);
        echo json_encode(['succes' => true, 'id' => $bdd->lastInsertId()]);
    }

    //modifer plat
    elseif ($action === 'modifier') {
        $stmt = $bdd->prepare("UPDATE Plat SET nom = ?, description = ?, prix = ?, image_url = ?, id_categorie = ? WHERE id_plat = ?");
        $stmt->execute([
            $donnees['nom'],
            $donnees['description'],
            $donnees['prix'],
            $donnees['image_url'],
            $donnees['id_categorie'],
            $donnees['id_plat']
        ]);
        echo json_encode(['succes' => true]);
    }

    //suppression plat
    elseif ($action === 'supprimer') {
        $stmt = $bdd->prepare("DELETE FROM Plat WHERE id_plat = ?");
        $stmt->execute([$donnees['id_plat']]);
        echo json_encode(['succes' => true]);
    }

    else {
        echo json_encode(['erreur' => 'Action inconnue']);
    }

} catch (PDOException $e) {
    echo json_encode(['erreur' => $e->getMessage()]);
}
?>