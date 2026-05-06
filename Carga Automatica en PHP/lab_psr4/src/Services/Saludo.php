<?php

// Namespace del servicio
namespace KLid\LabPsr4\Services;

// Clase Saludo
class Saludo
{
    // Método que genera saludo
    public function mensaje($nombre)
    {
        return "Hola " . $nombre;
    }
}