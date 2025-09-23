<section id="submit-article">
    <h2>Soumettre un article</h2>
    <p>Veuillez remplir le formulaire ci-dessous pour soumettre votre manuscrit. Tous les champs sont obligatoires.</p>

    <form id="submission-form" enctype="multipart/form-data" method="POST">
        <div id="form-response"></div>

        <div class="form-group">
            <label for="title">Titre de l'article</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="resume">Résumé</label>
            <textarea id="resume" name="resume" rows="8" required></textarea>
        </div>

        <div class="form-group">
            <label for="keywords">Mots-clés (séparés par des virgules)</label>
            <input type="text" id="keywords" name="keywords" required>
        </div>

        <div class="form-group">
            <label for="author_name">Votre Nom</label>
            <input type="text" id="author_name" name="author_name" required>
        </div>
        
        <div class="form-group">
            <label for="author_firstname">Votre Prénom</label>
            <input type="text" id="author_firstname" name="author_firstname" required>
        </div>

        <div class="form-group">
            <label for="author_email">Votre Email</label>
            <input type="email" id="author_email" name="author_email" required>
        </div>

        <div class="form-group">
            <label for="author_institution">Votre Institution</label>
            <input type="text" id="author_institution" name="author_institution" required>
        </div>

        <div class="form-group">
            <label for="author_phone">Votre Téléphone</label>
            <input type="tel" id="author_phone" name="author_phone" required>
        </div>

        <div class="form-group">
            <label for="manuscript">Votre manuscrit (PDF ou DOCX)</label>
            <input type="file" id="manuscript" name="manuscript" accept=".pdf,.doc,.docx" required>
        </div>

        <div class="form-group">
            <label for="article_image">Image de l'article (JPEG, PNG)</label>
            <input type="file" id="article_image" name="article_image" accept=".jpg,.jpeg,.png" required>
        </div>

        <button type="submit" id="submit-btn"><i class="fa-solid fa-paper-plane"></i> Soumettre</button>
    </form>
</section>
