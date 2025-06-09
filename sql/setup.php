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

    // Orden correcto de archivos SQL (importante para las foreign keys)
    $sqlFiles = [
        './userInitializer.sql',
        './empresaInitializer.sql',
        './mesasInitializer.sql',
        './comandaInitializer.sql'
    ];

    // Ejecutar cada archivo SQL
    foreach ($sqlFiles as $file) {
        if (file_exists($file)) {
            echo "<br>Ejecutando $file...<br>";
            $sql = file_get_contents($file);
            
            // Dividir por punto y coma pero ignorar los que están dentro de comillas
            $queries = preg_split('/;(?=(?:[^\'"]|\'[^\']*\'|"[^"]*")*$)/', $sql);
            
            $contador = 0;
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    try {
                        $pdo->exec($query);
                        $contador++;
                    } catch (PDOException $e) {
                        // Solo mostrar error si no es de duplicados
                        if ($e->getCode() != '23000') {
                            echo "<span style='color: red;'>Error en query #$contador: " . $e->getMessage() . "</span><br>";
                        }
                    }
                }
            }
            echo "<span style='color: green;'>$file ejecutado correctamente ($contador queries).</span><br>";
        } else {
            echo "<span style='color: red;'>Archivo $file no encontrado.</span><br>";
        }
    }

    echo "<br><strong style='color: green;'>✓ Base de datos configurada correctamente!</strong><br><br>";
    echo "<div style='background-color: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    echo "<strong>Usuarios de acceso:</strong><br>";
    echo "• <strong>Admin:</strong> admin@local.com / Admin123!<br>";
    echo "• <strong>Mozo:</strong> mozo@local.com / Mozo123!<br>";
    echo "• <strong>Cocinero:</strong> cocinero@local.com / Cocinero123!<br>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<span style='color: red;'>Error crítico: " . $e->getMessage() . "</span>";
}
?>