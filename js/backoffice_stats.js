document.addEventListener('DOMContentLoaded', function() {
    fetch('php/fetch_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            // Mettre à jour les cartes de statistiques
            document.getElementById('count-submitted').textContent = data.counts.submitted || 0;
            document.getElementById('count-accepted').textContent = data.counts.accepted || 0;
            document.getElementById('count-published').textContent = data.counts.published || 0;
            document.getElementById('count-refused').textContent = data.counts.refused || 0;

            // Créer le graphique
            const ctx = document.getElementById('status-chart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Soumis', 'Acceptés', 'Publiés', 'Refusés'],
                    datasets: [{
                        label: 'Répartition des articles',
                        data: [
                            data.counts.submitted || 0,
                            data.counts.accepted || 0,
                            data.counts.published || 0,
                            data.counts.refused || 0
                        ],
                        backgroundColor: [
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Mettre à jour la liste des téléchargements
            const downloadsList = document.getElementById('downloads-list');
            if (data.downloads && data.downloads.length > 0) {
                let html = '<table><thead><tr><th>Article</th><th>Téléchargements</th></tr></thead><tbody>';
                data.downloads.forEach(item => {
                    html += `<tr><td>${item.titre}</td><td>${item.nombre_telechargements}</td></tr>`;
                });
                html += '</tbody></table>';
                downloadsList.innerHTML = html;
            } else {
                downloadsList.innerHTML = '<p>Aucune donnée de téléchargement disponible.</p>';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des statistiques:', error);
            document.querySelector('#admin-content').innerHTML = '<p>Impossible de charger les statistiques.</p>';
        });
});