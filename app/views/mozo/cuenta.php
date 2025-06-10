<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MesaLista - Cuenta Mesa <?= htmlspecialchars($mesa['nombre']) ?></title>
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .container { max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-receipt"></i> Cuenta - Mesa <?= htmlspecialchars($mesa['nombre']) ?>
                </h4>
            </div>
            <div class="card-body">
                <?php if (empty($comandas)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">No hay comandas activas para esta mesa</h5>
                        <p class="text-muted">Esta mesa no tiene pedidos pendientes de pago.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <h5>RESUMEN DE CONSUMO</h5>
                        <p class="text-muted"><?= date('d/m/Y H:i') ?></p>
                    </div>
                    
                    <?php foreach ($comandas as $comanda): ?>
                        <div class="mb-3">
                            <h6 class="text-muted">Comanda #<?= $comanda['id'] ?> - <?= $comanda['hora'] ?></h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Cant</th>
                                        <th>Descripción</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($comanda['items'])): ?>
                                        <?php foreach ($comanda['items'] as $item): ?>
                                            <tr>
                                                <td><?= $item['cantidad'] ?></td>
                                                <td><?= htmlspecialchars($item['nombre']) ?></td>
                                                <td class="text-end">S/ <?= number_format($item['precio'], 2) ?></td>
                                                <td class="text-end">S/ <?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Sin items</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>TOTAL A PAGAR:</h4>
                        <h4>S/ <?= number_format($total, 2) ?></h4>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer no-print">
                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>/mozo" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <?php if (!empty($comandas)): ?>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                        <button onclick="procesarPago()" class="btn btn-success">
                            <i class="bi bi-cash"></i> Procesar Pago
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function procesarPago() {
            if (confirm('¿Confirmar el pago de S/ <?= number_format($total, 2) ?>?')) {
                alert('Función de pago en desarrollo. Por ahora, cambie el estado de la mesa manualmente.');
            }
        }
    </script>
</body>
</html>