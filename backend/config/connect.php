<?php
define("hostname", "localhost");
define("database", "braise_marine");
define("username", "root");
define("password", "");

$dsn = 'mysql:dbname=' . database . ';host=' . hostname . ';charset=utf8';

try {
    $bdd = new PDO($dsn, username, password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode(['erreur' => 'Connexion échouée : ' . $e->getMessage()]));
}
