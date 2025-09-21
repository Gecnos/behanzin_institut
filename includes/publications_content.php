<section id="publications">
    <h2>Publications</h2>

    <form id="filter-form">
        <div class="filter-group">
            <label for="category">Catégorie:</label>
            <select name="category" id="category">
                <!-- Les catégories seront chargées ici -->
            </select>
        </div>
        <div class="filter-group">
            <label for="author">Auteur:</label>
            <input type="text" name="author" id="author" placeholder="Nom de l'auteur">
        </div>
        <div class="filter-group">
            <label for="keywords">Mots-clés:</label>
            <input type="text" name="keywords" id="keywords" placeholder="ex: histoire, afrique">
        </div>
        <div class="filter-group">
            <label for="date-from">Date (de):</label>
            <input type="date" name="date-from" id="date-from">
        </div>
        <div class="filter-group">
            <label for="date-to">Date (à):</label>
            <input type="date" name="date-to" id="date-to">
        </div>
        <button type="submit">Filtrer</button>
        <button type="reset" id="reset-filters">Réinitialiser</button>
    </form>

    <div id="articles-list">
        <!-- Les articles filtrés seront affichés ici -->
    </div>
</section>

<script>
// Un peu de JS pour gérer le rechargement des articles sur cette page spécifique
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const articlesList = document.getElementById('articles-list');
    const resetBtn = document.getElementById('reset-filters');

    function fetchFilteredArticles(query = '') {
        fetch(`php/fetch_filtered_articles.php?${query}`)
            .then(response => response.text())
            .then(html => {
                articlesList.innerHTML = html;
            })
            .catch(error => console.error('Erreur de filtrage:', error));
    }

    // Chargement initial
    fetchFilteredArticles();

    // Au submit du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const query = new URLSearchParams(formData).toString();
        fetchFilteredArticles(query);
    });

    // Réinitialisation
    resetBtn.addEventListener('click', function() {
        form.reset();
        fetchFilteredArticles();
    });

    // Charger les catégories dynamiquement dans le select
    const categorySelect = document.getElementById('category');
    if(categorySelect) {
        fetch('php/fetch_categories.php')
            .then(response => response.text())
            .then(html => categorySelect.innerHTML = html)
            .catch(error => console.error('Erreur chargement catégories:', error));
    }
});
</script>
