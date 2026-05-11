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

const plats = [
    {
        id: 1,
        nom: "Poulet Braisé",
        prix: 15,
        image: "../assets/images/1.jpg",
        description: "Accompagné d'allocos et sauce maison.",
        categorie: "viande",
        accompagnements: ["Alloco", "Riz blanc", "Attiéké"]
    },
    {
        id: 2,
        nom: "Poisson Braisé",
        prix: 20,
        image: "../assets/images/2.jpg",
        description: "Poisson frais grillé au feu de bois.",
        categorie: "poisson",
        accompagnements: ["Frites de patate", "Alloco", "Riz"]
    },
    {
        id: 3,
        nom: "Côtelettes d'Agneau",
        prix: 22,
        image: "../assets/images/3.jpg",
        description: "Agneau tendre mariné aux herbes tropicales.",
        categorie: "viande",
        accompagnements: ["Alloco", "Riz blanc", "Attiéké"]
    },
    {
        id: 4,
        nom: "Gambas Grillées",
        prix: 25,
        image: "../assets/images/4.jpg",
        description: "Gambas géantes marinées au citron vert.",
        categorie: "poisson",
        accompagnements: ["Riz safrané", "Légumes grillés"]
    },
    {
        id: 5,
        nom: "Brochettes Mixtes",
        prix: 18,
        image: "../assets/images/5.jpg",
        description: "Assortiment de viandes grillées.",
        categorie: "viande",
        accompagnements: ["Alloco", "Frites", "Salade"]
    }
]

function obtenirPanier() {
    return JSON.parse(localStorage.getItem("panier")) || []
}

function sauvegarderPanier(panier) {
    localStorage.setItem("panier", JSON.stringify(panier))
}

function updateBadge() {
    const panier = obtenirPanier()
    const total = panier.reduce((sum, item) => sum + item.quantite, 0)
    const badge = document.querySelector('.fa-shopping-basket + span')
    if (badge) badge.textContent = total
}

function genererOptionsAccompagnement(plat) {
    const conteneurOptions = document.querySelector('#options-accompagnement')
    if (!conteneurOptions) return

    conteneurOptions.innerHTML = `<p class="font-bold text-gray-700 mb-2">Choisissez votre accompagnement :</p>`

    plat.accompagnements.forEach((acc, index) => {
        const div = document.createElement('div')
        div.className = "flex items-center space-x-3 mb-2 p-2 border rounded-xl hover:bg-stone-50 cursor-pointer"
        div.innerHTML = `
            <input type="radio" id="acc-${index}" name="accompagnement" value="${acc}" ${index === 0 ? 'checked' : ''} class="accent-braise w-4 h-4">
            <label for="acc-${index}" class="flex-grow cursor-pointer text-gray-600">${acc}</label>
        `
        div.addEventListener('click', () => {
            div.querySelector('input').checked = true
        })
        conteneurOptions.appendChild(div)
    })
}

function ajouterAuPanier(plat) {
    let panier = obtenirPanier()
    const optionChoisie = document.querySelector('input[name="accompagnement"]:checked').value
    
    const index = panier.findIndex(item => item.id === plat.id && item.accompagnement === optionChoisie)

    if (index !== -1) {
        panier[index].quantite += 1
    } else {
        panier.push({ ...plat, accompagnement: optionChoisie, quantite: 1 })
    }

    sauvegarderPanier(panier)
    updateBadge()
}

function afficherContenuModal(index) {
    indexPlatActuel = index
    platSelectionne = plats[indexPlatActuel]

    document.querySelector('#modal-titre').textContent = platSelectionne.nom
    document.querySelector('#modal-prix').textContent = `${platSelectionne.prix}€`
    document.querySelector('#modal-desc').textContent = platSelectionne.description

    const imgModal = document.querySelector('#modal-img')
    imgModal.src = platSelectionne.image
    imgModal.alt = platSelectionne.nom

    genererOptionsAccompagnement(platSelectionne)
}

function ouvrirModal(plat) {
    const indexClicke = plats.findIndex(p => p.id === plat.id)
    afficherContenuModal(indexClicke)
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
    allbtn.forEach(bouton => {
        bouton.classList.remove('bg-braise', 'text-white', 'font-bold', 'shadow-md')
        bouton.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200')
    })
}

function appliquerStyleActif(bouton) {
    resetStyles()
    bouton.classList.add('bg-braise', 'text-white', 'font-bold', 'shadow-md')
    bouton.classList.remove('bg-white', 'text-gray-700', 'border-gray-200')
}

function afficherMenu(listeAPresenter) {
    const zoneFaim = document.querySelector('#conteneur-grid')
    zoneFaim.innerHTML = ``
    if (nbrplat) nbrplat.textContent = `${listeAPresenter.length} plat(s) disponible(s)`

    listeAPresenter.forEach(plat => {
        const article = document.createElement('article')
        article.className = "max-w-sm bg-white rounded-super shadow-lg overflow-hidden border border-gray-100 hover:border-braise transition-all duration-300 hover:shadow-2xl group cursor-pointer"
        article.innerHTML = `
            <div class="h-56 overflow-hidden">
                <img src="${plat.image}" alt="${plat.nom}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-tropical-green">${plat.nom}</h3>
                    <span class="text-braise font-bold text-lg">${plat.prix}€</span>
                </div>
                <p class="text-gray-600 text-sm mb-2 italic">Cliquez pour voir les détails</p>
            </div>`
        article.addEventListener('click', () => ouvrirModal(plat))
        zoneFaim.appendChild(article)
    })
}

burgerMenu.addEventListener('click', () => navlinks.classList.toggle('hidden'))
navlinks.addEventListener('click', () => navlinks.classList.add('hidden'))

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        mainheader.classList.add('shadow-md', 'py-2')
        mainheader.classList.remove('py-4')
    } else {
        mainheader.classList.remove('shadow-md', 'py-2')
        mainheader.classList.add('py-4')
    }
})

voirmenu.addEventListener('click', () => menu.scrollIntoView({ behavior: 'smooth' }))

btnCommanderModal.addEventListener('click', (e) => {
    e.preventDefault()
    if (platSelectionne) {
        const texteOriginal = btnCommanderModal.innerHTML
        ajouterAuPanier(platSelectionne)
        btnCommanderModal.innerHTML = `<i class="fa fa-check"></i> Ajouté !`
        btnCommanderModal.classList.replace('bg-tropical-green', 'bg-green-600')
        setTimeout(() => {
            fermerModal()
            btnCommanderModal.innerHTML = texteOriginal
            btnCommanderModal.classList.replace('bg-green-600', 'bg-tropical-green')
        }, 800)
    }
})

closeModal.addEventListener('click', fermerModal)
modal.addEventListener('click', (e) => { if (e.target === modal) fermerModal() })
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') fermerModal() })

btnNext.addEventListener('click', (e) => {
    e.stopPropagation()
    indexPlatActuel = (indexPlatActuel + 1) % plats.length
    afficherContenuModal(indexPlatActuel)
})

btnPrev.addEventListener('click', (e) => {
    e.stopPropagation()
    indexPlatActuel = (indexPlatActuel - 1 + plats.length) % plats.length
    afficherContenuModal(indexPlatActuel)
})

btnPoissons.addEventListener('click', () => {
    appliquerStyleActif(btnPoissons)
    afficherMenu(plats.filter(p => p.categorie === "poisson"))
})

btnViandes.addEventListener('click', () => {
    appliquerStyleActif(btnViandes)
    afficherMenu(plats.filter(p => p.categorie === "viande"))
})

btnTous.addEventListener('click', () => {
    appliquerStyleActif(btnTous)
    afficherMenu(plats)
})

if (soumissionForm) {
    soumissionForm.addEventListener('submit', (e) => {
        e.preventDefault()
        const confirmation = document.getElementById('soumission')
        confirmation.textContent = "Réservation réussie ! À très bientôt."
        confirmation.classList.add('text-tropical-green', 'mt-4', 'font-bold')
        soumissionForm.reset()
    })
}

updateBadge()
appliquerStyleActif(btnTous)
afficherMenu(plats)