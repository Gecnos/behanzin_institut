<?php
require 'php/database.php';

echo "<pre>";

$email = 'relecteur@test.com';
$password = 'password123';

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute(['Relecteur', 'Super', $email, $hashed_password, 'relecteur']);

    echo "Utilisateur administrateur créé avec succès !\n";
    echo "Email: {$email}\n";
    echo "Mot de passe: {$password}\n";

} catch (PDOException $e) {
    echo "Erreur lors de la création de l'utilisateur : " . $e->getMessage();
}

echo "</pre>";
?>