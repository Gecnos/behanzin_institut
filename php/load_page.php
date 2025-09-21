<?php
$page = $_GET['page'] ?? 'accueil';

// Sécuriser le nom de la page pour éviter les inclusions de fichiers malveillantes
$allowed_pages = [
    'accueil' => '../includes/home_content.php',
    'publications' => '../includes/publications_content.php',
    'a-propos' => '../includes/about_content.php',
    'contact' => '../includes/contact_content.php',
    'soumettre' => '../includes/submit_content.php',
    'search_results' => '../php/search_results.php'
];

if (array_key_exists($page, $allowed_pages)) {
    $file_path = $allowed_pages[$page];
    if (file_exists($file_path)) {
        include $file_path;
    } else {
        echo '<p>Contenu non trouvé.</p>';
    }
} else {
    http_response_code(404);
    echo '<p>Page non trouvée.</p>';
}
?>