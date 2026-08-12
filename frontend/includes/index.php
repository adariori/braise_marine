<?php
    /**
     * @author    ARIORI OLOROUNKO Adéliyi Odjouola Moshood
     * @github    https://github.com/adariori
     * @web       https://portefolio-nine-iota.vercel.app/
     * @contact   adariori3@gmail.com
     * @location  Cotonou, Benin
     *
     * @project   Braise Marine
     * @version   1.0.0
     * @year      2026
     * @stack     PHP, MySQL, Tailwind CSS, JavaScript
     *
     * @license   Creative Commons BY-NC-ND 4.0
     *            © 2026 ARIORI OLOROUNKO Adéliyi Odjouola Moshood
     *            Consultation autorisée à titre de référence uniquement. Toute réutilisation commerciale ou modification est interdite.
     */
    $page = 'index';
    $titre = 'BRAISE MARINE | Le Goût du Feu';
    include 'header.php';
    ?>


    <main>
        <!-- hero -->
        <section class="hero-section relative min-h-[85vh] flex items-center justify-center text-center text-white" style="background-image: url('../assets/images/hero.jpg');">
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/70"></div>
            <div class="relative z-10 container mx-auto px-6">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 tracking-tight font-display">
                    Le Goût Authentique des <span class="text-amber-glow">Tropiques</span>
                </h1>
                <p class="text-lg md:text-xl mb-10 max-w-2xl mx-auto text-gray-100">
                    Découvrez nos grillades au feu de bois, marinées avec passion et servies face à la mer.
                </p>

                <div class="hero-buttons flex flex-col md:flex-row justify-center gap-4">
                    <button
                        class="bg-braise text-white px-10 py-4 rounded-full font-bold text-lg shadow-lg hover:shadow-fire transition-all hover:scale-105 btn-primary"
                        id="voir_menu">
                        Voir le menu
                    </button>
                    <a href="#reservation"
                        class="bg-transparent border-2 border-white hover:bg-white hover:text-tropical-green text-white px-10 py-4 rounded-full font-bold text-lg transition-all text-center">
                        Réserver une table
                    </a>
                </div>
            </div>
        </section>

        <!-- Annonces -->
        <section class="py-16 bg-gradient-to-r from-braise to-amber-600">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-10 font-display">Annonces & Événements</h2>

                <div id="conteneur-annonces" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Injecté par JS -->
                </div>
            </div>
        </section>

        <!-- about -->
        <section id="about" class="py-20 bg-sand">
            <div class="container mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold text-tropical-green mb-6 font-display">L'Art de la Braise</h2>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        Nous sommes un restaurant de grillades tropicales situé à la plage. Nous proposons une cuisine
                        délicieuse et des plats préparés avec les meilleurs ingrédients locaux.
                    </p>
                </div>
                <div class="rounded-super overflow-hidden shadow-2xl aspect-video bg-gray-200">
                    <img src="../assets/images/about.png" alt="Notre restaurant" class="w-full h-full object-cover" loading="lazy">
                </div>
            </div>
        </section>

        <!-- Menu -->
        <section class="py-24 bg-white" id="menu">
            <div class="container mx-auto px-6">
                <div class="flex justify-center gap-4 mb-12">
                    <button id="btn-tous"
                        class="btn-filtre bg-braise text-white px-8 py-2 rounded-full font-bold shadow-md transition-all" aria-pressed="true">Tous</button>
                    <button id="btn-viandes"
                        class="btn-filtre bg-white border border-gray-200 text-gray-700 px-8 py-2 rounded-full hover:border-braise transition-all" aria-pressed="false">Viandes</button>
                    <button id="btn-poissons"
                        class="btn-filtre bg-white border border-gray-200 text-gray-700 px-8 py-2 rounded-full hover:border-braise transition-all" aria-pressed="false">Poissons</button>
                </div>

                <h2 class="text-4xl md:text-5xl font-bold text-center text-stone-900 mb-4 font-display" id="notre-menu">
                    Notre Menu
                </h2>
                <div class="w-24 h-1 bg-braise mx-auto mb-10 rounded-full"></div>

                <p id="nbr_plat" class="text-center mb-8 text-gray-500 italic"></p>

                <div id="conteneur-grid"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 justify-items-center">
                </div>
            </div>
        </section>

        <!-- Reservation -->
        <section class="py-20 bg-tropical-green" id="reservation">
            <div class="container mx-auto px-6">
                <div
                    class="max-w-2xl mx-auto bg-white p-10 rounded-super shadow-2xl text-gray-800 border border-white/20">
                    <h2 class="text-3xl md:text-4xl font-bold text-center text-tropical-green mb-8 font-display">Réserver une table</h2>
                    <form id="form-reservation" action="#" class="space-y-6">
                        <div>
                            <label for="nom-resa" class="block text-sm font-semibold text-gray-700 mb-2">Nom complet</label>
                            <input type="text" id="nom-resa" placeholder="Votre nom"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition"
                                required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="convives-resa" class="block text-sm font-semibold text-gray-700 mb-2">Convives</label>
                                <input type="number" id="convives-resa" placeholder="Nombre de personnes"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none">
                            </div>
                            <div>
                                <label for="date-resa" class="block text-sm font-semibold text-gray-700 mb-2">Date & Heure</label>
                                <input type="datetime-local" id="date-resa"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none"
                                    required>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-braise text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-fire transition-all btn-primary">
                            Confirmer la réservation
                        </button>
                        <p id="soumission" class="text-center font-medium text-tropical-green"></p>
                    </form>
                </div>
            </div>
        </section>

        <!-- infos -->
        <section class="py-24 bg-white" id="contact">
            <div class="container mx-auto px-6 grid md:grid-cols-2 gap-16">
                <div class="flex flex-col justify-center">
                    <h2 class="text-4xl md:text-5xl font-bold text-tropical-green mb-8 font-display">Nous trouver</h2>
                    <div class="space-y-6 text-gray-600">
                        <div class="flex items-start space-x-4">
                            <div class="bg-stone-100 p-4 rounded-full text-braise">
                                <i class="fas fa-location-dot text-xl"></i>
                            </div>
                            <p class="text-lg">Route de la Plage, 97190 Le Gosier, Guadeloupe</p>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-stone-100 p-4 rounded-full text-braise">
                                <i class="fas fa-phone text-xl"></i>
                            </div>
                            <p class="text-lg">+229 XX XX XX XX</p>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="bg-stone-100 p-4 rounded-full text-braise">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <p class="text-lg">Ouvert 7j/7 : 12h-15h / 19h-23h</p>
                        </div>
                    </div>
                </div>
                <div class="h-[400px] rounded-super overflow-hidden shadow-2xl border-8 border-sand">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3830.4!2d-61.4!3d16.2!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTbCsDEyJzAwLjAiTiA2McKwMjQnMDAuMCJX!5e0!3m2!1sfr!2sfr!4v123456789"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        title="Carte Google Maps de Braise Marine"></iframe>
                </div>
            </div>
        </section>

        <!-- Script annonces -->
        <script>
            // Charger les annonces
            function chargerAnnonces() {
                fetch('http://localhost/GRTR/backend/api/annonces.php')
                    .then(r => r.json())
                    .then(annonces => {
                        const conteneur = document.getElementById('conteneur-annonces');

                        if (annonces.length === 0) {
                            conteneur.innerHTML = '<p class="text-white text-center col-span-full">Aucune annonce pour le moment</p>';
                            return;
                        }

                        conteneur.innerHTML = annonces.map(annonce => `
                    <div class="bg-white rounded-super shadow-lg overflow-hidden hover:shadow-2xl transition-all">
                        ${annonce.image_url ? `<img src="${annonce.image_url}" alt="${annonce.titre}" class="w-full h-40 object-cover">` : ''}
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-tropical-green mb-2">${annonce.titre}</h3>
                            <p class="text-gray-600 text-sm mb-4">${annonce.description}</p>
                            ${annonce.date_debut || annonce.date_fin ? `
                                <p class="text-xs text-gray-500">
                                    ${annonce.date_debut ? `Du ${new Date(annonce.date_debut).toLocaleDateString('fr-FR')}` : ''}
                                    ${annonce.date_fin ? ` au ${new Date(annonce.date_fin).toLocaleDateString('fr-FR')}` : ''}
                                </p>
                            ` : ''}
                        </div>
                    </div>
                `).join('');
                                    })
                                    .catch(err => console.error("Erreur chargement annonces :", err));
                            }

                            // Charger au démarrage
                            document.addEventListener('DOMContentLoaded', chargerAnnonces);
                        </script>

    </main>

    <!-- footer -->
    <?php include 'footer.php'; ?>

    <!-- Modal -->
    <div id="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-titre"
        class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4 z-[9999]">
        <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl relative">

            <!-- Bouton fermeture -->
            <button id="close-modal"
                class="absolute top-4 right-4 bg-white/90 hover:bg-braise hover:text-white w-10 h-10 rounded-full flex items-center justify-center transition z-10 text-xl shadow-soft"
                aria-label="Fermer la fenêtre">
                <i class="fas fa-times"></i>
            </button>

            <!-- Image du plat -->
            <div class="h-64 relative shrink-0">
                <!-- bouton prec -->
                <button id="prev-plat"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-braise hover:text-white w-12 h-12 rounded-full flex items-center justify-center z-20 transition shadow-soft"
                    aria-label="Plat précédent">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <img src="" alt="" id="modal-img" class="w-full h-full object-cover">

                <!-- bouton suiv -->
                <button id="next-plat"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-braise hover:text-white w-12 h-12 rounded-full flex items-center justify-center z-20 transition shadow-soft"
                    aria-label="Plat suivant">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Contenu -->
            <div class="p-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="modal-titre" class="text-3xl font-bold text-tropical-green font-display"></h2>
                    <span id="modal-prix" class="text-2xl font-bold text-braise"></span>
                </div>
                <p id="modal-desc" class="text-gray-600 leading-relaxed mb-8"></p>

                <!-- accompagnement -->
                <div id="options-accompagnement"></div>

                <!-- bouton Commander -->
                <button id="btn-commander-modal"
                    class="w-full bg-tropical-green text-white font-bold py-4 rounded-xl hover:bg-braise transition-all shadow-lg flex items-center justify-center gap-2 mt-4 btn-primary">
                    <i class="fas fa-cart-shopping"></i>
                    Commander ce plat
                </button>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>