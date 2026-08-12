<?php
session_start();

// Si non connecté, dirige vers login
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../../backend/config/connect.php';

// Récupération des commandes
$commandes = $bdd->query("
    SELECT c.id_com, c.date_heure, c.type, c.total_prix, c.statut, c.adresse_livraison,
           cl.nom_complet, cl.telephone
    FROM Commande c
    JOIN Client cl ON c.id_client = cl.id_client
    ORDER BY c.date_heure DESC
")->fetchAll();

// Récupération des réservations
$reservations = $bdd->query("
    SELECT r.id_reser, r.date_heure, r.nb_personnes, r.statut, r.commentaire,
           cl.nom_complet, cl.telephone
    FROM Reservation r
    JOIN Client cl ON r.id_client = cl.id_client
    ORDER BY r.date_heure DESC
")->fetchAll();

// Récupération des catégories pour les plats
$categories = $bdd->query("SELECT * FROM Categorie")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title>Admin | Braise Marine</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>

<body class="antialiased bg-sand text-charcoal">

    <header class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <span class="text-xl font-bold text-tropical-green font-display">
                Braise<span class="text-braise">Marine</span> <span class="text-gray-400 text-sm font-normal">— Admin</span>
            </span>
            <a href="login.php?logout=1" class="text-red-500 font-medium hover:text-red-600 transition flex items-center gap-2">
                <i class="fas fa-sign-out-alt"></i>Se déconnecter
            </a>
        </nav>
    </header>

    <!-- Layout avec sidebar collée à droite -->
    <div class="flex min-h-screen">

        <!-- SIDEBAR - Collée à droite sur toute la hauteur -->
        <aside class="w-80 bg-white border-l border-gray-200 shadow-soft sticky top-0 h-screen overflow-y-auto">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-tropical-green flex items-center justify-center">
                        <i class="fas fa-bolt text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-charcoal text-sm font-display">Menu Admin</h3>
                        <p class="text-xs text-gray-500">Gestion complète</p>
                    </div>
                </div>
            </div>

            <ul class="p-3 space-y-1">
                <li>
                    <button onclick="afficherSection('statistiques')"
                        class="nav-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 text-gray-700 hover:text-braise hover:bg-orange-50 group">
                        <i class="fas fa-chart-bar text-braise w-5 text-center"></i>
                        <span class="font-medium text-sm">Tableau de bord</span>
                    </button>
                </li>
                <li>
                    <button onclick="afficherSection('commandes')"
                        class="nav-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 text-gray-700 hover:text-braise hover:bg-orange-50 group">
                        <i class="fas fa-receipt text-braise w-5 text-center"></i>
                        <span class="font-medium text-sm">Commandes</span>
                    </button>
                </li>
                <li>
                    <button onclick="afficherSection('reservations')"
                        class="nav-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 text-gray-700 hover:text-braise hover:bg-orange-50 group">
                        <i class="fas fa-calendar-check text-braise w-5 text-center"></i>
                        <span class="font-medium text-sm">Réservations</span>
                    </button>
                </li>
                <li>
                    <button onclick="afficherSection('plats')"
                        class="nav-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 text-gray-700 hover:text-braise hover:bg-orange-50 group">
                        <i class="fas fa-utensils text-braise w-5 text-center"></i>
                        <span class="font-medium text-sm">Carte & Plats</span>
                    </button>
                </li>
                <li>
                    <button onclick="afficherSection('accompagnements')"
                        class="nav-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 text-gray-700 hover:text-braise hover:bg-orange-50 group">
                        <i class="fas fa-carrot text-braise w-5 text-center"></i>
                        <span class="font-medium text-sm">Accompagnements</span>
                    </button>
                </li>
                <li>
                    <button onclick="afficherSection('annonces')"
                        class="nav-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center gap-3 text-gray-700 hover:text-braise hover:bg-orange-50 group">
                        <i class="fas fa-bullhorn text-braise w-5 text-center"></i>
                        <span class="font-medium text-sm">Annonces</span>
                    </button>
                </li>
            </ul>

            <div class="p-4 border-t border-gray-100 mt-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-tropical-green flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-700">Administrateur</p>
                        <p class="text-xs text-gray-500">Connecté</p>
                    </div>
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                </div>
            </div>
        </aside>

        <!-- CONTENU PRINCIPAL  -->
        <main class="flex-1 px-6 py-8">

            <!-- SECTION STATISTIQUES (ACCUEIL PAR DÉFAUT - EN PREMIER) -->
            <section id="section-statistiques" class="section-content">
                <h2 class="text-3xl font-bold text-tropical-green mb-6 font-display">Statistiques</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <div class="bg-white rounded-super shadow-card border border-gray-100 p-6 hover:shadow-card-hover transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Chiffre d'affaires</p>
                                <p class="text-3xl font-bold text-braise" id="stat-ca">0 FCFA</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                                <i class="fas fa-coins text-braise"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-super shadow-card border border-gray-100 p-6 hover:shadow-card-hover transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Commandes totales</p>
                                <p class="text-3xl font-bold text-tropical-green" id="stat-commandes">0</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-tropical-green"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-super shadow-card border border-gray-100 p-6 hover:shadow-card-hover transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">En cours</p>
                                <p class="text-3xl font-bold text-orange-500" id="stat-en-cours">0</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                                <i class="fas fa-clock text-orange-500"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-super shadow-card border border-gray-100 p-6 hover:shadow-card-hover transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">Clients uniques</p>
                                <p class="text-3xl font-bold text-purple-600" id="stat-clients">0</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-super shadow-card border border-gray-100 p-6">
                        <p class="text-gray-500 text-sm mb-2">Commandes aujourd'hui</p>
                        <p class="text-2xl font-bold text-tropical-green" id="stat-jour">0</p>
                    </div>
                    <div class="bg-white rounded-super shadow-card border border-gray-100 p-6">
                        <p class="text-gray-500 text-sm mb-2">CA aujourd'hui</p>
                        <p class="text-2xl font-bold text-braise" id="stat-ca-jour">0 FCFA</p>
                    </div>
                    <div class="bg-white rounded-super shadow-card border border-gray-100 p-6">
                        <p class="text-gray-500 text-sm mb-2">Panier moyen</p>
                        <p class="text-2xl font-bold text-green-600" id="stat-panier">0 FCFA</p>
                    </div>
                </div>
            </section>

            <!-- SECTION COMMANDES -->
            <section id="section-commandes" class="section-content hidden">
                <h2 class="text-3xl font-bold text-tropical-green mb-6 font-display">Commandes</h2>

                <div class="hidden md:block bg-white rounded-super shadow-card border border-gray-100 overflow-x-auto mb-12">
                    <table class="w-full text-sm min-w-[800px]">
                        <thead class="bg-stone-50 text-gray-600 font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Client</th>
                                <th class="px-6 py-4 text-left">Téléphone</th>
                                <th class="px-6 py-4 text-left">Type</th>
                                <th class="px-6 py-4 text-left">Total</th>
                                <th class="px-6 py-4 text-left">Statut</th>
                                <th class="px-6 py-4 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($commandes as $cmd) : ?>
                                <tr class="hover:bg-stone-50 transition">
                                    <td class="px-6 py-4 font-medium">#<?php echo (int)$cmd['id_com'] ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($cmd['nom_complet'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($cmd['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $cmd['type'] === 'livraison' ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' ?>">
                                            <?php echo htmlspecialchars($cmd['type'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-braise"><?php echo number_format($cmd['total_prix'], 0, ',', ' ') ?> FCFA</td>
                                    <td class="px-6 py-4">
                                        <select onchange="changerStatut('Commande', <?php echo (int)$cmd['id_com'] ?>, this.value, this)"
                                            class="border border-gray-200 rounded-xl px-3 py-1 text-sm focus:border-braise focus:ring-1 focus:ring-braise outline-none bg-white transition">
                                            <?php foreach (['en_attente', 'en_preparation', 'en_livraison', 'livree', 'annulee'] as $s) : ?>
                                                <option value="<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" <?php echo $cmd['statut'] === $s ? 'selected' : '' ?>>
                                                    <?php echo htmlspecialchars(str_replace('_', ' ', $s), ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs"><?php echo htmlspecialchars($cmd['date_heure'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4 mb-12">
                    <?php foreach ($commandes as $cmd) : ?>
                        <div class="bg-white rounded-super shadow-card border border-gray-100 p-5">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="font-bold text-lg text-tropical-green font-display">#<?php echo (int)$cmd['id_com'] ?></h3>
                                <span class="text-xs bg-stone-100 text-gray-600 px-3 py-1 rounded-full"><?php echo htmlspecialchars($cmd['date_heure'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Client</span>
                                    <span class="font-medium"><?php echo htmlspecialchars($cmd['nom_complet'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Téléphone</span>
                                    <span><?php echo htmlspecialchars($cmd['telephone'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Type</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo $cmd['type'] === 'livraison' ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' ?>"><?php echo htmlspecialchars($cmd['type'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Total</span>
                                    <span class="font-bold text-braise"><?php echo number_format($cmd['total_prix'], 0, ',', ' ') ?> FCFA</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Statut</label>
                                <select onchange="changerStatut('Commande', <?php echo (int)$cmd['id_com'] ?>, this.value, this)"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-braise focus:ring-1 focus:ring-braise outline-none bg-white transition">
                                    <?php foreach (['en_attente', 'en_preparation', 'en_livraison', 'livree', 'annulee'] as $s) : ?>
                                        <option value="<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" <?php echo $cmd['statut'] === $s ? 'selected' : '' ?>>
                                            <?php echo htmlspecialchars(str_replace('_', ' ', $s), ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- SECTION RÉSERVATIONS -->
            <section id="section-reservations" class="section-content hidden">
                <h2 class="text-3xl font-bold text-tropical-green mb-6 font-display">Réservations</h2>

                <div class="hidden md:block bg-white rounded-super shadow-card border border-gray-100 overflow-x-auto mb-12">
                    <table class="w-full text-sm min-w-[800px]">
                        <thead class="bg-stone-50 text-gray-600 font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Client</th>
                                <th class="px-6 py-4 text-left">Téléphone</th>
                                <th class="px-6 py-4 text-left">Personnes</th>
                                <th class="px-6 py-4 text-left">Statut</th>
                                <th class="px-6 py-4 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($reservations as $res) : ?>
                                <tr class="hover:bg-stone-50 transition">
                                    <td class="px-6 py-4 font-medium">#<?php echo (int)$res['id_reser'] ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($res['nom_complet'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($res['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-6 py-4"><?php echo (int)$res['nb_personnes'] ?> pers.</td>
                                    <td class="px-6 py-4">
                                        <select onchange="changerStatut('Reservation', <?php echo (int)$res['id_reser'] ?>, this.value, this)"
                                            class="border border-gray-200 rounded-xl px-3 py-1 text-sm focus:border-braise focus:ring-1 focus:ring-braise outline-none bg-white transition">
                                            <?php foreach (['en_attente', 'confirmee', 'annulee'] as $s) : ?>
                                                <option value="<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" <?php echo $res['statut'] === $s ? 'selected' : '' ?>>
                                                    <?php echo htmlspecialchars(str_replace('_', ' ', $s), ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs"><?php echo htmlspecialchars($res['date_heure'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4 mb-12">
                    <?php foreach ($reservations as $res) : ?>
                        <div class="bg-white rounded-super shadow-card border border-gray-100 p-5">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="font-bold text-lg text-tropical-green font-display">#<?php echo (int)$res['id_reser'] ?></h3>
                                <span class="text-xs bg-stone-100 text-gray-600 px-3 py-1 rounded-full"><?php echo htmlspecialchars($res['date_heure'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Client</span>
                                    <span class="font-medium"><?php echo htmlspecialchars($res['nom_complet'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Téléphone</span>
                                    <span><?php echo htmlspecialchars($res['telephone'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Personnes</span>
                                    <span class="font-medium"><?php echo (int)$res['nb_personnes'] ?> pers.</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Statut</label>
                                <select onchange="changerStatut('Reservation', <?php echo (int)$res['id_reser'] ?>, this.value, this)"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-braise focus:ring-1 focus:ring-braise outline-none bg-white transition">
                                    <?php foreach (['en_attente', 'confirmee', 'annulee'] as $s) : ?>
                                        <option value="<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" <?php echo $res['statut'] === $s ? 'selected' : '' ?>>
                                            <?php echo htmlspecialchars(str_replace('_', ' ', $s), ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- SECTION PLATS -->
            <section id="section-plats" class="section-content hidden">
                <h2 class="text-3xl font-bold text-tropical-green mb-6 font-display">Gestion des plats</h2>

                <div class="bg-white rounded-super shadow-card border border-gray-100 p-8 mb-8">
                    <h3 id="form-titre" class="text-xl font-bold mb-6 font-display">Ajouter un plat</h3>
                    <form id="form-plat" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" id="plat-id">
                        <div>
                            <label for="plat-nom" class="block text-sm font-semibold text-gray-700 mb-2">Nom du plat</label>
                            <input type="text" id="plat-nom" required placeholder="Ex: Poulet Braisé"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                        </div>
                        <div>
                            <label for="plat-prix" class="block text-sm font-semibold text-gray-700 mb-2">Prix (FCFA)</label>
                            <input type="number" id="plat-prix" required placeholder="Ex: 3500"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                        </div>
                        <div>
                            <label for="plat-categorie" class="block text-sm font-semibold text-gray-700 mb-2">Catégorie</label>
                            <select id="plat-categorie" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-white">
                                <?php foreach ($categories as $cat) : ?>
                                    <option value="<?php echo (int)$cat['id_categorie'] ?>"><?php echo htmlspecialchars($cat['libelle'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="plat-image-file" class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                            <input type="file" id="plat-image-file" accept="image/jpeg,image/png,image/webp"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                            <input type="hidden" id="plat-image">
                            <div id="plat-image-preview" class="mt-3 hidden">
                                <img id="plat-preview-img" src="" alt="Aperçu" class="w-32 h-32 rounded-lg object-cover border border-gray-200">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label for="plat-description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea id="plat-description" rows="2" placeholder="Description du plat..."
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50"></textarea>
                        </div>
                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit"
                                class="bg-braise text-white font-bold py-3 px-8 rounded-xl hover:shadow-fire transition btn-primary">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                            <button type="button" id="btn-annuler" class="hidden bg-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl hover:bg-gray-300 transition">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <div class="hidden md:block bg-white rounded-super shadow-card border border-gray-100 overflow-x-auto">
                    <table class="w-full text-sm min-w-[700px]">
                        <thead class="bg-stone-50 text-gray-600 font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Image</th>
                                <th class="px-6 py-4 text-left">Nom</th>
                                <th class="px-6 py-4 text-left">Catégorie</th>
                                <th class="px-6 py-4 text-left">Prix</th>
                                <th class="px-6 py-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableau-plats" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="cartes-plats" class="md:hidden space-y-4">
                </div>
            </section>

            <!-- SECTION ANNONCES -->
            <section id="section-annonces" class="section-content hidden">
                <h2 class="text-3xl font-bold text-tropical-green mb-6 font-display">Gestion des annonces</h2>

                <div class="bg-white rounded-super shadow-card border border-gray-100 p-8 mb-8">
                    <h3 id="form-annonce-titre" class="text-xl font-bold mb-6 font-display">Ajouter une annonce</h3>
                    <form id="form-annonce" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" id="annonce-id">
                        <div class="md:col-span-2">
                            <label for="annonce-titre" class="block text-sm font-semibold text-gray-700 mb-2">Titre</label>
                            <input type="text" id="annonce-titre" required placeholder="Ex: Grand événement ce weekend"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                        </div>
                        <div class="md:col-span-2">
                            <label for="annonce-description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea id="annonce-description" rows="3" placeholder="Détails de l'annonce..."
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50"></textarea>
                        </div>
                        <div>
                            <label for="annonce-image-file" class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                            <input type="file" id="annonce-image-file" accept="image/jpeg,image/png,image/webp"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                            <input type="hidden" id="annonce-image">
                            <div id="annonce-image-preview" class="mt-3 hidden">
                                <img id="annonce-preview-img" src="" alt="Aperçu" class="w-32 h-32 rounded-lg object-cover border border-gray-200">
                            </div>
                        </div>
                        <div>
                            <label for="annonce-date-debut" class="block text-sm font-semibold text-gray-700 mb-2">Date de début</label>
                            <input type="date" id="annonce-date-debut"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                        </div>
                        <div>
                            <label for="annonce-date-fin" class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                            <input type="date" id="annonce-date-fin"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                        </div>
                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit"
                                class="bg-braise text-white font-bold py-3 px-8 rounded-xl hover:shadow-fire transition btn-primary">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                            <button type="button" id="btn-annuler-annonce" class="hidden bg-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl hover:bg-gray-300 transition">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <div class="hidden md:block bg-white rounded-super shadow-card border border-gray-100 overflow-x-auto">
                    <table class="w-full text-sm min-w-[700px]">
                        <thead class="bg-stone-50 text-gray-600 font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Titre</th>
                                <th class="px-6 py-4 text-left">Date début</th>
                                <th class="px-6 py-4 text-left">Date fin</th>
                                <th class="px-6 py-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableau-annonces" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="cartes-annonces" class="md:hidden space-y-4">
                </div>
            </section>

            <!-- SECTION ACCOMPAGNEMENTS -->
            <section id="section-accompagnements" class="section-content hidden">
                <h2 class="text-3xl font-bold text-tropical-green mb-6 font-display">Gestion des accompagnements</h2>

                <!-- Formulaire ajout/modification accompagnement -->
                <div class="bg-white rounded-super shadow-card border border-gray-100 p-8 mb-8">
                    <h4 id="form-accompagnement-titre" class="text-xl font-bold mb-6 font-display">Ajouter un accompagnement</h4>
                    <form id="form-accompagnement" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" id="accompagnement-id">
                        <div>
                            <label for="accompagnement-nom" class="block text-sm font-semibold text-gray-700 mb-2">Nom de l'accompagnement</label>
                            <input type="text" id="accompagnement-nom" required placeholder="Ex: Frites maison"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                        </div>
                        <div>
                            <label for="accompagnement-prix" class="block text-sm font-semibold text-gray-700 mb-2">Prix supplément (FCFA)</label>
                            <input type="number" id="accompagnement-prix" required placeholder="Ex: 500"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
                        </div>
                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit"
                                class="bg-braise text-white font-bold py-3 px-8 rounded-xl hover:shadow-fire transition btn-primary">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                            <button type="button" id="btn-annuler-accompagnement" class="hidden bg-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl hover:bg-gray-300 transition">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Liste des accompagnements -->
                <div class="hidden md:block bg-white rounded-super shadow-card border border-gray-100 overflow-x-auto mb-8">
                    <table class="w-full text-sm min-w-[700px]">
                        <thead class="bg-stone-50 text-gray-600 font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Nom</th>
                                <th class="px-6 py-4 text-left">Prix</th>
                                <th class="px-6 py-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableau-accompagnements" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="cartes-accompagnements" class="md:hidden space-y-4 mb-8">
                </div>

                <!-- SECTION ASSOCIATION PLAT-ACCOMPAGNEMENTS  -->
                <div class="mt-12 border-t-2 border-gray-200 pt-8">
                    <h3 class="text-2xl font-bold text-tropical-green mb-6 font-display">Associer des accompagnements à un plat</h3>

                    <div class="bg-white rounded-super shadow-card border border-gray-100 p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="plat-accompagnement-select" class="block text-sm font-semibold text-gray-700 mb-2">Choisir un plat</label>
                                <select id="plat-accompagnement-select" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-white">
                                    <option value="">-- Sélectionner un plat --</option>
                                </select>
                            </div>
                        </div>

                        <div id="accompagnements-disponibles" class="mt-6">
                            <p class="text-sm text-gray-500 mb-4">Accompagnements disponibles pour ce plat :</p>
                            <div id="liste-accompagnements-plat" class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-xl p-4 bg-stone-50">
                                <!-- Les accompagnements seront chargés ici dynamiquement -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>

    </div>

    <script src="../assets/js/admin.js"></script>

</body>

</html>