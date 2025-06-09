document.addEventListener('DOMContentLoaded', function() {
    const comandaItems = document.getElementById('comanda-items');
    const totalElement = document.getElementById('total-comanda');
    const idComanda = document.getElementById('id-comanda').value;
    const btnAceptar = document.getElementById('btn-aceptar');
    const btnSalir = document.getElementById('btn-salir');
    
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
            
            totalElement.textContent = `S/ ${total.toFixed(2)}`;
        });
    }
    
    // Comentarios
    document.addEventListener('click', function(e) {
        if (e.target.closest('.comentario-btn') || e.target.closest('.comentario-plato-btn')) {
            const btn = e.target.closest('.comentario-btn') || e.target.closest('.comentario-plato-btn');
            const modal = new bootstrap.Modal(document.getElementById('comentarioModal'));
            
            if (btn.classList.contains('comentario-btn')) {
                // Editar comentario existente
                document.getElementById('id-detalle').value = btn.dataset.idDetalle;
                document.getElementById('comentario').value = btn.dataset.comentario || '';
                document.getElementById('modo').value = 'editar';
            } else {
                // Nuevo producto con comentario
                document.getElementById('id-plato-nuevo').value = btn.dataset.idPlato;
                document.getElementById('comentario').value = '';
                document.getElementById('modo').value = 'nuevo';
            }
            
            modal.show();
        }
        
        // Eliminar item
        if (e.target.closest('.eliminar-btn')) {
            const idDetalle = e.target.closest('.eliminar-btn').dataset.idDetalle;
            
            if (confirm('¿Eliminar este producto?')) {
                eliminarItem(idDetalle);
            }
        }
    });
    
    // Guardar comentario
    document.getElementById('guardarComentario').addEventListener('click', function() {
        const modo = document.getElementById('modo').value;
        const comentario = document.getElementById('comentario').value;
        
        if (modo === 'editar') {
            const idDetalle = document.getElementById('id-detalle').value;
            actualizarComentario(idDetalle, comentario);
        } else {
            const idPlato = document.getElementById('id-plato-nuevo').value;
            const card = document.querySelector(`[data-id-plato="${idPlato}"]`);
            agregarProductoComanda(idPlato, card.dataset.nombre, card.dataset.precio, comentario);
        }
        
        bootstrap.Modal.getInstance(document.getElementById('comentarioModal')).hide();
    });
    
    // Función eliminar item
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
        });
    }
    
    // Función actualizar comentario
    function actualizarComentario(idDetalle, comentario) {
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
                mostrarAlerta('Comentario actualizado', 'success');
            }
        });
    }
    
    // Enviar comanda
    btnAceptar.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('confirmarEnvioModal'));
        modal.show();
    });
    
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
            }
        });
    });
    
    // Salir
    btnSalir.addEventListener('click', function() {
        if (confirm('¿Salir sin enviar la comanda?')) {
            window.location.href = `${BASE_URL}/mozo`;
        }
    });
    
    // Función mostrar alertas
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
    
    // Cargar comanda inicial
    actualizarComanda();
});