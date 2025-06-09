CREATE TABLE
    tipo_producto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL
    );

CREATE TABLE
    tamano (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL
    );

CREATE TABLE
    producto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        estado TINYINT (1) DEFAULT 1,
        precio DECIMAL(10, 2) NOT NULL,
        stock INT,
        tipo_producto_id INT,
        tamano_id INT DEFAULT NULL,
        imagen VARCHAR(255) DEFAULT 'sin imagen.jpg',
        FOREIGN KEY (tipo_producto_id) REFERENCES tipo_producto (id),
        FOREIGN KEY (tamano_id) REFERENCES tamano (id)
    );

CREATE TABLE
    tipo_bebida (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL
    );

CREATE TABLE
    tipo_plato (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL
    );

CREATE TABLE
    producto_bebida (
        producto_id INT PRIMARY KEY,
        tipo_bebida_id INT,
        FOREIGN KEY (producto_id) REFERENCES producto (id),
        FOREIGN KEY (tipo_bebida_id) REFERENCES tipo_bebida (id)
    );

CREATE TABLE
    producto_plato (
        producto_id INT PRIMARY KEY,
        tipo_plato_id INT,
        FOREIGN KEY (producto_id) REFERENCES producto (id),
        FOREIGN KEY (tipo_plato_id) REFERENCES tipo_plato (id)
    );

CREATE TABLE
    guarnicion (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10, 2),
        estado TINYINT (1) DEFAULT 1,
        stock INT,
        imagen VARCHAR(255) DEFAULT 'sin imagen.jpg'
    );

CREATE TABLE
    producto_guarnicion (
        producto_id INT,
        guarnicion_id INT,
        PRIMARY KEY (producto_id, guarnicion_id),
        FOREIGN KEY (producto_id) REFERENCES producto (id),
        FOREIGN KEY (guarnicion_id) REFERENCES guarnicion (id)
    );

CREATE TABLE
    combo_componentes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        combo_id INT,
        producto_id INT,
        obligatorio TINYINT (1) DEFAULT 1,
        cantidad INT DEFAULT 1,
        grupo VARCHAR(50),
        FOREIGN KEY (combo_id) REFERENCES producto (id),
        FOREIGN KEY (producto_id) REFERENCES producto (id)
    );

CREATE TABLE
    tipo_entrega (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL
    );

CREATE TABLE
    comanda (
        id INT AUTO_INCREMENT PRIMARY KEY,
        estado VARCHAR(20) DEFAULT 'pendiente',
        tipo_entrega_id INT,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        extensiones INT DEFAULT (0),
        FOREIGN KEY (tipo_entrega_id) REFERENCES tipo_entrega (id)
    );

CREATE TABLE
    detalle_comanda (
        id INT AUTO_INCREMENT PRIMARY KEY,
        comanda_id INT,
        producto_id INT,
        cantidad INT NOT NULL,
        comentario TEXT,
        cancelado TINYINT(1) DEFAULT 0,
        FOREIGN KEY (comanda_id) REFERENCES comanda (id),
        FOREIGN KEY (producto_id) REFERENCES producto (id)
    );

CREATE TABLE
    detalle_comanda_guarnicion (
        id INT AUTO_INCREMENT PRIMARY KEY,
        detalle_comanda_id INT,
        guarnicion_id INT,
        FOREIGN KEY (detalle_comanda_id) REFERENCES detalle_comanda (id),
        FOREIGN KEY (guarnicion_id) REFERENCES guarnicion (id)
    );

CREATE TABLE
    detalle_comanda_combo_opciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        detalle_comanda_id INT,
        producto_id INT,
        FOREIGN KEY (detalle_comanda_id) REFERENCES detalle_comanda (id),
        FOREIGN KEY (producto_id) REFERENCES producto (id)
    );

-- Datos: Tipo de producto
INSERT INTO
    tipo_producto (nombre)
VALUES
    ('bebida'),
    ('plato'),
    ('postre'),
    ('combo');

-- Datos: Tipo de bebida
INSERT INTO
    tipo_bebida (nombre)
VALUES
    ('refresco natural'),
    ('gaseosa'),
    ('jugo'),
    ('agua mineral'),
    ('cerveza'),
    ('café');

-- Datos: Tipo de plato
INSERT INTO
    tipo_plato (nombre)
VALUES
    ('entrada'),
    ('ronda'),
    ('fondo'),
    ('ensalada'),
    ('sopa'),
    ('postre');

-- Datos: Tipo de entrega
INSERT INTO
    tipo_entrega (nombre)
VALUES
    ('delivery'),
    ('para llevar'),
    ('comedor');

-- Datos: Tipo de tamaños
INSERT INTO
    tamano (nombre)
VALUES
    ('250 ml'),
    ('330 ml'),
    ('500 ml'),
    ('1 litro'),
    ('1.5 litros'),
    ('3 litros'),
    ('individual'),
    ('mediano'),
    ('grande');

-- Datos: Guarniciones
INSERT INTO
    guarnicion (nombre, descripcion, precio, estado, stock)
VALUES
    (
        'Papas fritas',
        'Papas cortadas en bastones y fritas',
        3.50,
        1,
        100
    ),
    (
        'Ensalada fresca',
        'Mezcla de lechuga, tomate y zanahoria',
        4.00,
        1,
        50
    ),
    (
        'Arroz blanco',
        'Arroz cocido al estilo tradicional',
        2.50,
        1,
        80
    ),
    (
        'Yuca frita',
        'Yuca cortada y frita hasta dorar',
        3.80,
        1,
        60
    ),
    (
        'Plátano frito',
        'Rodajas de plátano dulce fritas',
        3.00,
        1,
        70
    );

-- Datos: Productos
INSERT INTO
    producto (
        nombre,
        descripcion,
        precio,
        stock,
        tipo_producto_id,
        tamano_id
    )
VALUES
    (
        'Ceviche de pescado',
        'Pescado fresco marinado en limón y especias',
        25.00,
        30,
        2,
        NULL
    ),
    (
        'Ronda criolla',
        'Variedad de platos criollos para compartir',
        40.00,
        20,
        2,
        NULL
    ),
    (
        'Sudado de pescado',
        'Pescado cocido en caldo con verduras',
        28.00,
        25,
        2,
        NULL
    ),
    (
        'Ensalada fresca',
        'Lechuga, tomate, zanahoria y aderezo especial',
        15.00,
        50,
        2,
        7
    ), -- individual
    (
        'Gaseosa Coca-Cola 330 ml',
        'Bebida gaseosa en botella pequeña',
        4.50,
        100,
        1,
        2
    ),
    (
        'Gaseosa Coca-Cola 1.5 litros',
        'Bebida gaseosa en botella grande',
        10.00,
        50,
        1,
        5
    ),
    (
        'Agua mineral 500 ml',
        'Agua mineral natural embotellada',
        3.00,
        80,
        1,
        3
    ),
    (
        'Cerveza Pilsen 330 ml',
        'Cerveza lager nacional',
        6.00,
        70,
        1,
        2
    ),
    (
        'Café americano',
        'Café filtrado de alta calidad',
        5.00,
        40,
        1,
        NULL
    ),
    (
        'Lomo saltado',
        'Carne de res salteada con cebolla y tomate',
        30.00,
        25,
        2,
        NULL
    ),
    (
        'Arroz chaufa',
        'Arroz frito estilo chino-peruano',
        20.00,
        30,
        2,
        7
    ), -- individual
    (
        'Alfajor de maicena',
        'Dulce tradicional relleno de manjar',
        3.50,
        100,
        3,
        NULL
    ),
    (
        'Helado de vainilla',
        'Postre frío de vainilla natural',
        7.00,
        40,
        3,
        NULL
    ),
    (
        'Combo familiar',
        'Incluye 2 rondas criollas + 4 bebidas',
        100.00,
        15,
        4,
        NULL
    ),
    (
        'Jugo de maracuyá 500 ml',
        'Jugo natural de maracuyá',
        6.00,
        60,
        1,
        3
    ),
    (
        'Sopa de pollo',
        'Caldo con pollo y verduras',
        18.00,
        40,
        2,
        NULL
    ),
    (
        'Ensalada mediana',
        'Porción mediana de ensalada fresca',
        25.00,
        35,
        2,
        8
    ), -- mediano
    (
        'Yuca frita',
        'Yuca dorada y crujiente',
        12.00,
        50,
        2,
        NULL
    ),
    (
        'Cerveza Cusqueña 1 litro',
        'Cerveza premium en botella grande',
        12.00,
        30,
        1,
        4
    ),
    (
        'Postre tres leches',
        'Bizcocho bañado en tres tipos de leche',
        8.00,
        45,
        3,
        NULL
    ),
    (
        'Combo Menú Diario',
        'Incluye un plato de fondo, una bebida y una entrada',
        35.00,
        20,
        4,
        NULL
    );

-- Datos: Relacion producto / bebida
INSERT INTO
    producto_bebida (producto_id, tipo_bebida_id)
VALUES
    (5, 2),
    (6, 2),
    (7, 4),
    (8, 5),
    (19, 5),
    (9, 6),
    (15, 3);

-- Datos: Relacion producto / plato
INSERT INTO
    producto_plato (producto_id, tipo_plato_id)
VALUES
    (1, 3),
    (2, 2),
    (3, 3),
    (4, 4),
    (10, 3),
    (11, 3),
    (16, 5),
    (17, 4),
    (18, 1);

-- Datos: Relacion producto / guarnicion
INSERT INTO
    producto_guarnicion (producto_id, guarnicion_id)
VALUES
    (10, 1),
    (10, 3),
    (3, 4),
    (1, 5),
    (11, 2),
    (2, 1),
    (2, 3),
    (2, 5);

-- Datos: Combo familiar Producto ID: 4
INSERT INTO
    combo_componentes (
        combo_id,
        producto_id,
        obligatorio,
        cantidad,
        grupo
    )
VALUES
    (14, 2, 1, 2, 'platos principales');

INSERT INTO
    combo_componentes (
        combo_id,
        producto_id,
        obligatorio,
        cantidad,
        grupo
    )
VALUES
    (14, 6, 1, 2, 'bebidas');

INSERT INTO
    combo_componentes (
        combo_id,
        producto_id,
        obligatorio,
        cantidad,
        grupo
    )
VALUES
    (14, 8, 0, 2, 'bebidas');

-- Datos: Combo familiar Producto ID: 21
INSERT INTO
    combo_componentes (
        combo_id,
        producto_id,
        obligatorio,
        cantidad,
        grupo
    )
VALUES
    (21, 10, 1, 1, 'fondo');

INSERT INTO
    combo_componentes (
        combo_id,
        producto_id,
        obligatorio,
        cantidad,
        grupo
    )
VALUES
    (21, 4, 1, 1, 'entrada');

INSERT INTO
    combo_componentes (
        combo_id,
        producto_id,
        obligatorio,
        cantidad,
        grupo
    )
VALUES
    (21, 15, 1, 1, 'bebida');

-- 1. Registrar la comanda (ej: cliente pidió en comedor)
INSERT INTO
    comanda (tipo_entrega_id, estado)
VALUES
    (3, 'pendiente');

INSERT INTO
    detalle_comanda (comanda_id, producto_id, cantidad, comentario)
VALUES
    (1, 10, 1, 'Sin cebolla, por favor'),
    (1, 21, 1, 'Combo con jugo');

INSERT INTO
    detalle_comanda_guarnicion (detalle_comanda_id, guarnicion_id)
VALUES
    (1, 1),
    (1, 3);

INSERT INTO
    detalle_comanda_combo_opciones (detalle_comanda_id, producto_id)
VALUES
    (2, 10),
    (2, 4),
    (2, 15);

-- Comanda 2
INSERT INTO
    comanda (id, tipo_entrega_id)
VALUES
    (2, 2);

-- Detalles comanda 2
INSERT INTO
    detalle_comanda (id, comanda_id, producto_id, cantidad, comentario)
VALUES
    (4, 2, 3, 1, 'Sin picante'),
    (5, 2, 6, 1, ''),
    (6, 2, 20, 1, '');

-- Guarniciones de sudado de pescado
INSERT INTO
    detalle_comanda_guarnicion (detalle_comanda_id, guarnicion_id)
VALUES
    (4, 4);

    -- Agregar columnas faltantes a la tabla comanda
