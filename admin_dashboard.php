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

// Variables for Editing
$edit_mode = false;
$edit_d_number = "";
$edit_first_name = "";
$edit_last_name = "";
$edit_religion = "";
$edit_accommodation = "";
$edit_department = "";
$edit_class = "";
$edit_year = "";
$edit_section = "";

// 1. Handle "Edit" Click
if (isset($_GET['edit'])) {
    $edit_d_number = mysqli_real_escape_string($conn, $_GET['edit']);
    $fetch_edit = mysqli_query($conn, "SELECT * FROM student WHERE d_number = '$edit_d_number'");
    if ($edit_row = mysqli_fetch_assoc($fetch_edit)) {
        $edit_mode = true;
        $edit_first_name = $edit_row['first_name'];
        $edit_last_name = $edit_row['last_name'];
        $edit_religion = $edit_row['religion'];
        $edit_accommodation = $edit_row['accommodation_type'];
        $edit_department = $edit_row['department'];
        $edit_class = $edit_row['class'];
        $edit_year = $edit_row['year'];
        $edit_section = $edit_row['section'];
    }
}

// 2. Handle Update Student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student'])) {
    $d_number      = mysqli_real_escape_string($conn, trim($_POST['d_number']));
    $first_name    = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name     = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $religion      = mysqli_real_escape_string($conn, trim($_POST['religion']));
    $accommodation = mysqli_real_escape_string($conn, trim($_POST['accommodation_type']));
    $department    = mysqli_real_escape_string($conn, trim($_POST['department']));
    $class         = mysqli_real_escape_string($conn, trim($_POST['class']));
    $year          = mysqli_real_escape_string($conn, trim($_POST['year']));
    $section       = mysqli_real_escape_string($conn, trim($_POST['section']));

    $update_sql = "UPDATE student SET 
                    first_name = '$first_name', 
                    last_name = '$last_name', 
                    religion = '$religion', 
                    accommodation_type = '$accommodation', 
                    department = '$department', 
                    class = '$class', 
                    year = '$year', 
                    section = '$section' 
                   WHERE d_number = '$d_number'";

    if (mysqli_query($conn, $update_sql)) {
        $message = "Student record updated successfully!";
        $edit_mode = false;
    } else {
        $error = "Error updating record: " . mysqli_error($conn);
    }
}

// 3. Handle Add New Student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $d_number      = mysqli_real_escape_string($conn, trim($_POST['d_number']));
    $first_name    = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name     = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $religion      = mysqli_real_escape_string($conn, trim($_POST['religion']));
    $accommodation = mysqli_real_escape_string($conn, trim($_POST['accommodation_type']));
    $department    = mysqli_real_escape_string($conn, trim($_POST['department']));
    $class         = mysqli_real_escape_string($conn, trim($_POST['class']));
    $year          = mysqli_real_escape_string($conn, trim($_POST['year']));
    $section       = mysqli_real_escape_string($conn, trim($_POST['section']));

    $check = mysqli_query($conn, "SELECT * FROM student WHERE d_number = '$d_number'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Student with D-Number '$d_number' already exists!";
    } else {
        $insert_sql = "INSERT INTO student (d_number, first_name, last_name, religion, accommodation_type, department, class, year, section) 
                       VALUES ('$d_number', '$first_name', '$last_name', '$religion', '$accommodation', '$department', '$class', '$year', '$section')";
        if (mysqli_query($conn, $insert_sql)) {
            $message = "Student record added successfully!";
        } else {
            $error = "Error adding student: " . mysqli_error($conn);
        }
    }
}

// 4. Handle Bulk CSV Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_csv'])) {
    if (isset($_FILES['csv_file']['name']) && $_FILES['csv_file']['name'] != "") {
        $fileName = $_FILES['csv_file']['tmp_name'];
        if ($_FILES['csv_file']['size'] > 0) {
            $file = fopen($fileName, "r");
            fgetcsv($file); // Skip header row
            $imported_count = 0;
            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                if (!empty($column[0])) {
                    $d_num      = mysqli_real_escape_string($conn, trim($column[0]));
                    $f_name     = mysqli_real_escape_string($conn, trim($column[1]));
                    $l_name     = mysqli_real_escape_string($conn, trim($column[2]));
                    $rel        = mysqli_real_escape_string($conn, trim($column[3]));
                    $accom      = mysqli_real_escape_string($conn, trim($column[4]));
                    $dept       = mysqli_real_escape_string($conn, trim($column[5]));
                    $cls        = mysqli_real_escape_string($conn, trim($column[6]));
                    $yr         = mysqli_real_escape_string($conn, trim($column[7]));
                    $sec        = mysqli_real_escape_string($conn, trim($column[8]));

                    $csv_sql = "REPLACE INTO student (d_number, first_name, last_name, religion, accommodation_type, department, class, year, section) 
                                VALUES ('$d_num', '$f_name', '$l_name', '$rel', '$accom', '$dept', '$cls', '$yr', '$sec')";
                    mysqli_query($conn, $csv_sql);
                    $imported_count++;
                }
            }
            fclose($file);
            $message = "Bulk import completed! $imported_count student records processed.";
        }
    } else {
        $error = "Please upload a valid CSV file.";
    }
}

// 5. Handle Student Deletion
if (isset($_GET['delete'])) {
    $d_number = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM student WHERE d_number = '$d_number'");
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all students
$students_result = mysqli_query($conn, "SELECT * FROM student ORDER BY department, class, year, section, d_number ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Campus Ministry</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }
        .cards-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 20px; margin-bottom: 30px; }
        .card-box { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .card-box h3 { margin-bottom: 15px; color: #2b6cb0; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e0; padding: 8px; text-align: left; font-size: 13px; }
        th { background-color: #edf2f7; }
        .btn-edit { color: #2b6cb0; font-weight: bold; text-decoration: none; margin-right: 10px; }
        .btn-delete { color: #e53e3e; font-weight: bold; text-decoration: none; }
        .btn-logout { background: #e53e3e; color: #fff; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
        .success-box { background: #c6f6d5; color: #22543d; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .cancel-edit { display: inline-block; margin-left: 10px; color: #718096; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <div>
            <h2>Admin Control Console</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> | <strong>System Administrator</strong></p>
        <div style="">
            <a href="admin_dashboard.php" >Manage Students</a>
            <a href="manage_staff.php" style="font-weight: bold; color: #2b6cb0;">Manage Staff Accounts →</a>
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

    <div class="cards-grid">
        <!-- Manual Add / Edit Student Form -->
        <div class="card-box">
            <h3><?php echo $edit_mode ? "Edit Student Record" : "Add New Student"; ?></h3>
            <form action="admin_dashboard.php" method="POST">
                <div class="form-row">
                    <input type="text" name="d_number" placeholder="D-Number (e.g. 24PCS801)" 
                           value="<?php echo htmlspecialchars($edit_d_number); ?>" 
                           <?php echo $edit_mode ? 'readonly style="background:#edf2f7;"' : 'required'; ?>>
                    <input type="text" name="first_name" placeholder="First Name" 
                           value="<?php echo htmlspecialchars($edit_first_name); ?>" required>
                </div>

                <div class="form-row">
                    <input type="text" name="last_name" placeholder="Last Name (Initial)" 
                           value="<?php echo htmlspecialchars($edit_last_name); ?>" required>
                    <select name="religion" required>
                        <option value="">Select Religion</option>
                        <option value="Catholic" <?php if($edit_religion == 'Catholic') echo 'selected'; ?>>Catholic</option>
                        <option value="Non-Catholic" <?php if($edit_religion == 'Non-Catholic') echo 'selected'; ?>>Non-Catholic</option>
                    </select>
                </div>

                <div class="form-row">
                    <select name="accommodation_type" required>
                        <option value="">Select Accommodation</option>
                        <option value="Hosteler" <?php if($edit_accommodation == 'Hosteler') echo 'selected'; ?>>Hosteler</option>
                        <option value="Day Scholar" <?php if($edit_accommodation == 'Day Scholar') echo 'selected'; ?>>Day Scholar</option>
                    </select>
                    
                    <!-- Department Dropdown -->
                    <select name="department" id="deptSelect" onchange="updateCourses()" required>
                        <option value="">Select Department</option>
                        <option value="CS" <?php if($edit_department == 'CS') echo 'selected'; ?>>Computer Science (CS)</option>
                        <option value="IT" <?php if($edit_department == 'IT') echo 'selected'; ?>>Information Technology (IT)</option>
                    </select>
                </div>

                <div class="form-row">
                    <!-- Class / Course Dropdown -->
                    <select name="class" id="classSelect" onchange="updateYears()" required>
                        <option value="">Select Course</option>
                    </select>

                    <!-- Year Dropdown -->
                    <select name="year" id="yearSelect" required>
                        <option value="">Select Year</option>
                    </select>
                </div>

                <div class="form-row">
                    <input type="text" name="section" placeholder="Section (e.g. A / B)" 
                           value="<?php echo htmlspecialchars($edit_section); ?>" required>
                </div>

                <?php if ($edit_mode): ?>
                    <button type="submit" name="update_student" class="btn-login" style="background:#d69e2e; margin-top:5px;">Update Student Record</button>
                    <a href="admin_dashboard.php" class="cancel-edit">Cancel Edit</a>
                <?php else: ?>
                    <button type="submit" name="add_student" class="btn-login" style="margin-top:5px;">Save Student Record</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Bulk CSV Import Form -->
        <div class="card-box">
            <h3>Bulk Import via CSV</h3>
            <p style="font-size: 13px; color: #4a5568; margin-bottom: 12px;">
                CSV columns order:<br>
                <code>D_Number, First_Name, Last_Name, Religion, Accommodation, Department, Class, Year, Section</code>
            </p>
            <form action="admin_dashboard.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="file" name="csv_file" accept=".csv" required>
                </div>
                <button type="submit" name="upload_csv" class="btn-login" style="background:#319795; margin-top:10px;">Upload & Import CSV</button>
            </form>
        </div>
    </div>

    <!-- Student Records Table -->
    <h3>Enrolled Students Directory (Total: <?php echo mysqli_num_rows($students_result); ?>)</h3>
    <table>
        <thead>
            <tr>
                <th>D-Number</th>
                <th>Full Name</th>
                <th>Religion</th>
                <th>Accommodation</th>
                <th>Department</th>
                <th>Class</th>
                <th>Year</th>
                <th>Section</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($students_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($students_result)): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['d_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['religion']); ?></td>
                    <td><?php echo htmlspecialchars($row['accommodation_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                    <td><?php echo htmlspecialchars($row['class']); ?></td>
                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                    <td>
                        <a href="admin_dashboard.php?edit=<?php echo urlencode($row['d_number']); ?>" class="btn-edit">Edit</a>
                        <a href="admin_dashboard.php?delete=<?php echo urlencode($row['d_number']); ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Delete this record permanently?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #718096;">No student records found in the database.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>



<!-- Dynamic Course & Year Script -->
<script>
var currentClass = "<?php echo $edit_class; ?>";
var currentYear  = "<?php echo $edit_year; ?>";

function updateCourses() {
    var dept = document.getElementById("deptSelect").value;
    var classSelect = document.getElementById("classSelect");
    classSelect.innerHTML = '<option value="">Select Course</option>';

    var courses = [];
    if (dept === "CS") {
        courses = ["B.Sc CS", "MCA"];
    } else if (dept === "IT") {
        courses = ["BCA", "M.Sc CS"];
    }

    for (var i = 0; i < courses.length; i++) {
        var opt = document.createElement("option");
        opt.value = courses[i];
        opt.text = courses[i];
        if (courses[i] === currentClass) {
            opt.selected = true;
        }
        classSelect.appendChild(opt);
    }
    updateYears();
}

function updateYears() {
    var selectedCourse = document.getElementById("classSelect").value;
    var yearSelect = document.getElementById("yearSelect");
    yearSelect.innerHTML = '<option value="">Select Year</option>';

    var years = [];
    if (selectedCourse === "M.Sc CS" || selectedCourse === "MCA") {
        years = ["I Year", "II Year"];
    } else if (selectedCourse === "B.Sc CS" || selectedCourse === "BCA") {
        years = ["I Year", "II Year", "III Year"];
    }

    for (var j = 0; j < years.length; j++) {
        var opt = document.createElement("option");
        opt.value = years[j];
        opt.text = years[j];
        if (years[j] === currentYear) {
            opt.selected = true;
        }
        yearSelect.appendChild(opt);
    }
}

// Auto-trigger on page load (for Edit mode)
window.onload = function() {
    if (document.getElementById("deptSelect").value !== "") {
        updateCourses();
    }
};
</script>

</body>
</html>