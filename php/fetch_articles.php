<?php
require 'database.php';

$query_part = "";
$limit = 20;

if (isset($_GET['latest'])) {
    $limit = 5;
} elseif (isset($_GET['featured'])) {
    $query_part = " AND est_en_avant = 1";
    $limit = 3;
}

try {
    $stmt = $pdo->prepare(
        "SELECT titre, resume, date_publication, id_article 
         FROM Articles 
         WHERE statut = 'publié' {$query_part}
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
            echo '<span class="date-pub">'. (new DateTime($article['date_publication']))->format('d/m/Y') .'</span>';
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