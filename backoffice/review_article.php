<?php
require 'auth_check.php';
require_role(['relecteur', 'administrateur']);

$article_id = $_GET['id'] ?? null;
if (!$article_id) die('ID manquant.');

// Vérifier que cet article est bien assigné au relecteur actuel
$stmt = $pdo->prepare("SELECT id_article FROM Commentaires_Relecture WHERE id_article = :id AND id_relecteur = :user_id");
$stmt->execute(['id' => $article_id, 'user_id' => $_SESSION['user_id']]);
if (!$stmt->fetch() && $_SESSION['user_role'] !== 'administrateur') {
    die('Accès non autorisé à cet article.');
}

// Récupérer les détails de l'article et le commentaire existant
$stmt = $pdo->prepare(
    "SELECT a.titre, a.resume, a.fichier_manuscrit, a.image, cr.commentaire, cr.recommandation
     FROM Articles a
     JOIN Commentaires_Relecture cr ON a.id_article = cr.id_article
     WHERE a.id_article = :id AND cr.id_relecteur = :user_id"
);
$stmt->execute(['id' => $article_id, 'user_id' => $_SESSION['user_id']]);
$article = $stmt->fetch();

?>
    <!-- $stmt->execute(['id' => $article_id, 'user_id' => $_SESSION['user_id']]);
    $article = $stmt->fetch();

    // Récupérer les catégories de l'article
    $stmt_cat = $pdo->prepare("SELECT id_categorie FROM Liaison_Article_Categorie WHERE id_article = :id");
    $stmt_cat->execute(['id' => $article_id]);
    $article_categories = $stmt_cat->fetchAll(PDO::FETCH_COLUMN, 0);

    // Récupérer toutes les catégories disponibles
    $all_categories = $pdo->query("SELECT id_categorie, nom FROM categories ORDER BY nom")->fetchAll();

?> -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relecture d'article</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="backoffice-body">
    <header class="backoffice-header">
        <h1>Relecture : <?= htmlspecialchars($article['titre']) ?></h1>
        <nav>
            <a href="review_dashboard.php"><i class="fa-solid fa-arrow-left"></i> Retour</a>
            <a href="categories.php"><i class="fa-solid fa-tags"></i> Catégories</a>
        </nav>
    </header>

    <main id="admin-content">
        <h3>Résumé</h3>
        <p><?= nl2br(htmlspecialchars($article['resume'])) ?></p>
        <?php if ($article['image']): ?>
            <h3>Image de l'article</h3>
            <img src="../<?= htmlspecialchars($article['image']) ?>" alt="Image de l'article" style="max-width: 400px; height: auto;">
        <?php endif; ?>
        <p><a href="../<?= htmlspecialchars($article['fichier_manuscrit']) ?>" download class="btn">Télécharger le manuscrit</a></p>
        <hr>

        <form id="review-form">
            <h3>Votre Analyse</h3>
            <div id="form-response"></div>
            <input type="hidden" name="article_id" value="<?= $article_id ?>">

            <div class="form-group">
                <label for="recommandation">Recommandation</label>
                <select name="recommandation" id="recommandation" required>
                    <option value="">-- Votre choix --</option>
                    <option value="accepter" <?= $article['recommandation'] == 'accepter' ? 'selected' : '' ?>>Accepter</option>
                    <option value="revisions_mineures" <?= $article['recommandation'] == 'revisions_mineures' ? 'selected' : '' ?>>Révisions Mineures</option>
                    <option value="revisions_majeures" <?= $article['recommandation'] == 'revisions_majeures' ? 'selected' : '' ?>>Révisions Majeures</option>
                    <option value="refuser" <?= $article['recommandation'] == 'refuser' ? 'selected' : '' ?>>Refuser</option>
                </select>
            </div>

            <div class="form-group">
                <label for="commentaire">Commentaires (pour l'éditeur et l'auteur)</label>
                <textarea name="commentaire" id="commentaire" rows="15" required><?= htmlspecialchars($article['commentaire']) ?></textarea>
            </div>

            <h3>Catégories</h3>
            <div class="form-group-checkbox">
                <?php foreach ($all_categories as $category): ?>
                    <label>
                        <input type="checkbox" name="categories[]" value="<?= $category['id_categorie'] ?>" <?= in_array($category['id_categorie'], $article_categories) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($category['nom']) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="submit">Soumettre la relecture</button>
        </form>
    </main>

    <script>
    document.getElementById('review-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {
            article_id: articleId,
            recommandation: formData.get('recommandation'),
            commentaire: formData.get('commentaire'),
            categories: formData.getAll('categories[]')
        };

        fetch('php/submit_review.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            const responseDiv = document.getElementById('form-response');
            responseDiv.textContent = result.message;
            responseDiv.className = result.success ? 'response success' : 'response error';
        });
    });
    </script>
</body>
</html>