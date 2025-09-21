<?php
require 'database.php';

// 1. Construction de la requête SQL de base
$sql = "
    SELECT DISTINCT a.id_article, a.titre, a.resume, a.date_publication, aut.nom as auteur_nom
    FROM Articles a
    JOIN auteur aut ON a.id_auteur = aut.id_auteur
    LEFT JOIN Liaison_Article_Categorie lac ON a.id_article = lac.id_article
    LEFT JOIN categories cat ON lac.id_categorie = cat.id_categorie
    LEFT JOIN Liaison_Article_Mot_Cle lamc ON a.id_article = lamc.id_article
    LEFT JOIN Mots_Cles mc ON lamc.id_mot_cle = mc.id_mot_cle
    WHERE a.statut = 'publié'
";

$params = [];

// 2. Ajout des filtres en fonction des paramètres GET
if (!empty($_GET['category'])) {
    $sql .= " AND cat.id_categorie = :category";
    $params[':category'] = $_GET['category'];
}

if (!empty($_GET['author'])) {
    $sql .= " AND aut.nom LIKE :author";
    $params[':author'] = '%' . $_GET['author'] . '%';
}

if (!empty($_GET['keywords'])) {
    $sql .= " AND (a.titre LIKE :keywords OR a.resume LIKE :keywords OR mc.mot_cle LIKE :keywords)";
    $params[':keywords'] = '%' . $_GET['keywords'] . '%';
}

if (!empty($_GET['date-from'])) {
    $sql .= " AND a.date_publication >= :date_from";
    $params[':date_from'] = $_GET['date-from'];
}

if (!empty($_GET['date-to'])) {
    $sql .= " AND a.date_publication <= :date_to";
    $params[':date_to'] = $_GET['date-to'] . ' 23:59:59';
}

// Ajout du filtre de recherche générale
if (!empty($_GET['query'])) {
    $sql .= " AND (a.titre LIKE :general_query OR a.resume LIKE :general_query OR mc.mot_cle LIKE :general_query OR aut.nom LIKE :general_query)";
    $params[':general_query'] = '%' . $_GET['query'] . '%';
}

$sql .= " ORDER BY a.date_publication DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll();

    if ($articles) {
        foreach ($articles as $article) {
            echo '<article class="list-item">';
            echo '<h3>' . htmlspecialchars($article['titre']) . '</h3>';
            echo '<p class="author-date'>Par ' . htmlspecialchars($article['auteur_nom']) . ' - ' . (new DateTime($article['date_publication']))->format('d/m/Y') . '</p>';
            echo '<p>' . htmlspecialchars($article['resume']) . '</p>';
            echo '<a href="article.php?id=' . $article['id_article'] . '" class="read-more">Lire la suite</a>';
            echo '</article>';
        }
    } else {
        echo '<p>Aucun article ne correspond à vos critères de recherche.</p>';
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>Erreur lors de la recherche d'articles.</p>";
    error_log($e->getMessage()); // Log pour le debug
}
?>