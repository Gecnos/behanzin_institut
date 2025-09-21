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
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Back-Office</h1>
        <nav>
            <a href="index.php">Soumissions</a>
            <a href="stats.php">Statistiques</a>
            <a href="../index.php" target="_blank">Voir le site</a>
        </nav>
    </header>

    <main id="admin-content">
        <h2>Statistiques Générales</h2>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Soumis</h3>
                <p id="count-submitted">0</p>
            </div>
            <div class="stat-card">
                <h3>Acceptés</h3>
                <p id="count-accepted">0</p>
            </div>
            <div class="stat-card">
                <h3>Publiés</h3>
                <p id="count-published">0</p>
            </div>
            <div class="stat-card">
                <h3>Refusés</h3>
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