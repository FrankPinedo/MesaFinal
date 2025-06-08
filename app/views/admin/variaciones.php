<?php if (!defined('BASE_URL')) require_once __DIR__ . '/../../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MesaLista - Gestión</title>

    <!-- Logo -->
    <link rel="icon" href="<?= BASE_URL ?>/public/assets/img/logo_1.png" />

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin/fragment.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin/platos/productos.css" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />

    <!-- Iconos -->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>

    <header>
        <div class="page-loader flex-column" id="page-loader">
            <div
                class="d-flex flex-column align-items-center justify-content-center">
                <span class="spinner-border text-dark" role="status"></span>
                <span class="text-muted fs-6 fw-semibold mt-4">Cargando...</span>
            </div>
        </div>

        <div class="container-fluid p-0 flex-column" id="headerDesktop">
            <div class="linkHome_desktop p-2 py-3 w-100">
                <img src="<?= BASE_URL ?>/public/assets/img/logo.png" class="iconHome" />
                <span>Panel de Control</span>
            </div>

            <div class="profile_desktop my-2" id="btnProfile">
                <div class="btnProfile">
                    <div class="imgProfile_desktop">
                        <img
                            src="<?= BASE_URL ?>/public/assets/img/perfil_defect.jpg"
                            alt=""
                            class="iconHome"
                            style="filter: invert(1)" />
                    </div>
                    <div class="dateProfile">
                        <span class="nameAdmin"><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></span>
                        <span class="roleAdmin">Administrador</span>
                    </div>
                </div>

                <div id="containProfile" class="hidden">
                    <div class="container-fluid p-0 h-100 flex-column d-flex">
                        <div class="d-flex contain_DataProfile">
                            <div class="contain_BackProfile"></div>
                            <div class="containImgProfile">
                                <img
                                    src="<?= BASE_URL ?>/public/assets/img/perfil_defect.jpg"
                                    alt=""
                                    class="iconHome"
                                    style="filter: invert(1)" />
                            </div>
                        </div>
                        <div class="d-flex flex-column py-1">
                            <div class="d-inline w-100 text-center">
                                <span class="nameAdmin"><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></span>
                            </div>
                            <div class="d-inline w-100 text-center">
                                <span class="fw-semibold">Administrador</span>
                            </div>
                        </div>
                        <div class="formProfile p-2">
                            <form action="<?= BASE_URL ?>/admin/logout" method="post" class="px-3 py-2">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="menuItems_desktop">
                <ul class="listItems p-0 m-0">
                    <li>
                        <a href="<?= BASE_URL; ?>/admin" class="navlink" target="_blank">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> home </span>
                            <span class="nameItem">Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>/admin/empresa" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> storefront </span>
                            <span class="nameItem">Empresa</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>/admin/platos" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> flatware </span>
                            <span class="nameItem">Platos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>/admin/bebidas" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> liquor </span>
                            <span class="nameItem">Bebidas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL; ?>/admin/usuarios" class="navlink">
                            <div class="line"></div>
                            <span class="material-symbols-outlined"> groups </span>
                            <span class="nameItem">Usuarios</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="footer_desktop mt-auto">
                <button id="btn_Desktop" class="btn">
                    <span class="material-symbols-outlined"> keyboard_tab_rtl </span>
                </button>
            </div>
        </div>

        <div class="container-fluid p-0" id="headerMobile">
            <div class="contenedorResponsivo">
                <div class="linkHome_Mobile">
                    <img src="<?= BASE_URL ?>/public/assets/img/logo.png" alt="" class="iconHome" />
                    <span>Panel de Control</span>
                </div>

                <div class="ms-auto">
                    <button
                        class="btn"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#menuResponsive"
                        aria-expanded="false"
                        aria-controls="menuResponsive">
                        <span class="material-symbols-outlined m-auto"> menu </span>
                    </button>
                </div>
            </div>

            <div class="collapse w-100" id="menuResponsive">
                <div class="profile_desktop my-2">
                    <div class="btnProfile">
                        <div class="imgProfile_desktop">
                            <img
                                src="<?= BASE_URL ?>/public/assets/img/perfil_defect.jpg"
                                alt=""
                                class="iconHome"
                                style="filter: invert(1)" />
                        </div>
                        <div class="dateProfile">
                            <span class="nameAdmin"><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="menuItems_desktop">
                    <ul class="listItems p-0 m-0">
                        <li>
                            <a href="<?= BASE_URL; ?>/admin" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> home </span>
                                <span class="nameItem">Inicio</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>/admin/empresa" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> storefront </span>
                                <span class="nameItem">Empresa</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>/admin/platos" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> flatware </span>
                                <span class="nameItem">Platos</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>/admin/bebidas" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> liquor </span>
                                <span class="nameItem">Bebidas</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL; ?>/admin/usuarios" class="navlink">
                                <div class="line"></div>
                                <span class="material-symbols-outlined"> groups </span>
                                <span class="nameItem">Usuarios</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container mx-auto px-4 py-8">
            <div class="container mx-auto px-4 py-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">Gestión de Productos</h1>

                <div class="flex space-x-4 mb-8">
                    <button id="btnBebida" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Bebida</button>
                    <button id="btnPlato" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">Plato</button>
                    <button id="btnTamano" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition">Tamaño</button>
                </div>

                <!-- Sección Bebida -->
                <div id="seccionBebida" class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Formulario de Bebida</h2>
                        <form action="<?= BASE_URL ?>/admin/guardarBebida" method="post" class="max-w-md">
                            <div class="mb-4">
                                <label for="nombreBebida" class="block text-gray-700 mb-2">Nombre de Bebida</label>
                                <input type="text" id="nombreBebida" name="nombre" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                Guardar Bebida
                            </button>
                        </form>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold mb-4">Tabla de Bebidas</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="py-2 px-4 border-b">ID</th>
                                        <th class="py-2 px-4 border-b">Nombre</th>
                                        <th class="py-2 px-4 border-b">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tiposBebida as $bebida): ?>
                                        <tr>
                                            <td class="py-2 px-4 border-b "><?= htmlspecialchars($bebida['id']) ?></td>
                                            <td class="py-2 px-4 border-b "><?= htmlspecialchars($bebida['nombre']) ?></td>
                                            <td class="py-2 px-4 border-b ">
                                                <button class="text-blue-500 hover:text-blue-700 mr-2">Editar</button>
                                                <button class="text-red-500 hover:text-red-700">Eliminar</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sección Plato -->
                <div id="seccionPlato" class="bg-white p-6 rounded-lg shadow-md mb-8 hidden">
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Formulario de Plato</h2>
                        <form action="<?= BASE_URL ?>/admin/guardarPlato" method="post" class="max-w-md">
                            <div class="mb-4">
                                <label for="nombrePlato" class="block text-gray-700 mb-2">Nombre de Plato</label>
                                <input type="text" id="nombrePlato" name="nombre" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                Guardar Plato
                            </button>
                        </form>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold mb-4">Tabla de Platos</h2>
                        <div class="overflow-x-auto">

                            <table class="min-w-full bg-white border border-gray-200 mb-6">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="py-2 px-4 border-b">ID</th>
                                        <th class="py-2 px-4 border-b">Nombre</th>
                                        <th class="py-2 px-4 border-b">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tiposPlato as $plato): ?>
                                        <tr>
                                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($plato['id']) ?></td>
                                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($plato['nombre']) ?></td>
                                            <td class="py-2 px-4 border-b">
                                                <button class="text-blue-500 hover:text-blue-700 mr-2">Editar</button>
                                                <button class="text-red-500 hover:text-red-700">Eliminar</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <!-- Sección Tamaño -->
                <div id="seccionTamano" class="bg-white p-6 rounded-lg shadow-md mb-8 hidden">
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Formulario de Tamaño</h2>
                        <form action="<?= BASE_URL ?>/admin/guardarTamano" method="post" class="max-w-md">
                            <div class="mb-4">
                                <label for="nombreTamano" class="block text-gray-700 mb-2">Nombre de Tamaño</label>
                                <input type="text" id="nombreTamano" name="nombre" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition">
                                Guardar Tamaño
                            </button>
                        </form>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold mb-4">Tabla de Tamaños</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="py-2 px-4 border-b">ID</th>
                                        <th class="py-2 px-4 border-b">Nombre</th>
                                        <th class="py-2 px-4 border-b">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tamanos as $tam): ?>
                                        <tr>
                                            <td class="py-2 px-4 border-b "><?= htmlspecialchars($tam['id']) ?></td>
                                            <td class="py-2 px-4 border-b "><?= htmlspecialchars($tam['nombre']) ?></td>
                                            <td class="py-2 px-4 border-b ">
                                                <button class="text-blue-500 hover:text-blue-700 mr-2">Editar</button>
                                                <button class="text-red-500 hover:text-red-700">Eliminar</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para deshabilitar producto -->
    <div class="modal fade" id="disableModal" tabindex="-1" aria-labelledby="disableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disableModalLabel">Deshabilitar Guarnición</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas deshabilitar la guarnición <span id="disableProductName" class="font-semibold"></span>?</p>
                    <p class="text-red-500 mt-2">Esta guarnición ya no estará disponible para la venta.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger">Confirmar Deshabilitar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para habilitar producto -->
    <div class="modal fade" id="enableModal" tabindex="-1" aria-labelledby="enableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enableModalLabel">Habilitar Guarnición</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas habilitar la guarnicióon <span id="enableProductName" class="font-semibold"></span>?</p>
                    <p class="text-green-500 mt-2">Esta guarnición estará disponible para la venta.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success">Confirmar Habilitar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Deshabilitar
            const disableModal = document.getElementById('disableModal');
            disableModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const productId = button.getAttribute('data-product-id');
                const productName = button.getAttribute('data-product-name');
                disableModal.querySelector('#disableProductName').textContent = productName;

                // Guardar ID en botón de confirmar
                const confirmBtn = disableModal.querySelector('.btn-danger');
                confirmBtn.onclick = function() {
                    window.location.href = `?accion=deshabilitar&id=${productId}`;
                };
            });

            // Habilitar
            const enableModal = document.getElementById('enableModal');
            enableModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const productId = button.getAttribute('data-product-id');
                const productName = button.getAttribute('data-product-name');
                enableModal.querySelector('#enableProductName').textContent = productName;

                // Guardar ID en botón de confirmar
                const confirmBtn = enableModal.querySelector('.btn-success');
                confirmBtn.onclick = function() {
                    window.location.href = `?accion=habilitar&id=${productId}`;
                };
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnBebida = document.getElementById('btnBebida');
            const btnPlato = document.getElementById('btnPlato');
            const btnTamano = document.getElementById('btnTamano');

            const seccionBebida = document.getElementById('seccionBebida');
            const seccionPlato = document.getElementById('seccionPlato');
            const seccionTamano = document.getElementById('seccionTamano');

            // Mostrar sección Bebida por defecto
            seccionBebida.classList.remove('hidden');

            btnBebida.addEventListener('click', function() {
                seccionBebida.classList.remove('hidden');
                seccionPlato.classList.add('hidden');
                seccionTamano.classList.add('hidden');
            });

            btnPlato.addEventListener('click', function() {
                seccionBebida.classList.add('hidden');
                seccionPlato.classList.remove('hidden');
                seccionTamano.classList.add('hidden');
            });

            btnTamano.addEventListener('click', function() {
                seccionBebida.classList.add('hidden');
                seccionPlato.classList.add('hidden');
                seccionTamano.classList.remove('hidden');
            });
        });
    </script>

    <script src="<?= BASE_URL ?>/public/assets/js/admin/fragment.js"></script>

    <script src="<?= BASE_URL ?>/public/assets/js/admin/agregar.js"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>

</body>

</html>