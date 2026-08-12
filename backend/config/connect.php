<?php
// En local : valeurs par défaut ci-dessous.
// En production (Vercel) : Vercel n'héberge pas de MySQL, ces valeurs doivent
// être fournies via des variables d'environnement pointant vers une base
// externe (PlanetScale, Railway, Aiven, Clever Cloud...).
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
