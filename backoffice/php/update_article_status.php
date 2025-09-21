<?php
require '../../php/database.php';

header('Content-Type: application/json');

// TODO: Ajouter une vérification de session et de rôle (admin, editeur)

$data = json_decode(file_get_contents('php://input'), true);

$article_id = $data['article_id'] ?? null;
$action = $data['action'] ?? null;

if (!$article_id || !in_array($action, ['accepté', 'refusé'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE Articles SET statut = :statut WHERE id_article = :id");
    $stmt->execute([
        ':statut' => $action,
        ':id' => $article_id
    ]);

    if ($stmt->rowCount() > 0) {
        // TODO: Envoyer un email de notification à l'auteur
        // Exemple:
        // $stmt = $pdo->prepare("SELECT aut.email FROM auteur aut JOIN Articles a ON a.id_auteur = aut.id_auteur WHERE a.id_article = :id");
        // $stmt->execute(['id' => $article_id]);
        // $author_email = $stmt->fetchColumn();
        // mail($author_email, "Mise à jour de votre soumission", "Le statut de votre article est maintenant: {$action}");
        echo json_encode(['success' => true, 'message' => "L'article a bien été " . $action . "."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune modification effectuée.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur update_article_status: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}
?>