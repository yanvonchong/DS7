<?php
$pageTitle = 'Reporte de Inscriptores – iTECH';
require __DIR__ . '/partials/header.php';
?>

<div class="container">
    <div class="reporte-header">
        <h1>📊 Reporte de Inscriptores</h1>
        <div class="reporte-actions">
            <a href="/Parcial1/" class="btn btn-secondary">← Nuevo Registro</a>
            <a href="/Parcial1/export.php" class="btn btn-success">📥 Exportar a Excel</a>
        </div>
    </div>

    <!-- Leyenda de integridad -->
    <div class="integrity-legend">
        <span class="badge badge-ok">✔ Íntegro</span> Firma digital válida (datos no alterados)
        &nbsp;&nbsp;
        <span class="badge badge-err">✘ Corrompido</span> La firma no coincide con los datos almacenados
    </div>

    <?php if (empty($inscriptores)): ?>
        <div class="alert alert-info">No hay inscriptores registrados todavía.</div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="reporte-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Integridad</th>
                        <th>Identidad</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Edad</th>
                        <th>Sexo</th>
                        <th>País</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Áreas de Interés</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscriptores as $i => $ins): ?>
                        <tr class="<?= $ins['integro'] ? 'row-ok' : 'row-err' ?>">
                            <td><?= $i + 1 ?></td>
                            <td class="td-badge">
                                <?php if ($ins['integro']): ?>
                                    <span class="badge badge-ok">✔ Íntegro</span>
                                <?php else: ?>
                                    <span class="badge badge-err">✘ Corrompido</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($ins['identidad']) ?></td>
                            <td><?= htmlspecialchars($ins['nombre']) ?></td>
                            <td><?= htmlspecialchars($ins['apellido']) ?></td>
                            <td><?= (int)$ins['edad'] ?></td>
                            <td><?= htmlspecialchars($ins['sexo']) ?></td>
                            <td><?= htmlspecialchars($ins['nombre_pais']) ?></td>
                            <td><?= htmlspecialchars($ins['correo']) ?></td>
                            <td><?= htmlspecialchars($ins['celular']) ?></td>
                            <td class="td-areas">
                                <?= htmlspecialchars(implode(', ', $ins['areas'])) ?>
                            </td>
                            <td><?= htmlspecialchars($ins['fecha_registro']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="total-count">Total de inscriptores: <strong><?= count($inscriptores) ?></strong></p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
