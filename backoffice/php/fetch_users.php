<?php
require '../../php/database.php';

// TODO: Session check (admin only)

try {
    // On fusionne les utilisateurs de l'équipe éditoriale et les auteurs
    $sql = "
        (SELECT id_utilisateur as id, nom, prenom, email, role, 'utilisateurs' as source FROM Utilisateurs)
        UNION
        (SELECT id_auteur as id, nom, prenom, email, 'auteur' as role, 'auteur' as source FROM auteur)
        ORDER BY nom, prenom
    ";
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll();

    $roles = ['administrateur', 'editeur', 'relecteur'];

    if ($users) {
        echo '<table>';
        echo '<thead><tr><th>Nom</th><th>Email</th><th>Rôle</th></tr></thead>';
        echo '<tbody>';
        foreach ($users as $user) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($user['prenom'] . ' ' . $user['nom']) . '</td>';
            echo '<td>' . htmlspecialchars($user['email']) . '</td>';
            echo '<td>';
            if ($user['source'] === 'utilisateurs') {
                echo '<select class="role-select" data-userid="' . $user['id'] . '">';
                foreach ($roles as $role) {
                    $selected = ($user['role'] == $role) ? 'selected' : '';
                    echo '<option value="' . $role . '" ' . $selected . '>' . ucfirst($role) . '</option>';
                }
                echo '</select>';
            } else {
                echo 'Auteur';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>Aucun utilisateur trouvé.</p>';
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>Erreur lors de la récupération des utilisateurs.</p>";
    error_log("Erreur fetch_users: " . $e->getMessage());
}
?>