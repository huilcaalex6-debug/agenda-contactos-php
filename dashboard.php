<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth/login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT * FROM contactos WHERE id_usuario = :id_usuario ORDER BY nombre ASC");
$stmt->execute(['id_usuario' => $id_usuario]);
$contactos = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h4 class="mb-1">Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h4>
            <p class="text-muted mb-0">Correo: <?= htmlspecialchars($_SESSION['usuario_correo']) ?></p>
        </div>
        <a href="contactos/crear.php" class="btn btn-success mt-2 mt-md-0">+ Nuevo Contacto</a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <?php
        $mensajes = [
            'creado' => 'Contacto registrado correctamente.',
            'editado' => 'Contacto actualizado correctamente.',
            'eliminado' => 'Contacto eliminado correctamente.',
            'error_acceso' => 'No tienes permiso para acceder a ese registro.'
        ];
        echo $mensajes[$_GET['msg']] ?? 'Operación realizada.';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Mis Contactos (<?= count($contactos) ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($contactos)): ?>
            <p class="p-4 text-center text-muted mb-0">Aún no tienes contactos registrados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo Electrónico</th>
                            <th>Ciudad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contactos as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['nombre']) ?></td>
                                <td><?= htmlspecialchars($c['telefono']) ?></td>
                                <td><?= htmlspecialchars($c['correo']) ?></td>
                                <td><?= htmlspecialchars($c['ciudad']) ?></td>
                                <td class="text-end">
                                    <a href="contactos/editar.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="contactos/eliminar.php?id=<?= $c['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Estás seguro de eliminar a <?= htmlspecialchars(addslashes($c['nombre'])) ?>?');">
                                       Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>