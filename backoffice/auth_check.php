<?php
session_start();

// Inclut la connexion à la DB pour toutes les pages du back-office
require_once __DIR__ . '/../php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Optionnel: vérifier le rôle
function require_role($allowed_roles) {
    if (!in_array($_SESSION['user_role'], $allowed_roles)) {
        // On pourrait rediriger vers une page d'erreur "accès refusé"
        die('Accès non autorisé.');
    }
}
?>