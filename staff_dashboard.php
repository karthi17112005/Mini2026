<?php
session_start();

// Check if Staff is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Capture Filter Values
$religion      = isset($_GET['religion']) ? mysqli_real_escape_string($conn, trim($_GET['religion'])) : '';
$accommodation = isset($_GET['accommodation']) ? mysqli_real_escape_string($conn, trim($_GET['accommodation'])) : '';
$department    = isset($_GET['department']) ? mysqli_real_escape_string($conn, trim($_GET['department'])) : '';
$class         = isset($_GET['class']) ? mysqli_real_escape_string($conn, trim($_GET['class'])) : '';
$year          = isset($_GET['year']) ? mysqli_real_escape_string($conn, trim($_GET['year'])) : '';
$section       = isset($_GET['section']) ? mysqli_real_escape_string($conn, trim($_GET['section'])) : '';
$search        = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

// Build Query Dynamically
$where_clauses = array("1=1");

if (!empty($religion)) {
    $where_clauses[] = "religion = '$religion'";
}
if (!empty($accommodation)) {
    $where_clauses[] = "accommodation_type = '$accommodation'";
}
if (!empty($department)) {
    $where_clauses[] = "department = '$department'";
}
if (!empty($class)) {
    $where_clauses[] = "class = '$class'";
}
if (!empty($year)) {
    $where_clauses[] = "year = '$year'";
}
if (!empty($section)) {
    $where_clauses[] = "section = '$section'";
}
if (!empty($search)) {
    $where_clauses[] = "(d_number LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%')";
}

$where_sql = implode(" AND ", $where_clauses);
$query = "SELECT * FROM student WHERE $where_sql ORDER BY department, class, year, section, d_number ASC";

// Handle Excel (CSV) Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Campus_Ministry_Student_List.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array('D-Number', 'First Name', 'Last Name', 'Religion', 'Accommodation', 'Department', 'Class', 'Year', 'Section'));

    $export_result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($export_result)) {
        fputcsv($output, array(
            $row['d_number'],
            $row['first_name'],
            $row['last_name'],
            $row['religion'],
            $row['accommodation_type'],
            $row['department'],
            $row['class'],
            $row['year'],
            $row['section']
        ));
    }
    fclose($output);
    exit();
}

$students_result = mysqli_query($conn, $query);
$total_students = mysqli_num_rows($students_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Console - Campus Ministry Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }
        .filter-card { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 25px; }
        .filter-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 15px; }
        .filter-actions { display: flex; gap: 10px; align-items: center; }
        .btn-filter { background: #2b6cb0; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-reset { background: #718096; color: #fff; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; }
        .btn-print { background: #319795; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-export { background: #38a169; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e0; padding: 10px; text-align: left; font-size: 13px; }
        th { background-color: #edf2f7; }
        .btn-logout { background: #e53e3e; color: #fff; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
        
        /* Print-specific layout */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .container { max-width: 100%; margin: 0; padding: 0; border: none; }
            table { width: 100%; font-size: 12px; }
            th, td { padding: 6px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <div>
            <h2>Staff Search & View Console</h2>
            <p>Staff User: <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> (<?php echo htmlspecialchars($_SESSION['department']); ?>)</p>
        </div>
        <a href="logout.php" class="btn-logout no-print">Logout</a>
    </div>

    <!-- Multi-Filter & Search Form -->
    <div class="filter-card no-print">
        <h3 style="margin-bottom: 15px; color: #2b6cb0;">Filter Student Directory</h3>
        <form method="GET" action="staff_dashboard.php">
            <div class="filter-grid">
                <!-- Search by D-Number or Name -->
                <input type="text" name="search" placeholder="Search D-Number / Name" value="<?php echo htmlspecialchars($search); ?>">

                <!-- Religion Filter -->
                <select name="religion">
                    <option value="">All Religions</option>
                    <option value="Catholic" <?php if ($religion === 'Catholic') echo 'selected'; ?>>Catholic</option>
                    <option value="Non-Catholic" <?php if ($religion === 'Non-Catholic') echo 'selected'; ?>>Non-Catholic</option>
                </select>

                <!-- Accommodation Filter -->
                <select name="accommodation">
                    <option value="">All Accommodations</option>
                    <option value="Hosteler" <?php if ($accommodation === 'Hosteler') echo 'selected'; ?>>Hosteler</option>
                    <option value="Day Scholar" <?php if ($accommodation === 'Day Scholar') echo 'selected'; ?>>Day Scholar</option>
                </select>

                <!-- Department Filter -->
                <select name="department">
                    <option value="">All Departments</option>
                    <option value="CS" <?php if ($department === 'CS') echo 'selected'; ?>>Computer Science (CS)</option>
                    <option value="IT" <?php if ($department === 'IT') echo 'selected'; ?>>Information Technology (IT)</option>
                </select>

                <!-- Class / Course Filter -->
                <select name="class">
                    <option value="">All Courses</option>
                    <option value="B.Sc CS" <?php if ($class === 'B.Sc CS') echo 'selected'; ?>>B.Sc CS</option>
                    <option value="MCA" <?php if ($class === 'MCA') echo 'selected'; ?>>MCA</option>
                    <option value="BCA" <?php if ($class === 'BCA') echo 'selected'; ?>>BCA</option>
                    <option value="M.Sc CS" <?php if ($class === 'M.Sc CS') echo 'selected'; ?>>M.Sc CS</option>
                </select>

                <!-- Year Filter -->
                <select name="year">
                    <option value="">All Years</option>
                    <option value="I Year" <?php if ($year === 'I Year') echo 'selected'; ?>>I Year</option>
                    <option value="II Year" <?php if ($year === 'II Year') echo 'selected'; ?>>II Year</option>
                    <option value="III Year" <?php if ($year === 'III Year') echo 'selected'; ?>>III Year</option>
                </select>

                <!-- Section Filter -->
                <input type="text" name="section" placeholder="Section (e.g. A / B)" value="<?php echo htmlspecialchars($section); ?>">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">Apply Filters</button>
                <a href="staff_dashboard.php" class="btn-reset">Reset Filters</a>
                <button type="button" class="btn-print" onclick="window.print()">Print Filtered List</button>
                <a href="staff_dashboard.php?<?php echo http_build_query(array_merge($_GET, array('export' => 'csv'))); ?>" class="btn-export">Export to Excel (CSV)</a>
            </div>
        </form>
    </div>

    <!-- Student Table View -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <h3>Filtered Results (Total Records: <?php echo $total_students; ?>)</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>D-Number</th>
                <th>Full Name</th>
                <th>Religion</th>
                <th>Accommodation</th>
                <th>Department</th>
                <th>Class</th>
                <th>Year</th>
                <th>Section</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_students > 0): ?>
                <?php 
                $sno = 1;
                while ($row = mysqli_fetch_assoc($students_result)): 
                ?>
                <tr>
                    <td><?php echo $sno++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['d_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['religion']); ?></td>
                    <td><?php echo htmlspecialchars($row['accommodation_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                    <td><?php echo htmlspecialchars($row['class']); ?></td>
                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #718096; padding: 20px;">No student records match the selected criteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>