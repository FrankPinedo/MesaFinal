document.addEventListener("DOMContentLoaded", function () {
    let mesasSeleccionadas = [];
    const BASE_URL = window.location.origin + '/MesaLista';
    const btnComanda = document.getElementById('btnComanda');
    const btnJuntarMesas = document.getElementById('btnJuntarMesas');
    const btnSepararMesas = document.getElementById('btnSepararMesas');
    const btnRecargar = document.getElementById('btnRecargar');
    const btnDelivery = document.getElementById('btnDelivery');
    
    // Función para actualizar estado de botones
    function actualizarBotones() {
        const totalSeleccionadas = mesasSeleccionadas.length;
        const todasLibres = mesasSeleccionadas.every(mesa => mesa.estado === 'libre');
        const hayCombinadaSeleccionada = mesasSeleccionadas.some(mesa => mesa.combinada === 'true');
        
        // Botón Comanda: solo activo si hay 1 mesa seleccionada
        btnComanda.disabled = totalSeleccionadas !== 1;
        
        // Botón Juntar: activo si hay 2 o más mesas libres seleccionadas
        btnJuntarMesas.disabled = !(totalSeleccionadas >= 2 && todasLibres);
        
        // Botón Separar: activo si hay 1 mesa combinada seleccionada
        btnSepararMesas.disabled = !(totalSeleccionadas === 1 && hayCombinadaSeleccionada);
        
        // Mostrar mensaje si hay selección múltiple
        const mensajeMultiple = document.getElementById('mensajeSeleccionMultiple');
        if (totalSeleccionadas > 1) {
            mensajeMultiple.classList.remove('d-none');
            if (!todasLibres) {
                mensajeMultiple.textContent = '⚠️ Para juntar mesas, todas deben estar libres';
                mensajeMultiple.classList.remove('alert-success');
                mensajeMultiple.classList.add('alert-warning');
            } else {
                mensajeMultiple.textContent = '✅ Mesas seleccionadas: ' + totalSeleccionadas + '. Puedes juntarlas si están libres.';
                mensajeMultiple.classList.remove('alert-warning');
                mensajeMultiple.classList.add('alert-success');
            }
        } else {
            mensajeMultiple.classList.add('d-none');
        }
    }
    
    // Manejar clic en mesa
    document.querySelectorAll('.mesa-card').forEach(mesa => {
        mesa.addEventListener('click', function(e) {
            // Si está en modo eliminar, no seleccionar
            if (this.classList.contains('modo-eliminar')) {
                return;
            }
            
            // Evitar selección si se hace clic en botones
            if (e.target.closest('.btn')) {
                return;
            }
            
            // Obtener datos de la mesa
            const mesaData = {
                id: this.dataset.id,
                nombre: this.dataset.mesa,
                estado: this.dataset.estado,
                combinada: this.dataset.combinada,
                elemento: this
            };
            
            // Toggle selección
            if (this.classList.contains('seleccionada') || this.classList.contains('seleccionada-multiple')) {
                // Deseleccionar
                this.classList.remove('seleccionada', 'seleccionada-multiple');
                mesasSeleccionadas = mesasSeleccionadas.filter(m => m.id !== mesaData.id);
            } else {
                // Seleccionar
                if (mesasSeleccionadas.length === 0) {
                    this.classList.add('seleccionada');
                } else {
                    this.classList.add('seleccionada-multiple');
                }
                mesasSeleccionadas.push(mesaData);
            }
            
            // Actualizar clases de todas las mesas seleccionadas
            if (mesasSeleccionadas.length > 1) {
                mesasSeleccionadas.forEach(mesa => {
                    mesa.elemento.classList.remove('seleccionada');
                    mesa.elemento.classList.add('seleccionada-multiple');
                });
            } else if (mesasSeleccionadas.length === 1) {
                mesasSeleccionadas[0].elemento.classList.remove('seleccionada-multiple');
                mesasSeleccionadas[0].elemento.classList.add('seleccionada');
            }
            
            actualizarBotones();
        });
    });
    
    // Manejar clic en botón Comanda
    btnComanda.addEventListener('click', function() {
        if (mesasSeleccionadas.length === 1) {
            window.location.href = `${BASE_URL}/mozo/comanda?mesa=${mesasSeleccionadas[0].id}`;
        }
    });
    
    // Manejar clic en botón Juntar Mesas
    btnJuntarMesas.addEventListener('click', function() {
        if (mesasSeleccionadas.length >= 2) {
            const todasLibres = mesasSeleccionadas.every(mesa => mesa.estado === 'libre');
            
            if (!todasLibres) {
                alert('Solo se pueden juntar mesas que estén libres');
                return;
            }
            
            // Mostrar información de las mesas seleccionadas
            const infoDiv = document.getElementById('mesasSeleccionadasInfo');
            const nombresJuntos = mesasSeleccionadas.map(m => m.nombre).join(' + ');
            infoDiv.innerHTML = `<strong>${nombresJuntos}</strong>`;
            
            // Guardar IDs para el formulario
            document.getElementById('mesaIds').value = JSON.stringify(mesasSeleccionadas.map(m => m.id));
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalJuntarMesas'));
            modal.show();
            
            // Limpiar selección
            limpiarSeleccion();
        }
    });
    
    // Manejar clic en botón Separar Mesas
    btnSepararMesas.addEventListener('click', function() {
        if (mesasSeleccionadas.length === 1 && mesasSeleccionadas[0].combinada === 'true') {
            const mesaNombre = mesasSeleccionadas[0].nombre;
            
            if (confirm(`¿Deseas separar la mesa ${mesaNombre}?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const input = document.createElement('input');
                input.name = 'separar_mesa_nombre';
                input.value = mesaNombre;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
            
            limpiarSeleccion();
        }
    });
    
    // Manejar clic en botón Recargar
    btnRecargar.addEventListener('click', function() {
        location.reload();
    });
    
    // Manejar clic en botón Delivery
    btnDelivery.addEventListener('click', function() {
        // Crear una comanda tipo delivery sin mesa
        window.location.href = `${BASE_URL}/mozo/comanda?tipo=delivery`;
    });
    
    // Función para limpiar selección
    function limpiarSeleccion() {
        document.querySelectorAll('.mesa-card').forEach(m => {
            m.classList.remove('seleccionada', 'seleccionada-multiple');
        });
        mesasSeleccionadas = [];
        actualizarBotones();
        document.getElementById('mensajeSeleccionMultiple').classList.add('d-none');
    }
    
    // Deseleccionar mesas al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.mesa-card') && 
            !e.target.closest('.menu-icon-btn') && 
            !e.target.closest('.modal')) {
            limpiarSeleccion();
        }
    });
    
    // Manejar eliminación de mesa
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-eliminar-mesa')) {
            const btn = e.target.closest('.btn-eliminar-mesa');
            document.getElementById('mesaAEliminarId').value = btn.dataset.id;
            document.getElementById('mesaAEliminarNombre').textContent = btn.dataset.nombre;
            const modal = new bootstrap.Modal(document.getElementById('modalEliminarMesa'));
            modal.show();
        }
    });
    
    // Inicializar estado de botones
    actualizarBotones();
});