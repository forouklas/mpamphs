<?php
/*
================================================================================
ΑΡΧΕΙΟ: register.php
ΣΚΟΠΟΣ: Εγγραφή Νέου Χρήστη (Φοιτητή ή Καθηγητή)
================================================================================
*/

require_once 'config.php';

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escapeString($connection, $_POST['username']);
    $email = escapeString($connection, $_POST['email']);
    $full_name = escapeString($connection, $_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role_id = (int)$_POST['role_id']; // 1 για Φοιτητής, 2 για Καθηγητής (ανάλογα τη βάση σου)

    // 1. Έλεγχος αν οι κωδικοί ταιριάζουν
    if ($password !== $confirm_password) {
        $error_message = "Οι κωδικοί πρόσβασης δεν ταιριάζουν!";
    } else {
        // 2. Έλεγχος αν το username ή το email υπάρχει ήδη
        $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = executeQuery($connection, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Το Username ή το Email χρησιμοποιείται ήδη!";
        } else {
            // 3. Εισαγωγή στη βάση
            $hashed_password = md5($password);
            $insert_query = "
                INSERT INTO users (username, password, email, full_name, role_id, is_active, created_at)
                VALUES ('$username', '$hashed_password', '$email', '$full_name', $role_id, 1, NOW())
            ";

            if (executeQuery($connection, $insert_query)) {
                $success_message = "Η εγγραφή ολοκληρώθηκε! Μπορείτε να συνδεθείτε.";
                // Προαιρετικά: redirect μετά από 2 δευτερόλεπτα
                // header("refresh:2;url=login.php");
            } else {
                $error_message = "Σφάλμα κατά την εγγραφή. Δοκιμάστε ξανά.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή | College Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #800000;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }
        .college-logo { text-align: center; font-size: 28px; font-weight: bold; color: #800000; text-transform: uppercase; }
        .header-text { text-align: center; color: #666; margin-bottom: 25px; font-size: 14px; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; color: #333; font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: 0.3s;
        }
        input:focus, select:focus { border-color: #ff3333; outline: none; }
        
        .btn-register {
            width: 100%;
            padding: 14px;
            background-color: #800000;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-register:hover { background-color: #b30000; }

        .error-box { background: #ffebee; color: #c62828; padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #ef9a9a; }
        .success-box { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #a5d6a7; }
        
        .back-link { display: block; text-align: center; margin-top: 20px; color: #800000; text-decoration: none; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="college-logo">college portal</div>
        <p class="header-text">Δημιουργία Νέου Λογαριασμού</p>
        
        <?php if ($error_message): ?>
            <div class="error-box"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success-box"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Ονοματεπώνυμο</label>
                <input type="text" name="full_name" placeholder="π.χ. Νίκος Παπαδόπουλος" required>
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Ιδιότητα (Ρόλος)</label>
                <select name="role_id" required>
                    <option value="">Επιλέξτε...</option>
                    <option value="2">Φοιτητής</option>
                    <option value="1">Καθηγητής</option>
                </select>
            </div>

            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div>
                    <label>Επιβεβαίωση</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>

            <button type="submit" class="btn-register">ΕΓΓΡΑΦΗ</button>
        </form>

        <a href="login.php" class="back-link">Έχετε ήδη λογαριασμό; Σύνδεση</a>
    </div>

</body>
</html>