<?php
header('Content-Type: application/json');

// TODO: Session check

$about_path_fr = '../../includes/about_content.php';
$contact_path_fr = '../../includes/contact_content.php';
$about_path_en = '../../includes/about_content_en.php';
$contact_path_en = '../../includes/contact_content_en.php';

$response = [
    'about_content_fr' => file_exists($about_path_fr) ? file_get_contents($about_path_fr) : '',
    'contact_content_fr' => file_exists($contact_path_fr) ? file_get_contents($contact_path_fr) : '',
    'about_content_en' => file_exists($about_path_en) ? file_get_contents($about_path_en) : '',
    'contact_content_en' => file_exists($contact_path_en) ? file_get_contents($contact_path_en) : ''
];

echo json_encode($response);
?>