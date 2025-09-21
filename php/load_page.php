<?php
$page = $_GET['page'] ?? 'accueil';
$lang = $_GET['lang'] ?? 'fr'; // Récupérer le paramètre de langue

// Définir le suffixe de fichier pour la langue
$lang_suffix = ($lang === 'en') ? '_en' : '';

// Sécuriser le nom de la page pour éviter les inclusions de fichiers malveillantes
$allowed_pages = [
    'accueil' => '../includes/home_content.php',
    'publications' => '../includes/publications_content.php',
    'a-propos' => '../includes/about_content' . $lang_suffix . '.php',
    'contact' => '../includes/contact_content' . $lang_suffix . '.php',
    'soumettre' => '../includes/submit_content.php',
    'search_results' => '../php/search_results.php'
];

if (array_key_exists($page, $allowed_pages)) {
    $file_path = $allowed_pages[$page];
    if (file_exists($file_path)) {
        // Pour search_results, on passe les paramètres GET originaux
        if ($page === 'search_results') {
            include $file_path;
        } else {
            include $file_path;
        }
    } else {
        echo '<p>Contenu non trouvé pour cette langue.</p>';
    }
} else {
    http_response_code(404);
    echo '<p>Page non trouvée.</p>';
}
?>