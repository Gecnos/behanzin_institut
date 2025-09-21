<?php
require 'auth_check.php';
require_role(['administrateur', 'editeur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Contenu - Back-Office</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
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
        <h2>Gestion du Contenu Statique</h2>
        <form id="content-form">
            <div id="form-response"></div>

            <h3>Page "À Propos" (Français)</h3>
            <textarea name="about_content_fr" id="about_content_fr" rows="15"></textarea>

            <h3>Page "À Propos" (Anglais)</h3>
            <textarea name="about_content_en" id="about_content_en" rows="15"></textarea>

            <h3>Page "Contact" (Français)</h3>
            <textarea name="contact_content_fr" id="contact_content_fr" rows="10"></textarea>

            <h3>Page "Contact" (Anglais)</h3>
            <textarea name="contact_content_en" id="contact_content_en" rows="10"></textarea>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const aboutTextFr = document.getElementById('about_content_fr');
    const aboutTextEn = document.getElementById('about_content_en');
    const contactTextFr = document.getElementById('contact_content_fr');
    const contactTextEn = document.getElementById('contact_content_en');
    const form = document.getElementById('content-form');
    const responseDiv = document.getElementById('form-response');

    // Charger le contenu initial
    fetch('php/fetch_content.php')
        .then(response => response.json())
        .then(data => {
            aboutTextFr.value = data.about_content_fr;
            aboutTextEn.value = data.about_content_en;
            contactTextFr.value = data.contact_content_fr;
            contactTextEn.value = data.contact_content_en;
        });

    // Gérer la soumission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const content = {
            about_content_fr: formData.get('about_content_fr'),
            about_content_en: formData.get('about_content_en'),
            contact_content_fr: formData.get('contact_content_fr'),
            contact_content_en: formData.get('contact_content_en')
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