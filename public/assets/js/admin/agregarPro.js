// productManagement.js
document.addEventListener("DOMContentLoaded", function () {
  // Manejo de campos específicos según tipo de producto
  const tipoProductoSelect = document.querySelector(
    'select[name="tipo_producto_id"]'
  );
  if (tipoProductoSelect) {
    tipoProductoSelect.addEventListener("change", function () {
      const tipo = this.value;
      document.querySelectorAll("#tipo-especifico > div").forEach((div) => {
        div.classList.add("hidden");
      });

      if (tipo === "1") {
        document.querySelector(".bebida-fields").classList.remove("hidden");
      } else if (tipo === "2") {
        document.querySelector(".plato-fields").classList.remove("hidden");
      } else if (tipo === "4") {
        document.querySelector(".combo-fields").classList.remove("hidden");
      }
    });
  }

  // Modales para habilitar/deshabilitar productos
  const disableModal = document.getElementById("disableModal");
  if (disableModal) {
    disableModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;
      const productId = button.getAttribute("data-product-id");
      const productName = button.getAttribute("data-product-name");
      disableModal.querySelector("#disableProductName").textContent =
        productName;

      const confirmBtn = disableModal.querySelector(".btn-danger");
      confirmBtn.onclick = function () {
        window.location.href = `?accion=deshabilitar&id=${productId}`;
      };
    });
  }

  const enableModal = document.getElementById("enableModal");
  if (enableModal) {
    enableModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;
      const productId = button.getAttribute("data-product-id");
      const productName = button.getAttribute("data-product-name");
      enableModal.querySelector("#enableProductName").textContent = productName;

      const confirmBtn = enableModal.querySelector(".btn-success");
      confirmBtn.onclick = function () {
        window.location.href = `?accion=habilitar&id=${productId}`;
      };
    });
  }

  // Manejo de componentes de combos
  const btnAgregar = document.getElementById("agregar-componente");
  const contenedor = document.getElementById("combo-componentes");
  const plantilla = document.getElementById("plantilla-componente");

  if (btnAgregar && contenedor && plantilla) {
    btnAgregar.addEventListener("click", () => {
      const clon = plantilla.content.cloneNode(true);
      contenedor.appendChild(clon);
    });

    document.addEventListener("click", function (e) {
      if (e.target.classList.contains("eliminar-componente")) {
        e.target.closest(".componente").remove();
      }
    });
  }
});
