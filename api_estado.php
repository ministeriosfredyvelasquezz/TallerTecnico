<?php
session_start();
require 'conexion.php';

// Avisamos al navegador que esta respuesta no es HTML, sino JSON puro
header('Content-Type: application/json');

// Verificamos que solo un técnico logueado pueda usar esta API
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

// Leemos los datos JSON que nos envía JavaScript desde el navegador
$datos = json_decode(file_get_contents('php://input'), true);

if (isset($datos['id']) && isset($datos['estado'])) {
    $stmt = $pdo->prepare("UPDATE ordenes SET estado = :estado WHERE id = :id");
    $stmt->execute([
        ':estado' => $datos['estado'],
        ':id'     => (int) $datos['id']
    ]);

    echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado en vivo']);
} else {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
}
?>
