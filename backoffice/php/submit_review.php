<?php
require '../auth_check.php';
require_role(['relecteur', 'administrateur']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$article_id = $data['article_id'] ?? null;
$recommandation = $data['recommandation'] ?? null;
$commentaire = $data['commentaire'] ?? '';
$categories = $data['categories'] ?? [];

if (!$article_id || !$recommandation) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit;
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare(
        "UPDATE Commentaires_Relecture 
         SET commentaire = :commentaire, recommandation = :recommandation, date_commentaire = NOW() 
         WHERE id_article = :article_id AND id_relecteur = :relecteur_id"
    );
    $stmt->execute([
        ':commentaire' => $commentaire,
        ':recommandation' => $recommandation,
        ':article_id' => $article_id,
        ':relecteur_id' => $_SESSION['user_id']
    ]);

    // Mettre à jour les catégories de l'article
    $stmt_delete_categories = $pdo->prepare("DELETE FROM Liaison_Article_Categorie WHERE id_article = :article_id");
    $stmt_delete_categories->execute(['article_id' => $article_id]);

    if (!empty($categories)) {
        $stmt_insert_category = $pdo->prepare("INSERT INTO Liaison_Article_Categorie (id_article, id_categorie) VALUES (:article_id, :id_categorie)");
        foreach ($categories as $category_id) {
            $stmt_insert_category->execute([
                ':article_id' => $article_id,
                ':id_categorie' => $category_id
            ]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Votre relecture et les catégories ont été enregistrées.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    error_log("Erreur submit_review: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}