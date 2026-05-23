<?php
session_start();

// Si non connecté, dirige vers login
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Grillades Tropicales</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/output.css">
</head>

<body class="antialiased bg-stone-50 text-gray-800">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <span class="text-xl font-bold text-tropical-green">
                Grillades<span class="text-braise">Tropicales</span> — Admin
            </span>
            <a href="login.php?logout=1" class="text-red-500 font-medium hover:underline">
                Se déconnecter
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-12">

        <!-- Zone Commandes -->
        <h2 class="text-3xl font-bold text-tropical-green mb-6">Commandes</h2>

        <!-- Desktop: Tableau -->
        <div class="hidden md:block bg-white rounded-super shadow-sm border border-gray-100 overflow-x-auto mb-12">
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
                        <tr class="hover:bg-stone-50">
                            <td class="px-6 py-4">#<?php echo (int)$cmd['id_com'] ?></td>
                            <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($cmd['nom_complet'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($cmd['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($cmd['type'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 font-bold text-braise"><?php echo number_format($cmd['total_prix'], 0, ',', ' ') ?> FCFA</td>
                            <td class="px-6 py-4">
                                <select onchange="changerStatut('Commande', <?php echo (int)$cmd['id_com'] ?>, this.value)"
                                    class="border border-gray-200 rounded-xl px-3 py-1 text-sm focus:border-braise outline-none bg-white">
                                    <?php foreach (['en_attente', 'en_preparation', 'en_livraison', 'livree', 'annulee'] as $s) : ?>
                                        <option value="<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" <?php echo $cmd['statut'] === $s ? 'selected' : '' ?>>
                                            <?php echo htmlspecialchars(str_replace('_', ' ', $s), ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($cmd['date_heure'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile: Cartes -->
        <div class="md:hidden space-y-4 mb-12">
            <?php foreach ($commandes as $cmd) : ?>
                <div class="bg-white rounded-super shadow-sm border border-gray-100 p-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-lg text-tropical-green">#<?php echo (int)$cmd['id_com'] ?></h3>
                        <span class="text-xs bg-stone-100 text-gray-600 px-2 py-1 rounded-full"><?php echo htmlspecialchars($cmd['date_heure'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="space-y-2 text-sm mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Client</span>
                            <span class="font-medium"><?php echo htmlspecialchars($cmd['nom_complet'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Téléphone</span>
                            <span><?php echo htmlspecialchars($cmd['telephone'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type</span>
                            <span class="font-medium"><?php echo htmlspecialchars($cmd['type'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total</span>
                            <span class="font-bold text-braise"><?php echo number_format($cmd['total_prix'], 0, ',', ' ') ?> FCFA</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-2">Statut</label>
                        <select onchange="changerStatut('Commande', <?php echo (int)$cmd['id_com'] ?>, this.value)"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-braise outline-none bg-white">
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

        <!-- Zone Réservations -->
        <h2 class="text-3xl font-bold text-tropical-green mb-6">Réservations</h2>

        <!-- Desktop: Tableau -->
        <div class="hidden md:block bg-white rounded-super shadow-sm border border-gray-100 overflow-x-auto mb-12">
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
                        <tr class="hover:bg-stone-50">
                            <td class="px-6 py-4">#<?php echo (int)$res['id_reser'] ?></td>
                            <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($res['nom_complet'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($res['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4"><?php echo (int)$res['nb_personnes'] ?> pers.</td>
                            <td class="px-6 py-4">
                                <select onchange="changerStatut('Reservation', <?php echo (int)$res['id_reser'] ?>, this.value)"
                                    class="border border-gray-200 rounded-xl px-3 py-1 text-sm focus:border-braise outline-none bg-white">
                                    <?php foreach (['en_attente', 'confirmee', 'annulee'] as $s) : ?>
                                        <option value="<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" <?php echo $res['statut'] === $s ? 'selected' : '' ?>>
                                            <?php echo htmlspecialchars(str_replace('_', ' ', $s), ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($res['date_heure'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile: Cartes -->
        <div class="md:hidden space-y-4 mb-12">
            <?php foreach ($reservations as $res) : ?>
                <div class="bg-white rounded-super shadow-sm border border-gray-100 p-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-lg text-tropical-green">#<?php echo (int)$res['id_reser'] ?></h3>
                        <span class="text-xs bg-stone-100 text-gray-600 px-2 py-1 rounded-full"><?php echo htmlspecialchars($res['date_heure'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="space-y-2 text-sm mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Client</span>
                            <span class="font-medium"><?php echo htmlspecialchars($res['nom_complet'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Téléphone</span>
                            <span><?php echo htmlspecialchars($res['telephone'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Personnes</span>
                            <span class="font-medium"><?php echo (int)$res['nb_personnes'] ?> pers.</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-2">Statut</label>
                        <select onchange="changerStatut('Reservation', <?php echo (int)$res['id_reser'] ?>, this.value)"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-braise outline-none bg-white">
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

        <!-- Zone Gestion des plats -->
        <h2 class="text-3xl font-bold text-tropical-green mb-6 mt-12">Gestion des plats</h2>

        <!-- Formulaire ajout/modif -->
        <div class="bg-white rounded-super shadow-sm border border-gray-100 p-8 mb-8">
            <h3 id="form-titre" class="text-xl font-bold mb-6">Ajouter un plat</h3>
            <form id="form-plat" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="hidden" id="plat-id">
                <div>
                    <label class="block text-sm font-semibold mb-1">Nom du plat</label>
                    <input type="text" id="plat-nom" required placeholder="Ex: Poulet Braisé"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Prix (FCFA)</label>
                    <input type="number" id="plat-prix" required placeholder="Ex: 3500"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Catégorie</label>
                    <select id="plat-categorie" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition bg-white">
                        <?php
                        $categories = $bdd->query("SELECT * FROM Categorie")->fetchAll();
                        foreach ($categories as $cat) :
                        ?>
                            <option value="<?php echo (int)$cat['id_categorie'] ?>"><?php echo htmlspecialchars($cat['libelle'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Image URL</label>
                    <input type="text" id="plat-image" placeholder="assets/images/6.jpg"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-1">Description</label>
                    <textarea id="plat-description" rows="2" placeholder="Description du plat..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition"></textarea>
                </div>
                <div class="md:col-span-2 flex gap-4">
                    <button type="submit"
                        class="bg-braise text-white font-bold py-3 px-8 rounded-xl hover:brightness-110 transition">
                        Enregistrer
                    </button>
                    <button type="button" id="btn-annuler" class="hidden bg-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl hover:bg-gray-300 transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>

        <!-- Desktop : Tableau des plats -->
        <div class="hidden md:block bg-white rounded-super shadow-sm border border-gray-100 overflow-x-auto">
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
                    <!-- Injecté par JS -->
                </tbody>
            </table>
        </div>

        <!-- Mobile: Cartes des plats -->
        <div id="cartes-plats" class="md:hidden space-y-4">
            <!-- Injecté par JS -->
        </div>

    </main>

    <script>
    // Configuration de base pour l'API (centralisée)
    const API_BASE_URL = '../../backend/api';

    // Variable globale pour stocker la liste locale des plats
    let listeDesPlats = [];

    // Sécurisation contre les failles XSS
    function escapeHtml(str) {
        if (!str) return '';
        return str.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function changerStatut(table, id, statut) {
        fetch(`${API_BASE_URL}/statut.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ table, id: parseInt(id), statut })
            })
            .then(r => r.json())
            .then(reponse => {
                if (reponse.succes) {
                    console.log('Statut mis à jour !')
                } else {
                    alert('Erreur : ' + reponse.erreur)
                }
            }).catch(err => console.error("Erreur réseau :", err));
    }

    // Chargement des plats optimisé et sécurisé
    function chargerPlats() {
        fetch(`${API_BASE_URL}/plats.php`)
            .then(r => r.json())
            .then(plats => {
                listeDesPlats = plats; 
                
                const tbody = document.getElementById('tableau-plats');
                const cartes = document.getElementById('cartes-plats');
                
                let htmlTableau = '';
                let htmlCartes = '';

                plats.forEach(plat => {
                    const platId = parseInt(plat.id);
                    const imageSrc = plat.image ? escapeHtml(plat.image) : '../assets/images/default.jpg';
                    const nomNettoye = escapeHtml(plat.nom);
                    const catNettoye = escapeHtml(plat.categorie);
                    const descNettoye = plat.description ? escapeHtml(plat.description) : 'Aucune description';
                    const prixFormate = numberFormat(plat.prix);

                    // Version Bureau (Tableau)
                    htmlTableau += `
                    <tr class="hover:bg-stone-50">
                        <td class="px-6 py-4 whitespace-nowrap">#${platId}</td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <img src="${imageSrc}" alt="${nomNettoye}" class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                        </td>
                        <td class="px-6 py-4 font-medium whitespace-nowrap">${nomNettoye}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${catNettoye}</td>
                        <td class="px-6 py-4 font-bold text-braise whitespace-nowrap">${prixFormate} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button onclick="modifierPlat(${platId})"
                                    class="bg-tropical-green text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:brightness-110 transition">
                                    Modifier
                                </button>
                                <button onclick="supprimerPlat(${platId})"
                                    class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-700 transition">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>`;

                    // Version Mobile (Cartes)
                    htmlCartes += `
                    <div class="bg-white rounded-super shadow-sm border border-gray-100 p-4">
                        <div class="flex gap-4 mb-4">
                            <img src="${imageSrc}" alt="${nomNettoye}" class="w-20 h-20 rounded-lg object-cover">
                            <div class="flex-grow">
                                <h3 class="font-bold text-lg text-tropical-green">${nomNettoye}</h3>
                                <p class="text-xs text-gray-500">${catNettoye}</p>
                                <p class="text-sm text-braise font-bold mt-2">${prixFormate} FCFA</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">${descNettoye}</p>
                        <div class="flex gap-2">
                            <button onclick="modifierPlat(${platId})"
                                class="flex-1 bg-tropical-green text-white px-3 py-2 rounded-lg text-xs font-bold hover:brightness-110">
                                Modifier
                            </button>
                            <button onclick="supprimerPlat(${platId})"
                                class="flex-1 bg-red-500 text-white px-3 py-2 rounded-lg text-xs font-bold hover:brightness-110">
                                Supprimer
                            </button>
                        </div>
                    </div>`;
                });

                tbody.innerHTML = htmlTableau;
                cartes.innerHTML = htmlCartes;

            }).catch(err => console.error("Erreur chargement plats :", err));
    }

    function numberFormat(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    function modifierPlat(id) {
        const plat = listeDesPlats.find(p => p.id == id);
        if (!plat) return;

        document.getElementById('form-titre').textContent = 'Modifier un plat'
        document.getElementById('plat-id').value = plat.id
        document.getElementById('plat-nom').value = plat.nom
        document.getElementById('plat-prix').value = plat.prix
        document.getElementById('plat-description').value = plat.description || ''
        document.getElementById('plat-image').value = plat.image || ''
        document.getElementById('plat-categorie').value = plat.id_categorie || '' 
        
        document.getElementById('btn-annuler').classList.remove('hidden')
        document.getElementById('form-titre').scrollIntoView({ behavior: 'smooth' });
    }

    document.getElementById('btn-annuler').addEventListener('click', () => {
        document.getElementById('form-plat').reset()
        document.getElementById('plat-id').value = ''
        document.getElementById('form-titre').textContent = 'Ajouter un plat'
        document.getElementById('btn-annuler').classList.add('hidden')
    })

    function supprimerPlat(id) {
        if (!confirm('Voulez-vous vraiment supprimer ce plat ?')) return
        fetch(`${API_BASE_URL}/gerer_plats.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'supprimer', id_plat: parseInt(id) })
            })
            .then(r => r.json())
            .then(reponse => {
                if (reponse.succes) chargerPlats()
                else alert('Erreur : ' + reponse.erreur)
            }).catch(err => console.error("Erreur réseau :", err));
    }

    document.getElementById('form-plat').addEventListener('submit', e => {
        e.preventDefault()
        const id = document.getElementById('plat-id').value
        const action = id ? 'modifier' : 'ajouter'

        const donnees = {
            action,
            id_plat: id ? parseInt(id) : null,
            nom: document.getElementById('plat-nom').value,
            prix: parseFloat(document.getElementById('plat-prix').value),
            description: document.getElementById('plat-description').value,
            image_url: document.getElementById('plat-image').value,
            id_categorie: parseInt(document.getElementById('plat-categorie').value)
        }

        fetch(`${API_BASE_URL}/gerer_plats.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(donnees)
            })
            .then(r => r.json())
            .then(reponse => {
                if (reponse.succes) {
                    chargerPlats()
                    document.getElementById('form-plat').reset()
                    document.getElementById('plat-id').value = ''
                    document.getElementById('form-titre').textContent = 'Ajouter un plat'
                    document.getElementById('btn-annuler').classList.add('hidden')
                } else {
                    alert('Erreur : ' + reponse.erreur)
                }
            }).catch(err => console.error("Erreur réseau :", err));
    })

    document.addEventListener('DOMContentLoaded', function() {
        chargerPlats()
    })
</script>

</body>
</html>