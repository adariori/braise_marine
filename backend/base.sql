-- @author    ARIORI OLOROUNKO Adéliyi Odjouola Moshood
-- @github    https://github.com/adariori
-- @web       https://portefolio-nine-iota.vercel.app/
-- @contact   adariori3@gmail.com
-- @location  Cotonou, Benin
--
-- @project   Braise Marine
-- @version   1.0.0
-- @year      2026
-- @stack     PHP, MySQL, Tailwind CSS, JavaScript
--
-- @license   Creative Commons BY-NC-ND 4.0
--            © 2026 ARIORI OLOROUNKO Adéliyi Odjouola Moshood
--            Consultation autorisée à titre de référence uniquement. Toute réutilisation commerciale ou modification est interdite.

CREATE DATABASE IF NOT EXISTS braise_marine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE braise_marine;

CREATE TABLE Categorie (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    icone VARCHAR(100)
);

CREATE TABLE Plat (
    id_plat INT AUTO_INCREMENT PRIMARY KEY,
    id_categorie INT NOT NULL,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    est_disponible TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_categorie) REFERENCES Categorie (id_categorie)
);

CREATE TABLE Accompagnement (
    id_acc INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    supplement_prix DECIMAL(10, 2) DEFAULT 0.00
);

CREATE TABLE Client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom_complet VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(150)
);

CREATE TABLE Livreur (
    id_livreur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    vehicule VARCHAR(100),
    telephone VARCHAR(20)
);

CREATE TABLE Commande (
    id_com INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_livreur INT,
    date_heure DATETIME DEFAULT CURRENT_TIMESTAMP,
    type ENUM('livraison', 'retrait') NOT NULL,
    total_prix DECIMAL(10, 2) NOT NULL,
    adresse_livraison TEXT,
    statut ENUM(
        'en_attente',
        'en_preparation',
        'en_livraison',
        'livree',
        'annulee'
    ) DEFAULT 'en_attente',
    FOREIGN KEY (id_client) REFERENCES Client (id_client),
    FOREIGN KEY (id_livreur) REFERENCES Livreur (id_livreur)
);

CREATE TABLE LigneCommande (
    id_ligne INT AUTO_INCREMENT PRIMARY KEY,
    id_com INT NOT NULL,
    id_plat INT NOT NULL,
    id_acc INT,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_com) REFERENCES Commande (id_com),
    FOREIGN KEY (id_plat) REFERENCES Plat (id_plat),
    FOREIGN KEY (id_acc) REFERENCES Accompagnement (id_acc)
);

CREATE TABLE Reservation (
    id_reser INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    date_heure DATETIME NOT NULL,
    nb_personnes INT NOT NULL,
    statut ENUM(
        'en_attente',
        'confirmee',
        'annulee'
    ) DEFAULT 'en_attente',
    commentaire TEXT,
    FOREIGN KEY (id_client) REFERENCES Client (id_client)
);

-- Table de liaison plats <-> accompagnements (utilisée par
-- backend/api/plats.php et backend/api/associer_accompagnements.php)
CREATE TABLE PlatAccompagnement (
    id_plat INT NOT NULL,
    id_acc INT NOT NULL,
    PRIMARY KEY (id_plat, id_acc),
    FOREIGN KEY (id_plat) REFERENCES Plat (id_plat),
    FOREIGN KEY (id_acc) REFERENCES Accompagnement (id_acc)
);

-- Annonces/promotions affichées sur la page d'accueil (backend/api/annonces.php)
CREATE TABLE Annonce (
    id_annonce INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    date_debut DATE,
    date_fin DATE,
    est_active TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Compte(s) admin pour le back-office (frontend/includes/login.php)
CREATE TABLE Admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO
    Categorie (libelle, icone)
VALUES ('Grillades', 'fa-fire'),
    ('Poissons', 'fa-tint'),
    ('Viandes', 'fa-cutlery');

INSERT INTO
    Accompagnement (nom, supplement_prix)
VALUES ('Alloco', 0.00),
    ('Riz blanc', 0.00),
    ('Attiéké', 2.00),
    ('Frites', 3.00),
    ('Légumes grillés', 2.50);

INSERT INTO
    Plat (
        id_categorie,
        nom,
        description,
        prix,
        image_url,
        est_disponible
    )
VALUES (
        3,
        'Poulet Braisé',
        'Accompagné d\'allocos et sauce maison.',
        15.00,
        '/frontend/assets/images/viandes/poulet-braise.svg',
        1
    ),
    (
        2,
        'Poisson Braisé',
        'Poisson frais grillé au feu de bois.',
        20.00,
        '/frontend/assets/images/poissons/poisson-braise.svg',
        1
    ),
    (
        3,
        'Côtelettes d\'Agneau',
        'Agneau tendre mariné aux herbes tropicales.',
        22.00,
        '/frontend/assets/images/viandes/agneau.svg',
        1
    ),
    (
        2,
        'Gambas Grillées',
        'Gambas géantes marinées au citron vert.',
        25.00,
        '/frontend/assets/images/poissons/gambas.svg',
        1
    ),
    (
        3,
        'Brochettes Mixtes',
        'Assortiment de viandes grillées.',
        18.00,
        '/frontend/assets/images/viandes/brochettes.svg',
        1
    );

-- Annonce de démo (bandeau promo sur la page d'accueil)
INSERT INTO
    Annonce (titre, description, image_url, date_debut, date_fin, est_active)
VALUES (
        'Soirée grillades du vendredi',
        'Menu spécial face à la mer, tous les vendredis soir.',
        '/frontend/assets/images/evenement.svg',
        NULL,
        NULL,
        1
    );

-- Chaque plat (id 1 à 5) accompagné par défaut d'Alloco et Riz blanc (id 1 et 2)
INSERT INTO
    PlatAccompagnement (id_plat, id_acc)
VALUES (1, 1), (1, 2),
    (2, 1), (2, 2),
    (3, 1), (3, 2),
    (4, 1), (4, 2),
    (5, 1), (5, 2);

-- Compte admin par défaut : identifiant "admin", mot de passe "admin123"
-- (À CHANGER après le premier déploiement — hash bcrypt de "admin123")
INSERT INTO
    Admin (username, password)
VALUES ('admin', '$2y$12$kPRPS.Ht1KGZlKjx7ZrOQuok6/Tm1/WmIEX9AJeDLcjcAiI18I5NS');