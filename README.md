# Braise Marine 🔥🐟

Site vitrine et back-office pour un restaurant de grillades face à la mer : menu en ligne, commandes (livraison/retrait), réservations de table et espace admin de gestion.

## Stack technique

- **Backend** : PHP + PDO/MySQL, API REST maison (endpoints JSON dans `backend/api/`)
- **Frontend** : PHP (includes) + JS vanilla (`fetch`) + Tailwind CSS v4
- **Base de données** : MySQL (`backend/base.sql`)

## Structure du projet

```
GRTR
├─ backend
│  ├─ api                     # Endpoints JSON (GET/POST)
│  │  ├─ accompagnements.php
│  │  ├─ annonces.php
│  │  ├─ associer_accompagnements.php
│  │  ├─ commandes.php
│  │  ├─ gerer_accompagnements.php
│  │  ├─ gerer_plats.php
│  │  ├─ plats.php
│  │  ├─ reservations.php
│  │  ├─ statistics.php
│  │  ├─ statut.php
│  │  └─ upload.php
│  ├─ base.sql                 # Schéma + données de départ
│  ├─ config
│  │  └─ connect.php           # Connexion PDO (hôte, base, identifiants)
│  ├─ schema_base_de_donnees.svg
│  └─ uploads/                 # Images uploadées (plats, annonces)
├─ frontend
│  ├─ assets
│  │  ├─ css                   # input.css (source Tailwind) / output.css (build)
│  │  ├─ images
│  │  └─ js                    # main.js, admin.js, commande.js
│  └─ includes
│     ├─ header.php / footer.php
│     ├─ index.php             # Page d'accueil (menu, annonces, réservation)
│     ├─ commande.php          # Panier / commande client
│     ├─ login.php             # Connexion admin
│     └─ admin.php             # Back-office (plats, commandes, réservations, stats)
├─ package.json
└─ tailwind.config.js
```

## Prérequis

- PHP ≥ 8.0 avec l'extension `pdo_mysql`
- MySQL / MariaDB
- Node.js + npm (pour compiler le CSS Tailwind)

## Installation

1. **Cloner le projet et installer les dépendances front**
   ```bash
   npm install
   ```

2. **Créer la base de données**
   ```bash
   mysql -u root -p < backend/base.sql
   ```

3. **Configurer la connexion** dans `backend/config/connect.php` (hôte, nom de base, identifiants).

4. **Compiler le CSS Tailwind**
   ```bash
   npx @tailwindcss/cli -i frontend/assets/css/input.css -o frontend/assets/css/output.css --watch
   ```

5. **Servir le projet** avec un serveur PHP local (Apache/Nginx, ou pour tester rapidement) :
   ```bash
   php -S localhost:8000 -t frontend
   ```
   Puis ouvrir `http://localhost:8000/includes/index.php`.

## Fonctionnalités

### Côté client
- Menu par catégories (grillades, poissons, viandes) avec accompagnements
- Panier et validation de commande (livraison ou retrait)
- Réservation de table
- Annonces/promotions mises en avant sur la page d'accueil

### Côté admin (`login.php` / `admin.php`)
- Authentification par session PHP
- Gestion des plats (ajout, modification, suppression, disponibilité)
- Gestion des accompagnements et de leur association aux plats
- Suivi des commandes et des réservations
- Statistiques (CA, nombre de commandes, panier moyen, etc.)

## Modèle de données

Tables principales (`backend/base.sql`) : `Categorie`, `Plat`, `Accompagnement`, `Client`, `Livreur`, `Commande`, `LigneCommande`, `Reservation`.

Voir `backend/schema_base_de_donnees.svg` pour le schéma relationnel complet.

## Roadmap

Le détail des chantiers en cours (statistiques avancées, gestion des commandes/réservations côté admin, paiement Stripe, etc.) est suivi dans [`frontend/suivi.txt`](frontend/suivi.txt).
