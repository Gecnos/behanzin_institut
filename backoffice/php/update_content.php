<?php
header('Content-Type: application/json');

// TODO: Session check (admin or editor)

$data = json_decode(file_get_contents('php://input'), true);

$about_content = $data['about_content'] ?? null;
$contact_content = $data['contact_content'] ?? null;

if ($about_content === null || $contact_content === null) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
    exit;
}

$about_path = '../../includes/about_content.php';
$contact_path = '../../includes/contact_content.php';

// Utiliser LOCK_EX pour éviter les écritures concurrentes
$about_result = file_put_contents($about_path, $about_content, LOCK_EX);
$contact_result = file_put_contents($contact_path, $contact_content, LOCK_EX);

if ($about_result !== false && $contact_result !== false) {
    echo json_encode(['success' => true, 'message' => 'Contenu mis à jour avec succès.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'écriture d\'un des fichiers.']);
}
?>