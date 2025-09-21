<?php
require '../../php/database.php';

header('Content-Type: application/json');

// TODO: Ajouter une vérification de session et de rôle

$response = [
    'counts' => [
        'submitted' => 0,
        'accepted' => 0,
        'published' => 0,
        'refused' => 0
    ],
    'downloads' => []
];

try {
    // 1. Compter les articles par statut
    $stmt = $pdo->query("SELECT statut, COUNT(*) as total FROM Articles GROUP BY statut");
    $status_counts = $stmt->fetchAll();

    foreach ($status_counts as $row) {
        switch ($row['statut']) {
            case 'en attente':
                $response['counts']['submitted'] = $row['total'];
                break;
            case 'accepté':
                $response['counts']['accepted'] = $row['total'];
                break;
            case 'publié':
                $response['counts']['published'] = $row['total'];
                break;
            case 'refusé':
                $response['counts']['refused'] = $row['total'];
                break;
        }
    }

    // 2. Récupérer les statistiques de téléchargement
    // NOTE: La table `Statistiques_Articles` n'est pas encore utilisée. 
    // On simule pour l'instant, mais la requête serait comme ci-dessous.
    $stmt = $pdo->query(
        "SELECT a.titre, s.nombre_telechargements
         FROM Statistiques_Articles s
         JOIN Articles a ON s.id_article = a.id_article
         ORDER BY s.nombre_telechargements DESC
         LIMIT 10"
    );
    $response['downloads'] = $stmt->fetchAll();

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur fetch_stats: " . $e->getMessage());
    $response['error'] = 'Erreur de base de données.';
}

echo json_encode($response);
?>