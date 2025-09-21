<?php
header('Content-Type: application/json');

// TODO: Session check (admin or editor)

$data = json_decode(file_get_contents('php://input'), true);

$about_content_fr = $data['about_content_fr'] ?? null;
$contact_content_fr = $data['contact_content_fr'] ?? null;
$about_content_en = $data['about_content_en'] ?? null;
$contact_content_en = $data['contact_content_en'] ?? null;

if ($about_content_fr === null || $contact_content_fr === null || $about_content_en === null || $contact_content_en === null) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
    exit;
}

$about_path_fr = '../../includes/about_content.php';
$contact_path_fr = '../../includes/contact_content.php';
$about_path_en = '../../includes/about_content_en.php';
$contact_path_en = '../../includes/contact_content_en.php';

// Utiliser LOCK_EX pour éviter les écritures concurrentes
$about_result_fr = file_put_contents($about_path_fr, $about_content_fr, LOCK_EX);
$contact_result_fr = file_put_contents($contact_path_fr, $contact_content_fr, LOCK_EX);
$about_result_en = file_put_contents($about_path_en, $about_content_en, LOCK_EX);
$contact_result_en = file_put_contents($contact_path_en, $contact_content_en, LOCK_EX);

if ($about_result_fr !== false && $contact_result_fr !== false && $about_result_en !== false && $contact_result_en !== false) {
    echo json_encode(['success' => true, 'message' => 'Contenu mis à jour avec succès.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'écriture d\'un des fichiers.']);
}
?>