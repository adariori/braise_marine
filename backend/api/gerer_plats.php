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
    // AJOUT d'un plat
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
    // MODIFICATION d'un plat
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
    // SUPPRESSION d'un plat
    elseif ($action === 'supprimer') {
        $id_plat = $donnees['id_plat'];
        
        // Vérifier si le plat existe
        $stmt = $bdd->prepare("SELECT id_plat FROM Plat WHERE id_plat = ?");
        $stmt->execute([$id_plat]);
        if ($stmt->rowCount() == 0) {
            echo json_encode(['succes' => false, 'erreur' => 'Plat non trouvé']);
            exit;
        }
        
        // Supprimer les associations avec les accompagnements
        $stmt = $bdd->prepare("DELETE FROM PlatAccompagnement WHERE id_plat = ?");
        $stmt->execute([$id_plat]);
        
        // Supprimer le plat
        $stmt = $bdd->prepare("DELETE FROM Plat WHERE id_plat = ?");
        $stmt->execute([$id_plat]);
        
        echo json_encode(['succes' => true]);
    }
    else {
        echo json_encode(['succes' => false, 'erreur' => 'Action inconnue']);
    }
} catch (PDOException $e) {
    echo json_encode(['succes' => false, 'erreur' => $e->getMessage()]);
}
?>