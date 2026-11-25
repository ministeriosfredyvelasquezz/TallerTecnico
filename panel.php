<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_orden'])) {
    $nombre_cliente = trim($_POST['nombre_cliente']);
    $telefono       = trim($_POST['telefono']);
    $equipo         = trim($_POST['equipo']);
    $falla          = trim($_POST['falla']);
    $costo          = (float) $_POST['costo'];
    $codigo_orden   = 'ORD-' . rand(1000, 9999);

    $stmtCliente = $pdo->prepare("INSERT INTO clientes (nombre, telefono) VALUES (:nombre, :telefono)");
    $stmtCliente->execute([':nombre' => $nombre_cliente, ':telefono' => $telefono]);
    $cliente_id = $pdo->lastInsertId();

    $stmtOrden = $pdo->prepare("INSERT INTO ordenes (codigo_orden, cliente_id, tecnico_id, equipo, falla_reportada, costo_estimado) 
                                VALUES (:codigo, :cliente_id, :tecnico_id, :equipo, :falla, :costo)");
    $stmtOrden->execute([
        ':codigo'     => $codigo_orden,
        ':cliente_id' => $cliente_id,
        ':tecnico_id' => $_SESSION['usuario_id'],
        ':equipo'     => $equipo,
        ':falla'      => $falla,
        ':costo'      => $costo
    ]);

    $mensaje = "✅ Orden <strong>$codigo_orden</strong> registrada con éxito.";
}

$sqlLista = "SELECT o.*, c.nombre AS cliente_nombre, c.telefono AS cliente_tel 
             FROM ordenes o 
             INNER JOIN clientes c ON o.cliente_id = c.id 
             ORDER BY o.fecha_ingreso DESC";
$ordenes = $pdo->query($sqlLista)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Taller Técnico</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f1f2f6; margin: 0; padding: 20px; color: #2f3542; }
        .topbar { max-width: 1150px; margin: 0 auto 20px auto; background: #2f3542; color: white; padding: 15px 25px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
        .topbar a { color: #ff6b81; text-decoration: none; font-weight: bold; margin-left: 15px; }
        .grid { max-width: 1150px; margin: auto; display: grid; grid-template-columns: 330px 1fr; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        h3 { margin-top: 0; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px; }
        label { display: block; margin-top: 10px; font-size: 13px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ced6e0; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 11px; background: #2ed573; color: #1e272e; border: none; border-radius: 5px; font-weight: bold; font-size: 15px; margin-top: 15px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 10px; border-bottom: 1px solid #dfe4ea; text-align: left; }
        th { background: #f8f9fa; }
        .alerta { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .codigo { background: #dfe4ea; padding: 3px 7px; border-radius: 4px; font-weight: bold; font-family: monospace; }
        .btn-wa { background: #25D366; color: white; padding: 6px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; display: inline-block; }
        #aviso-api { position: fixed; bottom: 20px; right: 20px; background: #1e272e; color: #2ed573; padding: 12px 18px; border-radius: 8px; display: none; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

<div class="topbar">
    <div>🔧 <strong>Taller Técnico</strong> | Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></div>
    <div>
        <a href="index.php" target="_blank" style="color:#70a1ff;">Ver Portal Clientes ↗</a>
        <a href="logout.php">Cerrar Sesión ✖</a>
    </div>
</div>

<div class="grid">
    <div class="card">
        <h3>+ Nueva Orden</h3>
        <?php if ($mensaje): ?><div class="alerta"><?= $mensaje ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="crear_orden" value="1">
            <label>Nombre del Cliente *</label>
            <input type="text" name="nombre_cliente" required placeholder="Ej. Juan Pérez">
            <label>WhatsApp / Teléfono *</label>
            <input type="text" name="telefono" required placeholder="Ej. 3001234567">
            <label>Equipo / Modelo *</label>
            <input type="text" name="equipo" required placeholder="Ej. Moto / Celular">
            <label>Falla reportada *</label>
            <textarea name="falla" rows="2" required placeholder="Ej. Cambio de kit / pantalla"></textarea>
            <label>Costo estimado (COP)</label>
            <input type="number" name="costo" value="120000">
            <button type="submit">Registrar Equipo</button>
        </form>
    </div>

    <div class="card">
        <h3>📋 Órdenes en Taller (<?= count($ordenes) ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Equipo / Falla</th>
                    <th>Costo</th>
                    <th>Estado (En vivo)</th>
                    <th>Avisar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordenes as $o): 
                    $tel = preg_replace('/[^0-9]/', '', $o['cliente_tel']);
                    $msgWa = urlencode("Hola {$o['cliente_nombre']}, te saludamos del Taller. Tu equipo ({$o['equipo']}) con código {$o['codigo_orden']} está en estado: {$o['estado']}.");
                ?>
                <tr>
                    <td><span class="codigo"><?= $o['codigo_orden'] ?></span></td>
                    <td><strong><?= htmlspecialchars($o['cliente_nombre']) ?></strong><br><small><?= htmlspecialchars($o['cliente_tel']) ?></small></td>
                    <td><strong><?= htmlspecialchars($o['equipo']) ?></strong><br><small><?= htmlspecialchars($o['falla_reportada']) ?></small></td>
                    <td>$<?= number_format($o['costo_estimado'], 0, ',', '.') ?></td>
                    <td>
                        <select onchange="cambiarEstado(<?= $o['id'] ?>, this.value)">
                            <option value="Recibido" <?= $o['estado']=='Recibido'?'selected':'' ?>>Recibido</option>
                            <option value="En reparacion" <?= $o['estado']=='En reparacion'?'selected':'' ?>>En reparación</option>
                            <option value="Listo" <?= $o['estado']=='Listo'?'selected':'' ?>>Listo</option>
                            <option value="Entregado" <?= $o['estado']=='Entregado'?'selected':'' ?>>Entregado</option>
                        </select>
                    </td>
                    <td><a class="btn-wa" href="https://wa.me<?= $tel ?>?text=<?= $msgWa ?>" target="_blank">WhatsApp</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="aviso-api">✔ Guardado en vivo</div>

<script>
// JavaScript consume nuestra API (api_estado.php) enviando JSON sin recargar la página
function cambiarEstado(ordenId, nuevoEstado) {
    fetch('api_estado.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: ordenId, estado: nuevoEstado })
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            const aviso = document.getElementById('aviso-api');
            aviso.style.display = 'block';
            setTimeout(() => { aviso.style.display = 'none'; }, 2000);
        }
    });
}
</script>

</body>
</html>
