<?php
require 'auth_check.php';
require_role(['administrateur', 'editeur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Contenu - Back-Office</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Back-Office</h1>
        <nav>
            <a href="index.php">Soumissions</a>
            <a href="stats.php">Statistiques</a>
            <a href="users.php">Utilisateurs</a>
            <a href="content.php">Contenu</a>
            <a href="../index.php" target="_blank">Voir le site</a>
        </nav>
    </header>

    <main id="admin-content">
        <h2>Gestion du Contenu Statique</h2>
        <form id="content-form">
            <div id="form-response"></div>

            <h3>Page "À Propos"</h3>
            <textarea name="about_content" id="about_content" rows="15"></textarea>

            <h3>Page "Contact"</h3>
            <textarea name="contact_content" id="contact_content" rows="10"></textarea>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const aboutText = document.getElementById('about_content');
        const contactText = document.getElementById('contact_content');
        const form = document.getElementById('content-form');
        const responseDiv = document.getElementById('form-response');

        // Charger le contenu initial
        fetch('php/fetch_content.php')
            .then(response => response.json())
            .then(data => {
                aboutText.value = data.about_content;
                contactText.value = data.contact_content;
            });

        // Gérer la soumission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const content = {
                about_content: formData.get('about_content'),
                contact_content: formData.get('contact_content')
            };

            fetch('php/update_content.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(content)
            })
            .then(response => response.json())
            .then(data => {
                responseDiv.textContent = data.message;
                responseDiv.className = data.success ? 'response success' : 'response error';
            });
        });
    });
    </script>
</body>
</html>