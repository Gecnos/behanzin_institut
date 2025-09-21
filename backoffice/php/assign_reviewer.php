<?php
require '../../php/database.php';

header('Content-Type: application/json');

// TODO: Ajouter une vérification de session et de rôle (admin, editeur)

$data = json_decode(file_get_contents('php://input'), true);

$article_id = $data['article_id'] ?? null;
$reviewer_id = $data['reviewer_id'] ?? null;

if (!$article_id || !$reviewer_id) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit;
}

try {
    // Vérifier si l'assignation existe déjà pour éviter les doublons
    $stmt = $pdo->prepare("SELECT id_commentaire FROM Commentaires_Relecture WHERE id_article = :article_id AND id_relecteur = :reviewer_id");
    $stmt->execute(['article_id' => $article_id, 'reviewer_id' => $reviewer_id]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cet article est déjà assigné à ce relecteur.']);
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO Commentaires_Relecture (id_article, id_relecteur, date_commentaire) VALUES (:article_id, :reviewer_id, NOW())"
    );
    $stmt->execute([
        ':article_id' => $article_id,
        ':reviewer_id' => $reviewer_id
    ]);

    // Optionnel: Mettre à jour le statut de l'article à 'en révision' si un tel statut existe
    // $pdo->prepare("UPDATE Articles SET statut = 'en révision' WHERE id_article = :id")->execute(['id' => $article_id]);

    // TODO: Envoyer un email de notification au relecteur
    echo json_encode(['success' => true, 'message' => 'Article assigné avec succès.']);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur assign_reviewer: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}
?>