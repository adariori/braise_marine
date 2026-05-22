<?php
require_once '../config/connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Récupérer les données envoyées par le JS
$donnees = json_decode(file_get_contents('php://input'), true);

if (!$donnees) {
    echo json_encode(['erreur' => 'Données invalides']);
    exit;
}

try {
    // 1. Créer le client
    $stmt = $bdd->prepare("INSERT INTO Client (nom_complet, telephone) VALUES (?, ?)");
    $stmt->execute([
        $donnees['client']['nom_complet'],
        $donnees['client']['telephone']
    ]);
    $id_client = $bdd->lastInsertId();

    // 2. Créer la commande
    $stmt = $bdd->prepare("INSERT INTO Commande (id_client, type, total_prix, adresse_livraison) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $id_client,
        $donnees['commande']['type'],
        $donnees['commande']['total_prix'],
        $donnees['commande']['adresse_livraison'] ?? null
    ]);
    $id_commande = $bdd->lastInsertId();

    // 3. Créer chaque ligne
    foreach ($donnees['lignes'] as $ligne) {
        $stmt = $bdd->prepare("INSERT INTO LigneCommande (id_com, id_plat, id_acc, quantite, prix_unitaire) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_commande,
            $ligne['id_plat'],
            $ligne['id_acc'] ?? null,
            $ligne['quantite'],
            $ligne['prix_unitaire']
        ]);
    }

    echo json_encode([
        'succes' => true,
        'id_commande' => $id_commande
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreur' => $e->getMessage()]);
}
?>