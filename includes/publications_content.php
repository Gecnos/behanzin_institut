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