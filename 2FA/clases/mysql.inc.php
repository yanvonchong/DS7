<?php

class mod_db
{
    private $conexion;
    private $perpage = 5;
    private $total;
    private $debug = false;

    public function __construct()
    {
        ##### Setting SQL Vars #####
        $sql_host  = "localhost";
        $sql_name  = "company_info";
        $sql_user  = "yan_user";
        $sql_pass  = "Yan2026!";
        $charset   = 'utf8mb4';

        $dsn = "mysql:host=$sql_host;dbname=$sql_name;charset=$charset";
        try {
            $this->conexion = new PDO($dsn, $sql_user, $sql_pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->exec("SET NAMES utf8mb4");
            if ($this->debug) {
                echo "Conexión exitosa a la base de datos<br>";
            }
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit;
        }
    }

    public function getConexion()
    {
        return $this->conexion;
    }

    public function disconnect()
    {
        $this->conexion = null;
    }

    // INSERT básico
    public function insert($tb_name, $cols, $val)
    {
        $cols = $cols ? "($cols)" : "";
        $sql  = "INSERT INTO $tb_name $cols VALUES ($val)";
        try {
            $this->conexion->exec($sql);
        } catch (PDOException $e) {
            echo "Error al insertar: " . $e->getMessage();
        }
    }

    // INSERT seguro con prepared statements
    public function insertSeguro($tb_name, $data)
    {
        $columns      = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql          = "INSERT INTO $tb_name ($columns) VALUES ($placeholders)";
        try {
            $stmt = $this->conexion->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error en INSERT: " . $e->getMessage();
            return false;
        }
    }

    // UPDATE básico
    public function update($tb_name, $string, $astriction)
    {
        $sql = "UPDATE $tb_name SET $string WHERE $astriction";
        try {
            $this->conexion->exec($sql);
        } catch (PDOException $e) {
            echo "Error al Modificar: " . $e->getMessage();
        }
    }

    // UPDATE seguro con prepared statements
    public function updateSeguro($tabla, $data, $condiciones)
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $setSQL = implode(", ", $set);

        $where = [];
        foreach ($condiciones as $key => $value) {
            $where[] = "$key = :cond_$key";
        }
        $whereSQL = implode(" AND ", $where);
        $sql      = "UPDATE $tabla SET $setSQL WHERE $whereSQL";

        try {
            $stmt = $this->conexion->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            foreach ($condiciones as $key => $value) {
                $stmt->bindValue(":cond_$key", $value);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error en UPDATE: " . $e->getMessage();
            return false;
        }
    }

    // DELETE
    public function del($tb_name, $astriction)
    {
        $sql = "DELETE FROM $tb_name";
        if ($astriction) {
            $sql .= " WHERE $astriction";
        }
        try {
            $this->conexion->exec($sql);
        } catch (PDOException $e) {
            echo "Error al eliminar: " . $e->getMessage();
        }
    }

    // Buscar usuario para login
    public function log($Usuario)
    {
        try {
            $sql  = "SELECT * FROM usuarios WHERE Usuario = :User";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':User', $Usuario, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchObject();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    // Consulta genérica
    public function executeQuery($string)
    {
        try {
            $stmt = $this->conexion->prepare($string);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    public function query($string)
    {
        return $this->executeQuery($string);
    }

    // Contar filas
    public function nums($string = "", $stmt = null)
    {
        if ($string) {
            $stmt = $this->executeQuery($string);
        }
        return ($stmt !== null) ? $stmt->rowCount() : 0;
    }

    // Fetch objeto
    public function objects($stmt = "")
    {
        return $stmt ? $stmt->fetch(PDO::FETCH_OBJ) : null;
    }

    // Fetch array asociativo
    public function Arreglos($string = "")
    {
        try {
            if ($string) {
                $stmt = $this->conexion->query($string);
                return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            }
            return [];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Último ID insertado
    public function insert_id()
    {
        return $this->conexion->lastInsertId();
    }
}
