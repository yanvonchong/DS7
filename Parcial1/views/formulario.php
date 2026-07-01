<?php
$pageTitle = 'Inscripción iTECH';
require __DIR__ . '/partials/header.php';
?>

<div class="container">
    <div class="form-card">
        <div class="form-header">
            <h1>Formulario de Inscripción</h1>
            <p>Completa tus datos para registrarte en los talleres <strong>iTECH</strong>.</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                ✅ <strong>¡Registro exitoso!</strong> Tus datos han sido guardados correctamente.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>⚠️ Por favor corrige los siguientes errores:</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/Parcial1/" novalidate>

            <!-- ── Datos Personales ── -->
            <fieldset>
                <legend>📋 Datos Personales</legend>

                <div class="form-row two-cols">
                    <div class="form-group">
                        <label for="identidad">Identidad <span class="req">*</span></label>
                        <input type="text" id="identidad" name="identidad" maxlength="20"
                               placeholder="Ej: 8-888-8888"
                               value="<?= htmlspecialchars($oldData['identidad'] ?? '') ?>" required>
                        <small>Cédula o pasaporte</small>
                    </div>
                    <div class="form-group">
                        <label for="edad">Edad <span class="req">*</span></label>
                        <input type="number" id="edad" name="edad" min="1" max="120"
                               value="<?= htmlspecialchars($oldData['edad'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row two-cols">
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="req">*</span></label>
                        <input type="text" id="nombre" name="nombre" maxlength="100"
                               placeholder="Ej: Juan"
                               value="<?= htmlspecialchars($oldData['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido <span class="req">*</span></label>
                        <input type="text" id="apellido" name="apellido" maxlength="100"
                               placeholder="Ej: Pérez"
                               value="<?= htmlspecialchars($oldData['apellido'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row two-cols">
                    <div class="form-group">
                        <label for="sexo">Sexo <span class="req">*</span></label>
                        <select id="sexo" name="sexo" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="M"    <?= (($oldData['sexo'] ?? '') === 'M')    ? 'selected' : '' ?>>Masculino</option>
                            <option value="F"    <?= (($oldData['sexo'] ?? '') === 'F')    ? 'selected' : '' ?>>Femenino</option>
                            <option value="Otro" <?= (($oldData['sexo'] ?? '') === 'Otro') ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nacionalidad">Nacionalidad <span class="req">*</span></label>
                        <input type="text" id="nacionalidad" name="nacionalidad" maxlength="100"
                               placeholder="Ej: Panameño"
                               value="<?= htmlspecialchars($oldData['nacionalidad'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_pais">País de Residencia <span class="req">*</span></label>
                    <select id="id_pais" name="id_pais" required>
                        <option value="">-- Seleccionar país --</option>
                        <?php foreach ($paises as $pais): ?>
                            <option value="<?= $pais['id_pais'] ?>"
                                <?= (($oldData['id_pais'] ?? '') == $pais['id_pais']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pais['nombre_pais']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </fieldset>

            <!-- ── Información de Contacto ── -->
            <fieldset>
                <legend>📬 Información de Contacto</legend>

                <div class="form-row two-cols">
                    <div class="form-group">
                        <label for="correo">Correo Electrónico <span class="req">*</span></label>
                        <input type="email" id="correo" name="correo" maxlength="150"
                               placeholder="correo@ejemplo.com"
                               value="<?= htmlspecialchars($oldData['correo'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="celular">Celular <span class="req">*</span></label>
                        <input type="tel" id="celular" name="celular" maxlength="20"
                               placeholder="+507 6000-0000"
                               value="<?= htmlspecialchars($oldData['celular'] ?? '') ?>" required>
                    </div>
                </div>
            </fieldset>

            <!-- ── Áreas de Interés ── -->
            <fieldset>
                <legend>💻 Temas Tecnológicos de Interés <span class="req">*</span></legend>
                <p class="fieldset-hint">Selecciona todos los que te interesan:</p>
                <div class="checkbox-grid">
                    <?php foreach ($areas as $area): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="areas[]" value="<?= $area['id_area'] ?>"
                                <?php
                                $oldAreas = $oldData['areas'] ?? [];
                                if (in_array($area['id_area'], $oldAreas)) echo 'checked';
                                ?>>
                            <span><?= htmlspecialchars($area['nombre_area']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <!-- ── Observaciones ── -->
            <fieldset>
                <legend>💬 Observaciones o Consulta</legend>
                <div class="form-group">
                    <label for="observaciones">¿Tienes alguna consulta sobre el evento?</label>
                    <textarea id="observaciones" name="observaciones" rows="4"
                              placeholder="Escribe aquí tu pregunta o comentario..."><?=
                        htmlspecialchars($oldData['observaciones'] ?? '')
                    ?></textarea>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="reset" class="btn btn-secondary">Limpiar</button>
                <button type="submit" class="btn btn-primary">Inscribirse →</button>
            </div>

        </form>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
