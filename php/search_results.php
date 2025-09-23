<?php
require 'database.php';

$query = $_GET['query'] ?? '';
$lang = $_GET['lang'] ?? 'fr';

$title_field = ($lang === 'en') ? 'titre_en' : 'titre';
$resume_field = ($lang === 'en') ? 'resume_en' : 'resume';

if (empty($query)) {
    echo '<p>Veuillez entrer un terme de recherche.</p>';
    exit;
}

try {
    $sql = "
        SELECT DISTINCT a.id_article, a.{$title_field} AS titre, a.{$resume_field} AS resume, a.date_publication, aut.nom as auteur_nom
        FROM Articles a
        JOIN auteur aut ON a.id_auteur = aut.id_auteur
        LEFT JOIN Liaison_Article_Mot_Cle lamc ON a.id_article = lamc.id_article
        LEFT JOIN Mots_Cles mc ON lamc.id_mot_cle = mc.id_mot_cle
        WHERE a.statut IN ('publié', 'accepté')
        AND (LOWER(a.{$title_field}) LIKE LOWER(:query) OR LOWER(a.{$resume_field}) LIKE LOWER(:query) OR LOWER(mc.mot_cle) LIKE LOWER(:query) OR LOWER(aut.nom) LIKE LOWER(:query))
        ORDER BY a.date_publication DESC
    ";
    $stmt = $pdo->prepare($sql);
    $search_param = '%' . $query . '%';
    $stmt->execute([':query' => $search_param]);
    $articles = $stmt->fetchAll();

    echo '<h2>Résultats de recherche pour "' . htmlspecialchars($query) . '"</h2>';

    if ($articles) {
        foreach ($articles as $article) {
            echo '<article class="list-item">';
            echo '<h3>' . htmlspecialchars($article['titre']) . '</h3>';
            echo '<p class="author-date">Par ' . htmlspecialchars($article['auteur_nom']);
            if ($article['date_publication']) {
                echo ' - ' . (new DateTime($article['date_publication']))->format('d/m/Y');
            }
            echo '</p>';
            echo '<p>' . htmlspecialchars(substr($article['resume'], 0, 200)) . '...</p>';
            echo '<a href="article.php?id=' . $article['id_article'] . '" class="read-more">Lire la suite</a>';
            echo '</article>';
        }
    } else {
        echo '<p>Aucun article ne correspond à votre recherche.</p>';
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>Erreur lors de la recherche d'articles.</p>";
    error_log("Erreur search_results: " . $e->getMessage());
}
?>