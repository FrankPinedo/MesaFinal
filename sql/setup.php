<?php
require_once '../config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "Base de datos '" . DB_NAME . "' creada o ya existe.<br>";

    // Conectarse a la nueva base
    $pdo->exec("USE " . DB_NAME);

    // Listar archivos SQL en el orden correcto
    $sqlFiles = [
        './userInitializer.sql',
        './empresaInitializer.sql',
        './mesasInitializer.sql',
        './comandaInitializer.sql'
        // Removí platosInitializer.sql porque ya está incluido en comandaInitializer como tabla producto
    ];

    // Crear tablas
    foreach ($sqlFiles as $file) {
        if (file_exists($file)) {
            echo "Ejecutando $file...<br>";
            $sql = file_get_contents($file);
            
            // Dividir por punto y coma, pero ignorar los que están dentro de comillas
            $queries = preg_split('/;(?=(?:[^\'"]|\'[^\']*\'|"[^"]*")*$)/', $sql);
            
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    try {
                        $pdo->exec($query);
                    } catch (PDOException $e) {
                        echo "Error en query: " . substr($query, 0, 100) . "...<br>";
                        echo "Mensaje: " . $e->getMessage() . "<br>";
                    }
                }
            }
            echo "$file ejecutado correctamente.<br>";
        } else {
            echo "Archivo $file no encontrado.<br>";
        }
    }

    echo "<br><strong>Base de datos configurada correctamente!</strong><br>";
    echo "Puedes acceder al sistema con estos usuarios:<br>";
    echo "- Admin: admin@local.com / password: Admin123!<br>";
    echo "- Mozo: mozo@local.com / password: Mozo123!<br>";
    echo "- Cocinero: cocinero@local.com / password: Cocinero123!<br>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}