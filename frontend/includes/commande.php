<?php
$page = 'commande';
$titre = 'Ma Commande | Grillades Tropicales';
include 'header.php';
?>

<main class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-bold text-tropical-green mb-10 text-center">Finaliser ma commande</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-super shadow-sm border border-gray-100">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <i class="fa fa-shopping-basket text-braise mr-3"></i>Panier
                </h2>

                <div id="panier-liste" class="divide-y divide-gray-100">
                </div>
            </div>

            <div class="bg-white p-8 rounded-super shadow-sm border border-gray-100">
                <h2 class="text-2xl font-bold mb-6">Mode de récupération</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label
                        class="relative flex items-center p-4 border-2 border-braise bg-amber-50 rounded-xl cursor-pointer mode-option">
                        <input type="radio" name="mode" value="livraison" class="mr-3 accent-braise" checked>
                        <div>
                            <span class="block font-bold">Livraison à domicile</span>
                            <span class="text-sm text-gray-500">Arrivée estimée : 30-45 min</span>
                        </div>
                    </label>
                    <label
                        class="relative flex items-center p-4 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-tropical-green transition mode-option">
                        <input type="radio" name="mode" value="retrait" class="mr-3 accent-tropical-green">
                        <div>
                            <span class="block font-bold">Retrait sur place (Click & Collect)</span>
                            <span class="text-sm text-gray-500">Prêt dans 15 min</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-super shadow-fire sticky top-28 border border-gray-100">
                <h2 class="text-2xl font-bold mb-6 text-tropical-green">Mes coordonnées</h2>
                <form id="form-commande" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nom complet</label>
                        <input type="text" id="nom-client" required placeholder="John Doe"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Téléphone</label>
                        <input type="tel" id="tel-client" required placeholder="+229 XX XX XX XX"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
                    </div>
                    <div id="zone-adresse">
                        <label class="block text-sm font-semibold mb-1">Adresse de livraison</label>
                        <textarea id="adresse-client" rows="2" placeholder="Quartier, Rue, Maison..."
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Instructions spéciales</label>
                        <input type="text" placeholder="Ex: Pas de piment, Allergies..."
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
                    </div>

                    <div class="border-t border-gray-100 pt-6 mt-6 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sous-total</span>
                            <span id="sous-total" class="font-bold">0.00€</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frais de livraison</span>
                            <span id="frais-livraison" class="font-bold">5.00€</span>
                        </div>
                        <div
                            class="flex justify-between text-xl font-black text-tropical-green pt-2 border-t border-stone-50">
                            <span>Total à payer</span>
                            <span id="total-final">0.00€</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-braise text-white font-bold py-4 rounded-xl shadow-lg hover:brightness-110 transition-all mt-6">
                        Passer la commande
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- footer -->
<?php include 'footer.php'; ?>

<script src="../assets/js/commande.js"></script>