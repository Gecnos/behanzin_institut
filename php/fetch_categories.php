<?php
require 'database.php';

try {
    $stmt = $pdo->query("SELECT id_categorie, nom FROM categories ORDER BY nom ASC");
    $categories = $stmt->fetchAll();
    
    echo '<option value="">Toutes les catégories</option>';
    foreach ($categories as $category) {
        echo '<option value="' . $category['id_categorie'] . '">' . htmlspecialchars($category['nom']) . '</option>';
    }
} catch (PDOException $e) {
    // Ne pas bloquer le rendu, juste logguer l'erreur
    error_log("Erreur fetch_categories: " . $e->getMessage());
    echo '<option value="">Erreur de chargement</option>';
}
?>