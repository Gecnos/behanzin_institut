
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Behanzin Institut</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400;1,700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <h1 class="site-title"><a href="#" data-page="accueil">Behanzin Institut</a></h1>
        
        <nav class="main-nav">
            <a href="#" data-page="accueil" data-lang-key="home">Accueil</a>
            <div class="nav-item has-dropdown">
                <a href="#" data-page="publications" data-lang-key="publications">Publications</a>
                <div class="dropdown-menu">
                    <a href="#" data-page="publications">Toutes les publications</a>
                    <!-- Categories can be loaded dynamically here if needed -->
                </div>
            </div>
            <div class="nav-item has-dropdown">
                <a href="#" data-page="a-propos" data-lang-key="about">À Propos</a>
                <div class="dropdown-menu">
                    <a href="#" data-page="a-propos">Notre mission</a>
                    <a href="#" data-page="contact" data-lang-key="contact">Contact</a>
                </div>
            </div>
            <a href="#" data-page="soumettre" data-lang-key="submit">Soumettre un article</a>
            
            <div id="lang-selector">
                <button data-lang="fr">FR</button>
                <button data-lang="en">EN</button>
            </div>
            
        </nav>
    </header>

    <main id="main-content">
        <!-- Le contenu sera chargé ici par AJAX -->
    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-column">
                <h4>Behanzin Institut</h4>
                <p>Analyses et perspectives sur le monde contemporain.</p>
            </div>
            <div class="footer-column">
                <h4>Navigation</h4>
                <ul class="footer-nav">
                    <li><a href="#" data-page="accueil" data-lang-key="home">Accueil</a></li>
                    <li><a href="#" data-page="publications" data-lang-key="publications">Publications</a></li>
                    <li><a href="#" data-page="a-propos" data-lang-key="about">À Propos</a></li>
                    <li><a href="#" data-page="contact" data-lang-key="contact">Contact</a></li>
                    <li><a href="#" data-page="soumettre" data-lang-key="submit">Soumettre un article</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Suivez-nous</h4>
                <div class="social-links">
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Behanzin Institut. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
