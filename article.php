<?php

// Affiche l'en-tête complet de la page uniquement si le fichier n'est pas chargé dynamiquement (via AJAX).
if (!defined('IS_DYNAMIC_LOAD')) {
    require_once 'includes/header.php';
}

// Inclut la connexion à la base de données. `require_once` évite les erreurs de re-déclaration.
require_once 'php/database.php';

// Récupération des paramètres de l'URL
$article_id = $_GET['id'] ?? null;
$lang = $_GET['lang'] ?? 'fr';

// Sélection des champs de la base de données en fonction de la langue
$title_field = ($lang === 'en') ? 'titre_en' : 'titre';
$resume_field = ($lang === 'en') ? 'resume_en' : 'resume';

// Arrête l'exécution si l'ID de l'article est manquant
if (!$article_id) {
    die('ID manquant.');
}

// Récupération des détails de l'article depuis la base de données
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
    die("Erreur de base de données: " . $e->getMessage());
}
?>

<!-- Conteneur principal de l'article -->
<article class="single-article-container">
    <h1 class="article-title"><?= htmlspecialchars($article['titre'] ?? 'Titre non disponible') ?></h1>
    <p class="article-meta">
        Par <?= htmlspecialchars(trim(($article['prenom'] ?? '') . ' ' . ($article['nom'] ?? ''))) ?: 'Auteur inconnu' ?>
        <?php if ($article['date_publication']): ?>
            | Publié le <?= (new DateTime($article['date_publication']))->format('d/m/Y') ?>
        <?php endif; ?>
    </p>
    
    <div class="article-summary">
        <h3>Résumé</h3>
        <p><?= nl2br(htmlspecialchars($article['resume'] ?? '')) ?></p>
    </div>

    <div class="article-full-content">
        <h3>Article Complet</h3>
        <a href="download.php?id=<?= $article['id_article'] ?>" class="btn btn-download"><i class="fa-solid fa-download"></i> Télécharger le PDF</a>
    </div>
</article>

<?php
// Affiche le pied de page complet uniquement si le fichier n'est pas chargé dynamiquement.
if (!defined('IS_DYNAMIC_LOAD')) {
    require_once 'includes/footer.php';
}
?>
