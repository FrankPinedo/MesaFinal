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
}
