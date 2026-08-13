<?php
// Identifiants réels chargés depuis un fichier local non versionné
// (voir connect.local.php.example) ou depuis des variables d'environnement
// (Vercel, ou tout autre hébergeur). Ne jamais mettre de vrais identifiants
// en dur ici : ce fichier est public sur le dépôt GitHub.
if (file_exists(__DIR__ . '/connect.local.php')) {
    require __DIR__ . '/connect.local.php';
}

define("hostname", getenv('DB_HOST') ?: 'localhost');
define("database", getenv('DB_NAME') ?: 'braise_marine');
define("username", getenv('DB_USER') ?: 'root');
define("password", getenv('DB_PASS') ?: '');
define("dbport", getenv('DB_PORT') ?: '3306');

$dsn = 'mysql:host=' . hostname . ';port=' . dbport . ';dbname=' . database . ';charset=utf8mb4';

try {
    $bdd = new PDO($dsn, username, password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['erreur' => 'Connexion échouée : ' . $e->getMessage()]));
}
