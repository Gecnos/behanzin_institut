<?php
require 'auth_check.php';
require_role(['administrateur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Back-Office</title>
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
        <h2>Gestion des Utilisateurs</h2>
        <div id="users-list">
            <!-- La liste des utilisateurs sera chargée ici -->
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const usersList = document.getElementById('users-list');

        function loadUsers() {
            fetch('php/fetch_users.php')
                .then(response => response.text())
                .then(html => usersList.innerHTML = html)
                .catch(error => usersList.innerHTML = '<p>Erreur de chargement.</p>');
        }

        usersList.addEventListener('change', function(e) {
            if (e.target.classList.contains('role-select')) {
                const userId = e.target.dataset.userid;
                const newRole = e.target.value;
                
                if (!confirm(`Changer le rôle de cet utilisateur en '${newRole}' ?`)) {
                    loadUsers(); // Recharger pour annuler le changement visuel
                    return;
                }

                fetch('php/update_user_role.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId, role: newRole })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    loadUsers(); // Recharger la liste pour confirmer
                })
                .catch(error => alert('Erreur lors de la mise à jour.'));
            }
        });

        loadUsers();
    });
    </script>
</body>
</html>