<?php

// Cargar autoload de Composer
require 'vendor/autoload.php';

// Importar clases
use KLid\LabPsr4\Models\Usuario;
use KLid\LabPsr4\Services\Saludo;

// Crear objetos
$usuario = new Usuario();
$saludo = new Saludo();

// Mostrar resultado
echo $saludo->mensaje($usuario->nombre());