<?php
require '../auth_check.php';
require_role(['administrateur', 'editeur']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$article_id = $data['article_id'] ?? null;
if (!$article_id) {
    echo json_encode(['success' => false, 'message' => 'ID manquant.']);
    exit;
}

$titre_fr = $data['titre_fr'] ?? '';
$titre_en = $data['titre_en'] ?? '';
$resume_fr = $data['resume_fr'] ?? '';
$resume_en = $data['resume_en'] ?? '';
$keywords_str = $data['keywords'] ?? '';
$categories = $data['categories'] ?? [];
$est_en_avant = !empty($data['est_en_avant']);

$pdo->beginTransaction();
try {
    // 1. Mettre à jour les champs principaux
    $stmt = $pdo->prepare("UPDATE Articles SET titre = :titre_fr, resume = :resume_fr, titre_en = :titre_en, resume_en = :resume_en, est_en_avant = :est_en_avant WHERE id_article = :id");
    $stmt->execute([
        ':titre_fr' => $titre_fr,
        ':resume_fr' => $resume_fr,
        ':titre_en' => $titre_en,
        ':resume_en' => $resume_en,
        ':est_en_avant' => $est_en_avant,
        ':id' => $article_id
    ]);

    // 2. Mettre à jour les mots-clés
    $pdo->prepare("DELETE FROM Liaison_Article_Mot_Cle WHERE id_article = :id")->execute(['id' => $article_id]);
    $keywords = explode(',', $keywords_str);
    foreach ($keywords as $kw) {
        $kw = trim($kw);
        if (empty($kw)) continue;
        $stmt = $pdo->prepare("SELECT id_mot_cle FROM Mots_Cles WHERE mot_cle = :kw");
        $stmt->execute(['kw' => $kw]);
        $id_mot_cle = $stmt->fetchColumn();
        if (!$id_mot_cle) {
            $stmt = $pdo->prepare("INSERT INTO Mots_Cles (mot_cle) VALUES (:kw)");
            $stmt->execute(['kw' => $kw]);
            $id_mot_cle = $pdo->lastInsertId();
        }
        $stmt = $pdo->prepare("INSERT INTO Liaison_Article_Mot_Cle (id_article, id_mot_cle) VALUES (:id_article, :id_mot_cle)");
        $stmt->execute(['id_article' => $article_id, 'id_mot_cle' => $id_mot_cle]);
    }

    // 3. Mettre à jour les catégories
    $pdo->prepare("DELETE FROM Liaison_Article_Categorie WHERE id_article = :id")->execute(['id' => $article_id]);
    foreach ($categories as $cat_id) {
        $stmt = $pdo->prepare("INSERT INTO Liaison_Article_Categorie (id_article, id_categorie) VALUES (:id_article, :id_cat)");
        $stmt->execute(['id_article' => $article_id, 'id_cat' => $cat_id]);
    }

    // 4. Enregistrer l'action dans l'historique
    $stmt = $pdo->prepare("INSERT INTO Historique_Soumission (id_article, id_utilisateur, action, date_action) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$article_id, $_SESSION['user_id'], 'Modification des détails']);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Modifications enregistrées avec succès.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    error_log("Erreur update_article_details: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}
?>