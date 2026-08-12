<?php
require_once '../config/connect.php';
session_start();

header('Content-Type: application/json');

try {
    // Chiffre d'affaires total (commandes livrées)
    $stmt = $bdd->query("SELECT COALESCE(SUM(total_prix), 0) as ca FROM Commande WHERE statut = 'livree'");
    $ca = $stmt->fetch(PDO::FETCH_ASSOC)['ca'];

    // Nombre total de commandes
    $stmt = $bdd->query("SELECT COUNT(*) as nb FROM Commande");
    $nb_commandes = $stmt->fetch(PDO::FETCH_ASSOC)['nb'];

    // Commandes en cours (non terminées)
    $stmt = $bdd->query("SELECT COUNT(*) as nb FROM Commande WHERE statut NOT IN ('livree', 'annulee')");
    $nb_en_cours = $stmt->fetch(PDO::FETCH_ASSOC)['nb'];

    // Nombre de clients uniques
    $stmt = $bdd->query("SELECT COUNT(DISTINCT id_client) as nb FROM Commande");
    $nb_clients = $stmt->fetch(PDO::FETCH_ASSOC)['nb'];

    // Commandes aujourd'hui
    $stmt = $bdd->query("SELECT COUNT(*) as nb FROM Commande WHERE DATE(date_heure) = CURDATE()");
    $nb_jour = $stmt->fetch(PDO::FETCH_ASSOC)['nb'];

    // CA aujourd'hui
    $stmt = $bdd->query("SELECT COALESCE(SUM(total_prix), 0) as ca FROM Commande WHERE DATE(date_heure) = CURDATE() AND statut = 'livree'");
    $ca_jour = $stmt->fetch(PDO::FETCH_ASSOC)['ca'];

    // Panier moyen
    $stmt = $bdd->query("SELECT COALESCE(AVG(total_prix), 0) as panier FROM Commande");
    $panier_moyen = $stmt->fetch(PDO::FETCH_ASSOC)['panier'];

    echo json_encode([
        'chiffre_affaires' => (float)$ca,
        'nb_commandes' => (int)$nb_commandes,
        'nb_en_cours' => (int)$nb_en_cours,
        'nb_clients' => (int)$nb_clients,
        'nb_jour' => (int)$nb_jour,
        'ca_jour' => (float)$ca_jour,
        'panier_moyen' => (float)$panier_moyen
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Erreur base de données']);
}