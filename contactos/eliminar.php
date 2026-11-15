<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_contacto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$id_usuario = $_SESSION['usuario_id'];

if ($id_contacto) {
    $stmt = $pdo->prepare("DELETE FROM contactos WHERE id = :id AND id_usuario = :id_usuario");
    $stmt->execute(['id' => $id_contacto, 'id_usuario' => $id_usuario]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../dashboard.php?msg=eliminado");
        exit;
    }
}

header("Location: ../dashboard.php?msg=error_acceso");
exit;