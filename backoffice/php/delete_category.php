<?php
require '../auth_check.php';
require_role(['administrateur', 'editeur']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de catégorie manquant.']);
    exit;
}

try {
    // Optional: Check if the category is in use before deleting
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM Liaison_Article_Categorie WHERE id_categorie = :id");
    $stmt_check->execute(['id' => $data['id']]);
    if ($stmt_check->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Cette catégorie est utilisée par des articles et ne peut être supprimée.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id_categorie = :id");
    $stmt->execute(['id' => $data['id']]);

    echo json_encode(['success' => true, 'message' => 'Catégorie supprimée avec succès.']);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur delete_category: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}
?>