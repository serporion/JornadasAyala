CREATE DATABASE jornadasayala;
SET NAMES UTF8;
USE jornadasayala;


CREATE TABLE roles (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users(
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         role_id INT NOT NULL,
                         name VARCHAR(255) NOT NULL,
                         email VARCHAR(255) NOT NULL UNIQUE,
                         password VARCHAR(255) NOT NULL,
                         confirmado BOOLEAN DEFAULT FALSE,
                         token VARCHAR(255) NULL,
                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         FOREIGN KEY (role_id) REFERENCES roles(id)
);





CREATE TABLE eventos (
                         id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                         tipo ENUM('conferencia', 'taller') NOT NULL,
                         nombre VARCHAR(255) NOT NULL,
                         descripcion TEXT NULL,
                         fecha DATE NOT NULL,
                         hora_inicio TIME NOT NULL,
                         duracion INT NOT NULL DEFAULT 55, -- Duración en minutos
                         lugar VARCHAR(255) NOT NULL, -- Salón de actos o aula
                         cupo_maximo INT NOT NULL
);


CREATE TABLE ponentes(
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         nombre VARCHAR(255) NOT NULL,
                         fotografia VARCHAR(255) NULL, -- URL o nombre del archivo de la fotografía
                         area_experiencia VARCHAR(255) NULL,
                         red_social VARCHAR(255) NULL
);



CREATE TABLE tipos_inscripcion (
                                   id INT AUTO_INCREMENT PRIMARY KEY,
                                   nombre VARCHAR(50) NOT NULL,
                                   precio DECIMAL(10, 2) NOT NULL
);


CREATE TABLE inscripciones(
                              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                              user_id INT NOT NULL,
                              tipo_inscripcion_id INT NOT NULL,
                              fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              FOREIGN KEY (user_id) REFERENCES users(id),
                              FOREIGN KEY (tipo_inscripcion_id) REFERENCES tipos_inscripcion(id)
);



CREATE TABLE inscripcion_eventos (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    inscripcion_id INT NOT NULL,
    evento_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE
);



CREATE TABLE eventos_ponentes (
                                  event_id INT NOT NULL,
                                  speaker_id INT NOT NULL,
                                  PRIMARY KEY (event_id, speaker_id),
                                  FOREIGN KEY (event_id) REFERENCES eventos(id),
                                  FOREIGN KEY (speaker_id) REFERENCES ponentes(id)
);


CREATE TABLE asistentes_eventos(
                                   user_id INT NOT NULL,
                                   event_id INT NOT NULL,
                                   PRIMARY KEY (user_id, event_id),
                                   FOREIGN KEY (user_id) REFERENCES users(id),
                                   FOREIGN KEY (event_id) REFERENCES eventos(id)
);


CREATE TABLE `alumnos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `dni` VARCHAR(20) UNIQUE NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE eventos
ADD COLUMN ponenteId BIGINT UNSIGNED AFTER cupo_maximo, 
ADD CONSTRAINT fk_eventos_ponente 
FOREIGN KEY (ponenteId) REFERENCES ponentes(id)
ON DELETE CASCADE; 

#INSERT INTO `alumnos` (`nombre`, `email`, `dni`) VALUES ('Orion Tillman', 'ewald79@example.net', '12345678A'), ('Nicolette Marvin', 'asa07@example.org', '87654321B'),('Kailee Haag', 'byost@example.com', '11223344C');
