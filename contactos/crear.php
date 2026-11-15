<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$errores = [];
$nombre = $telefono = $correo = $ciudad = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');

    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($telefono)) $errores[] = "El teléfono es obligatorio.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo electrónico no es válido.";
    if (empty($ciudad)) $errores[] = "La ciudad es obligatoria.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("INSERT INTO contactos (id_usuario, nombre, telefono, correo, ciudad) VALUES (:id_usuario, :nombre, :telefono, :correo, :ciudad)");
        $stmt->execute([
            'id_usuario' => $_SESSION['usuario_id'],
            'nombre' => $nombre,
            'telefono' => $telefono,
            'correo' => $correo,
            'ciudad' => $ciudad
        ]);

        header("Location: ../dashboard.php?msg=creado");
        exit;
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-4">Registrar Nuevo Contacto</h4>

                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errores as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($telefono) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($correo) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="ciudad" class="form-control" value="<?= htmlspecialchars($ciudad) ?>" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="../dashboard.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">Guardar Contacto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>