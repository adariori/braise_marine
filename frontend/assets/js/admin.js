// ============================================
// 1. VARIABLES GLOBALES & CONFIGURATION
// ============================================
const API_BASE_URL = (window.BASE_PATH || '') + '/backend/api';

// Préfixe une image stockée en base (chemin racine, ex: "/backend/uploads/x.jpg")
// avec le préfixe de site courant (vide sur Vercel, "/GRTR" en local).
function resoudreImage(chemin) {
    const base = window.BASE_PATH || ''
    if (!chemin) return base + '/frontend/assets/images/default.svg'
    return chemin.startsWith('http') ? chemin : base + chemin
}

let listeDesPlats = [];
let listeDesAnnonces = [];
let listeDesAccompagnements = [];
let platsCharges = false;
let annoncesChargees = false;
let accompagnementsCharges = false;

// ============================================
// 2. FONCTIONS UTILITAIRES
// ============================================
function escapeHtml(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function numberFormat(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}


// ============================================
// 2.5. NOTIFICATIONS (Alertes utilisateur)
// ============================================
function showNotification(message, type = 'success') {
    console.log('Notification:', message, type); // Pour déboguer
    
    // Supprimer les anciennes notifications
    const oldNotifs = document.querySelectorAll('.custom-notification');
    oldNotifs.forEach(notif => notif.remove());
    
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = 'custom-notification';
    
    // Couleur selon le type
    let bgColor = '';
    if (type === 'success') {
        bgColor = '#10b981'; // emerald-500
    } else if (type === 'error') {
        bgColor = '#ef4444'; // red-500
    } else if (type === 'warning') {
        bgColor = '#f97316'; // orange-500
    } else {
        bgColor = '#3b82f6'; // blue-500
    }
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background-color: ${bgColor};
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: bold;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
        font-size: 14px;
        font-family: system-ui, -apple-system, sans-serif;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        max-width: 350px;
    `;
    
    notification.innerHTML = message;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Disparition après 3 secondes
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// ============================================
// 3. GESTION DE LA NAVIGATION (SIDEBAR)
// ============================================
function afficherSection(sectionId) {
    // Masquer toutes les sections
    document.querySelectorAll('.section-content').forEach(section => {
        section.classList.add('hidden');
    });

    // Afficher la section demandée
    const section = document.getElementById(`section-${sectionId}`);
    if (section) {
        section.classList.remove('hidden');
    }

    // Mettre à jour la classe active des boutons
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active-nav', 'bg-tropical-green', 'text-white');
        btn.classList.add('text-gray-700');
    });

    const activeButton = document.querySelector(`.nav-btn[onclick="afficherSection('${sectionId}')"]`);
    if (activeButton) {
        activeButton.classList.add('active-nav', 'bg-tropical-green', 'text-white');
        activeButton.classList.remove('text-gray-700');
    }

    // Chargement des données selon la section
    if (sectionId === 'statistiques') {
        chargerStatistiques();
    }
    if (sectionId === 'plats' && !platsCharges) {
        chargerPlats();
        platsCharges = true;
    }
    if (sectionId === 'annonces' && !annoncesChargees) {
        chargerAnnonces();
        annoncesChargees = true;
    }
    if (sectionId === 'accompagnements' && !accompagnementsCharges) {
        chargerAccompagnements();
        chargerAssociationPlats();
        accompagnementsCharges = true;
    }
}

// ============================================
// 4. UPLOAD D'IMAGES
// ============================================
function uploadImage(file, inputId, imgId, previewId) {
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    fetch(`${API_BASE_URL}/upload.php`, {
        method: 'POST',
        body: formData
    })
        .then(r => r.json())
        .then(reponse => {
            if (reponse.succes) {
                document.getElementById(inputId).value = reponse.chemin;
                const img = document.getElementById(imgId);
                img.src = reponse.url;
                document.getElementById(previewId).classList.remove('hidden');
                console.log('Image uploadée :', reponse.chemin);
            } else {
                alert('Erreur upload : ' + reponse.erreur);
            }
        })
        .catch(err => {
            console.error('Erreur upload :', err);
            alert('Erreur lors de l\'upload');
        });
}

// ============================================
// 5. GESTION DES STATUTS (Commandes/Réservations)
// ============================================
function changerStatut(table, id, statut, selectElement) {
    const originalValue = selectElement.value;
    selectElement.disabled = true;
    
    fetch(`${API_BASE_URL}/statut.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCsrfToken() },
        body: JSON.stringify({ table, id: parseInt(id), statut })
    })
    .then(r => r.json())
    .then(reponse => {
        if (reponse.succes) {
            showNotification('Statut mis à jour', 'success');
            // Optionnel : recharger la section ou mettre à jour localement
        } else {
            showNotification('Erreur : ' + reponse.erreur, 'error');
            selectElement.value = originalValue;
        }
    })
    .catch(err => {
        console.error("Erreur réseau :", err);
        showNotification('Erreur réseau', 'error');
        selectElement.value = originalValue;
    })
    .finally(() => {
        selectElement.disabled = false;
    });
}

// ============================================
// 6. STATISTIQUES (Tableau de bord)
// ============================================
function chargerStatistiques() {
    fetch(`${API_BASE_URL}/statistics.php`)
        .then(r => r.json())
        .then(stats => {
            document.getElementById('stat-ca').textContent = numberFormat(stats.chiffre_affaires) + ' FCFA';
            document.getElementById('stat-commandes').textContent = stats.nb_commandes;
            document.getElementById('stat-en-cours').textContent = stats.nb_en_cours;
            document.getElementById('stat-clients').textContent = stats.nb_clients;
            document.getElementById('stat-jour').textContent = stats.nb_jour;
            document.getElementById('stat-ca-jour').textContent = numberFormat(stats.ca_jour) + ' FCFA';
            document.getElementById('stat-panier').textContent = numberFormat(stats.panier_moyen) + ' FCFA';
        })
        .catch(err => console.error('Erreur stats:', err));
}

// ============================================
// 7. GESTION DES PLATS (CRUD)
// ============================================
function chargerPlats() {
    fetch(`${API_BASE_URL}/plats.php`)
        .then(r => r.json())
        .then(plats => {
            listeDesPlats = plats;
            const tbody = document.getElementById('tableau-plats');
            const cartes = document.getElementById('cartes-plats');
            let htmlTableau = '';
            let htmlCartes = '';

            plats.forEach((plat, index) => {
                const platId = parseInt(plat.id);
                const imageSrc = resoudreImage(plat.image);
                const nomNettoye = escapeHtml(plat.nom);
                const catNettoye = escapeHtml(plat.categorie);
                const descNettoye = plat.description ? escapeHtml(plat.description) : 'Aucune description';
                const prixFormate = numberFormat(plat.prix);

                htmlTableau += `
                    <tr class="hover:bg-stone-50">
                        <td class="px-6 py-4">${index + 1}</td>
                        <td class="px-6 py-2">
                            <img src="${imageSrc}" alt="${nomNettoye}" class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                        </td>
                        <td class="px-6 py-4 font-medium">${nomNettoye}</td>
                        <td class="px-6 py-4">${catNettoye}</td>
                        <td class="px-6 py-4 font-bold text-braise">${prixFormate} FCFA</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="modifierPlat(${platId})" class="bg-tropical-green text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:brightness-110">Modifier</button>
                                <button onclick="supprimerPlat(${platId})" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-700">Supprimer</button>
                            </div>
                        </td>
                    </tr>
                `;

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
                            <button onclick="modifierPlat(${platId})" class="flex-1 bg-tropical-green text-white px-3 py-2 rounded-lg text-xs font-bold hover:brightness-110">Modifier</button>
                            <button onclick="supprimerPlat(${platId})" class="flex-1 bg-red-500 text-white px-3 py-2 rounded-lg text-xs font-bold hover:brightness-110">Supprimer</button>
                        </div>
                    </div>
                `;
            });

            if (tbody) tbody.innerHTML = htmlTableau;
            if (cartes) cartes.innerHTML = htmlCartes;
        })
        .catch(err => console.error("Erreur chargement plats :", err));
}

function modifierPlat(id) {
    const plat = listeDesPlats.find(p => p.id == id);
    if (!plat) return;
    document.getElementById('form-titre').textContent = 'Modifier un plat';
    document.getElementById('plat-id').value = plat.id;
    document.getElementById('plat-nom').value = plat.nom;
    document.getElementById('plat-prix').value = plat.prix;
    document.getElementById('plat-description').value = plat.description || '';
    document.getElementById('plat-image').value = plat.image || '';
    document.getElementById('plat-categorie').value = plat.id_categorie || '';
    document.getElementById('btn-annuler').classList.remove('hidden');
    document.getElementById('form-titre').scrollIntoView({ behavior: 'smooth' });
}

function supprimerPlat(id) {
    if (!confirm('Voulez-vous vraiment supprimer ce plat ? Cette action est irréversible.')) return;

    fetch(`${API_BASE_URL}/gerer_plats.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'supprimer', id_plat: parseInt(id) })
    })
        .then(r => r.json())
        .then(reponse => {
            if (reponse.succes) {
                showNotification('Plat supprimé avec succès !', 'success');
                chargerPlats();
            } else {
                showNotification('Erreur : ' + reponse.erreur, 'error');
            }
        })
        .catch(err => {
            console.error("Erreur réseau :", err);
            showNotification('Erreur lors de la suppression', 'error');
        });
}

// ============================================
// 8. GESTION DES ANNONCES (CRUD)
// ============================================
function chargerAnnonces() {
    fetch(`${API_BASE_URL}/annonces.php`)
        .then(r => r.json())
        .then(annonces => {
            listeDesAnnonces = annonces;
            const tbody = document.getElementById('tableau-annonces');
            const cartes = document.getElementById('cartes-annonces');
            let htmlTableau = '';
            let htmlCartes = '';

            annonces.forEach(annonce => {
                const annonceId = parseInt(annonce.id_annonce);
                const titre = escapeHtml(annonce.titre);
                const desc = escapeHtml(annonce.description);
                const dateDebut = annonce.date_debut ? new Date(annonce.date_debut).toLocaleDateString('fr-FR') : '-';
                const dateFin = annonce.date_fin ? new Date(annonce.date_fin).toLocaleDateString('fr-FR') : '-';

                htmlTableau += `
                    <tr class="hover:bg-stone-50">
                        <td class="px-6 py-4">${annonceId}</td>
                        <td class="px-6 py-4 font-medium">${titre}</td>
                        <td class="px-6 py-4">${dateDebut}</td>
                        <td class="px-6 py-4">${dateFin}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="modifierAnnonce(${annonceId})" class="bg-tropical-green text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:brightness-110">Modifier</button>
                                <button onclick="supprimerAnnonce(${annonceId})" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-700">Supprimer</button>
                            </div>
                        </td>
                    </tr>
                `;

                htmlCartes += `
                    <div class="bg-white rounded-super shadow-sm border border-gray-100 p-4">
                        <h3 class="font-bold text-lg text-tropical-green">${titre}</h3>
                        <p class="text-sm text-gray-600 my-2">${desc}</p>
                        <p class="text-xs text-gray-500">Du ${dateDebut} au ${dateFin}</p>
                        <div class="flex gap-2 mt-4">
                            <button onclick="modifierAnnonce(${annonceId})" class="flex-1 bg-tropical-green text-white px-3 py-2 rounded-lg text-xs font-bold">Modifier</button>
                            <button onclick="supprimerAnnonce(${annonceId})" class="flex-1 bg-red-500 text-white px-3 py-2 rounded-lg text-xs font-bold">Supprimer</button>
                        </div>
                    </div>
                `;
            });

            if (tbody) tbody.innerHTML = htmlTableau;
            if (cartes) cartes.innerHTML = htmlCartes;
        })
        .catch(err => console.error("Erreur chargement annonces :", err));
}

function modifierAnnonce(id) {
    const ann = listeDesAnnonces.find(a => a.id_annonce == id);
    if (!ann) return;
    document.getElementById('form-annonce-titre').textContent = 'Modifier une annonce';
    document.getElementById('annonce-id').value = ann.id_annonce;
    document.getElementById('annonce-titre').value = ann.titre;
    document.getElementById('annonce-description').value = ann.description || '';
    document.getElementById('annonce-image').value = ann.image_url || '';
    document.getElementById('annonce-date-debut').value = ann.date_debut || '';
    document.getElementById('annonce-date-fin').value = ann.date_fin || '';
    document.getElementById('btn-annuler-annonce').classList.remove('hidden');
    document.getElementById('form-annonce-titre').scrollIntoView({ behavior: 'smooth' });
}

function supprimerAnnonce(id) {
    if (!confirm('Voulez-vous vraiment supprimer cette annonce ?')) return;
    fetch(`${API_BASE_URL}/annonces.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'supprimer', id_annonce: parseInt(id) })
    })
        .then(r => r.json())
        .then(reponse => {
            if (reponse.succes) {
                showNotification(' Annonce supprimée avec succès !', 'success');
                chargerAnnonces();
            } else {
                showNotification('Erreur : ' + reponse.erreur, 'error');
            }
        })
        .catch(err => {
            console.error("Erreur réseau :", err);
            showNotification('Erreur lors de la suppression', 'error');
        });
}

// ============================================
// 9. GESTION DES ACCOMPAGNEMENTS (CRUD)
// ============================================
function chargerAccompagnements() {
    fetch(`${API_BASE_URL}/accompagnements.php`)
        .then(r => r.json())
        .then(accompagnements => {
            listeDesAccompagnements = accompagnements;
            const tbody = document.getElementById('tableau-accompagnements');
            const cartes = document.getElementById('cartes-accompagnements');
            let htmlTableau = '';
            let htmlCartes = '';

            accompagnements.forEach(acc => {
                const accId = parseInt(acc.id_accompagnement);
                const nom = escapeHtml(acc.nom);
                const prix = numberFormat(acc.supplement_prix);

                htmlTableau += `
                    <tr class="hover:bg-stone-50">
                        <td class="px-6 py-4">${accId}</td>
                        <td class="px-6 py-4 font-medium">${nom}</td>
                        <td class="px-6 py-4 font-bold text-braise">${prix} FCFA</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="modifierAccompagnement(${accId})" class="bg-tropical-green text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:brightness-110">Modifier</button>
                                <button onclick="supprimerAccompagnement(${accId})" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-700">Supprimer</button>
                            </div>
                        </td>
                    </tr>
                `;

                htmlCartes += `
                    <div class="bg-white rounded-super shadow-sm border border-gray-100 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-tropical-green">${nom}</h3>
                            <span class="text-braise font-bold">${prix} FCFA</span>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button onclick="modifierAccompagnement(${accId})" class="flex-1 bg-tropical-green text-white px-3 py-2 rounded-lg text-xs font-bold">Modifier</button>
                            <button onclick="supprimerAccompagnement(${accId})" class="flex-1 bg-red-500 text-white px-3 py-2 rounded-lg text-xs font-bold">Supprimer</button>
                        </div>
                    </div>
                `;
            });

            if (tbody) tbody.innerHTML = htmlTableau;
            if (cartes) cartes.innerHTML = htmlCartes;
        })
        .catch(err => console.error("Erreur chargement accompagnements :", err));
}

function modifierAccompagnement(id) {
    const acc = listeDesAccompagnements.find(a => a.id_accompagnement == id);
    if (!acc) return;
    document.getElementById('form-accompagnement-titre').textContent = 'Modifier un accompagnement';
    document.getElementById('accompagnement-id').value = acc.id_accompagnement;
    document.getElementById('accompagnement-nom').value = acc.nom;
    document.getElementById('accompagnement-prix').value = acc.supplement_prix;
    document.getElementById('btn-annuler-accompagnement').classList.remove('hidden');
    document.getElementById('form-accompagnement-titre').scrollIntoView({ behavior: 'smooth' });
}

function supprimerAccompagnement(id) {
    if (!confirm('Voulez-vous vraiment supprimer cet accompagnement ? Cette action est irréversible.')) return;

    fetch(`${API_BASE_URL}/gerer_accompagnements.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'supprimer', id_accompagnement: id })
    })
        .then(r => r.json())
        .then(reponse => {
            if (reponse.succes) {
                showNotification(' Accompagnement supprimé avec succès !', 'success');
                chargerAccompagnements();
                chargerPlats();
            } else {
                showNotification('Erreur : ' + reponse.erreur, 'error');
            }
        })
        .catch(err => {
            console.error("Erreur réseau :", err);
            showNotification('Erreur lors de la suppression', 'error');
        });
}

// ============================================
// 10. ASSOCIATION PLAT-ACCOMPAGNEMENTS
// ============================================
function chargerAssociationPlats() {
    fetch(`${API_BASE_URL}/plats.php`)
        .then(r => r.json())
        .then(plats => {
            const select = document.getElementById('plat-accompagnement-select');
            let options = '<option value="">-- Sélectionner un plat --</option>';
            plats.forEach(plat => {
                options += `<option value="${plat.id}">${escapeHtml(plat.nom)}</option>`;
            });
            select.innerHTML = options;
        });
}

function toggleAssociation(platId, accompagnementId, associer) {
    const action = associer ? 'associer' : 'dissocier';
    fetch(`${API_BASE_URL}/associer_accompagnements.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: action,
            id_plat: parseInt(platId),
            id_accompagnement: parseInt(accompagnementId)
        })
    })
        .then(r => r.json())
        .then(reponse => {
            if (reponse.succes) {
                const message = associer ? ' Accompagnement associé au plat !' : ' Accompagnement dissocié du plat !';
                showNotification(message, 'success');
            } else {
                showNotification('Erreur : ' + reponse.erreur, 'error');
            }
        })
        .catch(err => {
            console.error("Erreur réseau :", err);
            showNotification('Erreur lors de l\'association', 'error');
        });
}

// ============================================
// 11. ÉCOUTEURS D'ÉVÉNEMENTS (DOM)
// ============================================

// Upload images
if (document.getElementById('plat-image-file')) {
    document.getElementById('plat-image-file').addEventListener('change', function (e) {
        uploadImage(e.target.files[0], 'plat-image', 'plat-preview-img', 'plat-image-preview');
    });
}

if (document.getElementById('annonce-image-file')) {
    document.getElementById('annonce-image-file').addEventListener('change', function (e) {
        uploadImage(e.target.files[0], 'annonce-image', 'annonce-preview-img', 'annonce-image-preview');
    });
}

// Formulaire Plats
if (document.getElementById('form-plat')) {
    document.getElementById('form-plat').addEventListener('submit', e => {
        e.preventDefault();
        const id = document.getElementById('plat-id').value;
        const action = id ? 'modifier' : 'ajouter';
        const donnees = {
            action,
            id_plat: id ? parseInt(id) : null,
            nom: document.getElementById('plat-nom').value,
            prix: parseFloat(document.getElementById('plat-prix').value),
            description: document.getElementById('plat-description').value,
            image_url: document.getElementById('plat-image').value,
            id_categorie: parseInt(document.getElementById('plat-categorie').value)
        };
        fetch(`${API_BASE_URL}/gerer_plats.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(donnees)
        })
            .then(r => r.json())
            .then(reponse => {
                if (reponse.succes) {
                    const message = action === 'ajouter' ? ' Plat ajouté avec succès !' : ' Plat modifié avec succès !';
                    showNotification(message, 'success');
                    chargerPlats();
                    document.getElementById('btn-annuler').click();
                } else {
                    showNotification(' Erreur : ' + reponse.erreur, 'error');
                }
            })
            .catch(err => {
                console.error("Erreur réseau :", err);
                showNotification(' Erreur lors de l\'opération', 'error');
            });
    });
}

// Bouton Annuler Plat
if (document.getElementById('btn-annuler')) {
    document.getElementById('btn-annuler').addEventListener('click', () => {
        document.getElementById('form-plat').reset();
        document.getElementById('plat-id').value = '';
        document.getElementById('form-titre').textContent = 'Ajouter un plat';
        document.getElementById('btn-annuler').classList.add('hidden');
        document.getElementById('plat-image-preview').classList.add('hidden');
    });
}

// Formulaire Accompagnements
if (document.getElementById('form-accompagnement')) {
    document.getElementById('form-accompagnement').addEventListener('submit', e => {
        e.preventDefault();
        const id = document.getElementById('accompagnement-id').value;
        const action = id ? 'modifier' : 'ajouter';
        const donnees = {
            action,
            nom: document.getElementById('accompagnement-nom').value,
            prix_supplement: parseFloat(document.getElementById('accompagnement-prix').value)
        };
        if (id) {
            donnees.id_accompagnement = parseInt(id);
        }
        fetch(`${API_BASE_URL}/gerer_accompagnements.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(donnees)
        })
            .then(r => r.json())
            .then(reponse => {
                if (reponse.succes) {
                    const message = action === 'ajouter' ? ' Accompagnement ajouté avec succès !' : ' Accompagnement modifié avec succès !';
                    showNotification(message, 'success');
                    chargerAccompagnements();
                    chargerPlats();
                    document.getElementById('btn-annuler-accompagnement').click();
                } else {
                    showNotification(' Erreur : ' + reponse.erreur, 'error');
                }
            })
            .catch(err => {
                console.error("Erreur réseau :", err);
                showNotification(' Erreur lors de l\'opération', 'error');
            });
    });
}

// Bouton Annuler Accompagnement
if (document.getElementById('btn-annuler-accompagnement')) {
    document.getElementById('btn-annuler-accompagnement').addEventListener('click', () => {
        document.getElementById('form-accompagnement').reset();
        document.getElementById('accompagnement-id').value = '';
        document.getElementById('form-accompagnement-titre').textContent = 'Ajouter un accompagnement';
        document.getElementById('btn-annuler-accompagnement').classList.add('hidden');
    });
}


// Formulaire Annonces
if (document.getElementById('form-annonce')) {
    document.getElementById('form-annonce').addEventListener('submit', e => {
        e.preventDefault();
        const id = document.getElementById('annonce-id').value;
        const action = id ? 'modifier' : 'ajouter';
        const donnees = {
            action,
            id_annonce: id ? parseInt(id) : null,
            titre: document.getElementById('annonce-titre').value,
            description: document.getElementById('annonce-description').value,
            image_url: document.getElementById('annonce-image').value,
            date_debut: document.getElementById('annonce-date-debut').value,
            date_fin: document.getElementById('annonce-date-fin').value
        };
        fetch(`${API_BASE_URL}/annonces.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(donnees)
        })
        .then(r => r.json())
        .then(reponse => {
            if (reponse.succes) {
                const message = action === 'ajouter' ? ' Annonce ajoutée avec succès !' : ' Annonce modifiée avec succès !';
                showNotification(message, 'success');
                chargerAnnonces();
                document.getElementById('btn-annuler-annonce').click();
            } else {
                showNotification(' Erreur : ' + reponse.erreur, 'error');
            }
        })
        .catch(err => {
            console.error("Erreur réseau :", err);
            showNotification(' Erreur lors de l\'opération', 'error');
        });
    });
}
// Bouton Annuler Annonce
if (document.getElementById('btn-annuler-annonce')) {
    document.getElementById('btn-annuler-annonce').addEventListener('click', () => {
        document.getElementById('form-annonce').reset();
        document.getElementById('annonce-id').value = '';
        document.getElementById('form-annonce-titre').textContent = 'Ajouter une annonce';
        document.getElementById('btn-annuler-annonce').classList.add('hidden');
        document.getElementById('annonce-image-preview').classList.add('hidden');
    });
}

// Sélection du plat pour association
document.getElementById('plat-accompagnement-select')?.addEventListener('change', function () {
    const platId = this.value;
    if (!platId) {
        document.getElementById('liste-accompagnements-plat').innerHTML = '';
        return;
    }

    fetch(`${API_BASE_URL}/accompagnements.php`)
        .then(r => r.json())
        .then(accompagnements => {
            fetch(`${API_BASE_URL}/plats.php?id=${platId}`)
                .then(r => r.json())
                .then(plat => {
                    const accompagnementsPlat = plat.accompagnements || [];
                    const accompagnementsIds = accompagnementsPlat.map(a => a.id_accompagnement);
                    let html = '';
                    accompagnements.forEach(acc => {
                        const estAssocie = accompagnementsIds.includes(acc.id_accompagnement);
                        html += `
                            <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-100 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" 
                                           id="acc_${acc.id_accompagnement}" 
                                           value="${acc.id_accompagnement}"
                                           ${estAssocie ? 'checked' : ''}
                                           onchange="toggleAssociation(${platId}, ${acc.id_accompagnement}, this.checked)"
                                           class="w-4 h-4 text-braise rounded border-gray-300 focus:ring-braise">
                                    <label for="acc_${acc.id_accompagnement}" class="font-medium text-gray-700 cursor-pointer">
                                        ${escapeHtml(acc.nom)}
                                    </label>
                                </div>
                                <span class="text-braise font-semibold text-sm">+${numberFormat(acc.supplement_prix)} FCFA</span>
                            </div>
                        `;
                    });
                    document.getElementById('liste-accompagnements-plat').innerHTML = html || '<p class="text-gray-500 text-center py-8">Aucun accompagnement disponible</p>';
                });
        });
});

// ============================================
// 12. INITIALISATION AU CHARGEMENT DE LA PAGE
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    afficherSection('statistiques');
    chargerAccompagnements();
    chargerAssociationPlats();
    accompagnementsCharges = true;
});