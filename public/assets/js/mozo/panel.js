document.addEventListener("DOMContentLoaded", function () {
    let mesaSeleccionada = null;
    const btnComanda = document.getElementById('btnComanda');
    
    // Manejar clic en mesa
    document.querySelectorAll('.mesa-card').forEach(mesa => {
        mesa.addEventListener('click', function(e) {
            // Si está en modo eliminar o juntar, no seleccionar
            if (this.classList.contains('modo-eliminar') || 
                this.classList.contains('seleccionable') ||
                this.classList.contains('modo-separar')) {
                return;
            }
            
            // Evitar selección si se hace clic en botones
            if (e.target.closest('.btn')) {
                return;
            }
            
            // Deseleccionar todas las mesas
            document.querySelectorAll('.mesa-card').forEach(m => {
                m.classList.remove('seleccionada');
            });
            
            // Seleccionar la mesa actual
            this.classList.add('seleccionada');
            mesaSeleccionada = {
                id: this.dataset.id,
                nombre: this.dataset.mesa,
                estado: this.dataset.estado
            };
            
            // Habilitar botón comanda
            btnComanda.disabled = false;
        });
    });
    
    // Manejar clic en botón Comanda
    btnComanda.addEventListener('click', function() {
        if (mesaSeleccionada) {
            // Redirigir a la vista de comanda con el ID de la mesa
            window.location.href = `${BASE_URL}/mozo/comanda/${mesaSeleccionada.id}`;
        }
    });
    
    // Deseleccionar mesa al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.mesa-card') && !e.target.closest('#btnComanda')) {
            document.querySelectorAll('.mesa-card').forEach(m => {
                m.classList.remove('seleccionada');
            });
            mesaSeleccionada = null;
            btnComanda.disabled = true;
        }
    });
});