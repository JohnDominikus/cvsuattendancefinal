<?php
include '../connection/config.php';
session_start();

$user_email = $_SESSION['user_email'];

// Fetch the user's details from the database
$select = $conn->prepare("SELECT type, first_name, last_name, image FROM user_form WHERE email = ?");
$select->execute([$user_email]);
$fetch = $select->fetch(PDO::FETCH_ASSOC);

// If the user is not found or is not a teacher, redirect to the appropriate page
if (!$fetch || $fetch['type'] != 'teacher') {
    header('Location: ../dashboards/teacherdash.php');
    exit();
}

$first_name = $fetch['first_name'];
$last_name = $fetch['last_name'];
$image = $fetch['image'];
$type = $fetch['type'];

// Count the total number of teachers
$count_teachers_query = $conn->query("SELECT COUNT(*) FROM user_form WHERE type = 'teacher'");
$total_teachers = $count_teachers_query->fetchColumn();

// Count the total number of students
$count_students_query = $conn->query("SELECT COUNT(*) FROM students");
$total_students = $count_students_query->fetchColumn();

// Count the total number of active students
$count_active_students_query = $conn->query("SELECT COUNT(*) FROM students WHERE student_status IN ('Present', 'Absent', 'Excused')");
$total_active_students = $count_active_students_query->fetchColumn();

// Calculate inactive students (those not present, absent, or excused)
$total_inactive_students = $total_students - $total_active_students;

// Count students by specific statuses
$count_present_students_query = $conn->query("SELECT COUNT(*) FROM students WHERE student_status = 'Present'");
$total_present_students = $count_present_students_query->fetchColumn();

$count_absent_students_query = $conn->query("SELECT COUNT(*) FROM students WHERE student_status = 'Absent'");
$total_absent_students = $count_absent_students_query->fetchColumn();

$count_excused_students_query = $conn->query("SELECT COUNT(*) FROM students WHERE student_status = 'Excused'");
$total_excused_students = $count_excused_students_query->fetchColumn();
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
            background-color: #f0f4f7; /* CVSU theme background color */
        }

        .sidebar {
            width: 250px;
            background-color: #008000; /* CVSU theme sidebar background color */
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
            background-color: #fcc200; /* CVSU theme sidebar link hover color */
        }

        .sidebar-header {
            padding: 16px;
            text-align: center;
            font-size: 1.25rem;
            background-color: #008000; /* CVSU theme sidebar header background color */
        }

        .openbtn {
            background-color: #ffcc00; /* CVSU theme sidebar toggle button background color */
            color: black;
            border: none;
            padding: 8px 15px; /* Adjusted padding for better button size */
            cursor: pointer;
            border-radius: 5px; /* Adding border-radius for rounded corners */
        }

        .openbtn:hover {
            background-color: #008000; /* CVSU theme sidebar toggle button hover background color */
        }

        .total-box {
            background-color: #004d40; /* CVSU theme total box background color */
            padding: 1rem; /* Adjust padding as needed */
            text-align: center; /* Center align text */
            border-radius: 8px; /* Add rounded corners */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem; /* Add margin to separate boxes */
        }

        .total-box p {
            color: white; /* CVSU theme text color for paragraphs */
            margin-bottom: 0.5rem; /* Adjust spacing */
        }

        .total-box h4 {
            color: #ffcc00; /* CVSU theme text color for headings */
            margin-top: 0; /* Remove default margin */
            font-weight: bold; /* Ensure headings are bold */
            line-height: 1.5; /* Adjust line height for better readability */
        }

        .card-header {
            background-color: #004d40; /* CVSU theme card header background color */
            color: white;
            padding: 0.75rem;
            text-align: center;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .student-list {
            background-color: white;
            border: 1px solid #e2e8f0; /* CVSU theme border color */
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .student-item {
            padding: 0.5rem;
            border-bottom: 1px solid #e2e8f0; /* CVSU theme border color */
        }

        .active-status {
            color: #1e8449; /* CVSU theme active status color */
        }

        .inactive-status {
            color :  black; /* CVSU theme inactive status color */
        }

        .student-list p:last-child {
            border-bottom: none;
        }

        .student-list .card-body {
            max-height: 300px;
            overflow-y: auto;
        }

        .student-list .card-body::-webkit-scrollbar {
            width: 8px;
        }

        .student-list .card-body::-webkit-scrollbar-thumb {
            background-color: #38a169; /* CVSU theme scrollbar thumb color */
            border-radius: 4px;
        }

        .student-list .card-body::-webkit-scrollbar-track {
            background-color: #f0f4f7; /* CVSU theme scrollbar track color */
            border-radius: 4px;
        }

        .overall-student-list {
            background-color: white;
            border: 1px solid #e2e8f0; /* CVSU theme border color */
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 1rem; /* Add margin to separate sections */
        }

        .overall-student-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .overall-student-list th,
        .overall-student-list td {
            border: 1px solid #e2e8f0; /* CVSU theme border color */
            padding: 0.75rem;
            text-align: left;
        }

        .overall-student-list th {
            background-color: #004d40; /* CVSU theme table header background color */
            color: white;
        }

        .overall-student-list td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        /* Responsive Table */
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <div id="mySidebar" class="sidebar fixed top-0 left-0 h-full overflow-hidden">
        <div class="sidebar-header">
            <img src="../images/logo.png" class="w-24 mx-auto mb-3" alt="Logo">
            <p>Cavite State University Bacoor Campus</p>
        </div>
        <a href="../attendance/studentattendance.php">Student Management</a>
        <a href="../attendance/markattendancestudent.php">Mark Attendance Sheets</a>
        <a href="../attendance/viewattendance.php">View Attendance Sheets</a>
        <a href="../attendance/deletestudent.php">Drop Student</a>
        <a href="../schedules/teacherschedule.php">Schedule</a>
          <a href="../schedules/viewteacher schedule.php">  View Schedule</a>
        <a href="../logout.php">Log out</a>
    </div>

    <div id="main" class="ml-64 transition-all duration-300">
        <button class="openbtn m-4" onclick="toggleNav()">☰ Open Sidebar</button>

        <div class="container mx-auto p-4">
            <h3 class="mt-3 text-2xl font-bold text-green-900">Welcome, Teacher <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h3>

            <!-- Total Boxes -->
            <div class="container mx-auto p-4">
        <!-- Display totals and counts -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <!-- Total number of teachers -->
           
            <!-- Total number of students -->
            <div class="total-box p-4 rounded shadow">
                <p class="text-lg">Total number of students</p>
                <h4 class="text-2xl"><?php echo $total_students ?></h4>
            </div>
         
            <!-- Total number of present students -->
            <div class="total-box p-4 rounded shadow">
                <p class="text-lg">Present students</p>
                <h4 class="text-2xl"><?php echo $total_present_students ?></h4>
            </div>
            <!-- Total number of absent students -->
            <div class="total-box p-4 rounded shadow">
                <p class="text-lg">Absent students</p>
                <h4 class="text-2xl"><?php echo $total_absent_students ?></h4>
            </div>
            <!-- Total number of excused students -->
            <div class="total-box p-4 rounded shadow">
                <p class="text-lg">Excused students</p>
                <h4 class="text-2xl"><?php echo $total_excused_students ?></h4>
            </div>
        </div>


            <!-- Student List Section -->
            <div class="student-list mt-8 p-4 rounded shadow">
                <div class="card-header">
                    <h3 class="text-xl">Students</h3>
                </div>
                <div class="card-body">
                    <?php
                    $select_students = $conn->query("SELECT first_name, last_name, gender, student_status FROM students ORDER BY last_name ASC");

                    if ($select_students->rowCount() > 0) {
                        while ($row = $select_students->fetch(PDO::FETCH_ASSOC)) {
                            $status_class = ($row['student_status'] == 'Active') ? 'active-status' : 'inactive-status';
                            echo '<p class="student-item"><span class="' . $status_class . '">' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . ' (' . htmlspecialchars($row['gender']) . ')</span></p>';
                        }
                    } else {
                        echo '<p>No students found.</p>';
                    }
                    ?>
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
                                <th>Status</th>
                              
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch students from database
                            $stmt = $conn->query("SELECT * FROM students");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $student_number = $row['id'];
                                $first_name = $row['first_name'];
                                $last_name = $row['last_name'];
                                $status = $row['student_status'];
                                $image_path = $row['image']; // Adjust according to your database column name

                                // Display each student's information in a table row
                                echo "<tr>";
                                echo "<td><img src='../images/student_image/{$image_path}' alt='{$first_name}' class='student-img'></td>"; // Ensure correct path to images folder
                                echo "<td>{$student_number}</td>";
                                echo "<td>{$first_name}</td>";
                                echo "<td>{$last_name}</td>";
                                echo "<td>{$status}</td>";
                              
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
       function toggleNav() {
    var sidebar = document.getElementById("mySidebar");
    var main = document.getElementById("main");

    if (sidebar.style.marginLeft === "-250px" || sidebar.style.marginLeft === "") {
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
