<?php

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: loginForm.php");
    exit;
}

$userName = $_SESSION['username'];

function sanitizeInput($value): string {
    return htmlspecialchars(stripslashes(trim($value)));
}

// Delete project by ID
function deleteProject(PDO $pdo, int $projectId): void {
    $sql = "DELETE FROM project WHERE id = :projectId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':projectId' => $projectId]);
    echo "<div class='message success'>Project with ID $projectId deleted successfully.</div>";
}

// Delete task by ID
function deleteTask(PDO $pdo, int $taskId): void {
    $sql = "DELETE FROM task WHERE id = :taskId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':taskId' => $taskId]);
    echo "<div class='message success'>Task with ID $taskId deleted successfully.</div>";
}

try {
    require_once 'inc.db.php';
    $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
    $pdo = new PDO($dsn, USER, PWD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $deleteType = sanitizeInput($_POST['deleteType']);
        $recordId = (int)sanitizeInput($_POST['recordId']);
        $confirm = sanitizeInput($_POST['answer']);

        if ($confirm === 'yes') {
            if ($deleteType === 'project') {
                deleteProject($pdo, $recordId);
            } elseif ($deleteType === 'task') {
                deleteTask($pdo, $recordId);
            } else {
                echo "<div class='message error'>Invalid delete type selected.</div>";
            }
        } else {
            echo "<div class='message warning'>Deletion cancelled.</div>";
        }
    }
} catch (PDOException $e) {
    die("<div class='message error'>Error: " . $e->getMessage() . "</div>");
}

$pdo = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fcf3cf;
            margin: 0;
            padding: 0;
            min-height: normal;
            justify-content: center;
            align-items: center;
            height: 100vh;

        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label, input, select, button {
            display: block;
            width: 100%;
            margin: 10px 0;
        }

        input, select, button {
            padding: 10px;
            font-size: 16px;
        }

        button {
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #c0392b;
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


        /* Message Styles */
        .message {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            text-align: center;
            margin: 20px auto;
            max-width: 600px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .home-button {
            background-color: #34495e;
            color: white;
            padding: 10px;
            border: none;
            text-align: center;
            display: block;
            width: 100%;
            margin-top: 15px;
            cursor: pointer;
        }

        .home-button:hover {
            background-color: #2c3e50;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Delete a Record</h1>
        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label for="deleteType">Choose Type:</label>
            <select name="deleteType" id="deleteType" required>
                <option value="project">Project</option>
                <option value="task">Task</option>
            </select>

            <label for="recordId">Enter Record ID:</label>
            <input type="number" name="recordId" id="recordId" placeholder="Enter the ID to delete" required>

            <label for="answer">Are you sure?</label>
            <select name="answer" id="answer" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <button type="submit">Delete</button>
        </form>

        <button class="home-button" onclick="window.location.href='index.php';">Go to Home</button>
    </div>

    <footer id="footer" class="w3-container w3-center w3-text-gray">&copy; <?php echo date('Y'); ?> TaskList
</footer>

</body>
</html>
