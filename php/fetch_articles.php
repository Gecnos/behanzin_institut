<?php
require 'database.php';

// --- Parameters ---
$lang = $_GET['lang'] ?? 'fr';
$view = $_GET['view'] ?? 'latest_grid'; // default view

// --- SQL & Rendering Logic based on View ---
$title_field = ($lang === 'en') ? 'a.titre_en' : 'a.titre';
$resume_field = ($lang === 'en') ? 'a.resume_en' : 'a.resume';
$query_part = "";
$limit = 6;

switch ($view) {
    case 'featured_main':
        $query_part = " AND a.est_en_avant = 1";
        $limit = 1;
        break;
    case 'featured_side':
        $query_part = " AND a.est_en_avant = 0"; // Get non-featured articles
        $limit = 3;
        break;
    case 'latest_grid':
    default:
        $limit = 6;
        break;
}

try {
    $stmt = $pdo->prepare(
        "SELECT a.id_article, {$title_field} AS titre, {$resume_field} AS resume, a.image, a.date_publication, aut.nom, aut.prenom 
         FROM Articles a
         JOIN auteur aut ON a.id_auteur = aut.id_auteur
         WHERE a.statut = 'accepté' AND {$title_field} IS NOT NULL AND {$title_field} != '' {$query_part}
         ORDER BY a.date_publication DESC 
         LIMIT :limit"
    );
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll();

    if (!$articles) {
        echo '<p>' . ($view === 'latest_grid' ? 'Aucun article publié pour le moment.' : '') . '</p>';
        exit;
    }

    // --- Render based on view ---
    foreach ($articles as $article) {
        $author_name = htmlspecialchars($article['prenom'] . ' ' . $article['nom']);
        $article_title = htmlspecialchars($article['titre']);
        $article_url = '#'; // Placeholder, will be handled by JS
        $article_image = htmlspecialchars($article['image'] ?? '');

        if ($view === 'featured_main') {
            echo '<article class="vignette" data-page="article" data-id="' . $article['id_article'] . '">';
            if ($article_image) {
                echo '<img src="' . $article_image . '" alt="' . $article_title . '" class="vignette-image">';
            }
            echo '  <h2 class="vignette-title"><a>' . $article_title . '</a></h2>';
            echo '  <p>' . htmlspecialchars(substr($article['resume'], 0, 200)) . '...</p>';
            echo '  <p class="vignette-author">' . $author_name . '</p>';
            echo '</article>';
        } elseif ($view === 'featured_side') {
            echo '<article class="vignette" data-page="article" data-id="' . $article['id_article'] . '">';
            if ($article_image) {
                echo '<img src="' . $article_image . '" alt="' . $article_title . '" class="vignette-image-side">';
            }
            echo '  <h4 class="vignette-title"><a>' . $article_title . '</a></h4>';
            echo '  <p class="vignette-author">' . $author_name . '</p>';
            echo '</article>';
        } else { // latest_grid
            echo '<article class="vignette" data-page="article" data-id="' . $article['id_article'] . '">';
            if ($article_image) {
                echo '<img src="' . $article_image . '" alt="' . $article_title . '" class="vignette-image">';
            }
            echo '  <h4 class="vignette-title"><a>' . $article_title . '</a></h4>';
            echo '  <p class="vignette-author">' . $author_name . '</p>';
            if ($article['date_publication']) {
                $date = (new DateTime($article['date_publication']))->format('d/m/Y');
                echo '  <p class="vignette-date">' . $date . '</p>';
            }
            echo '</article>';
        }
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Fetch Articles Error: " . $e->getMessage());
    // Silent fail for the homepage so it doesn't show a broken section
}
?>