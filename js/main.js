document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.getElementById('main-content');
    let currentLang = 'fr';
    let searchTimeout;

    // --- Translation Functions --- //
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
                const currentPage = mainContent.dataset.currentPage || 'accueil';
                const currentId = mainContent.dataset.currentId || '';
                loadPage(currentPage, currentId ? `id=${currentId}` : '');
            })
            .catch(error => console.error('Translation Error:', error));
    }

    // --- Page Loading --- //
    function loadPage(page, params = '') {
        mainContent.dataset.currentPage = page;
        const searchParams = new URLSearchParams(params);
        mainContent.dataset.currentId = searchParams.get('id') || '';

        fetch(`php/load_page.php?page=${page}&lang=${currentLang}&${params}`)
            .then(response => response.text())
            .then(html => {
                mainContent.innerHTML = html;
                if (page === 'accueil') {
                    loadHomepageContent();
                } else if (page === 'publications') {
                    fetchFilteredArticles();
                    loadCategories();
                }
            })
            .catch(error => {
                console.error('Page Load Error:', error);
                mainContent.innerHTML = '<p>Erreur de chargement.</p>';
            });
    }

    function loadHomepageContent() {
        fetchContent('featured_main', '.featured-main');
        fetchContent('featured_side', '.featured-side');
        fetchContent('latest_grid', '#latest-articles-container');
    }

    function fetchContent(view, containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        fetch(`php/fetch_articles.php?view=${view}&lang=${currentLang}`)
            .then(response => response.text())
            .then(html => { container.innerHTML = html; })
            .catch(error => console.error(`Error loading ${view}:`, error));
    }

    // --- Publications Page Specific Functions --- //
    function loadCategories() {
        const categorySelect = document.getElementById('category');
        if (!categorySelect) return;
        fetch('php/fetch_categories.php')
            .then(response => response.text())
            .then(html => { categorySelect.innerHTML = html; })
            .catch(error => console.error('Category Load Error:', error));
    }

    // --- Initialization --- //
    loadPage('accueil');
    loadTranslations(currentLang);

    // --- Event Listeners --- //
    document.body.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        const article = e.target.closest('article[data-page="article"]');

        if (link && link.dataset.page) {
            e.preventDefault();
            let params = '';
            if (link.dataset.id) {
                params = `id=${link.dataset.id}`;
            }
            loadPage(link.dataset.page, params);
        } else if (article) {
            e.preventDefault();
            loadPage('article', `id=${article.dataset.id}`);
        } else if (e.target.closest('#lang-selector button')) {
            const lang = e.target.dataset.lang;
            if (lang && lang !== currentLang) loadTranslations(lang);
        } else if (e.target.closest('#toggle-advanced-filters')) {
            e.preventDefault();
            document.getElementById('filter-form').classList.toggle('hidden');
        } else if (e.target.closest('#simple-search-button')) {
            e.preventDefault();
            console.log('Simple search button clicked.');
            const searchInput = document.getElementById('publications-search-input');
            const query = `query=${encodeURIComponent(searchInput.value)}`;
            console.log('Fetching articles with query:', query);
            fetchFilteredArticles(query);
        }
    });

    document.body.addEventListener('submit', function(e) {
        if (e.target.id === 'filter-form') {
            e.preventDefault();
            const formData = new FormData(e.target);
            const query = new URLSearchParams(formData).toString();
            fetchFilteredArticles(query);
        } else if (e.target.id === 'submission-form') {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const formResponse = document.getElementById('form-response');
            formResponse.innerHTML = ''; // Clear previous messages

            fetch('php/handle_submission.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    formResponse.innerHTML = '<p class="success-message">' + data.message + '</p>';
                    form.reset(); // Clear the form
                } else {
                    formResponse.innerHTML = '<p class="error-message">' + data.message + '</p>';
                }
            })
            .catch(error => {
                console.error('Error during submission:', error);
                formResponse.innerHTML = '<p class="error-message">Une erreur inattendue est survenue.</p>';
            });
        }
    });

    function fetchFilteredArticles(query = '') {
        const articlesList = document.getElementById('articles-list');
        if (!articlesList) {
            console.log('articles-list element not found.');
            return;
        }

        console.log('fetchFilteredArticles called with query:', query);
        fetch(`php/fetch_filtered_articles.php?${query}&lang=${currentLang}`)
            .then(response => {
                console.log('Fetch response received. Status:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                console.log('HTML received:', html);
                articlesList.innerHTML = html;
            })
            .catch(error => console.error('Filter Error:', error));
    }
});