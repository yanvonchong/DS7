<?php
/**
 * ClaseRegistrese.php — RegistroUsuario
 * Maneja el registro de nuevos usuarios con soporte 2FA
 * Desarrollo de Software VII | UTP
 */
class RegistroUsuario
{
    // Accesos:
    // Private  :: Desde la misma clase que declara
    // Protected:: Desde la misma clase que declara + clases que heredan esta clase
    // Public   :: *.*

    Private $id;
    Private $Nombre;
    Private $Apellido;
    Private $Usuario;
    Private $Correo;

    Private $secret_2fa;

    Private $contrasena;
    Private $hastGenerado;
    Private $Sexo;

    Private $pdo;
    Private $tabla;
    Private $FechaSistema;

    Public function __construct($datos, $pdo, &$arrMensaje)
    {
        $this->pdo          = $pdo;
        $this->tabla        = "usuarios";
        $this->FechaSistema = date("Y-m-d H:i:s");

        if (isset($datos["nombre"])) {
            $this->Nombre = SanitizarEntrada::CadTitulo($datos["nombre"]);
        } else {
            $arrMensaje[1] = "No trajo datos la Columna Nombre";
        }

        if (isset($datos["apellido"])) {
            $this->Apellido = SanitizarEntrada::CadTitulo($datos["apellido"]);
        } else {
            $arrMensaje[2] = "No trajo datos la Columna Apellido";
        }

        if (isset($datos["usuario"])) {
            $this->Usuario = SanitizarEntrada::limpiarCadena($datos["usuario"]);
        } else {
            $arrMensaje[3] = "No trajo datos la Columna Usuario";
        }

        if (isset($datos["email1"])) {
            $this->Correo = SanitizarEntrada::limpiarEspacios($datos["email1"]);
        } else {
            $arrMensaje[4] = "No trajo datos la Columna Correo";
        }

        if (isset($datos["clave"])) {
            $this->contrasena = SanitizarEntrada::limpiarEspacios($datos["clave"]);
        } else {
            $arrMensaje[5] = "No trajo datos la Columna clave";
        }

        if (isset($datos["sexo"])) {
            $this->Sexo = SanitizarEntrada::limpiarCadena($datos["sexo"]);
        } else {
            $arrMensaje[6] = "No trajo datos la Columna Sexo";
        }
    } // introduceDatos

    // ── Encripta la contraseña con bcrypt ──────────────────────────────────────
    public function encriptarClave()
    {
        $options = [
            'cost' => 13,
        ];
        $this->hastGenerado = password_hash($this->contrasena, PASSWORD_BCRYPT, $options);
    }

    // ── Guarda el registro del usuario en la tabla ─────────────────────────────
    public function Guardar_RegistroUsuario()
    {
        $this->encriptarClave();

        $data = array(
            "Nombre"       => $this->Nombre,
            "Apellido"     => $this->Apellido,
            "Usuario"      => $this->Usuario,
            "Correo"       => $this->Correo,
            "HashMagic"    => $this->hastGenerado,
            "Sexo"         => $this->Sexo,
            "FechaSistema" => $this->FechaSistema
        );

        $this->pdo->insertSeguro("usuarios", $data);
        $this->id = $this->pdo->insert_id();
    }

    // ── Guarda el secreto TOTP en la tabla ────────────────────────────────────
    public function GuardarMySecreto($secreto)
    {
        $datoSecreto = array(
            "secret_2fa" => $secreto
        );
        $condicion = array(
            "id" => $this->id
        );

        if ($this->pdo->updateSeguro("usuarios", $datoSecreto, $condicion)) {
            return true;
        }
        return false;
    }

    // ── Getters ───────────────────────────────────────────────────────────────
    public function getUsuario()
    {
        return $this->Usuario;
    }

    public function getCorreo()
    {
        return $this->Correo;
    }

    public function getId()
    {
        return $this->id;
    }
} // fin de RegistroUsuario
