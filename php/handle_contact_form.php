<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = trim($_POST["message"]);

    if (empty($name) || empty($email) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Veuillez remplir tous les champs du formulaire.";
        exit;
    }

    $recipient = "vianneyhoueho@gmail.com"; // Remplacez par votre adresse email
    $email_subject = "Nouveau message de contact de: $name";
    $email_content = "Nom: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Sujet: $subject\n";
    $email_content .= "Message:\n$message\n";

    $email_headers = "From: $name <$email>";

    if (mail($recipient, $email_subject, $email_content, $email_headers)) {
        http_response_code(200);
        echo "Merci! Votre message a été envoyé.";
    } else {
        http_response_code(500);
        echo "Oops! Quelque chose s'est mal passé et nous n'avons pas pu envoyer votre message.";
    }

} else {
    http_response_code(403);
    echo "Il y a eu un problème avec votre soumission, veuillez réessayer.";
}
?>