<?php
require 'auth_check.php';
require_role(['administrateur', 'editeur']);

$article_id = $_GET['id'] ?? null;
if (!$article_id) {
    die('ID de l'article manquant.');
}

try {
    $stmt = $pdo->prepare(
        "SELECT a.*, aut.nom, aut.prenom, aut.email, aut.institution
         FROM Articles a
         JOIN auteur aut ON a.id_auteur = aut.id_auteur
         WHERE a.id_article = :id"
    );
    $stmt->execute(['id' => $article_id]);
    $article = $stmt->fetch();

    if (!$article) {
        die('Article non trouvé.');
    }

    // Récupérer les catégories de l'article
    $stmt_cat = $pdo->prepare("SELECT id_categorie FROM Liaison_Article_Categorie WHERE id_article = :id");
    $stmt_cat->execute(['id' => $article_id]);
    $article_categories = $stmt_cat->fetchAll(PDO::FETCH_COLUMN, 0);

    // Récupérer toutes les catégories disponibles
    $all_categories = $pdo->query("SELECT id_categorie, nom FROM categories ORDER BY nom")->fetchAll();

    // Récupérer les mots-clés de l'article
    $stmt_kw = $pdo->prepare(
        "SELECT GROUP_CONCAT(mc.mot_cle SEPARATOR ', ') as keywords
         FROM Mots_Cles mc
         JOIN Liaison_Article_Mot_Cle lamc ON mc.id_mot_cle = lamc.id_mot_cle
         WHERE lamc.id_article = :id"
    );
    $stmt_kw->execute(['id' => $article_id]);
    $keywords = $stmt_kw->fetchColumn();

} catch (PDOException $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail de la soumission - <?= htmlspecialchars($article['titre']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Détail de la soumission</h1>
        <nav><a href="index.php">&larr; Retour aux soumissions</a></nav>
    </header>

    <main id="admin-content">
        <form id="edit-article-form">
            <input type="hidden" name="article_id" value="<?= $article_id ?>">
            <div id="form-response"></div>

            <div class="article-details">
                <div class="form-group">
                    <label for="titre">Titre</label>
                    <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($article['titre']) ?>">
                </div>
                
                <p><strong>Auteur:</strong> <?= htmlspecialchars($article['prenom'] . ' ' . $article['nom']) ?></p>
                <p><strong>Statut actuel:</strong> <?= htmlspecialchars($article['statut']) ?></p>
                <label><input type="checkbox" name="est_en_avant" <?= $article['est_en_avant'] ? 'checked' : '' ?>> Mettre en avant</label>

                <div class="form-group">
                    <label for="resume">Résumé</label>
                    <textarea name="resume" id="resume" rows="10"><?= htmlspecialchars($article['resume']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="keywords">Mots-clés (séparés par virgule)</label>
                    <input type="text" name="keywords" id="keywords" value="<?= htmlspecialchars($keywords) ?>">
                </div>

                <h3>Manuscrit</h3>
                <p><a href="../<?= htmlspecialchars($article['fichier_manuscrit']) ?>" download>Télécharger le fichier</a></p>
            </div>

            <div class="actions">
                <h3>Catégories</h3>
                <div class="form-group-checkbox">
                    <?php foreach ($all_categories as $category): ?>
                        <label>
                            <input type="checkbox" name="categories[]" value="<?= $category['id_categorie'] ?>" <?= in_array($category['id_categorie'], $article_categories) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($category['nom']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <button type="submit">Enregistrer les modifications</button>
                <hr>

                <h3>Actions Éditoriales</h3>
                <div id="decision-form">
                    <button type="button" class="btn-action" data-action="accepté">Accepter</button>
                    <button type="button" class="btn-action" data-action="refusé">Refuser</button>
                </div>
                
                <hr>

                <hr>

                <h3>Avis des Relecteurs</h3>
                <div id="reviews-list">
                    <?php
                    $stmt_reviews = $pdo->prepare(
                        "SELECT cr.commentaire, cr.recommandation, u.nom, u.prenom 
                         FROM Commentaires_Relecture cr 
                         JOIN Utilisateurs u ON cr.id_relecteur = u.id_utilisateur 
                         WHERE cr.id_article = :id AND cr.commentaire IS NOT NULL"
                    );
                    $stmt_reviews->execute(['id' => $article_id]);
                    $reviews = $stmt_reviews->fetchAll();
                    if (empty($reviews)) {
                        echo "<p>Aucun avis de relecteur soumis pour le moment.</p>";
                    } else {
                        foreach ($reviews as $review) {
                            echo '<div class="review-item">';
                            echo '<strong>Relecteur: ' . htmlspecialchars($review['prenom'] . ' ' . $review['nom']) . '</strong>';
                            echo '<p><strong>Recommandation:</strong> ' . htmlspecialchars($review['recommandation']) . '</p>';
                            echo '<p>' . nl2br(htmlspecialchars($review['commentaire'])) . '</p>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>

                <h3>Assigner à un relecteur</h3>
                <div id="assign-form">
                    <select name="reviewer_id" required>
                        <!-- Les relecteurs seront chargés ici -->
                    </select>
                    <button type="button" id="assign-btn">Assigner</button>
                </div>
            </div>
        </form>
    </main>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const mainForm = document.getElementById('edit-article-form');
    const decisionForm = document.getElementById('decision-form');
    const assignBtn = document.getElementById('assign-btn');
    const reviewerSelect = document.querySelector('select[name="reviewer_id"]');
    const articleId = document.querySelector('input[name="article_id"]').value;
    const responseDiv = document.getElementById('form-response');

    function showResponse(message, isSuccess) {
        responseDiv.textContent = message;
        responseDiv.className = isSuccess ? 'response success' : 'response error';
    }

    // --- Charger les relecteurs ---
    fetch('php/fetch_reviewers.php')
        .then(response => response.text())
        .then(html => reviewerSelect.innerHTML = html)
        .catch(error => console.error('Erreur chargement relecteurs:', error));

    // --- Gérer la sauvegarde des modifications ---
    mainForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(mainForm);
        const data = {
            article_id: articleId,
            titre: formData.get('titre'),
            resume: formData.get('resume'),
            keywords: formData.get('keywords'),
            categories: formData.getAll('categories[]'),
            est_en_avant: formData.has('est_en_avant')
        };

        fetch('php/update_article_details.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            showResponse(result.message, result.success);
        })
        .catch(err => showResponse('Erreur technique.', false));
    });

    // --- Gérer les décisions (Accepter/Refuser) ---
    decisionForm.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-action')) {
            const action = e.target.dataset.action;
            if (!confirm(`Êtes-vous sûr de vouloir ${action} cet article ?`)) return;

            fetch('php/update_article_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ article_id: articleId, action: action })
            })
            .then(res => res.json())
            .then(result => {
                showResponse(result.message, result.success);
                if (result.success) {
                    setTimeout(() => window.location.reload(), 1500);
                }
            })
            .catch(err => showResponse('Erreur technique.', false));
        }
    });

    // --- Gérer l'assignation ---
    assignBtn.addEventListener('click', function() {
        const reviewerId = reviewerSelect.value;
        if (!reviewerId) {
            alert('Veuillez choisir un relecteur.');
            return;
        }

        fetch('php/assign_reviewer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ article_id: articleId, reviewer_id: reviewerId })
        })
        .then(res => res.json())
        .then(result => showResponse(result.message, result.success))
        .catch(err => showResponse('Erreur technique.', false));
    });
});
</script>
</body>
</html>