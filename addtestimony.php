<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = new mysqli('localhost', 'root', '', 'perfectmeal');

if ($mysqli->connect_error) {
    die('Database Connection Error: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Fetch POST data and sanitize
$id = session_id();
$name = mysqli_real_escape_string($mysqli, $_POST['name'] ?? '');
$profession = mysqli_real_escape_string($mysqli, $_POST['profession'] ?? '');
$feedback = mysqli_real_escape_string($mysqli, $_POST['feedback'] ?? '');

// Check for the image file
if (isset($_FILES['image']['tmp_name']) && file_exists($_FILES['image']['tmp_name'])) {
    $imageContent = file_get_contents($_FILES['image']['tmp_name']);
} else {
    echo 'image';
    exit;
}

// VALIDATION
if (strlen($name) < 2) {
    echo 'name';
} elseif (strlen($profession) < 2) {
    echo 'profession';
} elseif (strlen($feedback) <= 4) {
    echo 'feedback';
} elseif (empty($imageContent)) {
    echo 'image';
} else {
    $query = "SELECT * FROM feedback WHERE id='$id'";
    $result = $mysqli->query($query);

    if ($result->num_rows < 1) {
        $stmt = $mysqli->prepare("INSERT INTO feedback (id, name, profession, image, feedback) VALUES (?, ?, ?, ?, ?)");

        // Check if prepare() failed
        if (!$stmt) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            exit;
        }

        $stmt->bind_param("sssss", $id, $name, $profession, $imageContent, $feedback);

        // Execute and check for errors
        if ($stmt->execute()) {
            echo 'true';
        } else {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo 'false';
    }
}

$mysqli->close();
?>
