const panierListe = document.querySelector('#panier-liste')
const sousTotalEl = document.querySelector('#sous-total')
const livraisonEl = document.querySelector('#frais-livraison')
const totalFinalEl = document.querySelector('#total-final')
const formCommande = document.querySelector('#form-commande')
const blocAdresse = document.querySelector('#zone-adresse')
const adresseInput = document.querySelector('#adresse-client')

function updateBadge() {
    const panier = JSON.parse(localStorage.getItem('panier')) || [];
    const totalArticles = panier.reduce((acc, item) => acc + item.quantite, 0);
    const badge = document.getElementById('cart-count');
    if (badge) {
        badge.textContent = totalArticles;
        badge.classList.toggle('hidden', totalArticles === 0);
    }
}

function obtenirPanier() {
    return JSON.parse(localStorage.getItem("panier")) || []
}

function sauvegarderPanier(panier) {
    localStorage.setItem("panier", JSON.stringify(panier))
}

function afficherPanier() {
    const panier = obtenirPanier()
    if (!panierListe) return

    panierListe.innerHTML = ''

    if (panier.length === 0) {
        panierListe.innerHTML = `
            <div class="text-center py-10">
                <i class="fa fa-shopping-basket text-4xl text-gray-200 mb-3"></i>
                <p class="text-gray-500">Votre panier est vide</p>
                <a href="index.html" class="text-braise font-bold mt-2 inline-block">Retourner au menu</a>
            </div>`
        calculerTotaux()
        updateBadge()
        return
    }

    panier.forEach((item, index) => {
        const article = document.createElement('div')
        article.className = "py-4 flex items-center justify-between"

        article.innerHTML = `
            <div class="flex items-center space-x-4">
                <img src="${item.image}" class="w-16 h-16 rounded-xl object-cover">
                <div>
                    <h3 class="font-bold text-lg">${item.nom}</h3>
                    <p class="text-xs text-gray-500 italic">
                        Accompagnement : ${item.accompagnement || 'Standard'}
                    </p>
                    <p class="text-braise font-bold">${item.prix}€</p>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <div class="flex items-center border rounded-full px-3 py-1">
                    <button onclick="changerQuantite(${index}, -1)">-</button>
                    <span class="font-bold px-2">${item.quantite}</span>
                    <button onclick="changerQuantite(${index}, 1)">+</button>
                </div>

                <button onclick="supprimerArticle(${index})">
                    <i class="fa fa-trash-o text-xl text-gray-400 hover:text-red-500"></i>
                </button>
            </div>
        `
        panierListe.appendChild(article)
    })

    calculerTotaux()
    updateBadge()
}

window.changerQuantite = function (index, delta) {
    const panier = obtenirPanier(); 
    panier[index].quantite += delta;
    
    if (panier[index].quantite <= 0) {
        return supprimerArticle(index);
    }
    
    sauvegarderPanier(panier);
    afficherPanier();
}

window.supprimerArticle = function (index) {
    const panier = obtenirPanier()
    panier.splice(index, 1)
    sauvegarderPanier(panier)
    afficherPanier()
}

function calculerTotaux() {
    const panier = obtenirPanier();

    const sousTotal = panier.reduce((sum, item) => sum + (item.prix * item.quantite), 0);

    const modeRadio = document.querySelector('input[name="mode"]:checked');
    const mode = modeRadio ? modeRadio.value : 'livraison';

    const fraisLivraison = mode === 'livraison' ? 5 : 0;
    const totalFinal = sousTotal + fraisLivraison;

    if (sousTotalEl) sousTotalEl.textContent = `${sousTotal.toFixed(2)}€`;
    if (livraisonEl) livraisonEl.textContent = `${fraisLivraison.toFixed(2)}€`;
    if (totalFinalEl) totalFinalEl.textContent = `${totalFinal.toFixed(2)}€`;
}

document.querySelectorAll('input[name="mode"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const isRetrait = radio.value === 'retrait'

        if (blocAdresse) {
            blocAdresse.classList.toggle('hidden', isRetrait)
        }

        if (adresseInput) {
            adresseInput.required = !isRetrait
        }

        calculerTotaux()
    })
})

if (formCommande) {
    formCommande.addEventListener('submit', (e) => {
        e.preventDefault()

        const panier = obtenirPanier()

        if (panier.length === 0) {
            alert("Votre panier est vide !")
            return
        }

        const nom = e.target.querySelector('#nom-client')?.value || 'Client'

        alert(`Merci ${nom} ! Votre commande est validée.`)

        localStorage.removeItem("panier")
        window.location.href = "index.html"
    })
}

afficherPanier()
calculerTotaux()
updateBadge()