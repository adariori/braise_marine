// Données de démonstration affichées si l'API backend est injoignable
// (ex: pas de base de données MySQL branchée en production). Purement
// côté front, aucune commande/réservation réelle n'est possible avec ces
// données — c'est un filet pour ne pas laisser la page vide.
//
// Chemins d'image en racine (sans préfixe) : le rendu applique déjà
// window.BASE_PATH via resoudreImage() / la même logique inline, exactement
// comme pour les données venant de l'API.
function platsDeDemo() {
    return [
        {
            id: 1, categorie: 'Viandes', id_categorie: 3,
            nom: 'Poulet Braisé',
            description: "Accompagné d'allocos et sauce maison.",
            prix: 15.00,
            image: '/frontend/assets/images/viandes/poulet-braise.svg',
            accompagnements: [
                { id_acc: 1, nom: 'Alloco', supplement_prix: 0 },
                { id_acc: 2, nom: 'Riz blanc', supplement_prix: 0 }
            ]
        },
        {
            id: 2, categorie: 'Poissons', id_categorie: 2,
            nom: 'Poisson Braisé',
            description: 'Poisson frais grillé au feu de bois.',
            prix: 20.00,
            image: '/frontend/assets/images/poissons/poisson-braise.svg',
            accompagnements: [
                { id_acc: 1, nom: 'Alloco', supplement_prix: 0 },
                { id_acc: 2, nom: 'Riz blanc', supplement_prix: 0 }
            ]
        },
        {
            id: 3, categorie: 'Viandes', id_categorie: 3,
            nom: "Côtelettes d'Agneau",
            description: 'Agneau tendre mariné aux herbes tropicales.',
            prix: 22.00,
            image: '/frontend/assets/images/viandes/agneau.svg',
            accompagnements: [
                { id_acc: 1, nom: 'Alloco', supplement_prix: 0 },
                { id_acc: 2, nom: 'Riz blanc', supplement_prix: 0 }
            ]
        },
        {
            id: 4, categorie: 'Poissons', id_categorie: 2,
            nom: 'Gambas Grillées',
            description: 'Gambas géantes marinées au citron vert.',
            prix: 25.00,
            image: '/frontend/assets/images/poissons/gambas.svg',
            accompagnements: [
                { id_acc: 1, nom: 'Alloco', supplement_prix: 0 },
                { id_acc: 2, nom: 'Riz blanc', supplement_prix: 0 }
            ]
        },
        {
            id: 5, categorie: 'Viandes', id_categorie: 3,
            nom: 'Brochettes Mixtes',
            description: 'Assortiment de viandes grillées.',
            prix: 18.00,
            image: '/frontend/assets/images/viandes/brochettes.svg',
            accompagnements: [
                { id_acc: 1, nom: 'Alloco', supplement_prix: 0 },
                { id_acc: 2, nom: 'Riz blanc', supplement_prix: 0 }
            ]
        }
    ]
}

function annoncesDeDemo() {
    return [
        {
            id_annonce: 1,
            titre: 'Soirée grillades du vendredi',
            description: 'Menu spécial face à la mer, tous les vendredis soir.',
            image_url: '/frontend/assets/images/evenement.svg',
            date_debut: null,
            date_fin: null
        }
    ]
}
