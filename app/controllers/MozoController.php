<?php
class MozoController
{
    private $usuario;
    private $mesaModel;
    private $productoModel;
    private $comandaModel;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        require_once __DIR__ . '/../models/MesaModel.php';
        require_once __DIR__ . '/../models/ProductoModel.php';
        require_once __DIR__ . '/../models/ComandaModel.php';

        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'mozo') {
            require_once __DIR__ . '/../controllers/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }

        $this->mesaModel = new MesaModel();
        $this->productoModel = new ProductoModel();
        $this->comandaModel = new ComandaModel();
    }

    public function index()
    {
        $usuario = $this->usuario;
        $mesas = $this->mesaModel->obtenerMesas();
        require_once __DIR__ . '/../views/mozo/inicio.php';
    }

    public function comanda($mesaId = null)
    {
        // Si no hay mesa ID, intentar obtenerlo de GET
        if (!$mesaId && isset($_GET['mesa'])) {
            $mesaId = $_GET['mesa'];
        }

        if (!$mesaId) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        $usuario = $this->usuario;

        // Obtener información de la mesa
        $mesa = $this->mesaModel->obtenerMesaPorId($mesaId);
        if (!$mesa) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        // Verificar si hay comanda activa para esta mesa
        $comanda = $this->comandaModel->obtenerComandaActivaPorMesa($mesaId);
        if (!$comanda) {
            // Crear nueva comanda
            $comandaId = $this->comandaModel->crearComanda($mesaId, $usuario['id_user']);
            $comanda = ['id_comanda' => $comandaId, 'total' => 0];
            $detalles = [];
        } else {
            // Obtener detalles de comanda existente
            $detalles = $this->comandaModel->obtenerDetallesComandaCompletos($comanda['id']);
        }

        // Obtener productos disponibles por tipo
        $platos = $this->productoModel->obtenerProductosPorTipo(2, true);
        $bebidas = $this->productoModel->obtenerProductosPorTipo(1, true);
        $combos = $this->productoModel->obtenerProductosPorTipo(4, true);

        // Calcular total
        $total = 0;
        foreach ($detalles as $detalle) {
            $total += $detalle['precio'] * $detalle['cantidad'];
        }

        require_once __DIR__ . '/../views/mozo/comanda.php';
    }

    public function agregarItem()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $comandaId = $data['id_comanda'] ?? null;
        $productoId = $data['id_plato'] ?? null;
        $cantidad = $data['cantidad'] ?? 1;
        $comentario = $data['comentario'] ?? '';

        if (!$comandaId || !$productoId) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit();
        }

        try {
            // Verificar stock
            if (!$this->productoModel->verificarStock($productoId, $cantidad)) {
                echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
                exit();
            }

            // Agregar item a la comanda
            $itemId = $this->comandaModel->agregarItemComanda($comandaId, $productoId, $cantidad, $comentario);
            
            if ($itemId) {
                // Actualizar stock
                $this->productoModel->actualizarStock($productoId, -$cantidad);
                echo json_encode(['success' => true, 'message' => 'Producto agregado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al agregar producto']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function obtenerComanda($comandaId)
    {
        header('Content-Type: application/json');
        
        $detalles = $this->comandaModel->obtenerDetallesComandaCompletos($comandaId);
        echo json_encode(['detalles' => $detalles]);
    }

    public function actualizarComentario()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $detalleId = $data['id_detalle'] ?? null;
        $comentario = $data['comentario'] ?? '';

        if ($detalleId && $this->comandaModel->actualizarComentarioDetalle($detalleId, $comentario)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function eliminarItem()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $detalleId = $data['id_detalle'] ?? null;

        if (!$detalleId) {
            echo json_encode(['success' => false]);
            exit();
        }

        // Obtener info del detalle antes de eliminar
        $detalle = $this->comandaModel->obtenerDetalleComanda($detalleId);
        
        if ($this->comandaModel->eliminarDetalleComanda($detalleId)) {
            // Restaurar stock
            $this->productoModel->actualizarStock($detalle['producto_id'], $detalle['cantidad']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function enviarComanda()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $comandaId = $data['id_comanda'] ?? null;

        if (!$comandaId) {
            echo json_encode(['success' => false, 'message' => 'ID de comanda no proporcionado']);
            exit();
        }

        // Cambiar estado de comanda a "pendiente" (para cocina)
        if ($this->comandaModel->actualizarEstadoComanda($comandaId, 'pendiente')) {
            echo json_encode(['success' => true, 'message' => 'Comanda enviada a cocina']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al enviar comanda']);
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit();
    }
}