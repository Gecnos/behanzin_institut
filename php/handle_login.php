<?php
session_start();
require 'database.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis.']);
    exit;
}

// Chercher d'abord dans la table des utilisateurs (admin, editeur, relecteur)
$stmt = $pdo->prepare("SELECT id_utilisateur, nom, prenom, email, mot_de_passe, role FROM Utilisateurs WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['mot_de_passe'])) {
    $_SESSION['user_id'] = $user['id_utilisateur'];
    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
    $_SESSION['user_role'] = $user['role'];
    
    // Rediriger en fonction du rôle
    $redirect_url = ($user['role'] === 'relecteur') ? 'backoffice/review_dashboard.php' : 'backoffice/index.php';

    echo json_encode(['success' => true, 'redirect' => $redirect_url]);
    exit;
}

// Si non trouvé, chercher dans la table des auteurs
$stmt = $pdo->prepare("SELECT id_auteur, nom, prenom, email, password FROM auteur WHERE email = :email");
$stmt->execute(['email' => $email]);
$author = $stmt->fetch();

if ($author && password_verify($password, $author['password'])) {
    $_SESSION['user_id'] = $author['id_auteur'];
    $_SESSION['user_name'] = $author['prenom'] . ' ' . $author['nom'];
    $_SESSION['user_role'] = 'auteur';
    echo json_encode(['success' => true, 'redirect' => 'index.php']); // Rediriger vers l'accueil pour les auteurs
    exit;
}

echo json_encode(['success' => false, 'message' => 'Identifiants incorrects.']);
?>