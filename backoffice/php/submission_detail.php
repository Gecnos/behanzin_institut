<?php
// On inclura ici la vérification de session et des permissions (rôle 'editeur' ou 'administrateur')
require_once '../../php/db_config.php';

$id_article = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_article == 0) {
    echo "<p>Article non trouvé.</p>";
    exit();
}

$sql = "SELECT a.*, au.nom, au.prenom FROM Articles a JOIN Auteurs au ON a.id_auteur = au.id_auteur WHERE a.id_article = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_article);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $article = $result->fetch_assoc();
?>
    <section id="article-details">
        <h2>Détails de l'article : <?php echo htmlspecialchars($article['titre']); ?></h2>
        <p>Auteur : <?php echo htmlspecialchars($article['prenom'] . ' ' . $article['nom']); ?></p>
        <p>Statut actuel : <?php echo htmlspecialchars($article['statut']); ?></p>
        <a href="../<?php echo htmlspecialchars($article['fichier_manuscrit']); ?>" target="_blank" class="btn-download">Voir le manuscrit</a>
    </section>

    <section id="peer-review-section">
        <h3>Gestion de la révision par les pairs</h3>
        <form id="assign-reviewer-form">
            <input type="hidden" name="id_article" value="<?php echo $id_article; ?>">
            <label for="reviewer-select">Attribuer à un relecteur :</label>
            <select id="reviewer-select" name="id_relecteur">
                <option value="">Sélectionner un relecteur</option>
                <?php
                // On récupère la liste des relecteurs
                $reviewers_sql = "SELECT id_utilisateur, nom, prenom FROM Utilisateurs WHERE role = 'relecteur'";
                $reviewers_result = $conn->query($reviewers_sql);
                while ($reviewer = $reviewers_result->fetch_assoc()) {
                    echo "<option value='{$reviewer['id_utilisateur']}'>" . htmlspecialchars($reviewer['prenom'] . ' ' . $reviewer['nom']) . "</option>";
                }
                ?>
            </select>
            <button type="submit">Attribuer</button>
        </form>

        <h4>Commentaires des relecteurs</h4>
        <div id="reviewer-comments">
            </div>
    </section>
<?php
} else {
    echo "<p>Article non trouvé.</p>";
}
$conn->close();
?>