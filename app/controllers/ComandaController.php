<?php
class ComandaModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die('Conexión fallida: ' . $this->conn->connect_error);
        }
    }

    // Agregar estos métodos que faltan:

    public function obtenerDetallesComandaCompletos($comandaId)
    {
        $sql = "SELECT dc.id as id_detalle, dc.cantidad, dc.comentario, 
                p.id as id_plato, p.nombre, p.precio, p.descripcion
                FROM detalle_comanda dc
                JOIN producto p ON dc.producto_id = p.id
                WHERE dc.comanda_id = ? AND dc.cancelado = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $detalles = [];
        while ($row = $result->fetch_assoc()) {
            $detalles[] = $row;
        }
        
        return $detalles;
    }

    public function obtenerDetalleComanda($detalleId)
    {
        $sql = "SELECT * FROM detalle_comanda WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $detalleId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function actualizarComentarioDetalle($detalleId, $comentario)
    {
        $sql = "UPDATE detalle_comanda SET comentario = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $comentario, $detalleId);
        return $stmt->execute();
    }

    public function eliminarDetalleComanda($detalleId)
    {
        $sql = "DELETE FROM detalle_comanda WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $detalleId);
        return $stmt->execute();
    }

    // Agregar el parámetro mesa_id que falta
    public function crearComanda($mesaId, $usuarioId)
    {
        $sql = "INSERT INTO comanda (mesa_id, usuario_id, estado, tipo_entrega_id, fecha) 
                VALUES (?, ?, 'nueva', 3, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $mesaId, $usuarioId);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    // Resto de métodos existentes...
}