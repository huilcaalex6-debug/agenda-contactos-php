<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$errores = [];
$nombre = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar_password'] ?? '';

    if (empty($nombre)) $errores[] = "El nombre completo es obligatorio.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo electrónico no es válido.";
    if (strlen($password) < 6) $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    if ($password !== $confirmar) $errores[] = "Las contraseñas no coinciden.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo");
        $stmt->execute(['correo' => $correo]);

        if ($stmt->fetch()) {
            $errores[] = "El correo electrónico ya se encuentra registrado.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)");
            $insert->execute([
                'nombre' => $nombre,
                'correo' => $correo,
                'password' => $passwordHash
            ]);

            header("Location: login.php?registrado=1");
            exit;
        }
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="card-title mb-4 text-center">Crear Cuenta</h4>

                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errores as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($correo) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="confirmar_password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
                <div class="text-center mt-3">
                    <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>