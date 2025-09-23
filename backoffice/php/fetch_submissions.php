<?php
require '../../php/database.php';

// TODO: Ajouter une vérification de session et de rôle

try {
    $stmt = $pdo->prepare(
        "SELECT a.id_article, a.titre, a.image, a.date_soumission, aut.nom, aut.prenom
         FROM Articles a
         JOIN auteur aut ON a.id_auteur = aut.id_auteur
         WHERE a.statut = 'en attente'
         ORDER BY a.date_soumission ASC"
    );
    $stmt->execute();
    $submissions = $stmt->fetchAll();

    if ($submissions) {
        echo '<table>';
        echo '<thead><tr><th>Image</th><th>Titre</th><th>Auteur</th><th>Date de soumission</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        foreach ($submissions as $sub) {
            echo '<tr>';
            echo '<td>';
            if ($sub['image']) {
                echo '<img src="../' . htmlspecialchars($sub['image']) . '" alt="Article Image" style="width: 50px; height: auto;">';
            } else {
                echo 'No Image';
            }
            echo '</td>';
            echo '<td>' . htmlspecialchars($sub['titre']) . '</td>';
            echo '<td>' . htmlspecialchars($sub['prenom'] . ' ' . $sub['nom']) . '</td>';
            echo '<td>' . (new DateTime($sub['date_soumission']))->format('d/m/Y H:i') . '</td>';
            echo '<td><a href="submission_detail.php?id=' . $sub['id_article'] . '">Voir détails</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>Aucune soumission en attente.</p>';
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>Erreur lors de la récupération des soumissions.</p>";
    error_log("Erreur fetch_submissions: " . $e->getMessage());
}
?>