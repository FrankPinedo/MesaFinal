document.addEventListener("DOMContentLoaded", function () {
  const btnAgregarMesa = document.getElementById("btnAgregarMesa");
  const formAgregarMesa = document.getElementById("formAgregarMesa");
  const btnQuitarMesa = document.getElementById("btnQuitarMesa");
  const mensajeEliminar = document.getElementById("mensajeEliminarMesa");
  const btnJuntar = document.getElementById("btnJuntarMesas");
  const mensajeJuntar = document.getElementById("mensajeJuntarMesas");
  const btnSeparar = document.getElementById("btnSepararMesas");
  const mensajeSeparar = document.getElementById("mensajeSepararMesas");

  let modoEliminarActivo = false;
  let modoSeparar = false;
  let seleccionadasParaJuntar = [];

  // ✅ Agregar mesa
  if (btnAgregarMesa && formAgregarMesa) {
    btnAgregarMesa.addEventListener("click", function () {
      formAgregarMesa.submit();
    });
  }

  // ✅ Activar/desactivar modo eliminar
  if (btnQuitarMesa) {
    btnQuitarMesa.addEventListener("click", function () {
      modoEliminarActivo = !modoEliminarActivo;
      modoSeparar = false;
      mensajeSeparar.classList.add("d-none");
      document.querySelectorAll(".mesa-btn").forEach((mesa) => {
        mesa.classList.toggle("modo-eliminar", modoEliminarActivo);
      });

      if (modoEliminarActivo) {
        mensajeEliminar.classList.remove("d-none");
        mensajeJuntar.classList.add("d-none");
      } else {
        mensajeEliminar.classList.add("d-none");
      }
    });
  }

  // ✅ Eliminar mesa al hacer clic en modo eliminación
  document.querySelectorAll(".mesa-btn").forEach((mesa) => {
    mesa.addEventListener("click", function () {
      if (modoEliminarActivo) {
        const id = mesa.dataset.id;
        const nombre = mesa.dataset.mesa;
        document.getElementById("mesaAEliminarId").value = id;
        document.getElementById("mesaAEliminarNombre").textContent = nombre;

        const modal = new bootstrap.Modal(
          document.getElementById("modalEliminarMesa")
        );
        modal.show();

        mensajeEliminar.classList.add("d-none");
        modoEliminarActivo = false;
        document
          .querySelectorAll(".mesa-btn")
          .forEach((m) => m.classList.remove("modo-eliminar"));
      }

      // ✅ Separar mesa combinada
      if (modoSeparar) {
        const mesaNombre = mesa.dataset.mesa;
        const estado = mesa.dataset.estado;
        if (estado === "combinada" || mesaNombre.includes("|")) {
          const form = document.createElement("form");
          form.method = "POST";
          form.style.display = "none";

          const input = document.createElement("input");
          input.name = "separar_mesa_nombre";
          input.value = mesaNombre;

          form.appendChild(input);
          document.body.appendChild(form);
          form.submit();
        } else {
          alert("Solo puedes separar mesas combinadas.");
        }
      }
    });
  });

  // ✅ Mostrar modal al agregar mesa correctamente
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("mesa_agregada") === "1") {
    const modal = new bootstrap.Modal(
      document.getElementById("modalMesaAgregada")
    );
    modal.show();

    if (window.history.replaceState) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  }

  // ✅ Juntar mesas
  if (btnJuntar) {
    btnJuntar.addEventListener("click", () => {
      const mesasLibres = [...document.querySelectorAll(".mesa-btn")].filter(
        (m) => m.dataset.estado === "libre"
      );

      if (mesasLibres.length < 2) {
        alert("Se requieren al menos 2 mesas libres para juntarlas.");
        return;
      }

      mensajeJuntar.classList.remove("d-none");
      mensajeEliminar.classList.add("d-none");
      mensajeSeparar.classList.add("d-none");

      seleccionadasParaJuntar = [];
      mesasLibres.forEach((m) => {
        m.classList.add("seleccionable");
        m.addEventListener("click", seleccionarMesaParaJuntar);
      });
    });
  }

  function seleccionarMesaParaJuntar(e) {
    const mesa = e.currentTarget;

    if (seleccionadasParaJuntar.includes(mesa)) {
      mesa.classList.remove("seleccionada");
      seleccionadasParaJuntar = seleccionadasParaJuntar.filter(
        (m) => m !== mesa
      );
      return;
    }

    if (seleccionadasParaJuntar.length < 2) {
      mesa.classList.add("seleccionada");
      seleccionadasParaJuntar.push(mesa);
    }

    if (seleccionadasParaJuntar.length === 2) {
      mensajeJuntar.classList.add("d-none");
      document.getElementById("mesa1Id").value =
        seleccionadasParaJuntar[0].dataset.id;
      document.getElementById("mesa2Id").value =
        seleccionadasParaJuntar[1].dataset.id;
      document.getElementById("mesa1Nombre").textContent =
        seleccionadasParaJuntar[0].dataset.mesa;
      document.getElementById("mesa2Nombre").textContent =
        seleccionadasParaJuntar[1].dataset.mesa;

      const modal = new bootstrap.Modal(
        document.getElementById("modalJuntarMesas")
      );
      modal.show();

      seleccionadasParaJuntar.forEach((m) => {
        m.classList.remove("seleccionable");
        m.classList.remove("seleccionada");
      });
    }
  }

  // ✅ Separar mesas (activar modo)
  if (btnSeparar) {
    btnSeparar.addEventListener("click", () => {
      modoSeparar = !modoSeparar;
      modoEliminarActivo = false;
      mensajeEliminar.classList.add("d-none");
      mensajeJuntar.classList.add("d-none");

      if (modoSeparar) {
        mensajeSeparar.classList.remove("d-none");

        document.querySelectorAll(".mesa-btn").forEach((mesa) => {
          const nombre = mesa.dataset.mesa;
          if (nombre.includes("|")) {
            mesa.classList.add("modo-separar");
          }
        });
      } else {
        mensajeSeparar.classList.add("d-none");

        document.querySelectorAll(".mesa-btn").forEach((mesa) => {
          mesa.classList.remove("modo-separar");
        });
      }
    });
  }

  // ✅ Cambiar estado desde ícono
  document.querySelectorAll(".btn-cambiar-estado").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      const nombre = btn.dataset.nombre;
      const estado = btn.dataset.estado;

      document.getElementById("cambiarEstadoId").value = id;
      document.getElementById(
        "nombreMesaCambio"
      ).textContent = `Mesa: ${nombre}`;
    });
  });
});
