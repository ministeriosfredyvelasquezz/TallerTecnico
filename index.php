<?php
require 'conexion.php';
$orden = null;
$buscado = false;

if (isset($_GET['codigo']) && !empty(trim($_GET['codigo']))) {
    $buscado = true;
    $codigo = strtoupper(trim($_GET['codigo']));

    $stmt = $pdo->prepare("SELECT o.*, c.nombre AS cliente_nombre 
                           FROM ordenes o 
                           INNER JOIN clientes c ON o.cliente_id = c.id 
                           WHERE o.codigo_orden = :codigo LIMIT 1");
    $stmt->execute([':codigo' => $codigo]);
    $orden = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta tu Reparación - Taller Técnico</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #0984e3, #2c3e50); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        .box { background: white; padding: 35px; border-radius: 12px; width: 100%; max-width: 440px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); text-align: center; }
        h1 { margin-top: 0; color: #2c3e50; font-size: 24px; }
        input { width: 100%; padding: 12px; border: 2px solid #dfe6e9; border-radius: 8px; font-size: 16px; text-align: center; text-transform: uppercase; box-sizing: border-box; margin-top: 10px; }
        button { width: 100%; padding: 12px; background: #0984e3; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; margin-top: 12px; cursor: pointer; }
        .resultado { margin-top: 25px; background: #f8f9fa; padding: 18px; border-radius: 8px; text-align: left; border-left: 5px solid #00b894; }
        .estado-pill { display: inline-block; background: #00b894; color: white; padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 14px; }
        .error { margin-top: 20px; background: #ffeaa7; color: #d63031; padding: 12px; border-radius: 8px; }
        .link-admin { display: block; margin-top: 20px; font-size: 13px; color: #636e72; text-decoration: none; }
    </style>
</head>
<body>
<div class="box">
    <h1>🔍 Consulta tu Servicio</h1>
    <p style="color:#636e72; font-size:14px;">Ingresa el código de tu orden para ver el estado en tiempo real</p>

    <form method="GET">
        <input type="text" name="codigo" placeholder="Ej. ORD-3197" value="<?= isset($_GET['codigo']) ? htmlspecialchars($_GET['codigo']) : '' ?>" required>
        <button type="submit">Consultar Estado</button>
    </form>

    <?php if ($orden): ?>
        <div class="resultado">
            <p style="margin:0 0 8px 0;"><strong>Cliente:</strong> <?= htmlspecialchars($orden['cliente_nombre']) ?></p>
            <p style="margin:0 0 8px 0;"><strong>Equipo:</strong> <?= htmlspecialchars($orden['equipo']) ?></p>
            <p style="margin:0 0 8px 0;"><strong>Trabajo:</strong> <?= htmlspecialchars($orden['falla_reportada']) ?></p>
            <p style="margin:0 0 12px 0;"><strong>Valor estimado:</strong> $<?= number_format($orden['costo_estimado'], 0, ',', '.') ?> COP</p>
            <div><strong>Estado actual:</strong> <span class="estado-pill"><?= $orden['estado'] ?></span></div>
        </div>
    <?php elseif ($buscado): ?>
        <div class="error">No encontramos ninguna orden con ese código. Verifica e intenta de nuevo.</div>
    <?php endif; ?>

    <a href="login.php" class="link-admin">Acceso exclusivo técnicos →</a>
</div>
</body>
</html>
