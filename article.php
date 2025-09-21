<?php
require 'php/database.php';

$article_id = $_GET['id'] ?? null;
if (!$article_id) {
    die('ID manquant.');
}

try {
    $stmt = $pdo->prepare(
        "SELECT a.*, aut.nom, aut.prenom
         FROM Articles a
         JOIN auteur aut ON a.id_auteur = aut.id_auteur
         WHERE a.id_article = :id AND a.statut = 'publié'"
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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['titre']) ?></title>
    <meta name="description" content="<?= htmlspecialchars(substr($article['resume'], 0, 160)) ?>">
    <!-- Open Graph tags -->
    <meta property="og:title" content="<?= htmlspecialchars($article['titre']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars(substr($article['resume'], 0, 200)) ?>">
    <meta property="og:type" content="article">
    <!-- <meta property="og:image" content="URL_vers_une_image_representative"> -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Behanzin Institut</a></h1>
    </header>

    <main>
        <article class="full-article">
            <h2><?= htmlspecialchars($article['titre']) ?></h2>
            <p class="author-date">Par <?= htmlspecialchars($article['prenom'] . ' ' . $article['nom']) ?> | Publié le <?= (new DateTime($article['date_publication']))->format('d/m/Y') ?></p>
            
            <h3>Résumé</h3>
            <p><?= nl2br(htmlspecialchars($article['resume'])) ?></p>

            <h3>Article Complet</h3>
            <a href="download.php?id=<?= $article['id_article'] ?>" class="btn">Télécharger le PDF</a>
            
            <!-- On pourrait aussi intégrer un viewer PDF ici -->

        </article>
    </main>

    <footer>
        <p>&copy; 2025 Behanzin Institut. Tous droits réservés.</p>
    </footer>
</body>
</html>