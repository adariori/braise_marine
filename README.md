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
└─ vercel.json               # Config de déploiement Vercel
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

## Déploiement sur Vercel

Vercel ne peut exécuter que du code serverless : il n'héberge pas de MySQL et son
filesystem est en lecture seule (sauf `/tmp`, non persistant). Le projet est
adapté pour tourner dessus via le runtime communautaire [`vercel-php`](https://github.com/juicyfx/vercel-php)
(configuré dans `vercel.json`), avec les limites suivantes à connaître.

### 1. Base de données externe (obligatoire)
Provisionner une base MySQL accessible depuis Internet (ex. [PlanetScale](https://planetscale.com/),
[Railway](https://railway.app/), [Aiven](https://aiven.io/), [Clever Cloud](https://www.clever-cloud.com/)),
puis y importer `backend/base.sql`.

### 2. Variables d'environnement
Dans les réglages du projet Vercel (Settings → Environment Variables) :

| Variable       | Description                                  |
|----------------|-----------------------------------------------|
| `DB_HOST`      | Hôte de la base MySQL distante                |
| `DB_PORT`      | Port (généralement `3306`)                    |
| `DB_NAME`      | Nom de la base (`braise_marine`)              |
| `DB_USER`      | Utilisateur MySQL                             |
| `DB_PASS`      | Mot de passe MySQL                            |
| `APP_BASE_PATH`| Laisser vide (utilisé uniquement pour un déploiement sous un sous-chemin, ex. alias Apache local `/GRTR`) |

### 3. Limite connue : upload d'images
`backend/api/upload.php` refuse volontairement l'upload sur Vercel (erreur 501) plutôt que
d'écrire un fichier qui disparaîtrait au redémarrage de la fonction. Pour activer l'upload
en production il faut brancher un stockage externe persistant (ex. [Vercel Blob](https://vercel.com/docs/storage/vercel-blob),
Cloudinary, S3) — non implémenté ici.

### 4. Déployer
```bash
npm i -g vercel
vercel        # preview
vercel --prod # production
```
`vercel.json` utilise le schéma `builds`/`routes` (nécessaire car les PHP ne sont pas dans un
dossier `api/` à la racine) : il n'y a pas de build step côté Vercel, le CSS compilé
(`frontend/assets/css/output.css`) doit donc être à jour et commité **avant** de pousser —
pense à relancer `npm run build` après toute modif de `input.css`.

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

## Auteur

**ARIORI OLOROUNKO Adéliyi Odjouola Moshood**
- GitHub : [github.com/adariori](https://github.com/adariori)
- Portfolio : [portefolio-nine-iota.vercel.app](https://portefolio-nine-iota.vercel.app/)
- Contact : adariori3@gmail.com
- Lieu : Cotonou, Bénin

## Licence

`Creative Commons BY-NC-ND 4.0` — © 2026 ARIORI OLOROUNKO Adéliyi Odjouola Moshood.
Consultation autorisée à titre de référence uniquement. Toute réutilisation commerciale ou modification est interdite.
