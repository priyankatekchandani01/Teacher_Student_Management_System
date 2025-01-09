<?php
session_start();
include 'php/db_connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $class_id = $_POST['class_id'];
    $teacher_id = $_POST['teacher_id'];
    $date = $_POST['date'];

    // Validate input data
    if (empty($class_id) || empty($teacher_id) || empty($date)) {
        die('Invalid input');
    }

    // Connect to the database
    $conn = db_connect(); // Assume a function that returns a database connection

    // Insert the new class into the database
    $sql = "INSERT INTO classes (class_id, teacher_id, date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('iis', $class_id, $teacher_id, $date);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'Failed to add the class';
        }
        $stmt->close();
    } else {
        echo 'Database error';
    }

    $conn->close();
} else {
    echo 'Invalid request method';
}
?>
