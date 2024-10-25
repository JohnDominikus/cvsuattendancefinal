<?php
// delete_subject.php

// Include database connection
include '../connection/config.php';

// Check if ID parameter is set and numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $subject_id = $_GET['id'];

    // Update query to archive subject
    $stmt = $conn->prepare("UPDATE subjects SET archived = 1 WHERE subject_id = :subject_id");
    $stmt->bindParam(':subject_id', $subject_id);

    // Execute query
    if ($stmt->execute()) {
        // Redirect to subjects list or admin page
        header("Location: ../admin/subjects_list.php");
        exit;
    } else {
        // Error handling if update fails
        echo "Failed to archive subject.";
        exit;
    }
} else {
    // Redirect if ID parameter is missing or invalid
    header("Location: ../admin/subjects_list.php");
    exit;
}
?>
