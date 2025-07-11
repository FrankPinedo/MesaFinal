<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MesaLista - Comanda <?= htmlspecialchars($mesa) ?></title>

    <!-- Logo -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/mozo/comanda.css" />

    <!-- Iconos -->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Header con información general -->
            <header class="col-12 bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <?php if ($mesa === 'Delivery/Para Llevar'): ?>
                            <i class="bi bi-box-seam"></i> Pedido para Llevar
                        <?php else: ?>
                            Mesa <?= htmlspecialchars($mesa) ?>
                        <?php endif; ?>
                    </h4>
                    <small>Mozo: <?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></small>
                </div>
                <div>
                    <span class="badge bg-info">Total Mesa: S/ <?= number_format($totalMesa, 2) ?></span>
                </div>
            </header>

            <!-- Contenido principal -->
            <div class="col-12">
                <div class="row">
                    <!-- Sección Comanda (izquierda) -->
                    <div class="col-md-4 p-2 border-end">
                        <!-- Comandas anteriores (solo lectura) -->
                        <?php if (!empty($comandasAnteriores)): ?>
                            <?php 
                            $numComanda = 1;
                            foreach ($comandasAnteriores as $cmdAnterior): 
                                $detallesAnt = $this->comandaModel->obtenerDetallesComandaCompletos($cmdAnterior['id']);
                                $totalAnt = 0;
                                foreach ($detallesAnt as $det) {
                                    $totalAnt += $det['precio'] * $det['cantidad'];
                                }
                            ?>
                            <div class="card shadow-sm mb-2">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="card-title mb-0">
                                        COMANDA #<?= $numComanda++ ?> - <?= strtoupper($cmdAnterior['estado']) ?>
                                        <span class="badge bg-light text-dark float-end">S/ <?= number_format($totalAnt, 2) ?></span>
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-muted">
                                        <?php foreach ($detallesAnt as $det): ?>
                                            <div><?= $det['cantidad'] ?>x <?= htmlspecialchars($det['nombre']) ?></div>
                                        <?php endforeach; ?>
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Comanda actual -->
                        <div class="card shadow-sm">
                            <div class="card-header <?= $puedeEditar ? 'bg-primary' : 'bg-warning' ?> text-white">
                                <h5 class="card-title mb-0">
                                    COMANDA #<?= $numeroComanda ?> - <?= strtoupper($comanda['estado']) ?>
                                    <?php if (!$puedeEditar): ?>
                                        <i class="bi bi-lock-fill float-end"></i>
                                    <?php endif; ?>
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 50vh;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Cant</th>
                                                <th>Descripción</th>
                                                <th>Precio</th>
                                                <?php if ($puedeEditar): ?><th></th><?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody id="comanda-items">
                                            <?php if (!empty($detalles)): ?>
                                                <?php foreach ($detalles as $detalle): ?>
                                                    <tr>
                                                        <td><?= $detalle['cantidad'] ?></td>
                                                        <td>
                                                            <?= htmlspecialchars($detalle['nombre']) ?>
                                                            <?php if (!empty($detalle['comentario'])): ?>
                                                                <small class="text-muted d-block"><?= htmlspecialchars($detalle['comentario']) ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>S/ <?= number_format($detalle['precio'] * $detalle['cantidad'], 2) ?></td>
                                                        <?php if ($puedeEditar): ?>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class="btn btn-sm btn-outline-secondary comentario-btn" 
                                                                    data-id-detalle="<?= $detalle['id_detalle'] ?>"
                                                                    data-comentario="<?= htmlspecialchars($detalle['comentario'] ?? '') ?>">
                                                                    <i class="bi bi-chat-left-text"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger eliminar-btn" 
                                                                    data-id-detalle="<?= $detalle['id_detalle'] ?>">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="<?= $puedeEditar ? '4' : '3' ?>" class="text-center py-3">
                                                        No hay items en la comanda
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Total comanda:</h5>
                                        <h5 class="mb-0" id="total-comanda">S/ <?= number_format($total, 2) ?></h5>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button id="btn-salir" class="btn btn-secondary">Salir</button>
                                        <?php if ($puedeEditar && $comanda['estado'] === 'nueva'): ?>
                                            <button id="btn-aceptar" class="btn btn-success">
                                                Enviar a Cocina
                                            </button>
                                        <?php elseif ($comanda['estado'] === 'listo' || $comanda['estado'] === 'recibido'): ?>
                                            <button id="btn-nueva-comanda" class="btn btn-primary">
                                                Nueva Comanda
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Productos (derecha) -->
                    <div class="col-md-8 p-2">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    PRODUCTOS 
                                    <?php if (!$puedeEditar): ?>
                                        <span class="badge bg-warning text-dark">Comanda no editable</span>
                                    <?php endif; ?>
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <!-- Filtros de productos -->
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-comida-tab" data-bs-toggle="tab" data-bs-target="#nav-comida" type="button" role="tab" aria-controls="nav-comida" aria-selected="true">Comida</button>
                                    <button class="nav-link" id="nav-bebidas-tab" data-bs-toggle="tab" data-bs-target="#nav-bebidas" type="button" role="tab" aria-controls="nav-bebidas" aria-selected="false">Bebidas</button>
                                    <button class="nav-link" id="nav-combos-tab" data-bs-toggle="tab" data-bs-target="#nav-combos" type="button" role="tab" aria-controls="nav-combos" aria-selected="false">Combos</button>
                                </div>
                                
                                <!-- Contenido de las pestañas -->
                                <div class="tab-content" id="nav-tabContent">
                                    <!-- Tab Comida -->
                                    <div class="tab-pane fade show active" id="nav-comida" role="tabpanel" aria-labelledby="nav-comida-tab">
                                        <div class="row row-cols-1 row-cols-md-3 g-3 p-3">
                                            <?php if (!empty($platos)): ?>
                                                <?php foreach ($platos as $plato): ?>
                                                    <div class="col">
                                                        <div class="card h-100 producto-card <?= (!$puedeEditar || $plato['estado'] == 0 || $plato['stock'] <= 0) ? 'disabled bg-light' : '' ?>" 
                                                             data-id-plato="<?= $plato['id'] ?>" 
                                                             data-precio="<?= $plato['precio'] ?>" 
                                                             data-nombre="<?= htmlspecialchars($plato['nombre']) ?>"
                                                             data-disponible="<?= ($puedeEditar && $plato['estado'] == 1 && $plato['stock'] > 0) ? '1' : '0' ?>">
                                                            <div class="card-img-top text-center pt-2">
                                                                <?php if (!empty($plato['imagen']) && $plato['imagen'] != 'sin imagen.jpg'): ?>
                                                                    <img src="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($plato['imagen']) ?>" alt="<?= htmlspecialchars($plato['nombre']) ?>" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="bg-light p-4 rounded">
                                                                        <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6 class="card-title"><?= htmlspecialchars($plato['nombre']) ?></h6>
                                                                <p class="card-text small">
                                                                    <?php if (!empty($plato['descripcion'])): ?>
                                                                        <?= htmlspecialchars($plato['descripcion']) ?>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">Sin descripción</span>
                                                                    <?php endif; ?>
                                                                </p>
                                                            </div>
                                                            <div class="card-footer d-flex justify-content-between align-items-center">
                                                                <span class="fw-bold">S/ <?= number_format($plato['precio'], 2) ?></span>
                                                                <div>
                                                                    <span class="badge <?= $plato['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                                        Stock: <?= $plato['stock'] ?>
                                                                    </span>
                                                                    <?php if ($puedeEditar && $plato['estado'] == 1 && $plato['stock'] > 0): ?>
                                                                        <button class="btn btn-sm btn-outline-primary comentario-plato-btn" data-id-plato="<?= $plato['id'] ?>">
                                                                            <i class="bi bi-chat-left-text"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="col-12 text-center py-5">
                                                    <p class="text-muted">No hay platos disponibles</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Tab Bebidas (similar structure) -->
                                    <div class="tab-pane fade" id="nav-bebidas" role="tabpanel" aria-labelledby="nav-bebidas-tab">
                                        <!-- Similar estructura para bebidas -->
                                    </div>
                                    
                                    <!-- Tab Combos (similar structure) -->
                                    <div class="tab-pane fade" id="nav-combos" role="tabpanel" aria-labelledby="nav-combos-tab">
                                        <!-- Similar estructura para combos -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar comentarios -->
    <div class="modal fade" id="comentarioModal" tabindex="-1" aria-labelledby="comentarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="comentarioModalLabel">Agregar comentario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="comentarioForm">
                        <input type="hidden" id="id-detalle" name="id_detalle">
                        <input type="hidden" id="id-plato-nuevo" name="id_plato_nuevo">
                        <input type="hidden" id="modo" name="modo" value="editar">
                        <div class="mb-3">
                            <label for="comentario" class="form-label">Comentario</label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="3" placeholder="Ej: Sin ají, bien cocido, etc."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarComentario">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para enviar comanda -->
    <div class="modal fade" id="confirmarEnvioModal" tabindex="-1" aria-labelledby="confirmarEnvioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarEnvioModalLabel">Confirmar envío a cocina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas enviar esta comanda a cocina?</p>
                    <p class="text-warning"><i class="bi bi-exclamation-triangle"></i> Una vez enviada, solo podrás modificarla hasta que cocina la marque como "Recibida".</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmarEnvio">Enviar a cocina</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="id-comanda" value="<?= $comanda['id'] ?>">
    <input type="hidden" id="mesa-id" value="<?= $mesaId ?>">
    <input type="hidden" id="puede-editar" value="<?= $puedeEditar ? '1' : '0' ?>">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/mozo/comanda.js"></script>
</body>
</html>