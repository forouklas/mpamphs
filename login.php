<?php
/*
================================================================================
ΑΡΧΕΙΟ: login.php
================================================================================
*/

require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escapeString($connection, $_POST['username']);
    $password = $_POST['password'];
    $hashed_password = md5($password);
    
    $login_query = "
        SELECT u.user_id, u.username, u.email, u.full_name, u.role_id, u.is_active, r.role_name
        FROM users u
        INNER JOIN roles r ON u.role_id = r.role_id
        WHERE u.username = '$username' AND u.password = '$hashed_password'
    ";
    
    $result = executeQuery($connection, $login_query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if ($user['is_active'] == 0) {
            $error_message = "Ο λογαριασμός σας είναι ανενεργός.";
        } else {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role_name'] = $user['role_name'];
            
            $update_login = "UPDATE users SET last_login = NOW() WHERE user_id = {$user['user_id']}";
            executeQuery($connection, $update_login);
            redirect('dashboard.php');
        }
    } else {
        $error_message = "Λάθος στοιχεία σύνδεσης!";
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Είσοδος - College Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #800000;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .college-logo {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            color: #800000;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header-text { text-align: center; color: #666; margin-bottom: 25px; font-size: 14px; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; color: #333; font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: 0.3s;
        }
        input:focus { border-color: #ff3333; outline: none; }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: #800000;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-login:hover { background-color: #b30000; }

        /* REGISTER SECTION */
        .register-box {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }
        .register-box p { font-size: 13px; color: #777; margin-bottom: 12px; }
        .btn-register {
            display: block;
            text-decoration: none;
            padding: 10px;
            border: 2px solid #800000;
            color: #800000;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-register:hover { background: #800000; color: white; }

        .error-box {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #999; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="college-logo">college</div>
        <p class="header-text">Portal Φοιτητών & Καθηγητών</p>
        
        <?php if (isset($error_message)): ?>
            <div class="error-box"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">ΕΙΣΟΔΟΣ</button>
        </form>

        <div class="register-box">
            <p>Δεν έχετε λογαριασμό;</p>
            <a href="register.php" class="btn-register">ΔΗΜΙΟΥΡΓΙΑ ΛΟΓΑΡΙΑΣΜΟΥ</a>
        </div>

        <a href="index.php" class="back-link">← Επιστροφή στην Αρχική</a>
    </div>

</body>
</html>