<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Personal de Contactos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/dashboard.php">Agenda Personal</a>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                <a href="/auth/logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container">