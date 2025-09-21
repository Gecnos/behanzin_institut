<?php
require 'php/database.php';

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// URL de base du site - à adapter si nécessaire
$base_url = "http://" . $_SERVER['HTTP_HOST'] . str_replace('/sitemap.php', '', $_SERVER['SCRIPT_NAME']);

// Pages statiques
echo "<url><loc>{$base_url}/</loc></url>";
echo "<url><loc>{$base_url}/#publications</loc></url>"; // Utiliser des ancres si SPA
echo "<url><loc>{$base_url}/#a-propos</loc></url>";

// Articles publiés
$stmt = $pdo->query("SELECT id_article, date_publication FROM Articles WHERE statut = 'publié' ORDER BY date_publication DESC");
while ($article = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $url = $base_url . '/article.php?id=' . $article['id_article'];
    $date = (new DateTime($article['date_publication']))->format('Y-m-d');
    echo "<url><loc>{$url}</loc><lastmod>{$date}</lastmod></url>";
}

echo '</urlset>';
?>