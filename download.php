<?php
require 'php/database.php';

$article_id = $_GET['id'] ?? null;
if (!$article_id) die('Fichier non spécifié.');

// 1. Récupérer le chemin du fichier
$stmt = $pdo->prepare("SELECT fichier_manuscrit FROM Articles WHERE id_article = :id AND statut = 'publié'");
$stmt->execute(['id' => $article_id]);
$file_path = $stmt->fetchColumn();

if (!$file_path || !file_exists($file_path)) {
    die('Fichier non trouvé ou article non publié.');
}

// 2. Mettre à jour les statistiques
// On utilise INSERT ... ON DUPLICATE KEY UPDATE pour créer ou incrémenter
// Il faut d'abord s'assurer que id_article est une clé unique dans Statistiques_Articles
// ALTER TABLE Statistiques_Articles ADD UNIQUE (id_article);
$sql = "INSERT INTO Statistiques_Articles (id_article, nombre_telechargements, date_mise_a_jour) 
        VALUES (:id, 1, NOW()) 
        ON DUPLICATE KEY UPDATE nombre_telechargements = nombre_telechargements + 1, date_mise_a_jour = NOW()";
$stmt_stat = $pdo->prepare($sql);
$stmt_stat->execute(['id' => $article_id]);

// 3. Servir le fichier
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
?>