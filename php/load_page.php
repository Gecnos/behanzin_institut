<?php
require 'database.php'; // Make DB connection available

$page = $_GET['page'] ?? 'accueil';
$lang = $_GET['lang'] ?? 'fr';

// --- New logic for DB-driven pages ---
if ($page === 'a-propos') {
    $db_key_base = ($page === 'a-propos') ? 'about' : 'contact';
    $db_key = $db_key_base . $lang;

    try {
        $stmt = $pdo->prepare("SELECT valeur FROM ContenuStatique WHERE cle = :cle");
        $stmt->execute(['cle' => $db_key]);
        $content = $stmt->fetchColumn();

        if ($content !== false) {
            // Add structure for the static pages
            $title = ($page === 'a-propos') ? 'À Propos' : 'Contact';
            echo "<div class='static-page-container'>";
            echo "<h2>".htmlspecialchars($title)."</h2>";
            echo "<div class='static-content'>" . $content . "</div>";
            echo "</div>";
        } else {
            echo '<p>Contenu non disponible.</p>';
        }
    } catch (PDOException $e) {
        http_response_code(500);
        error_log("Erreur load_page (statique): " . $e->getMessage());
        echo '<p>Erreur lors du chargement du contenu.</p>';
    }
    exit; // Stop script execution for these pages
}

// --- Old logic for file-based pages ---
$lang_suffix = ($lang === 'en') ? '_en' : '';

$allowed_pages = [
    'accueil' => '../includes/home_content.php',
    'publications' => '../includes/publications_content.php',
    'article' => '../article.php',
    'contact' => '../includes/contact_content.php',
    // 'a-propos' and 'contact' are now handled above
    'soumettre' => '../includes/submit_content.php',
    'search_results' => '../php/search_results.php'
];

if (array_key_exists($page, $allowed_pages)) {
    $file_path = $allowed_pages[$page];
    if (file_exists($file_path)) {
        define('IS_DYNAMIC_LOAD', true);
        include $file_path;
    } else {
        echo '<p>Contenu non trouvé.</p>';
    }
} else {
    http_response_code(404);
    echo '<p>Page non trouvée.</p>';
}
?>