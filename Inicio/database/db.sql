-- Primero creo la base de datos

CREATE DATABASE IF NOT EXISTS task_master;
USE task_master;

-- Creo la tabla de usuarios para guardar cuando se cree un nuevo usuario
-- Necesaria para cumplir con el requisito del panel de administración y la persistencia de datos por usuario

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Almacenar siempre el hash, no texto plano
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ahora creo la tabla de asignaturas para que el usuario pueda agregar sus asignaturas. Representa los contenedores donde se insertarán las tareas

CREATE TABLE asignaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    color_distintivo VARCHAR(7), -- Para personalizar el contenedor visualmente
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Ahora creo la tabla de tareas para que el usuario pueda agregar sus tareas. Contiene la información específica de cada actividad académica, incluyendo el semáforo de prioridad y la fecha límite para el contador

CREATE TABLE tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asignatura_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT, -- Opcional según tu abstracto
    fecha_limite DATETIME NOT NULL, -- Crucial para tu contador en retroceso
    prioridad ENUM('alta', 'media', 'baja') NOT NULL, -- La prioridad de la tarea (rojo, amarillo, verde)
    completada BOOLEAN DEFAULT FALSE, -- Para marcar una vez finalizada
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id) ON DELETE CASCADE
);

-- Ahora creo el usuario para conectarlo luego con las otras paginas

CREATE USER 
'Dominique'@'localhost' 
IDENTIFIED  BY 'Dominique123$';

GRANT USAGE ON *.* TO 'Dominique'@'localhost';

ALTER USER 'Dominique'@'localhost' 
REQUIRE NONE 
WITH MAX_QUERIES_PER_HOUR 0 
MAX_CONNECTIONS_PER_HOUR 0 
MAX_UPDATES_PER_HOUR 0 
MAX_USER_CONNECTIONS 0;

GRANT ALL PRIVILEGES ON task_master.* 
TO 'Dominique'@'localhost';

FLUSH PRIVILEGES;