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
    "SELECT a.titre, a.resume, a.fichier_manuscrit, cr.commentaire, cr.recommandation
     FROM Articles a
     JOIN Commentaires_Relecture cr ON a.id_article = cr.id_article
     WHERE a.id_article = :id AND cr.id_relecteur = :user_id"
);
$stmt->execute(['id' => $article_id, 'user_id' => $_SESSION['user_id']]);
$article = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relecture d'article</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Relecture : <?= htmlspecialchars($article['titre']) ?></h1>
        <nav><a href="review_dashboard.php">&larr; Retour</a></nav>
    </header>

    <main id="admin-content">
        <h3>Résumé</h3>
        <p><?= nl2br(htmlspecialchars($article['resume'])) ?></p>
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

            <button type="submit">Soumettre la relecture</button>
        </form>
    </main>

    <script>
    document.getElementById('review-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

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