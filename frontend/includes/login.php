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
</head>
<body class="antialiased bg-stone-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-10 rounded-super shadow-2xl w-full max-w-md border border-gray-100">
        <h1 class="text-3xl font-bold text-tropical-green text-center mb-8">
            Espace Admin
        </h1>

        <?php if ($erreur) : ?>
            <p class="text-red-500 text-center mb-4 font-medium"><?php echo $erreur ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Identifiant</label>
                <input type="text" name="username" required placeholder="admin"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-braise outline-none transition">
            </div>
            <button type="submit"
                class="w-full bg-braise text-white font-bold py-4 rounded-xl shadow-lg hover:brightness-110 transition-all mt-4">
                Se connecter
            </button>
        </form>
    </div>

</body>
</html>