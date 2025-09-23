<section id="contact">
    <h2>Nous contacter</h2>
    <div class="contact-container">
        <div class="contact-form">
            <form id="contact-form" method="POST" action="php/handle_contact_form.php">
                <div id="form-response"></div>
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="subject">Sujet</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="8" required></textarea>
                </div>
                <button type="submit">Envoyer le message</button>
            </form>
        </div>
        <div class="contact-info">
            <h3>Nos coordonnées</h3>
            <p><i class="fa-solid fa-map-marker-alt"></i> Adresse : 123 Rue de l'Institut, Cotonou, Bénin</p>
            <p><i class="fa-solid fa-phone"></i> Téléphone : +229 97 00 00 00</p>
            <p><i class="fa-solid fa-envelope"></i> Email : contact@behanzin-institut.com</p>
            <h3>Suivez-nous</h3>
            <div class="social-links">
                <a href="#" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</section>
