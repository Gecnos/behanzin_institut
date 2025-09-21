<?php
require 'auth_check.php';
require_role(['administrateur', 'editeur', 'relecteur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back-Office - Behanzin Institut</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <header>
        <h1>Back-Office</h1>
        <nav>
            <a href="index.php">Soumissions</a>
            <a href="stats.php">Statistiques</a>
            <a href="../index.php" target="_blank">Voir le site</a>
            <a href="../php/logout.php">Déconnexion</a>
        </nav>
    </header>

    <main id="admin-content">
        <h2>Soumissions en attente</h2>
        <div id="submissions-list">
            <!-- Les soumissions seront chargées ici -->
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const submissionsList = document.getElementById('submissions-list');

        function loadSubmissions() {
            fetch('php/fetch_submissions.php')
                .then(response => response.text())
                .then(html => {
                    submissionsList.innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur chargement soumissions:', error);
                    submissionsList.innerHTML = '<p>Impossible de charger les soumissions.</p>';
                });
        }

        loadSubmissions();
    });
    </script>
</body>
</html>