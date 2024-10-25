<?php
include '../connection/config.php';

if (isset($_GET['id'])) {
    $archivedId = intval($_GET['id']);

    // Move the subject back to subjects
    $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, description, time, credits)
                            SELECT subject_code, subject_name, description, time, credits 
                            FROM archived_subjects 
                            WHERE archived_id = :archived_id");
    $stmt->bindParam(':archived_id', $archivedId);

    if ($stmt->execute()) {
        // Now delete from archived_subjects table
        $deleteStmt = $conn->prepare("DELETE FROM archived_subjects WHERE archived_id = :archived_id");
        $deleteStmt->bindParam(':archived_id', $archivedId);

        if ($deleteStmt->execute()) {
            header("Location: archived_subjects.php");
            exit();
        } else {
            echo "Error deleting from archived subjects.";
        }
    } else {
        echo "Error restoring subject.";
    }
} else {
    echo "Invalid archived ID.";
}
?>
