<?php
require 'database.php';

$lang = $_GET['lang'] ?? 'fr';
$title_field = ($lang === 'en') ? 'titre_en' : 'titre';
$resume_field = ($lang === 'en') ? 'resume_en' : 'resume';

$query_part = "";
$limit = 20;

if (isset($_GET['latest'])) {
    $query_part = " AND {$title_field} IS NOT NULL AND {$resume_field} IS NOT NULL";
    $limit = 5;
} elseif (isset($_GET['featured'])) {
    $query_part = " AND est_en_avant = 1 AND {$title_field} IS NOT NULL AND {$resume_field} IS NOT NULL";
    $limit = 3;
}

try {
    $stmt = $pdo->prepare(
        "SELECT id_article, {$title_field} AS titre, {$resume_field} AS resume, date_publication 
         FROM Articles 
         WHERE statut = 'accepté' {$query_part}
         ORDER BY date_publication DESC 
         LIMIT :limit"
    );
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $articles = $stmt->fetchAll();

    if ($articles) {
        foreach ($articles as $article) {
            echo '<article class="vignette">';
            echo '<h4>' . htmlspecialchars($article['titre']) . '</h4>';
            echo '<p>' . htmlspecialchars(substr($article['resume'], 0, 150)) . '...</p>';
            if ($article['date_publication']) {
                echo '<span class="date-pub">'. (new DateTime($article['date_publication']))->format('d/m/Y') .'</span>';
            }
            echo '<br><a href="article.php?id=' . $article['id_article'] . '">Lire la suite</a>';
            echo '</article>';
        }
    } else {
        echo '<p>Aucun article publié pour le moment.</p>';
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>Erreur lors de la récupération des articles.</p>";
    // En développement, vous pourriez vouloir logguer l'erreur complète
    // error_log($e->getMessage());
}
?>