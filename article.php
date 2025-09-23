<?php
require 'php/database.php';

$article_id = $_GET['id'] ?? null;
$lang = $_GET['lang'] ?? 'fr';

$title_field = ($lang === 'en') ? 'titre_en' : 'titre';
$resume_field = ($lang === 'en') ? 'resume_en' : 'resume';

if (!$article_id) {
    die('ID manquant.');
}

try {
    $stmt = $pdo->prepare(
        "SELECT a.id_article, a.{$title_field} AS titre, a.{$resume_field} AS resume, a.fichier_manuscrit, a.date_publication, aut.nom, aut.prenom
         FROM Articles a
         JOIN auteur aut ON a.id_auteur = aut.id_auteur
         WHERE a.id_article = :id AND a.statut = 'accepté'"
    );
    $stmt->execute(['id' => $article_id]);
    $article = $stmt->fetch();

    if (!$article) {
        die('Article non trouvé ou non publié.');
    }
} catch (PDOException $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<article class="single-article-container">
    <h1 class="article-title"><?= htmlspecialchars($article['titre']) ?></h1>
    <p class="article-meta">Par <?= htmlspecialchars($article['prenom'] . ' ' . $article['nom']) ?><?php if ($article['date_publication']): ?> | Publié le <?= (new DateTime($article['date_publication']))->format('d/m/Y') ?><?php endif; ?></p>
    
    <div class="article-summary">
        <h3>Résumé</h3>
        <p><?= nl2br(htmlspecialchars($article['resume'])) ?></p>
    </div>

    <div class="article-full-content">
        <h3>Article Complet</h3>
        <a href="download.php?id=<?= $article['id_article'] ?>" class="btn btn-download"><i class="fa-solid fa-download"></i> Télécharger le PDF</a>
        <!-- On pourrait aussi intégrer un viewer PDF ici -->
    </div>
</article>