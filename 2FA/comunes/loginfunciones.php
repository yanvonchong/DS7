<?PHP 
function redireccionar($url){
  if(!empty($url))
    echo "<meta http-equiv='refresh' content='0; URL=$url'>";
}//redireccionar
function EliminandoCaracteresdeInyeccion($cad2){
	

//$cad2 = " OR 1 '=' 1";
//$cad2 = " samuelaaron";
//echo "la cadena de inicio es: ".$cad2."<br>";
//$cad2 = trim(" samuelaaron"); //Antess de empeza el proceso se limpia la cadena;

$cad2 = trim($cad2); //Antess de empeza el proceso se limpia la cadena;

$cantCaracteres = strlen($cad2);

//echo "La cantidad de car�cteres es: ".$cant."<br>";

 $cont = 1;
 $caracter = "";
 $posicion_cadena = 0;
 $CaracterSustitudo = "G";//car�cter que cubrir�..los guiones y los ''' para cubrir inyecci�n de sql
 $cadenaResultante = "";

            

   while ($cont <= $cantCaracteres) { 
    //echo "la cadena analizar es: ".$cad2."<br>";
    //echo "el contador $cont esta en ".$cont."<br>";
    //echo "la bandera esta en ".$bandera."<br>";

   	 $caracter = substr($cad2,$posicion_cadena,1); //posici�n cero un car�cter. Un c�racter (1)


   			if ($caracter == "=" or $caracter == " " or $caracter == "'") {
          		//echo  "Entro en el If <br>";
          		//echo "EL car�cter en la posici�n 0 es : ".$caracter."<br>";
      			 $cadenaResultante = $cadenaResultante.$CaracterSustitudo;
        		// ereg_replace ("parque","circo",$cadena);parque se sustituye por circo

    		}else{
       			 $cadenaResultante = $cadenaResultante.$caracter;
			}
        $cont = $cont + 1;
        $posicion_cadena = $posicion_cadena + 1;
		
 	 }//fin del mientras
                                                               
// echo "la cadena resultantes es: ".$cadenaResultante."<br>";
                                                               
return($cadenaResultante);

}//fin de la funci�n
//Funci�n que obtiene mensajes de errores generales
function getMsg($num){
  $msg = "";
  switch($num){
    case 1:
	     $msg="El username o password son incorrectos";
		 break;
  }
  return $msg;
}


// ── Funciones Anti-CSRF ───────────────────────────────────────────────────────

/**
 * Genera un token CSRF aleatorio y lo guarda en sesión.
 * Se llama al mostrar cualquier formulario.
 */
function csrf_generar() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida el token CSRF enviado por el formulario.
 * Detiene la ejecución con error 403 si no coincide.
 */
function csrf_validar() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    $tokenGuardado = $_SESSION['csrf_token'] ?? '';

    if (empty($tokenEnviado) || empty($tokenGuardado) ||
        !hash_equals($tokenGuardado, $tokenEnviado)) {
        http_response_code(403);
        echo "⚠️ Error de seguridad: token CSRF inválido. Recarga la página e intenta de nuevo.";
        exit;
    }
    // Rotar token después de validar
    unset($_SESSION['csrf_token']);
}

/**
 * Imprime el campo hidden con el token CSRF listo para usar en un <form>.
 */
function csrf_campo() {
    $token = csrf_generar();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

?>