const voirmenu = document.querySelector('#voir_menu')
const menu = document.querySelector('#notre-menu')
const navlinks = document.querySelector('#nav-links')
const burgerMenu = document.querySelector('#burger-menu')
const mainheader = document.querySelector("#main-header")
const btnTous = document.querySelector("#btn-tous")
const btnViandes = document.querySelector("#btn-viandes")
const btnPoissons = document.querySelector("#btn-poissons")
const allbtn = document.querySelectorAll('.btn-filtre')

burgerMenu.addEventListener('click', (e) => {
    navlinks.classList.toggle('hidden');
})

navlinks.addEventListener('click', (e) => {
    navlinks.classList.add('hidden')
})

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        mainheader.classList.add('shadow-md')
    } else {
        mainheader.classList.remove('shadow-md')
    }
})

const plats = [
    {
        nom: "Poulet Braisé",
        prix: 15,
        image: "poulet.jpg",
        description: "Accompagné d'allocos et sauce maison.",
        categorie: "viande"
    },
    {
        nom: "Poisson Braisé",
        prix: 20,
        image: "poisson.jpg",
        description: "Poisson frais grillé au feu de bois.",
        categorie: "poisson"
    },
    {
        nom: "Côtelettes d'Agneau",
        prix: 22,
        image: "agneau.jpg",
        description: "Agneau tendre mariné aux herbes tropicales.",
        categorie: "viande"
    }
]

voirmenu.addEventListener('click', (e) => {
    menu.scrollIntoView({ behavior: 'smooth' })
})

function afficherMenu(listeAPresenter) {
    const zoneFaim = document.querySelector('#conteneur-grid')

    zoneFaim.innerHTML = ``;

    listeAPresenter.forEach(plat => {
        zoneFaim.innerHTML += ` <article class="max-w-sm bg-white rounded-super shadow-lg overflow-hidden border border-gray-100 hover:border-amber-glow transition-all duration-300 hover:shadow-2xl group">
                <div class="h-56 overflow-hidden">
                    <img src="${plat.image}" alt="${plat.nom}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-tropical-green">${plat.nom}</h3>
                        <span class="text-braise font-bold text-lg">${plat.prix}€</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-6">${plat.description}</p>
                </div>
            </article>`

    });
}

btnPoissons.addEventListener('click', () => {
    resetStyles()
    const listeFiltree = plats.filter(p => p.categorie === "poisson")
    btnPoissons.classList.add('bg-braise', 'text-white')
    afficherMenu(listeFiltree)
})

btnViandes.addEventListener('click', () => {
    resetStyles()
    const listeFiltree = plats.filter(p => p.categorie === "viande")
    btnViandes.classList.add('bg-braise', 'text-white')
    afficherMenu(listeFiltree)
})

btnTous.addEventListener('click', () => {
    resetStyles()
    btnTous.classList.add('bg-braise', 'text-white')
    afficherMenu(plats)
})

function resetStyles() {
    allbtn.forEach(bouton => {
        bouton.classList.remove('bg-braise','text-white')
        bouton.classList.add('bg-white','text-gray-700')
    })
}


afficherMenu(plats)