<?php require_once __DIR__ . '/base_path.php'; $base = basePath(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titre ?? 'Braise Marine'; ?></title>
    <link rel="stylesheet" href="<?php echo $base; ?>/frontend/assets/css/style.css" />
    <link rel="stylesheet" href="<?php echo $base; ?>/frontend/assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script>window.BASE_PATH = <?php echo json_encode($base); ?>;</script>
</head>

<body class="antialiased text-charcoal <?php echo $page === 'commande' ? 'bg-stone-50' : 'bg-white'; ?>">

    <?php if ($page === 'index') : ?>
        <div id="toast-container" class="fixed top-24 right-6 z-[10000] flex flex-col gap-3"></div>
    <?php endif; ?>

    <header id="main-header" class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50 transition-all duration-300">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">

            <!-- ZONE MODIFIÉE ICI -->
            <div class="flex items-center space-x-2">
                <!-- La classe w-10 h-10 a été changée pour w-8 h-8 -->
               
                <span class="text-xl font-bold tracking-tight text-tropical-green font-display">
                    Braise<span class="text-braise">Marine</span>
                </span>
            </div>
            <!-- FIN DE LA ZONE MODIFIÉE -->

            <?php if ($page === 'index') : ?>
                <div class="flex items-center space-x-6">
                    <ul id="nav-links" class="hidden md:flex space-x-8 font-medium text-gray-700">
                        <li class="hover:text-braise cursor-pointer transition"><a href="#menu">La Carte</a></li>
                        <li class="hover:text-braise cursor-pointer transition"><a href="#reservation">Réservations</a></li>
                        <li class="hover:text-braise cursor-pointer transition"><a href="#contact">Contact</a></li>
                    </ul>

                    <!-- Icône panier -->
                    <a href="<?php echo $base; ?>/commande" class="text-tropical-green hover:text-braise transition-colors relative" title="Ma commande" aria-label="Voir mon panier">
                        <i class="fas fa-shopping-basket text-2xl"></i>
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-braise text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full hidden">0</span>
                    </a>

                    <button id="burger-menu" class="md:hidden text-tropical-green" aria-expanded="false" aria-controls="nav-links" aria-label="Menu de navigation">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>

            <?php else : ?>
                <div class="flex items-center space-x-6">
                    <a href="<?php echo $base; ?>/" class="text-tropical-green font-medium hover:text-braise transition">
                        <i class="fas fa-arrow-left mr-2"></i>Retour au menu
                    </a>
                    <div class="relative">
                        <a href="<?php echo $base; ?>/commande" class="p-2 block" aria-label="Voir mon panier">
                            <i class="fas fa-shopping-basket text-2xl text-tropical-green"></i>
                            <span id="cart-count" class="absolute -top-1 -right-1 bg-braise text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </nav>
    </header>