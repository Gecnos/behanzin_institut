<?php
require '../../php/database.php';

// TODO: Ajouter une vérification de session

try {
    $stmt = $pdo->query("SELECT id_utilisateur, nom, prenom FROM Utilisateurs WHERE role = 'relecteur' ORDER BY nom ASC");
    $reviewers = $stmt->fetchAll();
    
    echo '<option value="">Choisir un relecteur</option>';
    foreach ($reviewers as $reviewer) {
        echo '<option value="' . $reviewer['id_utilisateur'] . '">' . htmlspecialchars($reviewer['prenom'] . ' ' . $reviewer['nom']) . '</option>';
    }
} catch (PDOException $e) {
    error_log("Erreur fetch_reviewers: " . $e->getMessage());
    echo '<option value="">Erreur de chargement</option>';
}
?>