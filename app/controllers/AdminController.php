<?php
class AdminController
{
    private $usuario;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../helpers/sesion.php';
        $this->usuario = verificarSesion();
        session_regenerate_id(true);

        if ($this->usuario['rol'] !== 'admin') {
            require_once __DIR__ . '/../controllers/ErrorController.php';
            (new ErrorController())->index('403');
            exit();
        }
    }

    public function index()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/inicio.php';
    }
    public function empresa()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/empresa.php';
    }
    public function contacto()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/contacto.php';
    }
    public function productos()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $productoModel = new ProductoModel();

        // Procesar acciones
        if (isset($_GET['accion'], $_GET['id'])) {
            $id = intval($_GET['id']);
            if ($_GET['accion'] === 'habilitar') {
                $productoModel->cambiarEstado($id, 1);
            } elseif ($_GET['accion'] === 'deshabilitar') {
                $productoModel->cambiarEstado($id, 0);
            }
            header("Location: " . BASE_URL . "/admin/productos");
            exit;
        }

        // Lógica de paginación...
        $limite = 20;
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $offset = ($pagina - 1) * $limite;

        $totalProductos = $productoModel->contarProductos();
        $productos = $productoModel->obtenerProductosPaginados($offset, $limite);
        $totalPaginas = ceil($totalProductos / $limite);

        require_once __DIR__ . '/../views/admin/productos.php';
    }
    public function usuarios()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../views/admin/usuarios.php';
    }
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit();
    }
    public function agregar_producto()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $productoModel = new ProductoModel();
        $tiposProducto = $productoModel->listarTiposProducto();
        $tamanos = $productoModel->listarTamanos();
        $tiposBebida = $productoModel->listarTiposBebida();
        $tiposPlato = $productoModel->listarTiposPlato();
        $guarniciones = $productoModel->listarGuarnicionesActivas();

        // Procesar acciones
        if (isset($_GET['accion'], $_GET['id'])) {
            $id = intval($_GET['id']);
            if ($_GET['accion'] === 'habilitar') {
                $productoModel->cambiarEstado($id, 1);
            } elseif ($_GET['accion'] === 'deshabilitar') {
                $productoModel->cambiarEstado($id, 0);
            }
            header("Location: " . BASE_URL . "/admin/agregar");
            exit;
        }

        // Lógica de paginación...
        $limite = 20;
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $offset = ($pagina - 1) * $limite;

        $totalProductos = $productoModel->contarProductos();
        $productos = $productoModel->obtenerProductosPaginados($offset, $limite);
        $totalPaginas = ceil($totalProductos / $limite);
        require_once __DIR__ . '/../views/admin/agregarPro.php';
    }
    public function guardarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/ProductoModel.php';
            $productoModel = new ProductoModel();

            // Procesar datos básicos
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $stock = $_POST['stock'] ?? 0;
            $tipo_producto_id = $_POST['tipo_producto_id'] ?? null;
            $tamano_id = $_POST['tamano_id'] ?? null;

            // Procesar imagen
            $imagenNombre = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $imagenNombre = $productoModel->guardarImagen($_FILES['imagen']);
                if (!$imagenNombre) {
                    echo "Error: Solo se permiten archivos de imagen válidos.";
                    exit;
                }
            }

            // Preparar datos para insertar
            $data = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'stock' => $stock,
                'tipo_producto_id' => $tipo_producto_id,
                'tamano_id' => $tamano_id,
                'imagen' => $imagenNombre,
            ];

            // Agregar campos específicos según el tipo de producto
            if ($tipo_producto_id == 1) { // Bebida
                $data['tipo_bebida_id'] = $_POST['tipo_bebida_id'] ?? null;
            } elseif ($tipo_producto_id == 2) { // Plato
                $data['tipo_plato_id'] = $_POST['tipo_plato_id'] ?? null;
                $data['guarniciones'] = $_POST['guarniciones'] ?? [];
            }

            // Insertar producto
            $productoId = $productoModel->insertarProducto($data);

            // Solo procesar componentes si es un combo
            if ($tipo_producto_id == 4 && isset($_POST['componentes'])) {
                $componentes = [];

                // Reestructurar el array de componentes
                foreach ($_POST['componentes'] as $comp) {
                    if (!empty($comp['producto_id'])) {
                        $componentes[] = [
                            'producto_id' => $comp['producto_id'],
                            'cantidad' => $comp['cantidad'] ?? 1,
                            'obligatorio' => isset($comp['obligatorio']),
                            'grupo' => $comp['grupo'] ?? ''
                        ];
                    }
                }

                if (!empty($componentes)) {
                    $productoModel->insertarComponentesCombo($productoId, $componentes);
                }
            }

            header("Location: " . BASE_URL . "/admin/productos");
            exit();
        }
    }
    public function agregar_guarnicion()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $productoModel = new ProductoModel();
        $guarniciones = $productoModel->listarGuarniciones();

        // Procesar acciones
        if (isset($_GET['accion'], $_GET['id'])) {
            $id = intval($_GET['id']);
            if ($_GET['accion'] === 'habilitar') {
                $productoModel->cambiarEstadoGuarnicion($id, 1);
            } elseif ($_GET['accion'] === 'deshabilitar') {
                $productoModel->cambiarEstadoGuarnicion($id, 0);
            }
            header("Location: " . BASE_URL . "/admin/agregar_guarnicion");
            exit;
        }

        $limite = 20;
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $offset = ($pagina - 1) * $limite;

        $totalGuarniciones = $productoModel->contarGuarniciones();
        $productos = $productoModel->obtenerGuarnicionesPaginados($offset, $limite);
        $totalPaginas = ceil($totalGuarniciones / $limite);
        require_once __DIR__ . '/../views/admin/agregarGua.php';
    }
    public function guardarGuarnicion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/ProductoModel.php';
            $productoModel = new ProductoModel();

            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);
            $precio = floatval($_POST['precio']);
            $estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
            $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

            // Imagen
            $imagenNombre = 'sin imagen.jpg';
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $imagenSubida = $productoModel->guardarImagen($_FILES['imagen']);
                if ($imagenSubida !== false) {
                    $imagenNombre = $imagenSubida;
                } else {
                    echo "Error: Imagen no válida.";
                    exit;
                }
            }

            $data = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'estado' => $estado,
                'stock' => $stock,
                'imagen' => $imagenNombre
            ];

            if ($productoModel->insertarGuarnicion($data)) {
                header("Location: " . BASE_URL . "/admin/agregar_guarnicion");
            } else {
                echo "Error al guardar la guarnición.";
            }
            exit;
        }
    }
    public function variaciones()
    {
        require_once __DIR__ . '/../models/ProductoModel.php';
        $model = new ProductoModel();

        $tiposBebida = $model->listarTiposBebida();
        $tiposPlato = $model->listarTiposPlato();
        $tamanos = $model->listarTamanos();

        $usuario = $this->usuario;

        require_once __DIR__ . '/../views/admin/variaciones.php';
    }
    public function guardarBebida()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $model = new ProductoModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
            if (!empty($nombre)) {
                $model->guardarTipoBebida($nombre);
            }
        }
        header("Location: " . BASE_URL . "/admin/variaciones");
    }

    public function guardarPlato()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $model = new ProductoModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
            if (!empty($nombre)) {
                $model->guardarTipoPlato($nombre);
            }
        }

        header("Location: " . BASE_URL . "/admin/variaciones");
    }

    public function guardarTamano()
    {
        $usuario = $this->usuario;
        require_once __DIR__ . '/../models/ProductoModel.php';
        $model = new ProductoModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
            if (!empty($nombre)) {
                $model->guardarTamano($nombre);
            }
        }

        header("Location: " . BASE_URL . "/admin/variaciones");
    }
}
