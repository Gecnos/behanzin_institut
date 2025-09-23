<?php
require '../auth_check.php';
require_role(['administrateur', 'editeur']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data)) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
    exit;
}

// The content keys we expect from the frontend
$content_keys = [
    'about_content_fr',
    'about_content_en'
];

try {
    $pdo->beginTransaction();

    // Using INSERT ... ON DUPLICATE KEY UPDATE is efficient.
    // It requires a PRIMARY or UNIQUE key on the 'cle' column.
    $stmt = $pdo->prepare(
        "INSERT INTO ContenuStatique (cle, valeur) 
         VALUES (:cle, :valeur) 
         ON DUPLICATE KEY UPDATE valeur = :valeur"
    );

    foreach ($content_keys as $key) {
        if (isset($data[$key])) {
            // The key in the DB will be 'about_fr', 'contact_en', etc.
            $db_key = str_replace(['_content_', '_'], ['', ''], $key);
            
            $stmt->execute([
                ':cle' => $db_key,
                ':valeur' => $data[$key]
            ]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Contenu mis à jour avec succès.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    error_log("Erreur update_content: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données.']);
}
?>