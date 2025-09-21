<?php
require 'auth_check.php';
require_role(['relecteur', 'administrateur']); // Admin peut aussi voir

$user_id = $_SESSION['user_id'];

// Récupérer les articles assignés à ce relecteur
$stmt = $pdo->prepare(
    "SELECT a.id_article, a.titre, cr.date_commentaire, cr.commentaire
     FROM Articles a
     JOIN Commentaires_Relecture cr ON a.id_article = cr.id_article
     WHERE cr.id_relecteur = :user_id
     ORDER BY cr.date_commentaire DESC"
);
$stmt->execute(['user_id' => $user_id]);
$assigned_articles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Relecteur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Tableau de Bord Relecteur</h1>
        <nav><a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></nav>
    </header>

    <main id="admin-content">
        <h2>Articles à relire</h2>
        <table>
            <thead><tr><th>Titre de l'article</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
                <?php if (empty($assigned_articles)): ?>
                    <tr><td colspan="3">Aucun article ne vous a été assigné pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($assigned_articles as $article): ?>
                        <tr>
                            <td><?= htmlspecialchars($article['titre']) ?></td>
                            <td><?= empty($article['commentaire']) ? 'En attente de relecture' : 'Relecture soumise' ?></td>
                            <td><a href="review_article.php?id=<?= $article['id_article'] ?>" class="btn">Ouvrir</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>