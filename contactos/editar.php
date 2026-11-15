<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_contacto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$id_usuario = $_SESSION['usuario_id'];

if (!$id_contacto) {
    header("Location: ../dashboard.php?msg=error_acceso");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM contactos WHERE id = :id AND id_usuario = :id_usuario");
$stmt->execute(['id' => $id_contacto, 'id_usuario' => $id_usuario]);
$contacto = $stmt->fetch();

if (!$contacto) {
    header("Location: ../dashboard.php?msg=error_acceso");
    exit;
}

$errores = [];
$nombre = $contacto['nombre'];
$telefono = $contacto['telefono'];
$correo = $contacto['correo'];
$ciudad = $contacto['ciudad'];

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
        $update = $pdo->prepare("UPDATE contactos SET nombre = :nombre, telefono = :telefono, correo = :correo, ciudad = :ciudad WHERE id = :id AND id_usuario = :id_usuario");
        $update->execute([
            'nombre' => $nombre,
            'telefono' => $telefono,
            'correo' => $correo,
            'ciudad' => $ciudad,
            'id' => $id_contacto,
            'id_usuario' => $id_usuario
        ]);

        header("Location: ../dashboard.php?msg=editado");
        exit;
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-4">Editar Contacto</h4>

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
                        <button type="submit" class="btn btn-primary">Actualizar Contacto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>