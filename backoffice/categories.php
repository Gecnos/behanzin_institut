<?php
require 'auth_check.php';
require_role(['administrateur', 'editeur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Catégories - Back-Office</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="backoffice-body">
    <header class="backoffice-header">
        <h1>Gestion des Catégories</h1>
        <nav>
            <a href="index.php"><i class="fa-solid fa-inbox"></i> Soumissions</a>
            <a href="stats.php"><i class="fa-solid fa-chart-line"></i> Statistiques</a>
            <a href="users.php"><i class="fa-solid fa-users"></i> Utilisateurs</a>
            <a href="content.php"><i class="fa-solid fa-file-pen"></i> Contenu</a>
            <a href="categories.php"><i class="fa-solid fa-tags"></i> Catégories</a>
            <a href="../index.php" target="_blank"><i class="fa-solid fa-globe"></i> Voir le site</a>
            <a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
        </nav>
    </header>

    <main id="admin-content">
        <div class="actions">
            <h3>Ajouter une catégorie</h3>
            <form id="add-category-form">
                <div class="form-group">
                    <input type="text" name="category_name" placeholder="Nom de la catégorie" required>
                    <button type="submit">Ajouter</button>
                </div>
            </form>
        </div>

        <hr>

        <h3>Catégories existantes</h3>
        <div id="categories-list">
            <!-- La liste des catégories sera chargée ici -->
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const addCategoryForm = document.getElementById('add-category-form');
        const categoriesList = document.getElementById('categories-list');

        function fetchCategories() {
            fetch('php/fetch_categories_list.php')
                .then(response => response.text())
                .then(html => categoriesList.innerHTML = html)
                .catch(error => console.error('Erreur chargement catégories:', error));
        }

        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(addCategoryForm);
            const categoryName = formData.get('category_name');

            fetch('php/add_category.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: categoryName })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    addCategoryForm.reset();
                    fetchCategories();
                } else {
                    alert(result.message);
                }
            });
        });

        categoriesList.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-btn')) {
                const categoryId = e.target.dataset.id;
                if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
                    fetch('php/delete_category.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: categoryId })
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            fetchCategories();
                        } else {
                            alert(result.message);
                        }
                    });
                }
            }
        });

        fetchCategories();
    });
    </script>
</body>
</html>