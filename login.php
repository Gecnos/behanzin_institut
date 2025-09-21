<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Behanzin Institut</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main id="login-page">
        <form id="login-form">
            <h2>Connexion</h2>
            <div id="form-response"></div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </main>

    <script>
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const responseDiv = document.getElementById('form-response');
        const formData = new FormData(this);

        fetch('php/handle_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                responseDiv.textContent = data.message;
                responseDiv.className = 'response error';
            }
        })
        .catch(error => {
            responseDiv.textContent = 'Erreur de connexion.';
            console.error(error);
        });
    });
    </script>
</body>
</html>