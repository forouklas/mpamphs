<?php
/*
================================================================================
ΑΡΧΕΙΟ: logout.php (ΕΝΗΜΕΡΩΜΕΝΟ DESIGN)
================================================================================
*/

require_once 'config.php';

// ============================================
// ΚΑΤΑΣΤΡΟΦΗ SESSION
// ============================================

$_SESSION = [];

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Αποσύνδεση | College Portal</title>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #800000; /* Το σκούρο κόκκινο του College */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            padding: 50px;
            text-align: center;
            max-width: 450px;
            width: 100%;
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .icon {
            font-size: 70px;
            margin-bottom: 20px;
            display: inline-block;
            animation: wave 1.2s infinite ease-in-out;
        }
        
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-15deg); }
            75% { transform: rotate(15deg); }
        }
        
        h1 {
            color: #800000;
            font-size: 26px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .message-box {
            background: #fdf2f2;
            border: 1px solid #ffcdd2;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            color: #b71c1c;
            font-weight: 500;
        }
        
        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
        }
        
        .btn-login {
            display: block;
            padding: 14px;
            background-color: #800000;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            transition: 0.3s;
        }
        
        .btn-login:hover {
            background-color: #b30000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .loader-bar {
            height: 4px;
            width: 100%;
            background: #eee;
            margin-top: 20px;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .loader-progress {
            height: 100%;
            background: #ff3333;
            width: 0%;
            animation: progress 3s linear forwards;
        }

        @keyframes progress {
            to { width: 100%; }
        }
    </style>
    
    <meta http-equiv="refresh" content="3;url=index.php">
</head>
<body>
    <div class="container">
        <div class="icon">👋</div>
        <h1>Έγινε αποσύνδεση</h1>
        
        <div class="message-box">
            Η συνεδρία σας έληξε με ασφάλεια.
        </div>
        
        <p>
            Σας ευχαριστούμε που χρησιμοποιήσατε την πύλη μας.<br>
            Επιστροφή στην αρχική σελίδα σε λίγο...
        </p>
        
        <a href="login.php" class="btn-login">ΣΥΝΔΕΣΗ ΞΑΝΑ</a>

        <div class="loader-bar">
            <div class="loader-progress"></div>
        </div>
    </div>
</body>
</html>