<?php
require 'auth_check.php';
require_role(['administrateur', 'editeur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Back-Office</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Back-Office</h1>
        <nav>
            <a href="index.php"><i class="fa-solid fa-inbox"></i> Soumissions</a>
            <a href="stats.php"><i class="fa-solid fa-chart-line"></i> Statistiques</a>
            <a href="users.php"><i class="fa-solid fa-users"></i> Utilisateurs</a>
            <a href="content.php"><i class="fa-solid fa-file-pen"></i> Contenu</a>
            <a href="../index.php" target="_blank"><i class="fa-solid fa-globe"></i> Voir le site</a>
            <a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
        </nav>
    </header>

    <main id="admin-content">
        <h2>Statistiques Générales</h2>

        <div class="stats-container">
            <div class="stat-card">
                <h3><i class="fa-solid fa-hourglass-half"></i> Soumis</h3>
                <p id="count-submitted">0</p>
            </div>
            <div class="stat-card">
                <h3><i class="fa-solid fa-check-circle"></i> Acceptés</h3>
                <p id="count-accepted">0</p>
            </div>
            <div class="stat-card">
                <h3><i class="fa-solid fa-globe"></i> Publiés</h3>
                <p id="count-published">0</p>
            </div>
            <div class="stat-card">
                <h3><i class="fa-solid fa-times-circle"></i> Refusés</h3>
                <p id="count-refused">0</p>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="status-chart"></canvas>
        </div>

        <h2>Téléchargements par article</h2>
        <div id="downloads-list">
            <!-- Liste des téléchargements -->
        </div>
    </main>

    <script src="../js/backoffice_stats.js"></script>
</body>
</html>