-- ========================================================
-- 1. ΚΑΘΑΡΙΣΜΟΣ ΚΑΙ ΔΗΜΙΟΥΡΓΙΑ ΒΑΣΗΣ
-- ========================================================
CREATE DATABASE IF NOT EXISTS role_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE role_system;

-- Διαγραφή πινάκων αν υπάρχουν (για "φρέσκια" εγκατάσταση)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- 2. ΔΗΜΙΟΥΡΓΙΑ ΠΙΝΑΚΩΝ
-- ========================================================

-- Πίνακας Ρόλων
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    role_description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Πίνακας Δικαιωμάτων
CREATE TABLE permissions (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(50) NOT NULL UNIQUE,
    permission_description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Πίνακας Χρηστών
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role_id INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Πίνακας Σύνδεσης Ρόλων-Δικαιωμάτων (Many-to-Many)
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 3. ΕΙΣΑΓΩΓΗ ΔΕΔΟΜΕΝΩΝ
-- ========================================================

-- Δημιουργία Ρόλων (Professor & Student)
INSERT INTO roles (role_name, role_description) VALUES
('professor', 'Καθηγητής - Πλήρη Δικαιώματα Διαχείρισης'),
('student', 'Μαθητής - Βασικά Δικαιώματα Προβολής');

-- Δημιουργία Δικαιωμάτων
INSERT INTO permissions (permission_name, permission_description) VALUES
('view_users', 'Προβολή Λίστας Χρηστών'),
('edit_user', 'Επεξεργασία Χρήστη'),
('view_content', 'Προβολή Περιεχομένου'),
('create_content', 'Δημιουργία Περιεχομένου'),
('delete_content', 'Διαγραφή Περιεχομένου'),
('grade_students', 'Βαθμολόγηση Μαθητών');

-- ΑΝΑΘΕΣΗ ΔΙΚΑΙΩΜΑΤΩΝ (Δυναμικά)

-- PROFESSOR: Παίρνει όλα τα δικαιώματα
INSERT INTO role_permissions (role_id, permission_id) 
SELECT (SELECT role_id FROM roles WHERE role_name = 'professor'), permission_id 
FROM permissions;

-- STUDENT: Παίρνει μόνο view_content (Προβολή)
INSERT INTO role_permissions (role_id, permission_id) 
VALUES (
    (SELECT role_id FROM roles WHERE role_name = 'student'), 
    (SELECT permission_id FROM permissions WHERE permission_name = 'view_content')
);

-- Δημιουργία Δοκιμαστικών Χρηστών
INSERT INTO users (username, email, password, full_name, role_id) VALUES
('Kathigiths', 'prof@example.com', MD5('PROF2025'), 'Ο Καθηγητής μας', 
    (SELECT role_id FROM roles WHERE role_name = 'professor')),
('Mathiths', 'student@example.com', MD5('STUD2025'), 'Ο Μαθητής', 
    (SELECT role_id FROM roles WHERE role_name = 'student'));