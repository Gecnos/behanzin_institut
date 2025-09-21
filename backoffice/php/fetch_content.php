<?php
header('Content-Type: application/json');

// TODO: Session check

$about_path = '../../includes/about_content.php';
$contact_path = '../../includes/contact_content.php';

$response = [
    'about_content' => file_exists($about_path) ? file_get_contents($about_path) : '',
    'contact_content' => file_exists($contact_path) ? file_get_contents($contact_path) : ''
];

echo json_encode($response);
?>