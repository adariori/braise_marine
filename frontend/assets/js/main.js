const voirmenu = document.querySelector('#voir_menu');
const menu = document.querySelector('#notre-menu');

const plats = [
    {
        nom: "Poulet Braisé",
        prix: 15,
        image: "poulet.jpg",
        description: "Accompagné d'allocos et sauce maison."
    },
    {
        nom: "Poisson Braisé",
        prix: 20,
        image: "poisson.jpg",
        description: "Poisson frais grillé au feu de bois."
    },
    {
        nom: "Côtelettes d'Agneau",
        prix: 22,
        image: "agneau.jpg",
        description: "Agneau tendre mariné aux herbes tropicales."
    }
];

voirmenu.addEventListener('click', (e) => {
    menu.scrollIntoView({ behavior: 'smooth' })
})


function afficherMenu() {
    const zoneFaim = document.querySelector('#conteneur-grid')

    plats.forEach(plat => {
        zoneFaim.innerHTML += `
            <article class="max-w-sm bg-white rounded-super shadow-lg overflow-hidden border border-gray-100 hover:border-amber-glow transition-all duration-300 hover:shadow-2xl group">
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
            </article>
        `;
    });
}

afficherMenu()