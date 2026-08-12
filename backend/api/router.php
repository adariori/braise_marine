<?php
/**
 * Front-controller unique pour tous les endpoints API.
 *
 * Le plan gratuit de Vercel limite le nombre de fonctions serverless
 * (12 sur le plan Hobby). Plutôt que de déployer chaque fichier de
 * backend/api/ comme sa propre lambda, on les fait tous passer par ce
 * routeur (voir les "routes" de vercel.json), qui se contente de
 * require() le bon fichier en fonction du nom demandé.
 *
 * Les require_once relatifs à l'intérieur de chaque fichier (ex:
 * '../../backend/config/connect.php') continuent de fonctionner
 * normalement : PHP les résout par rapport au fichier qui les contient,
 * pas par rapport à ce routeur.
 */

$allowed = [
    'accompagnements',
    'annonces',
    'associer_accompagnements',
    'commandes',
    'gerer_accompagnements',
    'gerer_plats',
    'plats',
    'reservations',
    'statistics',
    'statut',
    'upload',
];

$file = $_GET['file'] ?? '';

if (!in_array($file, $allowed, true)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['erreur' => 'Endpoint inconnu']);
    exit;
}

require __DIR__ . '/' . $file . '.php';
