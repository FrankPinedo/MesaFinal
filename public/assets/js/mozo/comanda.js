document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = window.location.origin + '/MesaLista';
    const comandaItems = document.getElementById('comanda-items');
    const totalElement = document.getElementById('total-comanda');
    const idComanda = document.getElementById('id-comanda').value;
    const btnAceptar = document.getElementById('btn-aceptar');
    const btnSalir = document.getElementById('btn-salir');
    const comentarioModal = new bootstrap.Modal(document.getElementById('comentarioModal'));
    const confirmarEnvioModal = new bootstrap.Modal(document.getElementById('confirmarEnvioModal'));
    
    let modoComentario = 'editar';
    let productoParaAgregar = null;
    
    // Doble clic para agregar plato
    document.querySelectorAll('.producto-card').forEach(card => {
        card.addEventListener('dblclick', function() {
            if (this.dataset.disponible === '0') {
                mostrarAlerta('Este producto no está disponible', 'warning');
                return;
            }
            
            const idPlato = this.dataset.idPlato;
            const nombre = this.dataset.nombre;
            const precio = parseFloat(this.dataset.precio);
            
            agregarProductoComanda(idPlato, nombre, precio);
        });
    });
    
    // Función para agregar producto
    function agregarProductoComanda(idPlato, nombre, precio, comentario = '') {
        fetch(`${BASE_URL}/mozo/agregarItem`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_comanda: idComanda,
                id_plato: idPlato,
                cantidad: 1,
                comentario: comentario
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarComanda();
                mostrarAlerta('Producto agregado', 'success');
            } else {
                mostrarAlerta(data.message || 'Error al agregar producto', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error de conexión', 'danger');
        });
    }
    
    // Función para actualizar vista de comanda
    function actualizarComanda() {
        fetch(`${BASE_URL}/mozo/obtenerComanda/${idComanda}`)
        .then(response => response.json())
        .then(data => {
            // Actualizar tabla
            comandaItems.innerHTML = '';
            let total = 0;
            
            if (data.detalles && data.detalles.length > 0) {
                data.detalles.forEach(item => {
                    total += item.precio * item.cantidad;
                    
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${item.cantidad}</td>
                        <td>
                            ${item.nombre}
                            ${item.comentario ? `<small class="text-muted d-block">${item.comentario}</small>` : ''}
                        </td>
                        <td>S/ ${(item.precio * item.cantidad).toFixed(2)}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-sm btn-outline-secondary comentario-btn" 
                                    data-id-detalle="${item.id_detalle}"
                                    data-comentario="${item.comentario || ''}">
                                    <i class="bi bi-chat-left-text"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger eliminar-btn" 
                                    data-id-detalle="${item.id_detalle}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    comandaItems.appendChild(tr);
                });
            } else {
                comandaItems.innerHTML = '<tr><td colspan="4" class="text-center py-3">No hay items en la comanda</td></tr>';
            }
            
            totalElement.textContent = `S/ ${total.toFixed(2)}`;
            
            // Re-asignar eventos a los nuevos botones
            asignarEventosBotones();
        })
        .catch(error => {
            console.error('Error al actualizar comanda:', error);
        });
    }
    
    // Función para asignar eventos a botones dinámicos
    function asignarEventosBotones() {
        // Botones de comentario en items existentes
        document.querySelectorAll('.comentario-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                modoComentario = 'editar';
                document.getElementById('id-detalle').value = this.dataset.idDetalle;
                document.getElementById('comentario').value = this.dataset.comentario || '';
                document.getElementById('modo').value = 'editar';
                comentarioModal.show();
            });
        });
        
        // Botones de eliminar
        document.querySelectorAll('.eliminar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('¿Eliminar este producto de la comanda?')) {
                    const idDetalle = this.dataset.idDetalle;
                    eliminarItem(idDetalle);
                }
            });
        });
    }
    
    // Botones de comentario para productos nuevos
    document.querySelectorAll('.comentario-plato-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const card = this.closest('.producto-card');
            
            if (card.dataset.disponible === '0') {
                mostrarAlerta('Este producto no está disponible', 'warning');
                return;
            }
            
            modoComentario = 'nuevo';
            productoParaAgregar = {
                id: card.dataset.idPlato,
                nombre: card.dataset.nombre,
                precio: parseFloat(card.dataset.precio)
            };
            
            document.getElementById('id-plato-nuevo').value = productoParaAgregar.id;
            document.getElementById('comentario').value = '';
            document.getElementById('modo').value = 'nuevo';
            comentarioModal.show();
        });
    });
    
    // Guardar comentario
    document.getElementById('guardarComentario').addEventListener('click', function() {
        const comentario = document.getElementById('comentario').value;
        
        if (modoComentario === 'editar') {
            // Actualizar comentario existente
            const idDetalle = document.getElementById('id-detalle').value;
            
            fetch(`${BASE_URL}/mozo/actualizarComentario`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_detalle: idDetalle,
                    comentario: comentario
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    actualizarComanda();
                    comentarioModal.hide();
                    mostrarAlerta('Comentario actualizado', 'success');
                }
            });
        } else {
            // Agregar nuevo producto con comentario
            if (productoParaAgregar) {
                agregarProductoComanda(
                    productoParaAgregar.id,
                    productoParaAgregar.nombre,
                    productoParaAgregar.precio,
                    comentario
                );
                comentarioModal.hide();
            }
        }
    });
    
    // Función para eliminar item
    function eliminarItem(idDetalle) {
        fetch(`${BASE_URL}/mozo/eliminarItem`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_detalle: idDetalle
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarComanda();
                mostrarAlerta('Producto eliminado', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al eliminar producto', 'danger');
        });
    }
    
    // Función para mostrar alertas
    function mostrarAlerta(mensaje, tipo) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
    
    // Botón Aceptar - Enviar a cocina
    btnAceptar.addEventListener('click', function() {
        // Verificar si hay items en la comanda
        const hayItems = comandaItems.querySelector('tr td[colspan="4"]') === null;
        
        if (!hayItems) {
            mostrarAlerta('La comanda está vacía', 'warning');
            return;
        }
        
        confirmarEnvioModal.show();
    });
    
    // Confirmar envío
    document.getElementById('confirmarEnvio').addEventListener('click', function() {
        fetch(`${BASE_URL}/mozo/enviarComanda`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_comanda: idComanda
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarAlerta('Comanda enviada a cocina', 'success');
                setTimeout(() => {
                    window.location.href = `${BASE_URL}/mozo`;
                }, 1500);
            } else {
                mostrarAlerta(data.message || 'Error al enviar comanda', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error de conexión', 'danger');
        });
    });
    
    // Botón Salir
    btnSalir.addEventListener('click', function() {
        if (confirm('¿Deseas salir sin enviar la comanda?')) {
            window.location.href = `${BASE_URL}/mozo`;
        }
    });
    
    // Cargar comanda inicial
    actualizarComanda();
});