<?php
require '../auth_check.php';
require_role(['administrateur', 'editeur']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['name'])) {
    echo json_encode(['success' => false, 'message' => 'Le nom de la catégorie est obligatoire.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (:nom)");
    $stmt->execute(['nom' => $data['name']]);

    echo json_encode(['success' => true, 'message' => 'Catégorie ajoutée avec succès.']);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur add_category: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}
?>