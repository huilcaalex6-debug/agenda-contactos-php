<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$error = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($correo) || empty($password)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nombre, correo, password FROM usuarios WHERE correo = :correo");
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];

            header("Location: ../dashboard.php");
            exit;
        } else {
            $error = "Credenciales incorrectas. Verifica tu correo y contraseña.";
        }
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="card-title mb-4 text-center">Iniciar Sesión</h4>

                <?php if (isset($_GET['registrado'])): ?>
                    <div class="alert alert-success">Registro exitoso. Ya puedes iniciar sesión.</div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($correo) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
                <div class="text-center mt-3">
                    <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>