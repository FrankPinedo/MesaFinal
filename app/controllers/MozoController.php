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

        // Procesar acciones POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Agregar mesa
            if (isset($_POST['agregar_mesa'])) {
                $this->mesaModel->agregarMesa();
                header("Location: " . BASE_URL . "/mozo?mesa_agregada=1");
                exit();
            }

            // Eliminar mesa
            if (isset($_POST['eliminar_mesa_id'])) {
                $id = intval($_POST['eliminar_mesa_id']);
                $this->mesaModel->eliminarMesa($id);
                header("Location: " . BASE_URL . "/mozo");
                exit();
            }

            // Juntar mesas
            if (isset($_POST['mesa_ids'])) {
                $mesaIds = json_decode($_POST['mesa_ids'], true);
                if (count($mesaIds) >= 2) {
                    // Juntar las primeras dos mesas
                    $this->mesaModel->juntarMesas($mesaIds[0], $mesaIds[1]);

                    // Si hay más de 2 mesas, juntar las demás a la primera
                    for ($i = 2; $i < count($mesaIds); $i++) {
                        // Obtener el ID actualizado de la mesa combinada
                        $mesaCombinada = $this->mesaModel->obtenerMesaPorId($mesaIds[0]);
                        if ($mesaCombinada) {
                            $this->mesaModel->juntarMesasMultiple($mesaCombinada['id'], $mesaIds[$i]);
                        }
                    }
                }
                header("Location: " . BASE_URL . "/mozo");
                exit();
            }

            // Separar mesa
            if (isset($_POST['separar_mesa_nombre'])) {
                $nombre = $_POST['separar_mesa_nombre'];
                $this->mesaModel->separarMesa($nombre);
                header("Location: " . BASE_URL . "/mozo");
                exit();
            }

            // Cambiar estado 
            if (isset($_POST['cambiar_estado_id']) && isset($_POST['nuevo_estado'])) {
                $id = intval($_POST['cambiar_estado_id']);
                $nuevoEstado = $_POST['nuevo_estado'];

                // Obtener el estado actual de la mesa
                $mesaActual = $this->mesaModel->obtenerMesaPorId($id);

                // Validar los cambios permitidos
                $cambiosPermitidos = [
                    'libre' => ['reservado', 'ocupada'],
                    'reservado' => ['ocupada', 'libre'], // Agregar 'libre' aquí también
                    'ocupada' => ['libre']
                ];

                // Debug - agregar temporalmente
                error_log("Estado actual: " . $mesaActual['estado']);
                error_log("Nuevo estado solicitado: " . $nuevoEstado);
                error_log("Cambio permitido: " . (isset($cambiosPermitidos[$mesaActual['estado']]) && in_array($nuevoEstado, $cambiosPermitidos[$mesaActual['estado']]) ? 'SI' : 'NO'));

                if (
                    $mesaActual &&
                    isset($cambiosPermitidos[$mesaActual['estado']]) &&
                    in_array($nuevoEstado, $cambiosPermitidos[$mesaActual['estado']])
                ) {

                    $resultado = $this->mesaModel->cambiarEstado($id, $nuevoEstado);
                    error_log("Resultado del cambio: " . ($resultado ? 'EXITOSO' : 'FALLIDO'));
                }

                header("Location: " . BASE_URL . "/mozo");
                exit();
            }
        }

        $mesas = $this->mesaModel->obtenerMesas();

        // Agregar información de comandas activas a cada mesa
        foreach ($mesas as &$mesa) {
            if ($mesa['estado'] === 'esperando') {
                $comandaActiva = $this->comandaModel->obtenerComandaActivaPorMesa($mesa['id']);
                if ($comandaActiva) {
                    $mesa['tiempo_comanda'] = $this->comandaModel->obtenerTiempoComanda($comandaActiva['id']);
                }
            }
        }

        require_once __DIR__ . '/../views/mozo/inicio.php';
    }

    public function comanda($mesaId = null)
    {
        // Verificar si es un pedido delivery
        $tipoComanda = isset($_GET['tipo']) ? $_GET['tipo'] : 'comedor';
        $comandaId = isset($_GET['comanda']) ? $_GET['comanda'] : null;

        // Si no hay mesa ID y no es delivery, intentar obtenerlo de GET
        if (!$mesaId && $tipoComanda !== 'delivery' && isset($_GET['mesa'])) {
            $mesaId = $_GET['mesa'];
        }

        // Para pedidos normales, verificar que haya mesa
        if ($tipoComanda !== 'delivery' && !$mesaId) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        $usuario = $this->usuario;

        // Manejar pedidos delivery
        if ($tipoComanda === 'delivery') {
            $mesa = 'Delivery/Para Llevar';
            $mesaId = null;

            if (!$comandaId) {
                $comandaId = $this->comandaModel->crearComandaDelivery($usuario['id_user']);
            }

            $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);
            $detalles = $this->comandaModel->obtenerDetallesComandaCompletos($comandaId);
            $comandasAnteriores = [];
            $numeroComanda = 1;
        } else {
            // Obtener información de la mesa para pedidos normales
            $mesaInfo = $this->mesaModel->obtenerMesaPorId($mesaId);
            if (!$mesaInfo) {
                header("Location: " . BASE_URL . "/mozo");
                exit();
            }

            // Obtener TODAS las comandas de la mesa
            $comandasMesa = $this->comandaModel->obtenerComandasActivasPorMesa($mesaId);

            // Si hay un comandaId específico, usar esa comanda
            if ($comandaId) {
                $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);
                if (!$comanda || $comanda['mesa_id'] != $mesaId) {
                    header("Location: " . BASE_URL . "/mozo");
                    exit();
                }
            } else {
                // Buscar una comanda editable (nueva o pendiente)
                $comandaEditable = null;
                foreach ($comandasMesa as $cmd) {
                    if (in_array($cmd['estado'], ['nueva', 'pendiente'])) {
                        $comandaEditable = $cmd;
                        break;
                    }
                }

                if ($comandaEditable) {
                    $comanda = $comandaEditable;
                    $comandaId = $comanda['id'];
                } else {
                    // Si no hay comanda editable, crear una nueva
                    $comandaId = $this->comandaModel->crearNuevaComanda($mesaId, $usuario['id_user']);
                    $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);

                    // Cambiar mesa a ocupada si está libre
                    if ($mesaInfo['estado'] === 'libre') {
                        $this->mesaModel->cambiarEstado($mesaId, 'ocupada');
                    }
                }
            }

            // Obtener detalles de la comanda actual
            $detalles = $this->comandaModel->obtenerDetallesComandaCompletos($comandaId);

            // Obtener comandas anteriores (que no sean la actual)
            $comandasAnteriores = array_filter($comandasMesa, function ($cmd) use ($comandaId) {
                return $cmd['id'] != $comandaId;
            });

            // Calcular número de comanda
            $numeroComanda = count($comandasAnteriores) + 1;

            $mesa = $mesaInfo['nombre'];
        }

        // Verificar si la comanda es editable
        $puedeEditar = $this->comandaModel->puedeEditarComanda($comandaId);

        // Obtener productos disponibles por tipo
        $platos = $this->productoModel->obtenerProductosPorTipo(2, true);
        $bebidas = $this->productoModel->obtenerProductosPorTipo(1, true);
        $combos = $this->productoModel->obtenerProductosPorTipo(4, true);

        // Calcular total de la comanda actual
        $total = 0;
        if (!empty($detalles)) {
            foreach ($detalles as $detalle) {
                $total += $detalle['precio'] * $detalle['cantidad'];
            }
        }

        // Calcular total de todas las comandas de la mesa
        $totalMesa = $mesaId ? $this->comandaModel->obtenerTotalMesa($mesaId) : $total;

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
            $result = $this->comandaModel->agregarItemComanda($comandaId, $productoId, $cantidad, $comentario);

            if ($result) {
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

        // Verificar que la comanda tenga items
        $detalles = $this->comandaModel->obtenerDetallesComandaCompletos($comandaId);
        if (empty($detalles)) {
            echo json_encode(['success' => false, 'message' => 'La comanda no tiene productos']);
            exit();
        }

        // Cambiar estado de comanda de 'nueva' a 'pendiente' (para cocina)
        if ($this->comandaModel->actualizarEstadoComanda($comandaId, 'pendiente')) {
            $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);
            if ($comanda && $comanda['mesa_id']) {
                // Cambiar a 'esperando' en lugar de solo 'esperando'
                $this->mesaModel->cambiarEstado($comanda['mesa_id'], 'esperando');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Comanda enviada a cocina',
                'comandaId' => $comandaId
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al enviar comanda']);
        }
    }

    public function crearComandaDelivery()
    {
        header('Content-Type: application/json');

        try {
            $comandaId = $this->comandaModel->crearComandaDelivery($this->usuario['id_user']);
            echo json_encode(['success' => true, 'comandaId' => $comandaId]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cerrarCuenta($mesaId = null)
    {
        header('Content-Type: application/json');

        if (!$mesaId) {
            echo json_encode(['success' => false, 'message' => 'Mesa no especificada']);
            exit();
        }

        try {
            // Obtener todas las comandas de la mesa en estado 'listo' o 'entregado'
            $total = $this->comandaModel->obtenerTotalMesa($mesaId);

            echo json_encode([
                'success' => true,
                'total' => $total,
                'mesaId' => $mesaId
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function mostrarCuenta($mesaId = null)
    {
        if (!$mesaId) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        $usuario = $this->usuario;
        $mesa = $this->mesaModel->obtenerMesaPorId($mesaId);
        $comandas = $this->comandaModel->obtenerComandasMesa($mesaId);
        $total = 0;

        foreach ($comandas as &$comanda) {
            $comanda['items'] = $this->comandaModel->obtenerDetallesComandaCompletos($comanda['id']);
            $subtotal = 0;
            foreach ($comanda['items'] as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
            }
            $comanda['subtotal'] = $subtotal;
            $total += $subtotal;
        }

        require_once __DIR__ . '/../views/mozo/cuenta.php';
    }

    public function verificarComandasListas()
    {
        header('Content-Type: application/json');

        try {
            $comandasListas = $this->comandaModel->obtenerComandasListas();
            echo json_encode(['comandasListas' => $comandasListas]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function verificarEstadoComanda($comandaId)
    {
        header('Content-Type: application/json');

        try {
            $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);
            echo json_encode([
                'success' => true,
                'estado' => $comanda['estado']
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function verificarComandasMesa($mesaId)
    {
        header('Content-Type: application/json');

        try {
            // Debug: Ver todas las comandas de esta mesa
            $sqlDebug = "SELECT id, estado, fecha FROM comanda WHERE mesa_id = ?";
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $stmt = $conn->prepare($sqlDebug);
            $stmt->bind_param("i", $mesaId);
            $stmt->execute();
            $result = $stmt->get_result();
            $todasLasComandas = [];
            while ($row = $result->fetch_assoc()) {
                $todasLasComandas[] = $row;
            }

            $comandas = $this->comandaModel->obtenerComandasMesa($mesaId);

            echo json_encode([
                'tieneComandas' => count($comandas) > 0,
                'cantidadComandas' => count($comandas),
                'comandas' => $comandas,
                'debug_todas' => $todasLasComandas,
                'mesa_id' => $mesaId
            ]);
        } catch (Exception $e) {
            echo json_encode(['tieneComandas' => false, 'error' => $e->getMessage()]);
        }
    }

    public function cambiarEstadoMesa()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $mesaId = $data['mesa_id'] ?? null;
        $estado = $data['estado'] ?? null;

        if (!$mesaId || !$estado) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit();
        }

        try {
            if ($this->mesaModel->cambiarEstado($mesaId, $estado)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar estado']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function notificaciones()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/mozo/notificaciones.php';
    }

    public function procesarPagoCompleto()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $mesaId = $data['mesa_id'] ?? null;

        if (!$mesaId) {
            echo json_encode(['success' => false, 'message' => 'Mesa no especificada']);
            exit();
        }

        try {
            // Procesar el pago completo (elimina todas las comandas y libera la mesa)
            $resultado = $this->comandaModel->procesarPagoCompleto($mesaId);

            if ($resultado) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al procesar el pago']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function marcarEntregada()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $comandaId = $data['comanda_id'] ?? null;

        if (!$comandaId) {
            echo json_encode(['success' => false, 'message' => 'ID de comanda no proporcionado']);
            exit();
        }

        try {
            // Obtener información de la comanda antes de actualizar
            $comanda = $this->comandaModel->obtenerComandaPorId($comandaId);

            // Cambiar estado a 'entregado'
            if ($this->comandaModel->actualizarEstadoComanda($comandaId, 'entregado')) {
                // Si la comanda tiene mesa asociada, cambiar estado a 'atendido'
                if ($comanda && $comanda['mesa_id']) {
                    $this->mesaModel->cambiarEstado($comanda['mesa_id'], 'atendido');
                }
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar estado']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
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
