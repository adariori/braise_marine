<?php
session_start();

//si connecte dirige vers login
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require_once '../../backend/config/connect.php';

// recup commandes
$commandes = $bdd->query("
    SELECT c.id_com, c.date_heure, c.type, c.total_prix, c.statut, c.adresse_livraison,
           cl.nom_complet, cl.telephone
    FROM Commande c
    JOIN Client cl ON c.id_client = cl.id_client
    ORDER BY c.date_heure DESC
")->fetchAll();

// recup réservations
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

    <!-- header -->
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

        <!-- zone-commande -->
        <h2 class="text-3xl font-bold text-tropical-green mb-6">Commandes</h2>
        <div class="bg-white rounded-super shadow-sm border border-gray-100 overflow-hidden mb-12">
            <table class="w-full text-sm">
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
                            <td class="px-6 py-4">#<?php echo $cmd['id_com'] ?></td>
                            <td class="px-6 py-4 font-medium"><?php echo $cmd['nom_complet'] ?></td>
                            <td class="px-6 py-4"><?php echo $cmd['telephone'] ?></td>
                            <td class="px-6 py-4"><?php echo $cmd['type'] ?></td>
                            <td class="px-6 py-4 font-bold text-braise"><?php echo $cmd['total_prix'] ?> FCFA</td>
                            <td class="px-6 py-4">
                                <select onchange="changerStatut('Commande', <?php echo $cmd['id_com'] ?>, this.value)"
                                    class="border border-gray-200 rounded-xl px-3 py-1 text-sm focus:border-braise outline-none">
                                    <?php foreach (['en_attente', 'en_preparation', 'en_livraison', 'livree', 'annulee'] as $s) : ?>
                                        <option value="<?php echo $s ?>" <?php echo $cmd['statut'] === $s ? 'selected' : '' ?>>
                                            <?php echo $s ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?php echo $cmd['date_heure'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- zone-reservation -->
        <h2 class="text-3xl font-bold text-tropical-green mb-6">Réservations</h2>
        <div class="bg-white rounded-super shadow-sm border border-gray-100">
            <table class="w-full text-sm">
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
                            <td class="px-6 py-4">#<?php echo $res['id_reser'] ?></td>
                            <td class="px-6 py-4 font-medium"><?php echo $res['nom_complet'] ?></td>
                            <td class="px-6 py-4"><?php echo $res['telephone'] ?></td>
                            <td class="px-6 py-4"><?php echo $res['nb_personnes'] ?> pers.</td>
                            <td class="px-6 py-4">
                                <select onchange="changerStatut('Reservation', <?php echo $res['id_reser'] ?>, this.value)"
                                    class="border border-gray-200 rounded-xl px-3 py-1 text-sm focus:border-braise outline-none">
                                    <?php foreach (['en_attente', 'confirmee', 'annulee'] as $s) : ?>
                                        <option value="<?php echo $s ?>" <?php echo $res['statut'] === $s ? 'selected' : '' ?>>
                                            <?php echo $s ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?php echo $res['date_heure'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


        <!-- zone-plat -->
        <h2 class="text-3xl font-bold text-tropical-green mb-6 mt-12">Gestion des plats</h2>

        <!-- formulaire ajout/modif -->
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
                    <select id="plat-categorie" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
                        <?php
                        $categories = $bdd->query("SELECT * FROM Categorie")->fetchAll();
                        foreach ($categories as $cat) :
                        ?>
                            <option value="<?php echo $cat['id_categorie'] ?>"><?php echo $cat['libelle'] ?></option>
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

        <!--tab de plat-->
        <div class="bg-white rounded-super shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="px-6 py-4 text-left">#</th>
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

    </main>

    <script>
        function changerStatut(table, id, statut) {
            fetch('http://localhost/GRTR/backend/api/statut.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        table,
                        id,
                        statut
                    })
                })
                .then(r => r.json())
                .then(reponse => {
                    if (reponse.succes) {
                        console.log('Statut mis à jour !')
                    } else {
                        alert('Erreur : ' + reponse.erreur)
                    }
                })
        }

        //chargement des plats
        function chargerPlats() {
            fetch('http://localhost/GRTR/backend/api/plats.php')
                .then(r => r.json())
                .then(plats => {
                    const tbody = document.getElementById('tableau-plats')
                    tbody.innerHTML = ''
                    plats.forEach(plat => {
                        tbody.innerHTML += `
                    <tr class="hover:bg-stone-50">
                        <td class="px-6 py-4">#${plat.id}</td>
                        <td class="px-6 py-4 font-medium">${plat.nom}</td>
                        <td class="px-6 py-4">${plat.categorie}</td>
                        <td class="px-6 py-4 font-bold text-braise">${plat.prix} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
    <div class="flex gap-3">
        <button onclick="modifierPlat(${JSON.stringify(plat).replace(/"/g, '&quot;')})"
            class="bg-tropical-green text-white px-4 py-1 rounded-lg text-xs font-bold hover:brightness-110">
            Modifier
        </button>
        <button onclick="supprimerPlat(${plat.id})"
            class="bg-tropical-green text-white px-4 py-1 rounded-lg text-xs font-bold hover:brightness-110">
            Supprimer
        </button>
    </div>
</td>
                    </tr>`
                    })
                })
        }

        //modif
        function modifierPlat(plat) {
            document.getElementById('form-titre').textContent = 'Modifier un plat'
            document.getElementById('plat-id').value = plat.id
            document.getElementById('plat-nom').value = plat.nom
            document.getElementById('plat-prix').value = plat.prix
            document.getElementById('plat-description').value = plat.description
            document.getElementById('plat-image').value = plat.image
            document.getElementById('btn-annuler').classList.remove('hidden')
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            })
        }

        //annuler
        document.getElementById('btn-annuler').addEventListener('click', () => {
            document.getElementById('form-plat').reset()
            document.getElementById('plat-id').value = ''
            document.getElementById('form-titre').textContent = 'Ajouter un plat'
            document.getElementById('btn-annuler').classList.add('hidden')
        })

        //supprimer
        function supprimerPlat(id) {
            if (!confirm('Supprimer ce plat ?')) return
            fetch('http://localhost/GRTR/backend/api/gerer_plats.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'supprimer',
                        id_plat: id
                    })
                })
                .then(r => r.json())
                .then(reponse => {
                    if (reponse.succes) chargerPlats()
                    else alert('Erreur : ' + reponse.erreur)
                })
        }

        //soumission formulare
        document.getElementById('form-plat').addEventListener('submit', e => {
            e.preventDefault()
            const id = document.getElementById('plat-id').value
            const action = id ? 'modifier' : 'ajouter'

            const donnees = {
                action,
                id_plat: id || null,
                nom: document.getElementById('plat-nom').value,
                prix: document.getElementById('plat-prix').value,
                description: document.getElementById('plat-description').value,
                image_url: document.getElementById('plat-image').value,
                id_categorie: document.getElementById('plat-categorie').value
            }

            fetch('http://localhost/GRTR/backend/api/gerer_plats.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
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
                })
        })

        chargerPlats()
    </script>

</body>

</html>