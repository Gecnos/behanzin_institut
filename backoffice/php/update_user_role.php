<?php
require '../../php/database.php';

header('Content-Type: application/json');

// TODO: Session check (admin only)

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$role = $data['role'] ?? null;

$allowed_roles = ['administrateur', 'editeur', 'relecteur'];

if (!$user_id || !in_array($role, $allowed_roles)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE Utilisateurs SET role = :role WHERE id_utilisateur = :id");
    $stmt->execute([
        ':role' => $role,
        ':id' => $user_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Rôle mis à jour avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune modification effectuée (l\'utilisateur avait peut-être déjà ce rôle).']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur update_user_role: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}
?>