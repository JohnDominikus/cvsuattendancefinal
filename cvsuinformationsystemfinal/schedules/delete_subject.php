<?php
include '../connection/config.php';

if (isset($_GET['id'])) {
    $subjectId = intval($_GET['id']);

    // Move the subject to archived_subjects
    $stmt = $conn->prepare("INSERT INTO archived_subjects (subject_id, subject_code, subject_name, description, time, credits)
                            SELECT subject_id, subject_code, subject_name, description, time, credits 
                            FROM subjects 
                            WHERE subject_id = :subject_id");
    $stmt->bindParam(':subject_id', $subjectId);

    if ($stmt->execute()) {
        // Now delete from subjects table
        $deleteStmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = :subject_id");
        $deleteStmt->bindParam(':subject_id', $subjectId);

        if ($deleteStmt->execute()) {
            header("Location: teacheradmin.php");
            exit();
        } else {
            echo "Error deleting subject.";
        }
    } else {
        echo "Error archiving subject.";
    }
} else {
    echo "Invalid subject ID.";
}
?>
