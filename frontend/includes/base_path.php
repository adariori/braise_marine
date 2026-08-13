<?php
/**
 * Calcule le préfixe de base du site à partir du chemin réel du script PHP
 * en cours d'exécution (SCRIPT_NAME), qui reflète toujours l'emplacement
 * physique du fichier — même quand l'URL affichée dans le navigateur est
 * une "jolie" URL réécrite par vercel.json ("/", "/admin", "/login", ...).
 *
 * Ça permet d'utiliser des liens/assets en chemin absolu qui fonctionnent
 * aussi bien :
 * - en local, derrière un alias Apache (ex: /GRTR/frontend/includes/...)
 * - en production sur Vercel (racine du domaine, ex: /frontend/includes/...)
 * sans dépendre de la profondeur de l'URL affichée par le navigateur.
 */
function basePath(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $pos = strpos($script, '/frontend/');
    return $pos === false ? '' : substr($script, 0, $pos);
}
