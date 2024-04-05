-- Borrar la base de datos si existe
DROP DATABASE IF EXISTS PerrosPerdidos;

-- Crear Base de Datos y usarla
CREATE DATABASE IF NOT EXISTS PerrosPerdidos;
USE PerrosPerdidos;

-- Crear la tabla Usuarios
CREATE TABLE IF NOT EXISTS Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    correoElectronico VARCHAR(50) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'admin') DEFAULT 'usuario' NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    direccion VARCHAR(100) NOT NULL,
    visible ENUM('si', 'no') DEFAULT 'si',
	fotoPerfil VARCHAR(255)
);

-- Crear la tabla Perros
CREATE TABLE IF NOT EXISTS Perros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    raza VARCHAR(50),
    edad INT,
    collar ENUM('si', 'no') DEFAULT 'no',
    chip ENUM('si', 'no') DEFAULT 'no',
    lugarPerdido VARCHAR(100),
    fechaPerdido DATE,
    lugarEncontrado VARCHAR(100),
    fechaEncontrado DATE,
    estado ENUM('perdido', 'encontrado', 'en adopción', 'con dueño') DEFAULT 'con dueño',
    lugar VARCHAR(100),
    fechaUltimaActualizacion DATETIME,
    foto VARCHAR(255),
    color VARCHAR(20),
    tamano ENUM('pequeño', 'mediano', 'grande'),
    descripcion TEXT,
    idDueno INT,
    FOREIGN KEY (idDueno) REFERENCES Usuarios(id) ON DELETE CASCADE
);

-- Crear la nueva tabla Publicaciones
CREATE TABLE IF NOT EXISTS Publicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    idAutor INT,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    tipo ENUM('encontrado', 'perdido', 'en adopción', 'otras') NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    meGusta INT DEFAULT 0,
    foto VARCHAR(255),
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (idAutor) REFERENCES Usuarios(id) ON DELETE SET NULL
);

-- Crear la tabla MeGustasPublicacion
CREATE TABLE IF NOT EXISTS MeGustasPublicacion (
    idUsuario INT,
    idPublicacion INT,    
    PRIMARY KEY (idUsuario, idPublicacion),
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (idPublicacion) REFERENCES Publicaciones(id) ON DELETE CASCADE
);

-- Crear la tabla Comentarios
CREATE TABLE IF NOT EXISTS Comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    idPublicacion INT,
    idAutor INT,
    texto TEXT NOT NULL,
    fecha DATETIME,
    meGusta INT DEFAULT 0,
    foto VARCHAR(255),
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (idPublicacion) REFERENCES Publicaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (idAutor) REFERENCES Usuarios(id) ON DELETE SET NULL
);

-- Crear la tabla MeGustasComentario
CREATE TABLE IF NOT EXISTS MeGustasComentario (
    idUsuario INT,
    idComentario INT,
    PRIMARY KEY (idUsuario, idComentario),
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (idComentario) REFERENCES Comentarios(id) ON DELETE CASCADE
);

-- Insertar registros en la tabla Usuarios
INSERT INTO Usuarios (nombre, correoElectronico, contrasena, rol, telefono, direccion, visible, fotoPerfil)
VALUES
    ('John Doe', 'john@example.com', '$2y$10$Z8gZThkDlHisezUatbJ0b.YJkC1pSy89xtS2vxSghEFKNs0p03n16', 'usuario', '123456789', '123 Main St', 'si', 'John Doe.jpg'),
    ('Jane Smith', 'jane@example.com', '$2y$10$0tLCnFBLLPODxQ6G295BI.lR039Ja3OsUjtzwZdReAHYpfBa7g8/G', 'usuario', '987654321', '456 Elm St', 'no', 'Jane Smith.jpg'),
    ('Admin', 'admin@example.com', '$2y$10$zZxHmlO5eWl2LeuDIrLz1.uiN0i15tSDOeP1EEY1tWmIHuO7sqdzi', 'admin', 656666777, '789 Palm St', 'si', 'Admin.jpg'),
    ('Sarah Johnson', 'sarah@example.com', '$2y$10$HoOrEH5KONBPFUW8.elQaeOcviPmOF4yRl.QZkvcdQkvfH8NFcO4K', 'usuario', '555555555', '789 Oak St', 'no', 'Sarah Johnson.jpg'),
    ('Mike Brown', 'mike@example.com', '$2y$10$XnJzjqB4w5oJ3oTDgt4Gv.dJAwVNoRTkOXq4n88m13.vEHEIJNlj2', 'usuario', '888888888', '321 Maple St', 'si', 'Mike Brown.jpg');

-- Insertar registros en la tabla Perros
INSERT INTO Perros (nombre, raza, edad, collar, chip, lugarPerdido, fechaPerdido, lugarEncontrado, fechaEncontrado, estado, lugar, fechaUltimaActualizacion, foto, color, tamano, descripcion, idDueno)
VALUES
    ('Max', 'Labrador Retriever', 3, 'si', 'si', 'Parque Central', '2022-12-31', 'N/A', NULL, 'perdido', 'N/A', '2023-01-01 10:00:00', 'max1.jpg', 'Negro', 'grande', 'Perro perdido en el parque', 1),
    ('Luna', 'Golden Retriever', 2, 'si', 'si', 'N/A', NULL, 'Vecindario Sur', '2023-01-01', 'encontrado', 'Avilés', '2023-01-02 15:30:00', 'luna2.jpg', 'Dorado', 'mediano', 'Perro encontrado en el vecindario', 2),
    ('Rocky', 'Bulldog Francés', 1, 'si', 'si', 'N/A', NULL, 'N/A', NULL, 'en adopcion', 'Oviedo', '2023-01-03 09:45:00', 'rocky1.jpg', 'Blanco y marrón', 'pequeño', 'Perro en adopción en Oviedo', 1),
    ('Bella', 'Husky Siberiano', 4, 'si', 'si', 'N/A', NULL, 'Parque Norte', '2023-01-04', 'encontrado', 'Gijón', '2023-01-05 12:00:00', 'bella3.jpg', 'Gris y blanco', 'grande', 'Perro encontrado en el parque', 3),
    ('Charlie', 'Border Collie', 2, 'si', 'si', 'Plaza Mayor', '2023-01-06', 'N/A', NULL, 'perdido', 'N/A', '2023-01-07 14:30:00', 'charlie4.jpg', 'Negro y blanco', 'mediano', 'Perro perdido en la plaza', 4),
    ('Lucy', 'Poodle', 3, 'si', 'si', 'N/A', NULL, 'Vecindario Este', '2023-01-08', 'encontrado', 'Gijón', '2023-01-09 09:00:00', 'lucy5.jpg', 'Blanco', 'pequeño', 'Perro encontrado en el vecindario', 5),
    ('Molly', 'Labrador Retriever', 2, 'no', 'si', 'N/A', NULL, 'N/A', NULL, 'en adopcion', 'Gijón', '2023-01-10 11:30:00', 'molly3.jpg', 'Chocolate', 'grande', 'Perro en adopción en Gijón', 3),
    ('Buddy', 'Golden Retriever', 3, 'si', 'no', 'N/A', NULL, 'N/A', NULL, 'en adopcion', 'Oviedo', '2023-01-11 16:45:00', 'buddy2.jpg', 'Dorado', 'mediano', 'Perro en adopción en Oviedo', 2),
    ('Lola', 'Husky Siberiano', 1, 'si', 'si', 'Plaza Principal', '2023-01-12', 'N/A', NULL, 'perdido', 'N/A', '2023-01-13 08:00:00', 'lola2.jpg', 'Gris y blanco', 'grande', 'Perro perdido en la plaza', 2);

-- Insertar registros en la tabla Publicaciones
INSERT INTO Publicaciones (idUsuario, idAutor, titulo, contenido, tipo, fecha, foto)
VALUES
    (1, 1, 'Perro perdido en el parque', 'Mi perro se perdió ayer en el parque', 'perdido', '2023-07-02 08:30:32', 'dog.jpg'),
    (2, 2, 'Perro encontrado en el vecindario', 'Encontré un perro perdido en el vecindario', 'encontrado', '2023-07-03 10:14:50', 'dog.jpg'),
    (1, 1, 'Perro en adopción', 'Tengo un perro para dar en adopción', 'en adopcion', '2023-07-04 15:44:26', 'dog.jpg'),
    (1, 1, 'Nueva tienda', 'Nueva tienda de Kiwoko en Santander', 'otras', '2023-07-06 12:34:56', 'publicacion_4.jpg');
    
    INSERT INTO MeGustasPublicacion (idUsuario, idPublicacion)
VALUES
    (2, 1),
    (1, 2),
    (5, 3);
    
-- Insertar registros en la tabla Comentarios
INSERT INTO Comentarios (idUsuario, idAutor, idPublicacion, texto, fecha, foto)
VALUES
    (2, 2, 1, 'Espero que lo encuentres pronto', '2023-01-01 10:05:00', 'dog.jpg'),
    (1, 1, 2, '¡Qué buena noticia!', '2023-01-02 15:35:00', 'dog.jpg'),
    (3, 3, 3, 'Me interesa adoptar al perro', '2023-01-03 10:00:00', 'dog.jpg');

-- Insertar registros en la tabla MeGustasComentario
INSERT INTO MeGustasComentario (idUsuario, idComentario)
VALUES
    (1, 1),
    (2, 2),
    (3, 3);

    
    
    

   
