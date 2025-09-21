document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.getElementById('main-content');
    let currentLang = 'fr'; // Langue par défaut

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
            })
            .catch(error => console.error('Erreur de traduction:', error));
    }


    function loadPage(page, params = '') {
        let url = `php/load_page.php?page=${page}`;
        if (params) {
            url += `&${params}`;
        }
        fetch(url)
            .then(response => response.text())
            .then(html => {
                mainContent.innerHTML = html;

                // Si on est sur la page d'accueil, charger les derniers articles et les articles phares
                if (page === 'accueil') {
                    loadLatestArticles();
                    loadFeaturedArticles();
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement de la page:', error);
                mainContent.innerHTML = '<p>Erreur de chargement du contenu. Veuillez réessayer.</p>';
            });
    }

    function loadLatestArticles() {
        const container = document.getElementById('latest-articles-container');
        if (!container) return;

        fetch('php/fetch_articles.php?latest=true')
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur lors du chargement des articles:', error);
                container.innerHTML = '<p>Impossible de charger les derniers articles.</p>';
            });
    }

    function loadFeaturedArticles() {
        const container = document.getElementById('featured-articles-container');
        if (!container) return;

        fetch('php/fetch_articles.php?featured=true')
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur chargement articles phares:', error);
                container.innerHTML = '<p>Impossible de charger les articles phares.</p>';
            });
    }

    // Charger la page d'accueil et les traductions par défaut
    loadPage('accueil');
    loadTranslations(currentLang);

    // Gérer le changement de langue
    document.getElementById('lang-selector').addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON') {
            const lang = e.target.dataset.lang;
            if (lang && lang !== currentLang) {
                loadTranslations(lang);
            }
        }
    });

    // Gérer la soumission de la barre de recherche générale
    document.getElementById('general-search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const query = this.querySelector('input[name="query"]').value;
        if (query) {
            // Charger une page de résultats de recherche via AJAX
            loadPage('search_results', `query=${encodeURIComponent(query)}`);
        }
    });

    document.body.addEventListener('click', function(e) {
        // On met l'écouteur sur le body pour gérer les liens chargés dynamiquement
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            e.preventDefault();
            const page = e.target.dataset.page;
            loadPage(page);
        }
    });
});