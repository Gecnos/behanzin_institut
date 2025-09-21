<?php
$lang = $_GET['lang'] ?? 'fr';
$allowed_langs = ['fr', 'en'];

if (!in_array($lang, $allowed_langs)) {
    $lang = 'fr';
}

$file_path = "../lang/{$lang}.json";

if (file_exists($file_path)) {
    header('Content-Type: application/json');
    readfile($file_path);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Fichier de langue non trouvé.']);
}
?>