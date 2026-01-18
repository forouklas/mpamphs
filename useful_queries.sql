-- ========================================================
-- ΚΑΘΑΡΙΣΜΟΣ ΒΑΣΗΣ (ΓΙΑ ΝΑ ΜΗΝ ΒΓΑΖΕΙ ΛΑΘΗ ΟΤΙ ΥΠΑΡΧΟΥΝ)
-- ========================================================
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- 1. ΔΗΜΙΟΥΡΓΙΑ ΠΙΝΑΚΩΝ (SCHEMA)
-- ========================================================

-- Πίνακας Ρόλων (Μόνο Καθηγητής & Μαθητής)
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE, 
    role_description VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Πίνακας Δικαιωμάτων (Τι μπορεί να κάνει κάποιος)
CREATE TABLE permissions (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(50) NOT NULL UNIQUE,
    permission_description VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Πίνακας Σύνδεσης (Ποιος ρόλος έχει ποια δικαιώματα)
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Πίνακας Χρηστών
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    full_name VARCHAR(100),
    role_id INT, 
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 2. ΕΙΣΑΓΩΓΗ ΔΕΔΟΜΕΝΩΝ (DATA)
-- ========================================================

-- 2.1 Εισαγωγή Ρόλων (Μόνο αυτοί οι δύο)
INSERT INTO roles (role_name, role_description) VALUES 
('professor', 'Καθηγητής - Διαχειρίζεται μαθήματα και βαθμούς'),
('student', 'Μαθητής - Βλέπει υλικό και βαθμούς');

-- 2.2 Εισαγωγή Δικαιωμάτων (Προσαρμοσμένα σε σχολείο)
INSERT INTO permissions (permission_name, permission_description) VALUES 
('view_courses', 'Προβολή μαθημάτων'),        -- ID: 1
('upload_material', 'Ανέβασμα υλικού'),       -- ID: 2
('grade_students', 'Βαθμολόγηση'),            -- ID: 3
('submit_homework', 'Υποβολή εργασίας');      -- ID: 4

-- 2.3 Ανάθεση Δικαιωμάτων στους Ρόλους

INSERT INTO role_permissions (role_id, permission_id) VALUES 
(1, 1), -- view_courses
(1, 2), -- upload_material
(1, 3); -- grade_students

INSERT INTO role_permissions (role_id, permission_id) VALUES 
(2, 1), -- view_courses
(2, 4); -- submit_homework

-- 2.4 Εισαγωγή Δοκιμαστικών Χρηστών

INSERT INTO users (username, email, password, full_name, role_id) VALUES 
(
    'kathigiths', 
    'prof@example.com', 
    MD5('PROF2025'), 
    'Xrhstos Filipopoulos', 
    1 -- Ρόλος Professor
);

INSERT INTO users (username, email, password, full_name, role_id) VALUES 
(
    'mathiths', 
    'student@example,com', 
    MD5('STUD2025'), 
    'Forhs Anyfanths', 
    2 -- Ρόλος Student
);