<?php
require '../auth_check.php';
require_role(['administrateur', 'editeur']);

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT cle, valeur FROM ContenuStatique");
    $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // The frontend expects keys like 'about_content_fr'.
    // The database has keys like 'aboutfr'.
    // We need to map them.
    $response = [
        'about_content_fr'   => $rows['aboutfr'] ?? '',
        'about_content_en'   => $rows['abouten'] ?? ''
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur fetch_content: " . $e->getMessage());
    // In case the table doesn't exist yet, send an empty response
    // to prevent breaking the frontend form.
    echo json_encode([
        'about_content_fr' => '',
        'about_content_en' => '',
        'contact_content_fr' => '',
        'contact_content_en' => ''
    ]);
}
?>