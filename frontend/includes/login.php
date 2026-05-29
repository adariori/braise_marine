<?php
session_start();

//deconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../backend/config/connect.php';
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $bdd->prepare("SELECT * FROM Admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $erreur = "Identifiant ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin | Grillades Tropicales</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body class="antialiased bg-sand flex items-center justify-center min-h-screen">

    <div class="bg-white p-10 rounded-super shadow-2xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-tropical-green font-display">
                Espace Admin
            </h1>
            <p class="text-gray-500 text-sm mt-2">Grillades Tropicales</p>
        </div>

        <?php if ($erreur) : ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-center mb-6 font-medium text-sm" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Identifiant</label>
                <input type="text" name="username" id="username" required placeholder="admin"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe</label>
                <input type="password" name="password" id="password" required placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise focus:ring-1 focus:ring-braise outline-none transition bg-stone-50">
            </div>
            <button type="submit"
                class="w-full bg-braise text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-fire transition-all btn-primary mt-6">
                <i class="fas fa-lock mr-2"></i>Se connecter
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-8">
            © 2026 Grillades Tropicales — Accès réservé
        </p>
    </div>

</body>
</html>