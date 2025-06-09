<?php
// filepath: c:\xampp\htdocs\MesaLista\app\views\mozo\inicio.php
if (!defined('BASE_URL'))
    require_once __DIR__ . '/../../../config/config.php';

require_once __DIR__ . '/../../models/MesaModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_mesa_id'])) {
    $mesaModel = new MesaModel();
    $mesaModel->eliminarMesa($_POST['eliminar_mesa_id']);
    header("Location: " . BASE_URL . "/mozo");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_mesa'])) {
    $mesaModel = new MesaModel();
    $mesaModel->agregarMesa();
    header("Location: " . BASE_URL . "/mozo?mesa_agregada=1");
    exit;

}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mesa1_id'], $_POST['mesa2_id'])) {
    $mesaModel = new MesaModel();
    $mesaModel->juntarMesas($_POST['mesa1_id'], $_POST['mesa2_id']);
    header("Location: " . BASE_URL . "/mozo");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['separar_mesa_nombre'])) {
    $mesaModel = new MesaModel();
    $mesaModel->separarMesa($_POST['separar_mesa_nombre']);
    header("Location: " . BASE_URL . "/mozo");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado_id'], $_POST['nuevo_estado'])) {
    $mesaModel = new MesaModel();
    $mesaModel->cambiarEstado($_POST['cambiar_estado_id'], $_POST['nuevo_estado']);
    header("Location: " . BASE_URL . "/mozo");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Panel Mozo - MesaLista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/mozo/inicio/inicio.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
/* ... estilos existentes ... */

/* Mesa seleccionada */
.mesa-card.seleccionada {
    border: 3px solid #0d6efd !important;
    box-shadow: 0 0 20px rgba(13, 110, 253, 0.5) !important;
    transform: scale(1.05);
}

/* Botón comanda deshabilitado */
.menu-icon-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.menu-icon-btn:disabled img {
    filter: grayscale(100%);
}
</style>

</head>

<body>
    <div class="d-flex vh-100">
        <!-- MENÚ LATERAL -->
        <div class="bg-dark text-white p-4 d-flex flex-column justify-content-between sidebar-mozo">
            <div>
                <h5 class="mb-4 text-center">MENÚ</h5>
                <div class="row row-cols-2 g-3 justify-content-center">
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnComanda" disabled>
                            <img src="<?= BASE_URL ?>/public/assets/img/comanda.png" alt="Comanda">
                            <span>Comanda</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn">
                            <img src="<?= BASE_URL ?>/public/assets/img/CerrarCuenta.png" alt="Cerrar Cuenta">
                            <span>Cerrar Cuenta</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn">
                            <img src="<?= BASE_URL ?>/public/assets/img/Recargar.png" alt="Recargar">
                            <span>Recargar</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn">
                            <img src="<?= BASE_URL ?>/public/assets/img/Deliviry.png" alt="Delivery">
                            <span>Delivery</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnJuntarMesas">
                            <img src="<?= BASE_URL ?>/public/assets/img/Juntar.png" alt="Juntar Mesas">
                            <span>Juntar Mesas</span>
                        </button>
                    </div>

                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnAgregarMesa">
                            <img src="<?= BASE_URL ?>/public/assets/img/Agregar.png" alt="Agregar Mesa">
                            <span>Agregar Mesa</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="menu-icon-btn" id="btnQuitarMesa">
                            <img src="<?= BASE_URL ?>/public/assets/img/Quitar.png" alt="Quitar Mesa">
                            <span>Quitar Mesa</span>
                        </button>
                    </div>
                    <div class="col text-center">
                        <button type="button" class="menu-icon-btn" id="btnSepararMesas">
                            <img src="<?= BASE_URL ?>/public/assets/img/Separar.png" alt="Separar Mesas">
                            <span>Separar Mesas</span>
                        </button>


                    </div>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/mozo/logout"
                class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 mt-4">
                Salir
            </a>
        </div>

        <!-- FORMULARIO OCULTO PARA AGREGAR -->
        <form method="post" id="formAgregarMesa" style="display: none;">
            <input type="hidden" name="agregar_mesa" value="1">
        </form>

        <!-- PANEL DE MESAS -->
        <div class="flex-grow-1 p-4 bg-light">
            <!-- TÍTULO -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>MESAS</h4>
                <i class="bi bi-bell-fill fs-3 text-warning"></i>
            </div>

            <!-- 🔴 MENSAJE -->
            <div id="mensajeEliminarMesa" class="alert alert-danger text-center fw-bold d-none mb-3">
                🔴 Modo eliminación activado: haz clic en una mesa para eliminarla.
            </div>
            <div id="mensajeJuntarMesas" class="alert alert-primary text-center fw-bold d-none mb-3">
                🟡 Modo juntar activado: selecciona 2 mesas libres para combinarlas.
            </div>
            <div id="mensajeSepararMesas" class="alert alert-info text-center fw-bold d-none mb-3">
                🔵 Modo separar activado: haz clic en una mesa combinada para dividirla en mesas libres.
            </div>



            <div class="row g-3" id="contenedorMesas">
                <?php foreach ($mesas as $mesa): ?>
                    <?php
                    // Determinar color del badge según estado
                    $badgeColor = 'secondary';
                    if ($mesa['estado'] === 'reservado') $badgeColor = 'warning';
                    elseif ($mesa['estado'] === 'esperando') $badgeColor = 'danger';
                    elseif ($mesa['estado'] === 'pagando') $badgeColor = 'success';

                    // Clases para la tarjeta según estado
                    $clase = 'mesa-libre';
                    if ($mesa['estado'] === 'reservado')
                        $clase = 'mesa-reservado';
                    elseif ($mesa['estado'] === 'esperando')
                        $clase = 'mesa-esperando';
                    elseif ($mesa['estado'] === 'pagando')
                        $clase = 'mesa-pagando';
                    elseif ($mesa['estado'] === 'combinada')
                        $clase = 'mesa-combinada';

                    $nombre = htmlspecialchars($mesa['nombre']);
                    ?>
                    <div class="col-6 col-sm-4 col-md-3">
                        <div class="card mesa-card shadow-sm rounded-4 animate-mesa <?= $clase ?>" data-id="<?= $mesa['id'] ?>"data-mesa="<?= $mesa['nombre'] ?>"data-estado="<?= $mesa['estado'] ?>">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4 position-relative">
                                <?php
                                if ($mesa['estado'] === 'combinada' && strpos($nombre, '|') !== false) {
                                    $partes = explode('|', $nombre);
                                    echo "<div class='mesa-combinada-interna mb-2'>
                                            <span class='lado-izquierdo'>" . trim($partes[0]) . "</span>
                                            <div class='divisor-central'></div>
                                            <span class='lado-derecho'>" . trim($partes[1]) . "</span>
                                          </div>";
                                } else {
                                    echo "<span class='fw-bold fs-4 mb-2'>{$nombre}</span>";
                                }
                                ?>
                                <span class="badge bg-<?= $badgeColor ?> mb-2"><?= ucfirst($mesa['estado']) ?></span>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary btn-sm btn-cambiar-estado"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalCambiarEstado"
                                        data-id="<?= $mesa['id'] ?>"
                                        data-nombre="<?= $mesa['nombre'] ?>"
                                        data-estado="<?= $mesa['estado'] ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm btn-eliminar-mesa"
                                        data-id="<?= $mesa['id'] ?>"
                                        data-nombre="<?= $mesa['nombre'] ?>"
                                        title="Eliminar mesa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>

            <!-- LEYENDA -->
            <div class="mt-4 d-flex justify-content-center gap-4">
                <span><span class="badge bg-secondary">&nbsp;&nbsp;</span> Libre</span>
                <span><span class="badge bg-warning">&nbsp;&nbsp;</span> Reservado</span>
                <span><span class="badge bg-danger">&nbsp;&nbsp;</span> Esperando</span>
                <span><span class="badge bg-success">&nbsp;&nbsp;</span> Pagando</span>
            </div>
        </div>
    </div>

    <!-- Modal: Éxito al agregar -->
    <div class="modal fade" id="modalMesaAgregada" tabindex="-1" aria-labelledby="modalMesaAgregadaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalMesaAgregadaLabel">Éxito</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">¡Mesa agregada correctamente!</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Confirmar eliminación -->
    <div class="modal fade" id="modalEliminarMesa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Eliminar Mesa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro que deseas eliminar la mesa <strong id="mesaAEliminarNombre"></strong>?
                </div>
                <div class="modal-footer">
                    <form method="post" id="formEliminarMesa">
                        <input type="hidden" name="eliminar_mesa_id" id="mesaAEliminarId">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalJuntarMesas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Confirmar Unión de Mesas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Deseas juntar la mesa <strong id="mesa1Nombre"></strong> con la mesa <strong
                        id="mesa2Nombre"></strong>?
                </div>
                <div class="modal-footer">
                    <form method="post" id="formJuntarMesas">
                        <input type="hidden" name="mesa1_id" id="mesa1Id">
                        <input type="hidden" name="mesa2_id" id="mesa2Id">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Cambiar Estado de Mesa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="cambiar_estado_id" id="cambiarEstadoId">
                        <p id="nombreMesaCambio"></p>
                        <select name="nuevo_estado" class="form-select" required>
                            <option value="libre">Libre</option>
                            <option value="reservado">Reservado</option>
                            <option value="esperando">Esperando</option>
                            <option value="pagando">Pagando</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Cambiar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script src="<?= BASE_URL ?>/public/assets/js/mozo/panel.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/mozo/mozo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    // panel.js o mozo.js
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-eliminar-mesa')) {
        const btn = e.target.closest('.btn-eliminar-mesa');
        document.getElementById('mesaAEliminarId').value = btn.dataset.id;
        document.getElementById('mesaAEliminarNombre').textContent = btn.dataset.nombre;
        const modal = new bootstrap.Modal(document.getElementById('modalEliminarMesa'));
        modal.show();
    }
});
</script>
</html>

<style>
.mesa-card {
    transition: box-shadow 0.3s, transform 0.2s;
}
.mesa-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    transform: translateY(-4px) scale(1.03);
}
.mesa-combinada-interna {
    background: linear-gradient(90deg, #e3f2fd 50%, #fff3cd 50%);
    border-radius: 10px;
    border: 2px solid #b6b6b6;
    padding: 0.5rem 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.lado-izquierdo, .lado-derecho {
    font-weight: bold;
    font-size: 1.1rem;
    flex: 1;
    text-align: center;
}
.divisor-central {
    width: 4px;
    height: 32px;
    background: #adb5bd;
    border-radius: 2px;
    display: inline-block;
}
.badge {
    font-size: 1rem;
    padding: 0.5em 1.2em;
    font-weight: 500;
    color: #fff !important;
}
.bg-warning { color: #212529 !important; }
.animate-mesa {
    animation: fadeInMesa 0.7s;
}
@keyframes fadeInMesa {
    from { opacity: 0; transform: scale(0.95);}
    to { opacity: 1; transform: scale(1);}
}
</style>