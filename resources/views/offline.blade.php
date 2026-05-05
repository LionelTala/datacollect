<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hors-ligne - DataCollect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea, #764ba2); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-offline { background: white; border-radius: 24px; padding: 2rem; text-align: center; max-width: 400px; margin: 1rem; }
    </style>
</head>
<body>
    <div class="card-offline">
        <i class="fas fa-wifi-slash fa-4x text-muted mb-3"></i>
        <h2 class="fw-bold">Hors-ligne</h2>
        <p class="text-muted">Reconnectez-vous pour synchroniser vos données.</p>
        <button onclick="location.reload()" class="btn btn-primary rounded-3">Réessayer</button>
    </div>
</body>
</html>
