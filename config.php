<?php
/*
================================================================================
ΑΡΧΕΙΟ: config.php
ΣΚΟΠΟΣ: Σύνδεση με τη Βάση Δεδομένων και Βασικές Ρυθμίσεις
================================================================================
*/

// ============================================
// ΡΥΘΜΙΣΕΙΣ ΒΑΣΗΣ ΔΕΔΟΜΕΝΩΝ
// ============================================

define('DB_HOST', 'localhost');      // Διακομιστής MySQL
define('DB_USER', 'root');           // Όνομα χρήστη
define('DB_PASS', '');               // Κωδικός πρόσβασης
define('DB_NAME', 'role_system');    // Όνομα βάσης δεδομένων

// ============================================
// ΣΥΝΔΕΣΗ ΜΕ ΤΗ ΒΑΣΗ
// ============================================

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Έλεγχος επιτυχίας σύνδεσης
if (!$connection) {
    die("
    <div style='font-family: Arial; padding: 20px; background: #ff4444; color: white; border-radius: 5px;'>
        <h2>⚠️ Σφάλμα Σύνδεσης</h2>
        <p>Αποτυχία σύνδεσης στη βάση δεδομένων: " . mysqli_connect_error() . "</p>
    </div>
    ");
}

// Ορισμός χαρακτήρων UTF-8
mysqli_set_charset($connection, 'utf8mb4');

// ============================================
// ΕΝΑΡΞΗ SESSION
// ============================================
// Απαιτείται για τη διατήρηση των στοιχείων χρήστη

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// ΒΟΗΘΗΤΙΚΕΣ ΣΥΝΑΡΤΗΣΕΙΣ
// ============================================

/**
 * Εκτέλεση SQL ερωτήματος και επιστροφή αποτελεσμάτων
 */
function executeQuery($connection, $query) {
    $result = mysqli_query($connection, $query);
    
    if (!$result) {
        die("Σφάλμα στο ερώτημα: " . mysqli_error($connection));
    }
    
    return $result;
}

/**
 * Ασφαλής διαφυγή χαρακτήρων για queries
 */
function escapeString($connection, $string) {
    return mysqli_real_escape_string($connection, $string);
}

/**
 * Έλεγχος αν ο χρήστης είναι συνδεδεμένος
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Ανακατεύθυνση σε σελίδα
 */
function redirect($page) {
    header("Location: " . $page);
    exit();
}

/**
 * Εμφάνιση μηνύματος
 */
function showMessage($message, $type = 'info') {
    $colors = [
        'success' => '#4CAF50',
        'error' => '#f44336',
        'warning' => '#ff9800',
        'info' => '#2196F3'
    ];
    
    $icons = [
        'success' => '✓',
        'error' => '✗',
        'warning' => '⚠',
        'info' => 'ℹ'
    ];
    
    echo "
    <div style='
        padding: 15px 20px;
        margin: 20px 0;
        background: {$colors[$type]};
        color: white;
        border-radius: 5px;
        font-family: Arial;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    '>
        <strong>{$icons[$type]}</strong> {$message}
    </div>
    ";
}

?>
