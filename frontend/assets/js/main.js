// Préfixe une image stockée en base (chemin racine, ex: "/backend/uploads/x.jpg")
// avec le préfixe de site courant (vide sur Vercel, "/GRTR" en local). Avec
// repli sur une image par défaut si aucune image n'est renseignée.
function resoudreImage(chemin) {
    const base = window.BASE_PATH || ''
    if (!chemin) return base + '/frontend/assets/images/default.svg'
    return chemin.startsWith('http') ? chemin : base + chemin
}

const voirmenu = document.querySelector('#voir_menu')
const menu = document.querySelector('#notre-menu')
const navlinks = document.querySelector('#nav-links')
const burgerMenu = document.querySelector('#burger-menu')
const mainheader = document.querySelector("#main-header")
const nbrplat = document.getElementById('nbr_plat')
const btnTous = document.querySelector("#btn-tous")
const btnViandes = document.querySelector("#btn-viandes")
const btnPoissons = document.querySelector("#btn-poissons")
const allbtn = document.querySelectorAll('.btn-filtre')
const modal = document.querySelector('#modal-overlay')
const btnCommanderModal = document.querySelector('#btn-commander-modal')
const btnPrev = document.querySelector('#prev-plat')
const btnNext = document.querySelector('#next-plat')
const closeModal = document.querySelector('#close-modal')
const soumissionForm = document.querySelector('#reservation form')

let platSelectionne = null
let indexPlatActuel = 0

let plats = []

function obtenirPanier() {
    return JSON.parse(localStorage.getItem("panier")) || []
}

function sauvegarderPanier(panier) {
    localStorage.setItem("panier", JSON.stringify(panier))
}

let prixTotalCalculé = 0

function genererOptionsAccompagnement(plat) {
    const conteneurOptions = document.querySelector('#options-accompagnement')
    if (!conteneurOptions) return

    conteneurOptions.innerHTML = `<p class="font-bold text-gray-700 mb-2">Choisissez votre accompagnement :</p>`

    const premierSupplement = plat.accompagnements[0]?.supplement_prix || 0
    prixTotalCalculé = plat.prix + premierSupplement

    const modalPrix = document.querySelector('#modal-prix')
    if (modalPrix) modalPrix.textContent = `${prixTotalCalculé}FCFA`

    plat.accompagnements.forEach((acc, index) => {
        const div = document.createElement('div')
        div.className = "flex items-center space-x-3 mb-2 p-3 border rounded-xl hover:border-braise hover:bg-stone-50 cursor-pointer transition-all"

        const texteSupplement = acc.supplement_prix === 0
            ? '<span class="text-green-600 font-medium">(inclus)</span>'
            : `<span class="text-gray-400">(+${acc.supplement_prix} FCFA)</span>`

        div.innerHTML = `
            <input type="radio" id="acc-${index}" name="accompagnement" value="${acc.nom}" data-sup="${acc.supplement_prix}" class="accent-braise w-4 h-4" ${index === 0 ? 'checked' : ''}>
            <label for="acc-${index}" class="flex-grow cursor-pointer text-gray-700 font-medium flex justify-between items-center">
                ${acc.nom} ${texteSupplement}
            </label>`

        div.addEventListener('click', () => {
            const input = div.querySelector('input')
            input.checked = true
            prixTotalCalculé = plat.prix + acc.supplement_prix
            const modalPrix = document.querySelector('#modal-prix')
            if (modalPrix) modalPrix.textContent = `${prixTotalCalculé}FCFA`
        })

        conteneurOptions.appendChild(div)
    })
}

function ajouterAuPanier(plat) {
    let panier = obtenirPanier()

    const radioCoche = document.querySelector('input[name="accompagnement"]:checked')
    const optionChoisie = radioCoche ? radioCoche.value : "Aucun"
    const supplement = radioCoche ? parseInt(radioCoche.dataset.sup) : 0

    const index = panier.findIndex(item => item.id === plat.id && item.accompagnement === optionChoisie)

    if (index !== -1) {
        panier[index].quantite += 1
    } else {
        panier.push({
            id: plat.id,
            nom: plat.nom,
            image: plat.image,
            basePrix: plat.prix,
            prix: plat.prix + supplement,
            accompagnement: optionChoisie,
            quantite: 1
        })
    }

    sauvegarderPanier(panier)

    if (typeof updateBadge === "function") updateBadge()
    if (typeof showToast === "function") showToast(`${plat.nom} ajouté au panier !`)
}

function afficherContenuModal(index) {
    indexPlatActuel = index
    platSelectionne = plats[indexPlatActuel]

    const titre = document.querySelector('#modal-titre')
    const prix = document.querySelector('#modal-prix')
    const desc = document.querySelector('#modal-desc')
    const img = document.querySelector('#modal-img')

    if (titre) titre.textContent = platSelectionne.nom
    if (prix) prix.textContent = `${platSelectionne.prix} FCFA`
    if (desc) desc.textContent = platSelectionne.description

    if (img) {
        img.src = resoudreImage(platSelectionne.image)
        img.alt = platSelectionne.nom
    }

    genererOptionsAccompagnement(platSelectionne)
}

function ouvrirModal(plat) {
    const index = plats.findIndex(p => p.id === plat.id)
    afficherContenuModal(index)

    modal.classList.remove('hidden')
    modal.classList.add('flex')
    document.body.classList.add('overflow-hidden')
}

function fermerModal() {
    modal.classList.remove('flex')
    modal.classList.add('hidden')
    document.body.classList.remove('overflow-hidden')
    platSelectionne = null
}

function resetStyles() {
    allbtn.forEach(b => {
        b.classList.remove('bg-braise', 'text-white', 'font-bold', 'shadow-md')
        b.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200')
    })
}

function appliquerStyleActif(bouton) {
    resetStyles()
    bouton.classList.add('bg-braise', 'text-white', 'font-bold', 'shadow-md')
    bouton.classList.remove('bg-white', 'text-gray-700', 'border-gray-200')
}

function afficherMenu(liste) {
    const zone = document.querySelector('#conteneur-grid')
    if (!zone) return

    zone.innerHTML = ''

    if (nbrplat) nbrplat.textContent = `${liste.length} plat(s) disponible(s)`

    liste.forEach(plat => {
        const article = document.createElement('article')
        article.className = "max-w-sm bg-white rounded-super shadow-lg overflow-hidden border border-gray-100 hover:border-braise transition-all duration-300 hover:shadow-2xl group cursor-pointer"

        article.innerHTML = `
            <div class="h-56 overflow-hidden">
                <img src="${resoudreImage(plat.image)}" alt="${plat.nom}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-tropical-green">${plat.nom}</h3>
                    <span class="text-braise font-bold text-lg">${plat.prix} FCFA</span>
                </div>
                <p class="text-gray-600 text-sm mb-2 italic">Cliquez pour voir les détails</p>
            </div>`

        article.addEventListener('click', () => ouvrirModal(plat))
        zone.appendChild(article)
    })
}

function updateBadge() {
    const panier = JSON.parse(localStorage.getItem("panier")) || []
    const badge = document.getElementById('cart-badge')

    const total = panier.reduce((a, b) => a + b.quantite, 0)

    if (!badge) return

    if (total > 0) {
        badge.innerText = total
        badge.classList.remove('hidden')
    } else {
        badge.classList.add('hidden')
    }
}

function showToast(message) {
    const container = document.getElementById('toast-container')
    if (!container) return

    const toast = document.createElement('div')
    toast.className = "bg-white border-l-4 border-braise shadow-lg rounded-r-lg p-4 flex items-center gap-3"

    toast.innerHTML = `<i class="fa fa-check-circle text-braise text-xl"></i><span>${message}</span>`

    container.appendChild(toast)

    setTimeout(() => toast.remove(), 3000)
}

document.addEventListener('DOMContentLoaded', updateBadge)

if (burgerMenu && navlinks) {
    burgerMenu.addEventListener('click', () => navlinks.classList.toggle('hidden'))
    navlinks.addEventListener('click', () => navlinks.classList.add('hidden'))
}

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        mainheader.classList.add('shadow-md', 'py-2')
    } else {
        mainheader.classList.remove('shadow-md', 'py-2')
    }
})

if (voirmenu && menu) {
    voirmenu.addEventListener('click', () => menu.scrollIntoView({ behavior: 'smooth' }))
}

if (btnCommanderModal) {
    btnCommanderModal.addEventListener('click', (e) => {
        e.preventDefault()
        if (platSelectionne) ajouterAuPanier(platSelectionne)
    })
}

if (closeModal) closeModal.addEventListener('click', fermerModal)

if (modal) {
    modal.addEventListener('click', e => {
        if (e.target === modal) fermerModal()
    })
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fermerModal()
})

if (btnNext) {
    btnNext.addEventListener('click', e => {
        e.stopPropagation()
        indexPlatActuel = (indexPlatActuel + 1) % plats.length
        afficherContenuModal(indexPlatActuel)
    })
}

if (btnPrev) {
    btnPrev.addEventListener('click', e => {
        e.stopPropagation()
        indexPlatActuel = (indexPlatActuel - 1 + plats.length) % plats.length
        afficherContenuModal(indexPlatActuel)
    })
}

if (btnPoissons) {
    btnPoissons.addEventListener('click', () => {
        appliquerStyleActif(btnPoissons)
        afficherMenu(plats.filter(p => p.categorie === "poisson"))
    })
}

if (btnViandes) {
    btnViandes.addEventListener('click', () => {
        appliquerStyleActif(btnViandes)
        afficherMenu(plats.filter(p => p.categorie === "viande"))
    })
}

if (btnTous) {
    btnTous.addEventListener('click', () => {
        appliquerStyleActif(btnTous)
        afficherMenu(plats)
    })
}

if (soumissionForm) {
    soumissionForm.addEventListener('submit', e => {
        e.preventDefault()

        // recup donnee du formulaire
        const nom = soumissionForm.querySelector('input[type="text"]').value
        const nb_personnes = soumissionForm.querySelector('input[type="number"]').value
        const date_heure = soumissionForm.querySelector('input[type="datetime-local"]').value

        const donnees = {
            nom_complet: nom,
            nb_personnes: parseInt(nb_personnes),
            date_heure: date_heure,
            commentaire: null
        }

        fetch((window.BASE_PATH || '') + '/backend/api/reservations.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(donnees)
        })
        .then(r => r.json())
        .then(reponse => {
            const confirmation = document.getElementById('soumission')
            if (reponse.succes) {
                confirmation.textContent = "Réservation réussie ! À très bientôt."
                confirmation.classList.add('text-tropical-green', 'font-bold')
                soumissionForm.reset()
            } else {
                confirmation.textContent = "Erreur : " + reponse.erreur
                confirmation.classList.add('text-red-500')
            }
        })
    })
}

fetch((window.BASE_PATH || '') + '/backend/api/plats.php')
    .then(r => r.json())
    .then(data => {
        plats = data
        afficherMenu(plats)
        appliquerStyleActif(btnTous)
    })