<?php
include '../connection/config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: ../login.php');
    exit();
}

$user_email = $_SESSION['user_email'];

// Fetch user details
$select = $conn->prepare("SELECT type, first_name, last_name, image FROM user_form WHERE email = ?");
$select->execute([$user_email]);
$fetch = $select->fetch(PDO::FETCH_ASSOC);

// Check if the user is an admin
if (!$fetch || $fetch['type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$first_name = $fetch['first_name'];
$last_name = $fetch['last_name'];
$image = $fetch['image'];

// Fetch counts for dashboard
$total_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_admins = $conn->query("SELECT COUNT(*) FROM user_form WHERE type = 'admin'")->fetchColumn();
$total_present_students = $conn->query("SELECT COUNT(*) FROM students WHERE student_status = 'Present'")->fetchColumn();
$total_absent_students = $conn->query("SELECT COUNT(*) FROM students WHERE student_status = 'Absent'")->fetchColumn();
$total_excused_students = $conn->query("SELECT COUNT(*) FROM students WHERE student_status = 'Excused'")->fetchColumn();
$total_teachers = $conn->query("SELECT COUNT(*) FROM user_form WHERE type = 'teacher'")->fetchColumn();
$total_subjects = $conn->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f7;
        }
        .sidebar {
            width: 250px;
            background-color: #008000;
            color: white;
            transition: margin-left 0.3s;
        }
        .sidebar a {
            display: block;
            padding: 16px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #fcc200;
        }
        .sidebar-header {
            padding: 16px;
            text-align: center;
            font-size: 1.25rem;
            background-color: #008000;
        }
        .openbtn {
            background-color: #ffcc00;
            color: black;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .openbtn:hover {
            background-color: #008000;
        }
        .total-box {
            background-color: #004d40;
            padding: 1rem;
            text-align: center;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
        }
        .total-box p {
            color: white;
            margin-bottom: 0.5rem;
        }
        .total-box h4 {
            color: #ffcc00;
            margin-top: 0;
            font-weight: bold;
            line-height: 1.5;
        }
        .card-header {
            background-color: #004d40;
            color: white;
            padding: 0.75rem;
            text-align: center;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .info-box {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .info-box-header {
            padding: 1rem;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-box-content {
            padding: 1rem;
        }
        .info-box p {
            margin: 0.5rem 0;
        }
        .mt-8 {
            margin-top: 2rem;
        }
        .table-responsive {
            overflow-x: auto;
        }
        @media print {
            .sidebar, .openbtn, .total-box {
                display: none;
            }
        }
        .student-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        .overall-student-list {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 1rem;
        }
        .overall-student-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .overall-student-list th,
        .overall-student-list td {
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            text-align: left;
        }
        .overall-student-list th {
            background-color: #004d40;
            color: white;
        }
        .overall-student-list td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>

<body>
<div id="mySidebar" class="sidebar fixed top-0 left-0 h-full bg-gray-800 text-white shadow-lg">
    <div class="sidebar-header p-4">
        <img src="../images/logo.png" class="w-24 mx-auto mb-3" alt="Logo">
        <p class="text-center">Cavite State University Bacoor Campus</p>
    </div>
    <div class="sidebar-content">
        <a href="../adminpanels/adminstudview.php" class="sidebar-link block py-2 px-4 text-sm hover:bg-gray-700">Student Attendance</a>
        <a href="../adminpanels/viewattendanceadmin.php" class="sidebar-link block py-2 px-4 text-sm hover:bg-gray-700">View Student Attendance</a>
        <a href="../adminpanels/deletestudentadmin.php" class="sidebar-link block py-2 px-4 text-sm hover:bg-gray-700">Student  Management</a>
        <a href="../schedules/teacheradmin.php" class="sidebar-link block py-2 px-4 text-sm hover:bg-gray-700">Schedule</a>
        <a href="../schedules/archived_subjects.php" class="sidebar-link block py-2 px-4 text-sm hover:bg-gray-700">Schedule  Archives</a>
        <a href="../logout.php" class="sidebar-link block py-2 px-4 text-sm hover:bg-gray-700">Log out</a>
    </div>
</div>

<div id="main" class="ml-64 transition-all duration-300">
    <button class="openbtn m-4" onclick="toggleNav()">☰ Open Sidebar</button>

    <div class="container mx-auto p-4">
        <h3 class="mt-3 text-2xl font-bold text-green-900">Welcome, Admin <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <a href="../adminpanels/adddrop.php" class="bg-green-500 text-white py-3 rounded-md text-center hover:bg-green-600 transition duration-300">Teachers Management</a>
    <a href="../adminpanels/deletestudentadmin.php" class="bg-green-500 text-white py-3 rounded-md text-center hover:bg-green-600 transition duration-300">Student Panel</a>
    <a href="../schedules/teacheradmin.php" class="bg-green-500 text-white py-3 rounded-md text-center hover:bg-green-600 transition duration-300">Edit Schedule</a>
    <a href="../adminpanels/restorestudent.php" class="bg-green-500 text-white py-3 rounded-md text-center hover:bg-green-600 transition duration-300">Student Archive</a>
    <a href="../adminpanels/usermanagement.php" class="bg-green-500 text-white py-3 rounded-md text-center hover:bg-green-600 transition duration-300">User Management</a>
    <a href="../adminpanels/userarchives.php" class="bg-green-500 text-white py-3 rounded-md text-center hover:bg-green-600 transition duration-300">User Archives</a>
    <a href="../schedules/archived_subjects.php" class="bg-green-500 text-white py-3 rounded-md text-center hover:bg-green-600 transition duration-300">Subject Archives</a>
</div>



        <!-- Student List Section -->
        <div class="mt-8 bg-white rounded-lg shadow-lg">
            <div class="card-header">Student List</div>
            <div class="student-list">
                <div class="card-body p-4">
                    <?php
                    $student_list_query = $conn->query("SELECT id, first_name, last_name, student_status FROM students");
                    while ($student = $student_list_query->fetch(PDO::FETCH_ASSOC)) {
                        $status_class = '';
                        switch ($student['student_status']) {
                            case 'Present':
                                $status_class = 'bg-green-500 text-white';
                                break;
                            case 'Absent':
                                $status_class = 'bg-red-500 text-white';
                                break;
                            case 'Excused':
                                $status_class = 'bg-yellow-500 text-black';
                                break;
                            default:
                                $status_class = 'bg-gray-500 text-white';
                                break;
                        }
                        echo '<p class="student-item rounded-lg p-2 ' . $status_class . '">' . htmlspecialchars($student['first_name'] . ' ' . $student['last_name'] . ' - ' . $student['student_status']) . '</p>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Overall Student List Section -->
        <div class="overall-student-list mt-8 p-4 rounded shadow">
            <div class="card-header">
                <h3 class="text-xl">Overall Student List</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Student Number</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stmt = $conn->query("SELECT * FROM students");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $student_number = $row['id'];
                        $first_name = $row['first_name'];
                        $last_name = $row['last_name'];
                        $gender = $row['gender'];
                        $email = $row['email'];
                        $status = $row['student_status'];
                        $image_path = $row['image'];

                        echo "<tr>";
                        echo "<td><img src='../images/student_image/{$image_path}' alt='{$first_name}' class='student-img'></td>";
                        echo "<td>{$student_number}</td>";
                        echo "<td>{$first_name}</td>";
                        echo "<td>{$last_name}</td>";
                        echo "<td>{$gender}</td>";
                        echo "<td>{$email}</td>";
                        echo "<td>{$status}</td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cards Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="total-box">
                <p>Total Students</p>
                <h4><?php echo htmlspecialchars($total_students); ?></h4>
            </div>
            <div class="total-box">
                <p>Total Admins</p>
                <h4><?php echo htmlspecialchars($total_admins); ?></h4>
            </div>
            <div class="total-box">
                <p>Total Teachers</p>
                <h4><?php echo htmlspecialchars($total_teachers); ?></h4>
            </div>
            <div class="total-box">
                <p>Total Subjects</p>
                <h4><?php echo htmlspecialchars($total_subjects); ?></h4>
            </div>
            <div class="total-box">
                <p>Present Students</p>
                <h4><?php echo htmlspecialchars($total_present_students); ?></h4>
            </div>
            <div class="total-box">
                <p>Absent Students</p>
                <h4><?php echo htmlspecialchars($total_absent_students); ?></h4>
            </div>
            <div class="total-box">
                <p>Excused Students</p>
                <h4><?php echo htmlspecialchars($total_excused_students); ?></h4>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleNav() {
        var sidebar = document.getElementById("mySidebar");
        var main = document.getElementById("main");

        if (sidebar.style.marginLeft === "-250px") {
            sidebar.style.marginLeft = "0";
            main.style.marginLeft = "250px";
        } else {
            sidebar.style.marginLeft = "-250px";
            main.style.marginLeft = "0";
        }
    }
</script>
</body>
</html>
