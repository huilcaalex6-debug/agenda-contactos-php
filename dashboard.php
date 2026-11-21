<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth/login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY id ASC")->fetchAll();

$buscar = trim($_GET['buscar'] ?? '');
$categoria_filtro = $_GET['categoria'] ?? '';
$solo_favoritos = isset($_GET['favoritos']) ? 1 : 0;
$orden = $_GET['orden'] ?? 'nombre';

$sql = "SELECT c.*, cat.nombre AS nombre_categoria 
        FROM contactos c 
        INNER JOIN categorias cat ON c.id_categoria = cat.id 
        WHERE c.id_usuario = :id_usuario";
$params = ['id_usuario' => $id_usuario];

if ($buscar !== '') {
    $sql .= " AND (c.nombre LIKE :buscar1 OR c.correo LIKE :buscar2 OR c.ciudad LIKE :buscar3)";
    $params['buscar1'] = "%$buscar%";
    $params['buscar2'] = "%$buscar%";
    $params['buscar3'] = "%$buscar%";
}

if ($categoria_filtro !== '') {
    $sql .= " AND c.id_categoria = :categoria";
    $params['categoria'] = (int)$categoria_filtro;
}

if ($solo_favoritos === 1) {
    $sql .= " AND c.favorito = 1";
}

if ($orden === 'ciudad') {
    $sql .= " ORDER BY c.ciudad ASC, c.nombre ASC";
} elseif ($orden === 'fecha') {
    $sql .= " ORDER BY c.fecha_creacion DESC";
} else {
    $sql .= " ORDER BY c.nombre ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Buscar contacto</label>
                <input type="text" name="buscar" class="form-control" placeholder="Nombre, correo o ciudad..." value="<?= htmlspecialchars($buscar) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Categoría</label>
                <select name="categoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoria_filtro == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1">Ordenar por</label>
                <select name="orden" class="form-select">
                    <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>>Nombre (A-Z)</option>
                    <option value="ciudad" <?= $orden === 'ciudad' ? 'selected' : '' ?>>Ciudad</option>
                    <option value="fecha" <?= $orden === 'fecha' ? 'selected' : '' ?>>Más recientes</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-center justify-content-between gap-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="favoritos" id="favoritos" value="1" <?= $solo_favoritos ? 'checked' : '' ?>>
                    <label class="form-check-label" for="favoritos">Solo favoritos</label>
                </div>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Mis Contactos (<?= count($contactos) ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($contactos)): ?>
            <p class="p-4 text-center text-muted mb-0">No se encontraron contactos con los criterios seleccionados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Favorito</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo Electrónico</th>
                            <th>Ciudad</th>
                            <th>Categoría</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contactos as $c): ?>
                            <tr>
                                <td>
                                    <?php if ($c['favorito']): ?>
                                        <span class="badge bg-warning text-dark">Sí</span>
                                    <?php else: ?>
                                        <span class="text-muted">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($c['nombre']) ?></td>
                                <td><?= htmlspecialchars($c['telefono']) ?></td>
                                <td><?= htmlspecialchars($c['correo']) ?></td>
                                <td><?= htmlspecialchars($c['ciudad']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($c['nombre_categoria']) ?></span></td>
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