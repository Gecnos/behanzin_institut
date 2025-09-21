document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.getElementById('main-content');
    let currentLang = 'fr'; // Langue par défaut
    let searchTimeout; // Pour la recherche en temps réel

    // --- Fonctions de base --- //

    function applyTranslations(translations) {
        document.querySelectorAll('[data-lang-key]').forEach(element => {
            const key = element.dataset.langKey;
            if (translations[key]) {
                element.textContent = translations[key];
            }
        });
    }

    function loadTranslations(lang) {
        fetch(`php/get_translation.php?lang=${lang}`)
            .then(response => response.json())
            .then(translations => {
                currentLang = lang;
                applyTranslations(translations);
                // Recharger le contenu de la page actuelle après changement de langue
                const currentPage = mainContent.dataset.currentPage || 'accueil';
                loadPage(currentPage);
            })
            .catch(error => console.error('Erreur de traduction:', error));
    }

    // Fonction principale de chargement de page via AJAX
    function loadPage(page, params = '') {
        let url = `php/load_page.php?page=${page}`;
        const langParam = `lang=${currentLang}`;
        url += params ? `&${params}&${langParam}` : `&${langParam}`;
        
        mainContent.dataset.currentPage = page; // Mettre à jour la page actuelle

        fetch(url)
            .then(response => response.text())
            .then(html => {
                mainContent.innerHTML = html;

                // Exécuter la logique spécifique à la page chargée
                if (page === 'accueil') {
                    loadLatestArticles();
                    loadFeaturedArticles();
                } else if (page === 'publications') {
                    fetchFilteredArticles(); // Charger les articles au chargement de la page publications
                    loadCategories(); // Charger les catégories
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement de la page:', error);
                mainContent.innerHTML = '<p>Erreur de chargement du contenu. Veuillez réessayer.</p>';
            });
    }

    // --- Fonctions spécifiques aux pages --- //

    function fetchFilteredArticles(query = '') {
        const articlesList = document.getElementById('articles-list');
        if (!articlesList) return; 

        fetch(`php/fetch_filtered_articles.php?${query}&lang=${currentLang}`)
            .then(response => response.text())
            .then(html => {
                articlesList.innerHTML = html;
            })
            .catch(error => console.error('Erreur de filtrage:', error));
    }

    function loadCategories() {
        const categorySelect = document.getElementById('category');
        if(categorySelect) {
            fetch('php/fetch_categories.php')
                .then(response => response.text())
                .then(html => categorySelect.innerHTML = html)
                .catch(error => console.error('Erreur chargement catégories:', error));
        }
    }

    function loadLatestArticles() {
        const container = document.getElementById('latest-articles-container');
        if (!container) return;

        fetch(`php/fetch_articles.php?latest=true&lang=${currentLang}`)
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur lors du chargement des derniers articles:', error);
                container.innerHTML = '<p>Impossible de charger les derniers articles.</p>';
            });
    }

    function loadFeaturedArticles() {
        const container = document.getElementById('featured-articles-container');
        if (!container) return;

        fetch(`php/fetch_articles.php?featured=true&lang=${currentLang}`)
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur lors du chargement des articles phares:', error);
                container.innerHTML = '<p>Impossible de charger les articles phares.</p>';
            });
    }

    // --- Initialisation --- //

    // Charger la page d'accueil et les traductions par défaut au démarrage
    loadPage('accueil');
    loadTranslations(currentLang);

    // --- Gestionnaires d'événements délégués (sur document.body) --- //

    // Clics
    document.body.addEventListener('click', function(e) {
        // Navigation AJAX
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            e.preventDefault();
            loadPage(e.target.dataset.page);
        }
        // Changement de langue
        else if (e.target.closest('#lang-selector button')) {
            const lang = e.target.dataset.lang;
            if (lang && lang !== currentLang) {
                loadTranslations(lang);
            }
        }
        // Toggle des filtres avancés (page publications)
        else if (e.target.id === 'toggle-advanced-filters') {
            const advancedFilters = document.querySelector('.advanced-filters');
            advancedFilters.classList.toggle('hidden');
            if (advancedFilters.classList.contains('hidden')) {
                e.target.innerHTML = '<i class="fa-solid fa-filter"></i> Filtres Avancés';
            } else {
                e.target.innerHTML = '<i class="fa-solid fa-filter-circle-xmark"></i> Masquer Filtres';
            }
        }
        // Réinitialisation des filtres (page publications)
        else if (e.target.id === 'reset-filters') {
            const form = document.getElementById('filter-form');
            const searchInput = document.getElementById('publications-search-input');
            if (form) form.reset();
            if (searchInput) searchInput.value = '';
            fetchFilteredArticles();
        }
    });

    // Soumissions de formulaires
    document.body.addEventListener('submit', function(e) {
        // Soumission de la barre de recherche générale
        if (e.target.id === 'general-search-form') {
            e.preventDefault();
            const query = e.target.querySelector('input[name="query"]').value;
            if (query) {
                loadPage('search_results', `query=${encodeURIComponent(query)}`);
            }
        }
        // Soumission du formulaire de filtres avancés (page publications)
        else if (e.target.id === 'filter-form') {
            e.preventDefault();
            const form = e.target;
            const searchInput = document.getElementById('publications-search-input');
            const formData = new FormData(form);
            if (searchInput) formData.append('query', searchInput.value);
            const query = new URLSearchParams(formData).toString();
            fetchFilteredArticles(query);
        }
    });

    // Inputs (pour la recherche en temps réel)
    document.body.addEventListener('input', function(e) {
        if (e.target.id === 'publications-search-input') {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const form = document.getElementById('filter-form');
                const searchInput = e.target;
                const formData = new FormData(form);
                if (searchInput) formData.append('query', searchInput.value);
                const query = new URLSearchParams(formData).toString();
                fetchFilteredArticles(query);
            }, 300);
        }
    });
});