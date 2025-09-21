<section id="publications">
    <h2>Publications</h2>

    <div class="search-filter-container">
        <div class="simple-search">
            <input type="text" id="publications-search-input" placeholder="Rechercher dans les publications...">
            <button id="toggle-advanced-filters"><i class="fa-solid fa-filter"></i> Filtres Avancés</button>
        </div>

        <form id="filter-form" class="advanced-filters hidden">
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
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Filtrer</button>
            <button type="reset" id="reset-filters"><i class="fa-solid fa-xmark"></i> Réinitialiser</button>
        </form>
    </div>

    <div id="articles-list">
        <!-- Les articles filtrés seront affichés ici -->
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const articlesList = document.getElementById('articles-list');
    const resetBtn = document.getElementById('reset-filters');
    const searchInput = document.getElementById('publications-search-input');
    const toggleFiltersBtn = document.getElementById('toggle-advanced-filters');
    const advancedFilters = document.querySelector('.advanced-filters');

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

    // Au submit du formulaire de filtres avancés
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        // Ajouter le terme de recherche simple aux filtres avancés
        formData.append('query', searchInput.value);
        const query = new URLSearchParams(formData).toString();
        fetchFilteredArticles(query);
    });

    // Recherche simple en temps réel (ou après un court délai)
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const formData = new FormData(form);
            formData.append('query', searchInput.value);
            const query = new URLSearchParams(formData).toString();
            fetchFilteredArticles(query);
        }, 300); // Délai de 300ms
    });

    // Réinitialisation
    resetBtn.addEventListener('click', function() {
        form.reset();
        searchInput.value = '';
        fetchFilteredArticles();
    });

    // Toggle des filtres avancés
    toggleFiltersBtn.addEventListener('click', function() {
        advancedFilters.classList.toggle('hidden');
        if (advancedFilters.classList.contains('hidden')) {
            toggleFiltersBtn.innerHTML = '<i class="fa-solid fa-filter"></i> Filtres Avancés';
        } else {
            toggleFiltersBtn.innerHTML = '<i class="fa-solid fa-filter-circle-xmark"></i> Masquer Filtres';
        }
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
<style>
    .advanced-filters.hidden {
        display: none;
    }
    .search-filter-container {
        margin-bottom: 2rem;
        background-color: var(--card-bg-color);
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .simple-search {
        display: flex;
        gap: 10px;
        margin-bottom: 1rem;
    }
    .simple-search input {
        flex-grow: 1;
        padding: 0.8rem;
        border: 1px solid var(--border-color);
        border-radius: 5px;
    }
    .simple-search button {
        white-space: nowrap;
    }
    .advanced-filters {
        margin-top: 1rem;
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
    }
    .advanced-filters .filter-group {
        margin-bottom: 1rem;
    }
</style>