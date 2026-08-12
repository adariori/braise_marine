<?php
require_once '../config/connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Récupération des plats
$sql = "SELECT 
    p.id_plat AS id,
    c.libelle AS categorie,
    p.nom,
    p.description,
    p.prix,
    p.image_url AS image,
    p.est_disponible,
    p.id_categorie
FROM Plat p
JOIN Categorie c ON p.id_categorie = c.id_categorie
WHERE p.est_disponible = 1";

$stmt = $bdd->query($sql);
$plats = $stmt->fetchAll();

// Chaque plat avec ses accompagnements
foreach ($plats as &$plat) {
    $plat['id'] = (int) $plat['id'];
    $plat['prix'] = (float) $plat['prix'];
    $plat['id_categorie'] = isset($plat['id_categorie']) ? (int) $plat['id_categorie'] : 0;
    
    $sql2 = "SELECT a.id_acc, a.nom, a.supplement_prix
        FROM Accompagnement a
        JOIN PlatAccompagnement pa ON a.id_acc = pa.id_acc
        WHERE pa.id_plat = ?";

    $stmt2 = $bdd->prepare($sql2);
    $stmt2->execute([$plat['id']]);
    $plat['accompagnements'] = $stmt2->fetchAll();

    foreach ($plat['accompagnements'] as &$acc) {
        $acc['id_acc'] = (int) $acc['id_acc'];
        $acc['supplement_prix'] = (float) $acc['supplement_prix'];
    }
}

echo json_encode($plats);
?>