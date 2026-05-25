<?php
require_once '../../backend/config/connect.php';

header('Content-Type: application/json');

// GET : Récupère toutes les annonces actives
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM Annonce WHERE est_active = 1 AND (date_fin IS NULL OR date_fin >= CURDATE()) ORDER BY date_creation DESC";
    $annonces = $bdd->query($query)->fetchAll();
    echo json_encode($annonces);
    exit;
}

// POST : Ajouter/Modifier une annonce (admin seulement)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] === 'ajouter') {
        $stmt = $bdd->prepare("INSERT INTO Annonce (titre, description, image_url, date_debut, date_fin, est_active) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$data['titre'], $data['description'], $data['image_url'], $data['date_debut'], $data['date_fin']]);
        echo json_encode(['succes' => true, 'id' => $bdd->lastInsertId()]);
    } 
    elseif ($data['action'] === 'modifier') {
        $stmt = $bdd->prepare("UPDATE Annonce SET titre = ?, description = ?, image_url = ?, date_debut = ?, date_fin = ? WHERE id_annonce = ?");
        $stmt->execute([$data['titre'], $data['description'], $data['image_url'], $data['date_debut'], $data['date_fin'], $data['id_annonce']]);
        echo json_encode(['succes' => true]);
    } 
    elseif ($data['action'] === 'supprimer') {
        $stmt = $bdd->prepare("DELETE FROM Annonce WHERE id_annonce = ?");
        $stmt->execute([$data['id_annonce']]);
        echo json_encode(['succes' => true]);
    }
}
?>