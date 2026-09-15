<?php
session_start();

// Check if Admin is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$message = "";
$error = "";

$edit_mode = false;
$edit_staff_id = "";
$edit_first_name = "";
$edit_last_name = "";
$edit_email = "";
$edit_department = "";
$edit_role = "";

// 1. Handle Edit Fetch
if (isset($_GET['edit'])) {
    $edit_staff_id = (int)$_GET['edit'];
    $fetch_staff = mysqli_query($conn, "SELECT * FROM staff WHERE staff_id = $edit_staff_id");
    if ($s_row = mysqli_fetch_assoc($fetch_staff)) {
        $edit_mode = true;
        $edit_first_name = $s_row['first_name'];
        $edit_last_name = $s_row['last_name'];
        $edit_email = $s_row['email'];
        $edit_department = $s_row['department'];
        $edit_role = $s_row['role'];
    }
}

// 2. Handle Update Staff
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_staff'])) {
    $staff_id   = (int)$_POST['staff_id'];
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name  = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email      = mysqli_real_escape_string($conn, trim($_POST['email']));
    $department = mysqli_real_escape_string($conn, trim($_POST['department']));
    $role       = mysqli_real_escape_string($conn, trim($_POST['role']));
    $password   = trim($_POST['password']);

    if (!empty($password)) {
        $update_sql = "UPDATE staff SET first_name='$first_name', last_name='$last_name', email='$email', password='$password', department='$department', role='$role' WHERE staff_id=$staff_id";
    } else {
        $update_sql = "UPDATE staff SET first_name='$first_name', last_name='$last_name', email='$email', department='$department', role='$role' WHERE staff_id=$staff_id";
    }

    if (mysqli_query($conn, $update_sql)) {
        $message = "Staff record updated successfully!";
        $edit_mode = false;
    } else {
        $error = "Error updating staff: " . mysqli_error($conn);
    }
}

// 3. Handle Add New Staff
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_staff'])) {
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name  = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email      = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password   = trim($_POST['password']);
    $department = mysqli_real_escape_string($conn, trim($_POST['department']));
    $role       = mysqli_real_escape_string($conn, trim($_POST['role']));

    $check = mysqli_query($conn, "SELECT * FROM staff WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Staff with email '$email' already exists!";
    } else {
        $insert_sql = "INSERT INTO staff (first_name, last_name, email, password, department, role) 
                       VALUES ('$first_name', '$last_name', '$email', '$password', '$department', '$role')";
        if (mysqli_query($conn, $insert_sql)) {
            $message = "New staff account created successfully!";
        } else {
            $error = "Error adding staff: " . mysqli_error($conn);
        }
    }
}

// 4. Handle Delete Staff
if (isset($_GET['delete'])) {
    $staff_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM staff WHERE staff_id = $staff_id");
    header("Location: manage_staff.php");
    exit();
}

$staff_list = mysqli_query($conn, "SELECT * FROM staff ORDER BY department, first_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff - Campus Ministry</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1100px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }
        .nav-links a { margin-right: 15px; text-decoration: none; font-weight: bold; color: #2b6cb0; }
        .card-box { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 30px; }
        .form-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e0; padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: #edf2f7; }
        .btn-edit { color: #2b6cb0; font-weight: bold; text-decoration: none; margin-right: 10px; }
        .btn-delete { color: #e53e3e; font-weight: bold; text-decoration: none; }
        .btn-logout { background: #e53e3e; color: #fff; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
        .success-box { background: #c6f6d5; color: #22543d; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <div>
            <h2>Staff Account Management</h2>
            <div class="nav-links" style="margin-top: 8px;">
                <a href="admin_dashboard.php">← Manage Students</a>
                <a href="manage_staff.php" style="color:#000;">Manage Staff</a>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="success-box"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Add / Edit Staff Form -->
    <div class="card-box">
        <h3><?php echo $edit_mode ? "Edit Staff Account" : "Add New Staff Member"; ?></h3>
        <form action="manage_staff.php" method="POST">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="staff_id" value="<?php echo $edit_staff_id; ?>">
            <?php endif; ?>

            <div class="form-row">
                <input type="text" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($edit_first_name); ?>" required>
                <input type="text" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($edit_last_name); ?>" required>
                <input type="email" name="email" placeholder="Staff Email (Login ID)" value="<?php echo htmlspecialchars($edit_email); ?>" required>
            </div>

            <div class="form-row">
                <input type="password" name="password" placeholder="<?php echo $edit_mode ? 'New Password (leave blank to keep current)' : 'Password'; ?>" <?php echo $edit_mode ? '' : 'required'; ?>>
                
                <select name="department" required>
                    <option value="">Select Department</option>
                    <option value="CS" <?php if ($edit_department === 'CS') echo 'selected'; ?>>Computer Science (CS)</option>
                    <option value="IT" <?php if ($edit_department === 'IT') echo 'selected'; ?>>Information Technology (IT)</option>
                </select>

                <input type="text" name="role" placeholder="Role (e.g. Tutor / Staff In-charge)" value="<?php echo htmlspecialchars($edit_role); ?>" required>
            </div>

            <?php if ($edit_mode): ?>
                <button type="submit" name="update_staff" class="btn-login" style="background:#d69e2e; width:auto; padding:8px 20px;">Update Staff Details</button>
                <a href="manage_staff.php" style="margin-left: 10px; color: #718096; text-decoration: none;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_staff" class="btn-login" style="width:auto; padding:8px 20px;">Create Staff Account</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Existing Staff List Table -->
    <h3>Registered Staff Accounts (Total: <?php echo mysqli_num_rows($staff_list); ?>)</h3>
    <table>
        <thead>
            <tr>
                <th>Staff ID</th>
                <th>Full Name</th>
                <th>Email ID</th>
                <th>Department</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($s = mysqli_fetch_assoc($staff_list)): ?>
            <tr>
                <td><?php echo $s['staff_id']; ?></td>
                <td><?php echo htmlspecialchars($s['first_name'] . " " . $s['last_name']); ?></td>
                <td><?php echo htmlspecialchars($s['email']); ?></td>
                <td><?php echo htmlspecialchars($s['department']); ?></td>
                <td><?php echo htmlspecialchars($s['role']); ?></td>
                <td>
                    <a href="manage_staff.php?edit=<?php echo $s['staff_id']; ?>" class="btn-edit">Edit</a>
                    <a href="manage_staff.php?delete=<?php echo $s['staff_id']; ?>" class="btn-delete" onclick="return confirm('Delete this staff account?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>