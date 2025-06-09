<?php
class MesaModel
{
    private $db;

    public function __construct()
    {
         try {
            // Usar las constantes de configuración
            $this->db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Agrega una nueva mesa con nombre automático y estado 'libre'
    public function agregarMesa()
    {
        // Obtener todos los nombres de mesas existentes
        $stmt = $this->db->query("SELECT nombre FROM mesas");
        $nombres = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $numerosUsados = [];

        foreach ($nombres as $nombre) {
            preg_match_all('/\d+/', $nombre, $matches);
            foreach ($matches[0] as $num) {
                $numerosUsados[] = (int) $num;
            }
        }

        // Eliminar duplicados y ordenar
        $numerosUnicos = array_unique($numerosUsados);
        sort($numerosUnicos);

        // Buscar el primer número faltante en la secuencia
        $nuevoNumero = 1;
        foreach ($numerosUnicos as $numero) {
            if ($numero == $nuevoNumero) {
                $nuevoNumero++;
            } else {
                break; // Encontró un hueco
            }
        }

        $nuevoNombre = 'M' . $nuevoNumero;

        $stmt = $this->db->prepare("INSERT INTO mesas (nombre, estado) VALUES (?, 'libre')");
        $stmt->execute([$nuevoNombre]);
    }





    public function juntarMesas($id1, $id2)
    {
        // Obtener los nombres reales de las mesas
        $stmt = $this->db->prepare("SELECT nombre FROM mesas WHERE id = ?");
        $stmt->execute([$id1]);
        $nombre1 = $stmt->fetchColumn();

        $stmt->execute([$id2]);
        $nombre2 = $stmt->fetchColumn();

        // Unir nombres visualmente
        $nuevoNombre = $nombre1 . ' | ' . $nombre2;

        // Actualizar la primera mesa con nuevo nombre y estado
        $sql1 = "UPDATE mesas SET nombre = ?, estado = 'reservado' WHERE id = ?";
        $this->db->prepare($sql1)->execute([$nuevoNombre, $id1]);

        // Eliminar la segunda mesa
        $sql2 = "DELETE FROM mesas WHERE id = ?";
        $this->db->prepare($sql2)->execute([$id2]);
    }


    // (Opcional) Eliminar una mesa
    public function eliminarMesa($id)
    {
        $sql = "DELETE FROM mesas WHERE id = ?";
        $this->db->prepare($sql)->execute([$id]);
    }

    // (Opcional) Obtener todas las mesas
    public function obtenerMesas()
    {
        $stmt = $this->db->query("SELECT * FROM mesas ORDER BY CAST(SUBSTRING(nombre, 2) AS UNSIGNED)");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function separarMesa($nombreCombinado)
    {
        $pdo = $this->db;

        // Eliminar mesa combinada
        $stmt = $pdo->prepare("DELETE FROM mesas WHERE nombre = ?");
        $stmt->execute([$nombreCombinado]);

        // Extraer cada número: "M5 | M6" => ["5", "6"]
        preg_match_all('/\d+/', $nombreCombinado, $matches);
        $numeros = $matches[0];

        foreach ($numeros as $n) {
            $nombre = 'M' . $n;
            $stmt = $pdo->prepare("INSERT INTO mesas (nombre, estado) VALUES (?, 'libre')");
            $stmt->execute([$nombre]);
        }
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        $stmt = $this->db->prepare("UPDATE mesas SET estado = ? WHERE id = ?");
        $stmt->execute([$nuevoEstado, $id]);
    }
    // Agregar este método que falta:
    public function obtenerMesaPorId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM mesas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
