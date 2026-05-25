<?php
require_once '../config/connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
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
    // AJOUT d'un accompagnement
    if ($action === 'ajouter') {
        $stmt = $bdd->prepare("INSERT INTO Accompagnement (nom, supplement_prix) VALUES (?, ?)");
        $stmt->execute([
            $donnees['nom'],
            $donnees['prix_supplement']
        ]);
        echo json_encode(['succes' => true, 'id' => $bdd->lastInsertId()]);
    }
    // MODIFICATION d'un accompagnement
    elseif ($action === 'modifier') {
        $id = $donnees['id_accompagnement'];
        
        // Vérifier si l'accompagnement existe
        $stmt = $bdd->prepare("SELECT id_acc FROM Accompagnement WHERE id_acc = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() == 0) {
            echo json_encode(['succes' => false, 'erreur' => 'Accompagnement non trouvé']);
            exit;
        }
        
        $stmt = $bdd->prepare("UPDATE Accompagnement SET nom = ?, supplement_prix = ? WHERE id_acc = ?");
        $stmt->execute([
            $donnees['nom'],
            $donnees['prix_supplement'],
            $id
        ]);
        echo json_encode(['succes' => true]);
    }
    // SUPPRESSION d'un accompagnement
    elseif ($action === 'supprimer') {
        $id = $donnees['id_accompagnement'];
        
        // Vérifier si l'accompagnement existe
        $stmt = $bdd->prepare("SELECT id_acc FROM Accompagnement WHERE id_acc = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() == 0) {
            echo json_encode(['succes' => false, 'erreur' => 'Accompagnement non trouvé']);
            exit;
        }
        
        // Supprimer les associations avec les plats
        $stmt = $bdd->prepare("DELETE FROM PlatAccompagnement WHERE id_acc = ?");
        $stmt->execute([$id]);
        
        // Supprimer l'accompagnement
        $stmt = $bdd->prepare("DELETE FROM Accompagnement WHERE id_acc = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['succes' => true]);
    }
    else {
        echo json_encode(['succes' => false, 'erreur' => 'Action inconnue: ' . $action]);
    }
} catch (PDOException $e) {
    echo json_encode(['succes' => false, 'erreur' => $e->getMessage()]);
}
?>