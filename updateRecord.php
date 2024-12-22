<?php
// File: updateRecord.php

session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['username'])) {
    header('Location: loginForm.php');
    exit;
}

require_once 'inc.db.php';
$dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
$pdo = new PDO($dsn, USER, PWD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Initialize variables for feedback
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve POST input
    $recordType = filter_input(INPUT_POST, 'recordType', FILTER_SANITIZE_STRING);
    $recordId = filter_input(INPUT_POST, 'recordId', FILTER_SANITIZE_NUMBER_INT);
    $newName = filter_input(INPUT_POST, 'newName', FILTER_SANITIZE_STRING);
    $newDueDate = filter_input(INPUT_POST, 'newDueDate', FILTER_SANITIZE_STRING);
    $newStatus = filter_input(INPUT_POST, 'newStatus', FILTER_SANITIZE_STRING);

    try {
        if ($recordType === 'project') {
            // Update project record
            $sql = "UPDATE project SET name = :name, dueDate = :dueDate WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $newName, ':dueDate' => $newDueDate, ':id' => $recordId]);
            $message = "Project updated successfully.";
        } elseif ($recordType === 'task') {
            // Update task record
            $sql = "UPDATE task SET name = :name, status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $newName, ':status' => $newStatus, ':id' => $recordId]);
            $message = "Task updated successfully.";
        } else {
            $message = "Invalid record type specified.";
        }
    } catch (PDOException $e) {
        $message = "Error updating record: " . $e->getMessage();
    }
}

$pdo = null;
?>


<!DOCTYPE html>
<html>
<head>
    <title>Update Record</title>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        
        body {
            background-color: #fcf3cf;
            font-family: Arial, sans-serif;
            min-height: normal;
            justify-content: center;
            align-items: center;
            height: 85vh;

        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-title {
            background-color:#f9e79f;
            color: black;
            padding: 10px 15px;
            margin: -20px -20px 20px;
            text-align: center;
            font-size: 24px;
            border-radius: 8px 8px 0 0;
        }
        .w3-button {
            width: 100%;
            margin-top: 15px;
        }
        .message {
            margin: 20px auto;
            max-width: 600px;
        }

    footer{
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 70px; /* Height of the footer */
        background-color: #f9e79f;
        color: #515a5a; /* Correctly set the text color */
        text-align: center;
        line-height: 70px; /* Matches the height of the footer for vertical centering */
        font-size: 16px; /* Adjust font size as needed */
        font-family: Arial, sans-serif; /* Optional: Define a font family */

        }


        
    </style>
</head>
<body>
    <div class="w3-container">

        <?php if ($message): ?>
            <div class="w3-panel w3-pale-green w3-border message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <div class="form-title">Update Record</div>
            <form method="POST" action="updateRecord.php" class="w3-container">
                <p>
                    <label for="recordType">Record Type</label>
                    <select name="recordType" id="recordType" class="w3-select" required>
                        <option value="" disabled selected>Choose record type</option>
                        <option value="project">Project</option>
                        <option value="task">Task</option>
                    </select>
                </p>
                <p>
                    <label for="recordId">Record ID</label>
                    <input class="w3-input w3-border" type="number" name="recordId" id="recordId" required>
                </p>
                <p>
                    <label for="newName">New Name</label>
                    <input class="w3-input w3-border" type="text" name="newName" id="newName" required>
                </p>
                <p>
                    <label for="newDueDate">New Due Date (Projects Only)</label>
                    <input class="w3-input w3-border" type="date" name="newDueDate" id="newDueDate">
                </p>
               
                <p>
                    <button class="w3-button w3-grey yellow  w3-round" type="submit">Update</button>
                </p>
            </form>
            <p>
                <a href="index.php" class="w3-button w3-black w3-round">Back to Home</a>
            </p>
        </div>
    </div>
</body>

<footer id="footer" class="w3-container w3-center w3-text-gray">&copy; <?php echo date('Y'); ?> TaskList
</footer>


</html>
