<?php
class ComandaController
{
    private $usuario;
    private $comandaModel;
    private $productoModel;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        require_once __DIR__ . '/../models/ComandaModel.php';
        require_once __DIR__ . '/../models/ProductoModel.php';

        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'mozo') {
            require_once __DIR__ . '/../controllers/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }

        $this->comandaModel = new ComandaModel();
        $this->productoModel = new ProductoModel();
    }

    public function index()
    {
        $mesaId = $_GET['mesa'] ?? null;
        
        if (!$mesaId) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        // Obtener información de la mesa
        $mesa = $this->obtenerMesa($mesaId);
        
        // Obtener o crear comanda para esta mesa
        $comanda = $this->comandaModel->obtenerComandaActivaPorMesa($mesaId);
        if (!$comanda) {
            $comandaId = $this->comandaModel->crearComanda($mesaId, $this->usuario['id_user']);
            $comanda = ['id_comanda' => $comandaId, 'total' => 0];
        }

        // Obtener detalles de la comanda
        $detalles = $this->comandaModel->obtenerDetallesComanda($comanda['id_comanda']);
        
        // Obtener productos disponibles
        $platos = $this->productoModel->obtenerProductosPorTipo(2, true); // Solo activos
        $bebidas = $this->productoModel->obtenerProductosPorTipo(1, true);
        $combos = $this->productoModel->obtenerProductosPorTipo(4, true);
        
        // Calcular total
        $total = array_sum(array_column($detalles, 'subtotal'));

        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/mozo/comanda.php';
    }

    public function agregarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comandaId = $_POST['comanda_id'];
            $productoId = $_POST['producto_id'];
            $cantidad = $_POST['cantidad'] ?? 1;
            $comentario = $_POST['comentario'] ?? '';

            // Verificar stock
            if ($this->productoModel->verificarStock($productoId, $cantidad)) {
                $this->comandaModel->agregarDetalle($comandaId, $productoId, $cantidad, $comentario);
                $this->productoModel->actualizarStock($productoId, -$cantidad);
                
                echo json_encode(['success' => true, 'message' => 'Producto agregado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
            }
            exit();
        }
    }

    public function actualizarComentario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $detalleId = $_POST['detalle_id'];
            $comentario = $_POST['comentario'];
            
            $this->comandaModel->actualizarComentarioDetalle($detalleId, $comentario);
            echo json_encode(['success' => true]);
            exit();
        }
    }

    public function eliminarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $detalleId = $_POST['detalle_id'];
            
            // Obtener info del detalle antes de eliminar
            $detalle = $this->comandaModel->obtenerDetalle($detalleId);
            
            if ($this->comandaModel->eliminarDetalle($detalleId)) {
                // Restaurar stock
                $this->productoModel->actualizarStock($detalle['producto_id'], $detalle['cantidad']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit();
        }
    }

    public function enviarACocina()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comandaId = $_POST['comanda_id'];
            
            // Cambiar estado de comanda a "enviada"
            $this->comandaModel->actualizarEstadoComanda($comandaId, 'pendiente');
            
            echo json_encode(['success' => true, 'message' => 'Comanda enviada a cocina']);
            exit();
        }
    }

    private function obtenerMesa($mesaId)
    {
        // Aquí deberías obtener la info de la mesa desde el modelo
        return $mesaId; // Por ahora retornamos solo el ID
    }
}