<?php
/*
================================================================================
ΑΡΧΕΙΟ: dashboard.php (ΕΝΗΜΕΡΩΜΕΝΟ DESIGN & RBAC)
================================================================================
*/

require_once 'config.php';

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isLoggedIn()) {
    redirect('login.php');
}

// Συναρτήσεις RBAC (Κρατάμε τις δικές σου)
function hasPermission($connection, $user_id, $permission_name) {
    $query = "
        SELECT p.permission_name 
        FROM users u
        INNER JOIN role_permissions rp ON u.role_id = rp.role_id
        INNER JOIN permissions p ON rp.permission_id = p.permission_id
        WHERE u.user_id = $user_id
        AND p.permission_name = '$permission_name'
    ";
    $result = executeQuery($connection, $query);
    return mysqli_num_rows($result) > 0;
}

function getUserPermissions($connection, $user_id) {
    $permissions = [];
    $query = "
        SELECT p.permission_name, p.permission_description
        FROM users u
        INNER JOIN role_permissions rp ON u.role_id = rp.role_id
        INNER JOIN permissions p ON rp.permission_id = p.permission_id
        WHERE u.user_id = $user_id
        ORDER BY p.permission_name
    ";
    $result = executeQuery($connection, $query);
    while ($row = mysqli_fetch_assoc($result)) { $permissions[] = $row; }
    return $permissions;
}

// Ανάκτηση Στατιστικών
$stats = [];
if (hasPermission($connection, $_SESSION['user_id'], 'view_users')) {
    $query = "SELECT COUNT(*) as total FROM users";
    $result = executeQuery($connection, $query);
    $stats['users'] = mysqli_fetch_assoc($result)['total'];
}
$user_permissions = getUserPermissions($connection, $_SESSION['user_id']);
$role = $_SESSION['role_name'];
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | College Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; display: flex; flex-direction: column; min-height: 100vh; }

        /* HEADER */
        header {
            background-color: #800000;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-left { display: flex; align-items: center; gap: 20px; }
        .menu-btn { background: none; border: 1px solid white; color: white; padding: 5px 12px; cursor: pointer; border-radius: 4px; }
        .logo { font-size: 22px; font-weight: bold; text-transform: uppercase; }
        .btn-logout { background: white; color: #800000; padding: 8px 18px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; }

        /* SLIDE SIDEBAR */
        .sidebar {
            position: fixed;
            left: -260px;
            top: 70px;
            width: 260px;
            height: calc(100vh - 70px);
            background: #ff3333;
            color: white;
            transition: 0.3s;
            z-index: 999;
            padding: 20px;
        }
        .sidebar.active { left: 0; }
        .sidebar ul { list-style: none; margin-top: 20px; }
        .sidebar li { padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a { color: white; text-decoration: none; font-weight: 500; display: block; }

        /* MAIN CONTENT */
        .container { padding: 30px; max-width: 1200px; margin: 0 auto; width: 100%; }
        
        .welcome-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-left: 8px solid #800000;
        }
        .role-tag {
            display: inline-block;
            background: #ff3333;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
            font-weight: bold;
        }

        /* ACTIONS GRID */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: 0.3s;
            border-top: 4px solid #800000;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .card i { font-size: 40px; display: block; margin-bottom: 15px; }
        .card h3 { font-size: 18px; margin-bottom: 10px; }
        .card p { font-size: 13px; color: #777; }

        .disabled { opacity: 0.5; filter: grayscale(1); cursor: not-allowed; pointer-events: none; }
        
        .overlay { display: none; position: fixed; top: 70px; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 998; }
        .overlay.active { display: block; }
    </style>
</head>
<body>

    <header>
        <div class="header-left">
            <button class="menu-btn" onclick="toggleMenu()">☰ MENU</button>
            <div class="logo">college portal</div>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <span>👋 <?php echo $_SESSION['full_name']; ?></span>
            <a href="logout.php" class="btn-logout">LOGOUT</a>
        </div>
    </header>

    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

    <aside class="sidebar" id="sidebar">
        <h3>Επιλογές</h3>
        <ul>
            <li><a href="dashboard.php">🏠 Αρχική</a></li>
            <li><a href="#">📅 Πρόγραμμα</a></li>
            <li><a href="#">📩 Μηνύματα</a></li>
            <li><a href="#">⚙️ Ρυθμίσεις</a></li>
        </ul>
    </aside>

    <div class="container">
        <div class="welcome-section">
            <h2>Καλώς ήρθατε στο Dashboard</h2>
            <p>Συνδεθήκατε επιτυχώς ως <strong><?php echo $_SESSION['full_name']; ?></strong>.</p>
            <span class="role-tag"><?php echo strtoupper($role); ?></span>
        </div>

        <h3 style="margin-bottom: 20px; color: #444;">⚡ Διαθέσιμες Λειτουργίες</h3>
        
        <div class="grid">
            <?php if ($role == 'professor'): ?>
                <a href="#" class="card">
                    <i>📚</i>
                    <h3>Τα Μαθήματά μου</h3>
                    <p>Διαχείριση και οργάνωση ύλης.</p>
                </a>
                <a href="#" class="card">
                    <i>📝</i>
                    <h3>Ανάρτηση Εργασίας</h3>
                    <p>Δημιουργήστε νέες εργασίες για φοιτητές.</p>
                </a>
                <a href="#" class="card">
                    <i>✅</i>
                    <h3>Βαθμολόγηση</h3>
                    <p>Δείτε υποβολές και βάλτε βαθμούς.</p>
                </a>
            <?php else: ?>
                <a href="#" class="card">
                    <i>📖</i>
                    <h3>Μαθήματα</h3>
                    <p>Δείτε το υλικό των μαθημάτων σας.</p>
                </a>
                <a href="#" class="card">
                    <i>📤</i>
                    <h3>Υποβολή Εργασίας</h3>
                    <p>Ανεβάστε τα αρχεία των εργασιών σας.</p>
                </a>
                <a href="#" class="card">
                    <i>📊</i>
                    <h3>Οι Βαθμοί μου</h3>
                    <p>Δείτε αναλυτικά την πρόοδό σας.</p>
                </a>
            <?php endif; ?>

            <a href="users.php" class="card <?php echo !hasPermission($connection, $_SESSION['user_id'], 'view_users') ? 'disabled' : ''; ?>">
                <i>👥</i>
                <h3>Χρήστες Συστήματος</h3>
                <p>Μόνο για διαχειριστές/εξουσιοδοτημένους.</p>
            </a>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
        }
    </script>
</body>
</html>