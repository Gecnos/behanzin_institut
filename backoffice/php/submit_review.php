<?php
require '../auth_check.php';
require_role(['relecteur', 'administrateur']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$article_id = $data['article_id'] ?? null;
$recommandation = $data['recommandation'] ?? null;
$commentaire = $data['commentaire'] ?? '';

if (!$article_id || !$recommandation) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit;
}

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

    echo json_encode(['success' => true, 'message' => 'Votre relecture a été enregistrée.']);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur submit_review: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}
?>