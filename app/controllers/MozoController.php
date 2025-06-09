<?php
class MozoController
{
    private $usuario;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        require_once __DIR__ . '/../models/MesaModel.php';

        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'mozo') {
            require_once __DIR__ . '/../controllers/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }

        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);

        // Procesar eliminación de mesa
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_mesa_id'])) {
            $id = intval($_POST['eliminar_mesa_id']);
            $stmt = $pdo->prepare("DELETE FROM mesas WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        // Separar mesa combinada
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['separar_mesa_nombre'])) {
            $mesaModel = new MesaModel();
            $mesaModel->separarMesa($_POST['separar_mesa_nombre']);
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }


        $stmt = $pdo->query("SELECT id, nombre, estado FROM mesas");
        $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ordenar por número extraído del nombre
        usort($mesas, function ($a, $b) {
            preg_match('/\d+/', $a['nombre'], $numA);
            preg_match('/\d+/', $b['nombre'], $numB);
            return intval($numA[0]) - intval($numB[0]);
        });



        $usuario = $this->usuario;
        require __DIR__ . '/../views/mozo/inicio.php';
    }

    public function index()
    {
        require_once __DIR__ . '/../views/mozo/inicio.php';
    }
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit();
    }
    public function comanda($mesaId = null)
    {
        if (!$mesaId) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        $usuario = $this->usuario;

        // Cargar modelos necesarios
        require_once __DIR__ . '/../models/MesaModel.php';
        require_once __DIR__ . '/../models/ProductoModel.php';
        require_once __DIR__ . '/../models/ComandaModel.php';

        $mesaModel = new MesaModel();
        $productoModel = new ProductoModel();
        $comandaModel = new ComandaModel();

        // Obtener información de la mesa
        $mesa = $mesaModel->obtenerMesaPorId($mesaId);
        if (!$mesa) {
            header("Location: " . BASE_URL . "/mozo");
            exit();
        }

        // Verificar si hay comanda activa para esta mesa
        $comanda = $comandaModel->obtenerComandaActivaPorMesa($mesaId);
        if (!$comanda) {
            // Crear nueva comanda
            $comandaId = $comandaModel->crearComanda($mesaId, $usuario['id_user']);
            $comanda = ['id_comanda' => $comandaId, 'total' => 0];
            $detalles = [];
        } else {
            // Obtener detalles de comanda existente
            $detalles = $comandaModel->obtenerDetallesComanda($comanda['id_comanda']);
        }

        // Obtener productos disponibles
        $platos = $productoModel->obtenerProductosDisponiblesPorTipo(2); // tipo plato
        $bebidas = $productoModel->obtenerProductosDisponiblesPorTipo(1); // tipo bebida
        $combos = $productoModel->obtenerProductosDisponiblesPorTipo(4); // tipo combo

        // Calcular total
        $total = 0;
        foreach ($detalles as $detalle) {
            $total += $detalle['precio'] * $detalle['cantidad'];
        }

        require_once __DIR__ . '/../views/mozo/comanda.php';
    }
}
