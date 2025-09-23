<?php
require 'database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Une erreur est survenue.'];

// --- Validation des données --- //
// Check if POST data is even received
if (empty($_POST)) {
    $response['message'] = "Aucune donnée POST reçue. Vérifiez la taille du fichier ou la configuration du serveur.";
    echo json_encode($response);
    exit;
}

$required_fields = ['title', 'resume', 'keywords', 'author_name', 'author_firstname', 'author_email', 'author_institution', 'author_phone'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] === '') { // Use !isset or empty string check
        $response['message'] = "Le champ '{$field}' est obligatoire et ne peut être vide.";
        echo json_encode($response);
        exit;
    }
}

if (!filter_var($_POST['author_email'], FILTER_VALIDATE_EMAIL)) {
    $response['message'] = "L'adresse email n'est pas valide.";
    echo json_encode($response);
    exit;
}

// --- Gestion du fichier uploadé --- //
if (isset($_FILES['manuscript']) && $_FILES['manuscript']['error'] == 0) {
    $allowed_extensions = ['pdf', 'doc', 'docx'];
    $file_extension = strtolower(pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        $response['message'] = "Format de fichier non autorisé. Uniquement PDF, DOC, DOCX.";
        echo json_encode($response);
        exit;
    }

    // Créer un nom de fichier unique pour éviter les conflits
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_name = uniqid('manuscrit_', true) . '.' . $file_extension;
    $upload_path = $upload_dir . $file_name;

    if (!move_uploaded_file($_FILES['manuscript']['tmp_name'], $upload_path)) {
        $response['message'] = "Erreur lors du téléversement du fichier.";
        echo json_encode($response);
        exit;
    }
} else {
    // More specific error messages for file upload
    switch ($_FILES['manuscript']['error']) {
        case UPLOAD_ERR_INI_SIZE:
            $response['message'] = "Le fichier est trop volumineux (dépasse upload_max_filesize).";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $response['message'] = "Le fichier est trop volumineux (dépasse MAX_FILE_SIZE).";
            break;
        case UPLOAD_ERR_PARTIAL:
            $response['message'] = "Le fichier n'a été que partiellement téléversé.";
            break;
        case UPLOAD_ERR_NO_FILE:
            $response['message'] = "Aucun fichier n'a été téléversé.";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $response['message'] = "Dossier temporaire manquant sur le serveur.";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $response['message'] = "Échec de l'écriture du fichier sur le disque.";
            break;
        case UPLOAD_ERR_EXTENSION:
            $response['message'] = "Une extension PHP a arrêté le téléversement du fichier.";
            break;
        default:
            $response['message'] = "Erreur inconnue lors du téléversement du fichier.";
            break;
    }
    echo json_encode($response);
    exit;
}

// --- Gestion de l'image de l'article --- //
$image_path = null;
if (isset($_FILES['article_image']) && $_FILES['article_image']['error'] == 0) {
    $allowed_image_extensions = ['jpg', 'jpeg', 'png'];
    $image_extension = strtolower(pathinfo($_FILES['article_image']['name'], PATHINFO_EXTENSION));

    if (!in_array($image_extension, $allowed_image_extensions)) {
        $response['message'] = "Format d'image non autorisé. Uniquement JPG, JPEG, PNG.";
        echo json_encode($response);
        exit;
    }

    $image_name = uniqid('article_img_', true) . '.' . $image_extension;
    $image_upload_path = $upload_dir . $image_name;

    if (!move_uploaded_file($_FILES['article_image']['tmp_name'], $image_upload_path)) {
        $response['message'] = "Erreur lors du téléversement de l'image.";
        echo json_encode($response);
        exit;
    }
    $image_path = $image_upload_path;
} else {
    $response['message'] = "L'image de l'article est obligatoire.";
    echo json_encode($response);
    exit;
}

// --- Insertion en base de données --- //
$pdo->beginTransaction();
try {
    // 1. Vérifier si l'auteur existe, sinon le créer
    $stmt = $pdo->prepare("SELECT id_auteur FROM auteur WHERE email = :email");
    $stmt->execute(['email' => $_POST['author_email']]);
    $id_auteur = $stmt->fetchColumn();

    if (!$id_auteur) {
        $stmt = $pdo->prepare(
            "INSERT INTO auteur (nom, prenom, institution, email, telephone, password) VALUES (:nom, :prenom, :institution, :email, :telephone, :password)"
        );
        $stmt->execute([
            ':nom' => $_POST['author_name'],
            ':prenom' => $_POST['author_firstname'],
            ':institution' => $_POST['author_institution'],
            ':email' => $_POST['author_email'],
            ':telephone' => $_POST['author_phone'],
            ':password' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT) // Mot de passe aléatoire
        ]);
        $id_auteur = $pdo->lastInsertId();
    }

    // 2. Insérer l'article
    $stmt = $pdo->prepare(
        "INSERT INTO Articles (titre, resume, fichier_manuscrit, image, statut, date_soumission, id_auteur) VALUES (:titre, :resume, :fichier, :image, 'en attente', NOW(), :id_auteur)"
    );
    $stmt->execute([
        ':titre' => $_POST['title'],
        ':resume' => $_POST['resume'],
        ':fichier' => $upload_path,
        ':image' => $image_path,
        ':id_auteur' => $id_auteur
    ]);
    $id_article = $pdo->lastInsertId();

    // 3. Gérer les mots-clés
    $keywords = explode(',', $_POST['keywords']);
    foreach ($keywords as $kw) {
        $kw = trim($kw);
        if (empty($kw)) continue;

        $stmt = $pdo->prepare("SELECT id_mot_cle FROM Mots_Cles WHERE mot_cle = :kw");
        $stmt->execute(['kw' => $kw]);
        $id_mot_cle = $stmt->fetchColumn();

        if (!$id_mot_cle) {
            $stmt = $pdo->prepare("INSERT INTO Mots_Cles (mot_cle) VALUES (:kw)");
            $stmt->execute(['kw' => $kw]);
            $id_mot_cle = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("INSERT INTO Liaison_Article_Mot_Cle (id_article, id_mot_cle) VALUES (:id_article, :id_mot_cle)");
        $stmt->execute(['id_article' => $id_article, 'id_mot_cle' => $id_mot_cle]);
    }

    $pdo->commit();
    $response['success'] = true;
    $response['message'] = 'Votre article a été soumis avec succès. Vous recevrez une notification par email.';

    // TODO: Envoyer un email de notification avec PHPMailer
    /*
    require 'vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    // Configurer SMTP...
    $mail->setFrom('no-reply@behanzin-institut.com', 'Behanzin Institut');
    $mail->addAddress($_POST['author_email']);
    $mail->addAddress('editeur@behanzin-institut.com');
    $mail->Subject = 'Nouvelle soumission d\'article';
    $mail->Body    = "L\'article '{$_POST['title']}' a été soumis.";
    $mail->send();
    */

} catch (PDOException $e) {
    $pdo->rollBack();
    $response['message'] = "Erreur de base de données. Votre soumission n'a pas pu être enregistrée.";
    error_log("Erreur handle_submission: " . $e->getMessage());
}

echo json_encode($response);
?>