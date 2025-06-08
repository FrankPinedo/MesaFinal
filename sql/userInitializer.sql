-- Crear tabla de roles
CREATE TABLE
    IF NOT EXISTS rol (
        id_rol INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL UNIQUE
    );

-- Crear tabla de usuarios
CREATE TABLE
    IF NOT EXISTS usuario (
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        nombres VARCHAR(100),
        apellidos VARCHAR(100),
        correo VARCHAR(100) UNIQUE,
        contraseña VARCHAR(255),
        estado BOOLEAN DEFAULT 1,
        codigo VARCHAR(8),
        dni VARCHAR(8) NOT NULL,
        digito CHAR(1) NOT NULL
    );

-- Relación usuario-rol
CREATE TABLE
    IF NOT EXISTS user_rol (
        id_user INT,
        id_rol INT,
        PRIMARY KEY (id_user, id_rol),
        FOREIGN KEY (id_user) REFERENCES usuario (id_user),
        FOREIGN KEY (id_rol) REFERENCES rol (id_rol)
    );

-- Insertar roles si no existen
INSERT IGNORE INTO rol (id_rol, nombre)
VALUES
    (1, 'admin'),
    (2, 'mozo'),
    (3, 'cocinero');

-- Insertar usuarios por defecto si no existen
INSERT INTO
    usuario (
        nombres,
        apellidos,
        correo,
        contraseña,
        estado,
        codigo,
        dni,
        digito
    )
SELECT
    *
FROM
    (
        SELECT
            'Susy',
            'Díaz',
            'admin@local.com',
            '$2y$10$xzvZdi.4ra9WGqowDc5.OOpGzB/dNKCZlcTuJrGFNafDLDTcUUGJK',
            1,
            'ADMIN01',
            '09999999',
            '9'
    ) AS tmp
WHERE
    NOT EXISTS (
        SELECT
            correo
        FROM
            usuario
        WHERE
            correo = 'admin@local.com'
    )
LIMIT
    1;

INSERT INTO
    usuario (
        nombres,
        apellidos,
        correo,
        contraseña,
        estado,
        codigo,
        dni,
        digito
    )
SELECT
    *
FROM
    (
        SELECT
            'Mozo',
            'Prueba',
            'mozo@local.com',
            '$2y$10$o4eBY0TE3HsC2yMMHYw0XOOyV6K5byEaYTT4.cQXME00aWeVKdase',
            1,
            'MOZO02',
            '08888888',
            '8'
    ) AS tmp
WHERE
    NOT EXISTS (
        SELECT
            correo
        FROM
            usuario
        WHERE
            correo = 'mozo@local.com'
    )
LIMIT
    1;

INSERT INTO
    usuario (
        nombres,
        apellidos,
        correo,
        contraseña,
        estado,
        codigo,
        dni,
        digito
    )
SELECT
    *
FROM
    (
        SELECT
            'Cocinero',
            'Prueba',
            'cocinero@local.com',
            '$2y$10$TSj8UXULQ0JVxsOhgr6kluNTTq/2elLNKdKD6zD7QdNqlahnbSrH6',
            1,
            'COCINERO03',
            '07777777',
            '7'
    ) AS tmp
WHERE
    NOT EXISTS (
        SELECT
            correo
        FROM
            usuario
        WHERE
            correo = 'cocinero@local.com'
    )
LIMIT
    1;

-- Asignar roles a los usuarios por defecto
INSERT INTO
    user_rol (id_user, id_rol)
SELECT
    u.id_user,
    r.id_rol
FROM
    usuario u
    JOIN rol r ON r.nombre = 'admin'
WHERE
    u.correo = 'admin@local.com'
    AND NOT EXISTS (
        SELECT
            *
        FROM
            user_rol
        WHERE
            id_user = u.id_user
            AND id_rol = r.id_rol
    );

INSERT INTO
    user_rol (id_user, id_rol)
SELECT
    u.id_user,
    r.id_rol
FROM
    usuario u
    JOIN rol r ON r.nombre = 'mozo'
WHERE
    u.correo = 'mozo@local.com'
    AND NOT EXISTS (
        SELECT
            *
        FROM
            user_rol
        WHERE
            id_user = u.id_user
            AND id_rol = r.id_rol
    );

INSERT INTO
    user_rol (id_user, id_rol)
SELECT
    u.id_user,
    r.id_rol
FROM
    usuario u
    JOIN rol r ON r.nombre = 'cocinero'
WHERE
    u.correo = 'cocinero@local.com'
    AND NOT EXISTS (
        SELECT
            *
        FROM
            user_rol
        WHERE
            id_user = u.id_user
            AND id_rol = r.id_rol
    );