SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

--
-- Estructura de tabla para la tabla `producto`
--
CREATE TABLE
    `platos` (
        `id_plato` int  AUTO_INCREMENT PRIMARY KEY,
        `nombre` varchar(100) NOT NULL,
        `precio` decimal(10, 2) NOT NULL,
        `stock` int (11) NOT NULL,
        `descripcion` text DEFAULT NULL,
        `estado` enum ('activo', 'inactivo') DEFAULT 'activo',
        `imagen` varchar(255) DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--
INSERT INTO
    `platos` (
        `id_plato`,
        `nombre`,
        `precio`,
        `stock`,
        `descripcion`,
        `estado`,
        `imagen`
    )
VALUES
    (
        1,
        'Chaufa',
        10.00,
        10,
        'chaufa de pollo',
        'activo',
        NULL
    ),
    (
        2,
        'Chaufa',
        10.00,
        10,
        'chaufa de pollo',
        'activo',
        NULL
    ),
    (
        3,
        'ceviche',
        12.00,
        10,
        'de pota',
        'activo',
        NULL
    ),
    (
        4,
        'caldo de gallina',
        20.00,
        12,
        'porción grande ',
        'activo',
        '681c492841b7a_caldo de gallina.jpg'
    ),
    (
        5,
        'chicha morada',
        5.00,
        30,
        'litros',
        'activo',
        '681c4cf753083_receta-de-chicha-morada-de-peru-1.jpg'
    ),
    (
        6,
        'Arroz con pollo',
        12.00,
        2,
        '',
        'inactivo',
        NULL
    );

-- --------------------------------------------------------