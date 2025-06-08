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

    /* Obtiene las comandas pendientes con sus detalles */
    public function obtenerComandasPendientes()
    {
        $sql = "SELECT c.id, c.estado, te.nombre as tipo_entrega, 
                DATE_FORMAT(c.fecha, '%H:%i') as hora, 
                TIMESTAMPDIFF(MINUTE, c.fecha, NOW()) as minutos_transcurridos
                FROM comanda c
                JOIN tipo_entrega te ON c.tipo_entrega_id = te.id
                WHERE c.estado IN ('pendiente', 'recibido')
                ORDER BY c.fecha ASC";

        $result = $this->conn->query($sql);
        if (!$result) {
            error_log("Error en obtenerComandasPendientes: " . $this->conn->error);
            return [];
        }

        $comandas = [];
        while ($row = $result->fetch_assoc()) {
            $comandaId = $row['id'];
            $row['items'] = $this->obtenerDetallesComanda($comandaId);
            $comandas[] = $row;
        }

        return $comandas;
    }

    /* Obtiene los detalles de una comanda específica */
    private function obtenerDetallesComanda($comandaId)
    {
        $items = [];
        $sql = "SELECT dc.id, p.nombre, p.descripcion, dc.cantidad, dc.comentario,
                       tp.nombre as tipo_producto, t.nombre as tamano
                FROM detalle_comanda dc
                JOIN producto p ON dc.producto_id = p.id
                JOIN tipo_producto tp ON p.tipo_producto_id = tp.id
                LEFT JOIN tamano t ON p.tamano_id = t.id
                WHERE dc.comanda_id = ? AND dc.cancelado = 0";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando obtenerDetallesComanda: " . $this->conn->error);
            return [];
        }

        $stmt->bind_param("i", $comandaId);
        if (!$stmt->execute()) {
            error_log("Error ejecutando obtenerDetallesComanda: " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();
        while ($item = $result->fetch_assoc()) {
            $itemId = $item['id'];
            $item['guarniciones'] = $this->obtenerGuarnicionesItem($itemId);
            $item['opciones_combo'] = $this->obtenerOpcionesCombo($itemId);
            $items[] = $item;
        }

        return $items;
    }

    /* Obtiene las guarniciones asociadas a un item de comanda */
    private function obtenerGuarnicionesItem($detalleComandaId)
    {
        $sql = "SELECT g.nombre, g.descripcion
                FROM detalle_comanda_guarnicion dcg
                JOIN guarnicion g ON dcg.guarnicion_id = g.id
                WHERE dcg.detalle_comanda_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $detalleComandaId) || !$stmt->execute()) {
            error_log("Error en obtenerGuarnicionesItem: " . ($stmt ? $stmt->error : $this->conn->error));
            return [];
        }

        $result = $stmt->get_result();
        $guarniciones = [];
        while ($row = $result->fetch_assoc()) {
            $guarniciones[] = $row;
        }

        return $guarniciones;
    }

    /* Obtiene las opciones de combo asociadas a un item de comanda */
    private function obtenerOpcionesCombo($detalleComandaId)
    {
        $sql = "SELECT p.nombre, p.descripcion
                FROM detalle_comanda_combo_opciones dcco
                JOIN producto p ON dcco.producto_id = p.id
                WHERE dcco.detalle_comanda_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $detalleComandaId) || !$stmt->execute()) {
            error_log("Error en obtenerOpcionesCombo: " . ($stmt ? $stmt->error : $this->conn->error));
            return [];
        }

        $result = $stmt->get_result();
        $opciones = [];
        while ($row = $result->fetch_assoc()) {
            $opciones[] = $row;
        }

        return $opciones;
    }

    /* Actualiza el estado de una comanda */
    public function actualizarEstadoComanda($comandaId, $estado)
    {
        $sql = "UPDATE comanda SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("si", $estado, $comandaId) || !$stmt->execute()) {
            error_log("Error en actualizarEstadoComanda: " . ($stmt ? $stmt->error : $this->conn->error));
            return false;
        }
        return true;
    }

    /* Cancela un item específico de una comanda */
    public function cancelarItem($itemId)
    {
        $sql = "UPDATE detalle_comanda SET cancelado = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $itemId) || !$stmt->execute()) {
            error_log("Error en cancelarItem: " . ($stmt ? $stmt->error : $this->conn->error));
            return false;
        }
        return true;
    }

    /* Cancela una comanda completa */
    public function cancelarComanda($comandaId)
    {
        $this->conn->begin_transaction();
        try {
            // Primero cancelamos todos los items
            $sqlItems = "UPDATE detalle_comanda SET cancelado = 1 WHERE comanda_id = ?";
            $stmtItems = $this->conn->prepare($sqlItems);
            if (!$stmtItems || !$stmtItems->bind_param("i", $comandaId) || !$stmtItems->execute()) {
                throw new Exception("Error cancelando items: " . ($stmtItems ? $stmtItems->error : $this->conn->error));
            }

            // Luego cancelamos la comanda
            $sqlComanda = "UPDATE comanda SET estado = 'cancelado' WHERE id = ?";
            $stmtComanda = $this->conn->prepare($sqlComanda);
            if (!$stmtComanda || !$stmtComanda->bind_param("i", $comandaId) || !$stmtComanda->execute()) {
                throw new Exception("Error cancelando comanda: " . ($stmtComanda ? $stmtComanda->error : $this->conn->error));
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    /* Obtiene el tiempo transcurrido desde la creación de la comanda */
    public function obtenerTiempoComanda($comandaId)
    {
        $sql = "SELECT TIMESTAMPDIFF(MINUTE, fecha, NOW()) as minutos 
                FROM comanda WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $comandaId) || !$stmt->execute()) {
            error_log("Error en obtenerTiempoComanda: " . ($stmt ? $stmt->error : $this->conn->error));
            return 0;
        }

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data ? $data['minutos'] : 0;
    }

    public function recuperarItem($itemId)
    {
        $sql = "UPDATE detalle_comanda SET cancelado = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt || !$stmt->bind_param("i", $itemId) || !$stmt->execute()) {
            error_log("Error en recuperarItem: " . ($stmt ? $stmt->error : $this->conn->error));
            return false;
        }
        return true;
    }

    public function __destruct()
    {
        $this->conn->close();
    }

    
}
