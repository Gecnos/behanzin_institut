<?php
require '../auth_check.php';
require_role(['administrateur', 'editeur']);

header('Content-Type: text/html');

try {
    $stmt = $pdo->query("SELECT id_categorie, nom FROM categories ORDER BY nom");
    $categories = $stmt->fetchAll();

    if (empty($categories)) {
        echo '<p>Aucune catégorie trouvée.</p>';
    } else {
        echo '<table>';
        echo '<thead><tr><th>Nom</th><th>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($categories as $category) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($category['nom']) . '</td>';
            echo '<td><button class="delete-btn" data-id="' . $category['id_categorie'] . '">Supprimer</button></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur fetch_categories_list: " . $e->getMessage());
    echo '<p>Erreur de base de données.</p>';
}
?>