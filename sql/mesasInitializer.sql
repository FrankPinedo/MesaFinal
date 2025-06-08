CREATE TABLE IF NOT EXISTS mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado ENUM('libre', 'reservado', 'esperando', 'pagando', 'combinada') NOT NULL DEFAULT 'libre'
);

-- Datos de ejemplo
INSERT INTO mesas (nombre, estado) VALUES
('M1', 'reservado'),
('M2', 'esperando'),
('M3', 'pagando'),
('M4', 'libre'),
('M5', 'libre'),
('C1', 'combinada'), 
('M8', 'libre');