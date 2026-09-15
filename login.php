<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    if ($role == 'admin') {
        $query = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['user_id']   = $row['admin_id'];
            $_SESSION['user_name'] = $row['first_name'] . " " . $row['last_name'];
            $_SESSION['email']     = $row['email'];
            $_SESSION['role']      = 'admin';

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid Admin Credentials!";
        }
    } elseif ($role == 'staff') {
        $query = "SELECT * FROM staff WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['user_id']    = $row['staff_id'];
            $_SESSION['user_name']  = $row['first_name'] . " " . $row['last_name'];
            $_SESSION['email']      = $row['email'];
            $_SESSION['department'] = $row['department'];
            $_SESSION['role']       = 'staff';

            header("Location: staff_dashboard.php");
            exit();
        } else {
            $error = "Invalid Staff Credentials!";
        }
    } else {
        $error = "Please select a valid role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Ministry Portal - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h2>Campus Ministry Portal</h2>
            <p class="subtitle">Secure Authentication Portal</p>
            
            <?php if (!empty($error)): ?>
                <div class="error-box"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>

                <div class="form-group">
                    <label>Login As</label>
                    <select name="role" required>
                        <option value="admin">Administrator</option>
                        <option value="staff">Staff Member</option>
                    </select>
                </div>

                <button type="submit" class="btn-login">Login to Portal</button>
            </form>
        </div>
    </div>
</body>
</html>