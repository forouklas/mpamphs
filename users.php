<?php
/*
================================================================================
ΑΡΧΕΙΟ: users.php (ΕΝΗΜΕΡΩΜΕΝΟ & ΔΥΝΑΜΙΚΟ)
================================================================================
*/

require_once 'config.php';

if (!isLoggedIn()) { redirect('login.php'); }

// ΣΥΝΑΡΤΗΣΗ ΕΛΕΓΧΟΥ ΔΙΚΑΙΩΜΑΤΩΝ
function hasPermission($connection, $user_id, $permission_name) {
    $query = "
        SELECT p.permission_name FROM users u
        INNER JOIN role_permissions rp ON u.role_id = rp.role_id
        INNER JOIN permissions p ON rp.permission_id = p.permission_id
        WHERE u.user_id = $user_id AND p.permission_name = '$permission_name'
    ";
    $result = executeQuery($connection, $query);
    return mysqli_num_rows($result) > 0;
}

// ΕΛΕΓΧΟΣ ΔΙΚΑΙΩΜΑΤΟΣ ΠΡΟΣΒΑΣΗΣ
if (!hasPermission($connection, $_SESSION['user_id'], 'view_users')) {
    die("Δεν έχετε δικαίωμα πρόσβασης.");
}

// ============================================
// ΕΠΕΞΕΡΓΑΣΙΑ ΕΝΕΡΓΕΙΑΣ (Αλλαγή Κατάστασης)
// ============================================
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    if (hasPermission($connection, $_SESSION['user_id'], 'edit_user')) {
        $action = $_GET['action'];
        $target_user_id = (int)$_GET['user_id'];
        
        $new_status = ($action == 'activate') ? 1 : 0;
        $update_query = "UPDATE users SET is_active = $new_status WHERE user_id = $target_user_id";
        executeQuery($connection, $update_query);
        
        redirect('users.php');
    }
}

// ============================================
// ΑΝΑΚΤΗΣΗ ΔΕΔΟΜΕΝΩΝ (STATS & LIST)
// ============================================

// Υπολογισμός Στατιστικών
$total_q = executeQuery($connection, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($total_q)['total'];

$active_q = executeQuery($connection, "SELECT COUNT(*) as active FROM users WHERE is_active = 1");
$active_users = mysqli_fetch_assoc($active_q)['active'];

$inactive_users = $total_users - $active_users;

// Λίστα Χρηστών
$users_query = "
    SELECT u.*, r.role_name 
    FROM users u
    INNER JOIN roles r ON u.role_id = r.role_id
    ORDER BY u.created_at DESC
";
$users_result = executeQuery($connection, $users_query);

?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Διαχείριση Χρηστών | College Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f6; color: #333; }
        header { background: #800000; color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
        .btn-back { text-decoration: none; color: white; border: 1px solid white; padding: 8px 15px; border-radius: 5px; font-size: 14px; }
        
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .stats-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; border-top: 4px solid #800000; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .stat-card p { font-size: 28px; font-weight: bold; color: #800000; }

        .table-box { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table-header { background: #f8f9fa; padding: 20px; color: #800000; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fafafa; padding: 15px; text-align: left; font-size: 12px; color: #666; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; }
        
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .active-badge { background: #e8f5e9; color: #2e7d32; }
        .inactive-badge { background: #ffebee; color: #c62828; }

        .btn-action { padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; text-decoration: none; color: white; transition: 0.3s; }
        .btn-deactivate { background: #ff9800; }
        .btn-activate { background: #4caf50; }
    </style>
</head>
<body>

<header>
    <h2>👥 Διαχείριση Χρηστών</h2>
    <a href="dashboard.php" class="btn-back">← Επιστροφή</a>
</header>

<div class="container">
    <div class="stats-container">
        <div class="stat-card"><h3>Σύνολο</h3><p><?php echo $total_users; ?></p></div>
        <div class="stat-card"><h3>Ενεργοί</h3><p><?php echo $active_users; ?></p></div>
        <div class="stat-card"><h3>Ανενεργοί</h3><p><?php echo $inactive_users; ?></p></div>
    </div>

    <div class="table-box">
        <div class="table-header">📋 Λίστα Εγγεγραμμένων Χρηστών</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Χρήστης</th>
                    <th>Email</th>
                    <th>Πλήρες Όνομα</th>
                    <th>Ρόλος</th>
                    <th>Κατάσταση</th>
                    <th>Τελευταία Σύνδεση</th>
                    <th>Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><strong><?php echo $user['username']; ?></strong></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['full_name']; ?></td>
                    <td><span style="font-size: 11px; font-weight: bold;"><?php echo $user['role_name']; ?></span></td>
                    <td>
                        <?php if($user['is_active']): ?>
                            <span class="status-badge active-badge">✓ ΕΝΕΡΓΟΣ</span>
                        <?php else: ?>
                            <span class="status-badge inactive-badge">✘ ΑΠΕΝΕΡΓΟΠΟΙΗΜΕΝΟΣ</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Ποτέ'; ?></td>
                    <td>
                        <?php if($user['is_active']): ?>
                            <a href="users.php?action=deactivate&user_id=<?php echo $user['user_id']; ?>" class="btn-action btn-deactivate">Απενεργοποίηση</a>
                        <?php else: ?>
                            <a href="users.php?action=activate&user_id=<?php echo $user['user_id']; ?>" class="btn-action btn-activate">Ενεργοποίηση</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>