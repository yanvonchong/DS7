<form class="cmxform" id="form1" name="form1" method="post" action="ProcesarRegistro.php">

    <input type="hidden" name="Accion" value="Guardar" />

    <fieldset>
        <legend>Nombre:</legend>
        <input name="nombre" id="nombre" type="text" onfocus="runInputs(this)" placeholder="Ej: Juan" />
    </fieldset>
    <br>

    <fieldset>
        <legend>Apellidos:</legend>
        <input name="apellido" id="apellido" type="text" onfocus="runInputs(this)" placeholder="Ej: Pérez" />
    </fieldset>
    <br>

    <fieldset>
        <legend>Usuario (nombre de usuario):</legend>
        <input name="usuario" id="usuario" type="text" onfocus="runInputs(this)" placeholder="Ej: jperez" />
    </fieldset>
    <br>

    <fieldset>
        <legend>Email personal:</legend>
        <input name="email1" id="email1" type="email" onfocus="runInputs(this)" placeholder="Ej: juan@correo.com" />
        <span id="mensaje-estado" style="margin-left:8px; font-size:0.9em;"></span>
    </fieldset>
    <br>

    <fieldset>
        <legend>Contraseña:</legend>
        <input name="clave" id="clave" type="password" onfocus="runInputs(this)" />
    </fieldset>
    <br>

    <fieldset>
        <legend>Repetir Contraseña:</legend>
        <input name="clave_again" id="clave_again" type="password" onfocus="runInputs(this)" />
    </fieldset>
    <br>

    <fieldset>
        <legend>Sexo:</legend>
        <select name="sexo" id="sexo">
            <option value="">-- Seleccione --</option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
        </select>
    </fieldset>
    <br>

    <div align="center">
        <input type="submit" name="Submit" value="Registrarse y Activar 2FA" class="clear" />
    </div>

</form>
