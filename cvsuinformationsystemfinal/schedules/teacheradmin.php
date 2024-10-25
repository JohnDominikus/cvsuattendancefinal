<?php
// Include database connection
include '../connection/config.php';

// Initialize variables to hold form input values
$subjectName = "";
$subjectCode = "";
$time = "";

// Handling form submission to add a new subject
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs (you should add more robust validation)
    $subjectName = htmlspecialchars($_POST['subject_name']);
    $subjectCode = htmlspecialchars($_POST['subject_code']);
    $time = htmlspecialchars($_POST['time']);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO subjects (subject_name, subject_code, time) VALUES (:subject_name, :subject_code, :time)");
    $stmt->bindParam(':subject_name', $subjectName);
    $stmt->bindParam(':subject_code', $subjectCode);
    $stmt->bindParam(':time', $time);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to refresh the subjects list after adding
        header("Location: teacheradmin.php");
        exit();
    } else {
        echo "Error adding subject.";
    }
}

// Query to fetch all subjects
$query = $conn->query("SELECT * FROM subjects");

// Check if there are any subjects
if ($query->rowCount() > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Teacher Admin</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <!-- Custom CSS -->
        <style>
            /* Additional styles if needed */
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Subjects List</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Subject ID</th>
                            <th scope="col">Subject Code</th>
                            <th scope="col">Subject Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Time</th>
                            <th scope="col">Credits</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through each row (subject)
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['subject_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['subject_code']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['credits']) . "</td>";
                            echo "<td>";
                            echo "<a href='../schedules/edit_subject.php?id=" . $row['subject_id'] . "' class='btn btn-sm btn-primary mr-1'>Edit</a>";
                            echo "<a href='delete_subject.php?id=" . $row['subject_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this subject?\")'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bootstrap JS and dependencies -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
} else {
    echo "<p>No subjects found.</p>";
}

// Add Subject Form
?>
<div class="container mt-4">
    <div class="card">
        <h5 class="card-header">Add New Subject</h5>
        <div class="card-body">
            <form action="teacheradmin.php" method="POST">
                <div class="form-group">
                    <label for="subject_name">Subject Name</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?= $subjectName ?>" required>
                </div>
                <div class="form-group">
                    <label for="subject_code">Subject Code</label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?= $subjectCode ?>" required>
                </div>
                <div class="form-group">
                    <label for="time">Time</label>
                    <select class="form-control" id="time" name="time">
                        <option value="08:00">08:00 AM</option>
                        <option value="09:00">09:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">01:00 PM</option>
                        <option value="14:00">02:00 PM</option>
                        <option value="15:00">03:00 PM</option>
                        <option value="16:00">04:00 PM</option>
                        <option value="17:00">05:00 PM</option>
                        <option value="18:00">06:00 PM</option>
                        <option value="19:00">07:00 PM</option>
                        <option value="20:00">08:00 PM</option>
                        <!-- Add more time options as needed -->
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Subject</button>
                <a href="../dashboards/admindash.php" class="btn btn-secondary">Back</a>
                <a href="archived_subjects.php" class="btn btn-secondary">View Archived Subjects</a>
            </form>
        </div>
    </div>
</div>
