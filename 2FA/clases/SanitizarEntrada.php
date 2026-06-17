<?PHP
class SanitizarEntrada {

    
    // Sanitiza una cadena eliminando espacios y etiquetas HTML
    public static function limpiarCadena($cadena) {
        return trim(strip_tags($cadena));
    }

    // Elimina espacios al inicio y fin
    public static function limpiarEspacios($cadena) {
        return trim(strip_tags($cadena));
    }

    // Convierte a formato título (primera letra de cada palabra en mayúscula)
    public static function CadTitulo($cadena) {
        return ucwords(strtolower(trim(strip_tags($cadena))));
    }

    // Sanitiza para uso en HTML (previene XSS)
    public static function limpiarHtml($cadena) {
        return htmlspecialchars(trim($cadena), ENT_QUOTES, 'UTF-8');
    }

}//SanitizarEntrada

//$nombre = "<b>Juan</b> ";
//$nombreLimpio = SanitizarEntrada::limpiarCadena($nombre);  
//echo "la salida es: ".$nombre."<br>";
?>