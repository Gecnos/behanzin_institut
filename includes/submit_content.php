<section id="submit-article">
    <h2>Soumettre un article</h2>
    <p>Veuillez remplir le formulaire ci-dessous pour soumettre votre manuscrit. Tous les champs sont obligatoires.</p>

    <form id="submission-form" enctype="multipart/form-data">
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

        <button type="submit" id="submit-btn"><i class="fa-solid fa-paper-plane"></i> Soumettre</button>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('submission-form');
    const responseDiv = document.getElementById('form-response');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.textContent = 'Envoi en cours...';

        const formData = new FormData(form);

        fetch('php/handle_submission.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            responseDiv.className = data.success ? 'response success' : 'response error';
            responseDiv.textContent = data.message;
            if (data.success) {
                form.reset();
            }
        })
        .catch(error => {
            responseDiv.className = 'response error';
            responseDiv.textContent = 'Une erreur technique est survenue. Veuillez réessayer.';
            console.error('Erreur de soumission:', error);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Soumettre';
        });
    });
});
</script>
