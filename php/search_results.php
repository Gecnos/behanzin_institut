<?php
require 'db_config.php';

// Récupère la requête de l'utilisateur
$search_query = isset($_GET['query']) ? $_GET['query'] : '';
$search_query = $conn->real_escape_string($search_query);

if (empty($search_query)) {
    echo "<p>Veuillez entrer un mot-clé de recherche.</p>";
    exit();
}

// Requête de recherche avancée : cherche dans le titre, le résumé, le nom de l'auteur et les mots-clés
$sql = "SELECT DISTINCT
            a.id_article, a.titre, a.resume, a.fichier_manuscrit, a.date_publication,
            au.nom AS auteur_nom, au.prenom AS auteur_prenom
        FROM Articles a
        JOIN Auteurs au ON a.id_auteur = au.id_auteur
        LEFT JOIN Liaison_Article_Mot_Cle lamc ON a.id_article = lamc.id_article
        LEFT JOIN Mots_Cles mc ON lamc.id_mot_cle = mc.id_mot_cle
        WHERE a.statut = 'publié'
          AND (
               a.titre LIKE '%$search_query%' 
            OR a.resume LIKE '%$search_query%' 
            OR au.nom LIKE '%$search_query%' 
            OR au.prenom LIKE '%$search_query%' 
            OR mc.mot_cle LIKE '%$search_query%'
          )
        ORDER BY a.date_publication DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Résultats de la recherche pour : '{$search_query}'</h3>";
    echo "<div class='publications-list'>";
    while($row = $result->fetch_assoc()) {
        $titre = htmlspecialchars($row['titre']);
        $resume = htmlspecialchars($row['resume']);
        $auteur = htmlspecialchars($row['auteur_prenom'] . ' ' . $row['auteur_nom']);
        $date = date("d/m/Y", strtotime($row['date_publication']));
        $pdf_link = htmlspecialchars($row['fichier_manuscrit']);

        echo "<div class='article-item'>";
        echo "<h4><a href='#'>{$titre}</a></h4>";
        echo "<p>Par {$auteur} - {$date}</p>";
        echo "<p>{$resume}</p>";
        echo "<a href='{$pdf_link}' target='_blank' class='btn-download'>Télécharger PDF</a>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p>Aucun résultat trouvé pour votre recherche.</p>";
}

$conn->close();
?>