document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = window.location.origin + '/MesaLista';
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
        })
        .catch(error => {
            console.error('Error al actualizar comanda:', error);
        });
    }
    
    // Resto del código JavaScript permanece igual...
    
    // Enviar comanda
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
    
    // Cargar comanda inicial
    actualizarComanda();
});