<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MesaLista - Notificaciones</title>
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h4 class="mb-0">
                    <i class="bi bi-bell-fill"></i> Notificaciones
                </h4>
            </div>
            <div class="card-body">
                <div id="lista-notificaciones">
                    <div class="text-center py-4">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= BASE_URL ?>/mozo" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        
        function cargarNotificaciones() {
            fetch(`${BASE_URL}/mozo/verificarComandasListas`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('lista-notificaciones');
                    
                    if (data.comandasListas && data.comandasListas.length > 0) {
                        let html = '<div class="list-group">';
                        data.comandasListas.forEach(comanda => {
                            html += `
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Comanda #${comanda.id}</h5>
                                        <span class="badge bg-success">LISTA</span>
                                    </div>
                                    <p class="mb-1">Mesa: ${comanda.mesa}</p>
                                    <small>Click para marcar como entregada</small>
                                </div>
                            `;
                        });
                        html += '</div>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                            <div class="text-center py-4">
                                <i class="bi bi-bell-slash fs-1 text-muted"></i>
                                <p class="text-muted mt-2">No hay notificaciones pendientes</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('lista-notificaciones').innerHTML = 
                        '<div class="alert alert-danger">Error al cargar notificaciones</div>';
                });
        }
        
        // Cargar al inicio
        cargarNotificaciones();
        
        // Actualizar cada 5 segundos
        setInterval(cargarNotificaciones, 5000);
    </script>
</body>
</html>