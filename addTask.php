<?php

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

$curYear = date('Y');
session_start();

$userName = $_SESSION['username'] ?? null;
if (isset($userName)) { 
    $welcomeMessage = "<h3>Add a Task | <a href='addRecord.php'>Add Project</a> | <a href='index.php'>Home</a></h3>";
} else {
    header('Location: loginForm.php');
    exit;
}

function sanitizeInput($value): string {
    return htmlspecialchars(stripslashes(trim($value)));
}

function insertTaskRecord(PDO $pdo, string $name, int $projectID): void {
    $sql = "INSERT INTO task (project\$id, name) VALUES (:projectID, :name)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
}

function saveProjectTask(PDO $pdo, string $name, string $userName): void {
    $projectID = getProjectID($pdo, $userName);
    if ($projectID === null) {
        echo "<h1 style='color:red'>***ERROR: No project exists, please add a project before adding a task.***</h1>";
    } else {
        insertTaskRecord($pdo, $name, $projectID);
        echo "<h1 style='color:green'>Task entered successfully!</h1>";
    }
}

function getUserID(PDO $pdo, string $userName): ?int {
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $userName, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['id'] ?? null;
}

function getProjectID(PDO $pdo, string $userName): ?int {
    $userID = getUserID($pdo, $userName);

    if ($userID === null) {
        return null;
    }

    $sql = "SELECT id FROM project WHERE user\$id = :userID ORDER BY id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['id'] ?? null;
}

$phpScript = sanitizeInput($_SERVER['PHP_SELF']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once 'inc.db.php';
        $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
        $pdo = new PDO($dsn, USER, PWD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $name = sanitizeInput($_POST['name']);
        saveProjectTask($pdo, $name, $userName);

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    } finally {
        $pdo = null;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
   
   <style>

body {
    background-color: #fcf3cf;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: normal;
    justify-content: center;
    align-items: center;
    height: 85vh;

}

header {
    background-color: #f9e79f;
    color: #333;
    padding: 15px 0;
    text-align: center;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for the header */
}

h3 {
    margin: 0;
    font-size: 24px;
}

.container {
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 650px;
    width: 100%; /* Ensure it takes up full width up to the max */
}

form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

input[type="text"], input[type="date"] {
    padding: 12px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ddd;
    box-sizing: border-box;
    width: 100%; /* Ensure input fields stretch across the container */
}

input[type="text"]:focus, input[type="date"]:focus {
    border-color: #f9e79f;
    outline: none;
}

button {
    background-color: #f9e79f;
    color: #333; /* Ensure contrast for readability */
    padding: 14px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease; /* Smooth transition */
}

button:hover {
    background-color: #e8d50f; /* Darken color on hover */
}

footer {
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
    font-family: Arial, sans-serif;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for footer */
}

.form-header {
    font-weight: bold;
    text-align: center;
    color: #333;
    font-size: 24px;
}

.form-text {
    font-size: 16px;
    color: #555;
    text-align: center;
}

.form-container {
    max-width: 500px;
    margin: 30px auto;
}

.link-text {
    text-align: center;
    color: #333;
    font-size: 16px;
}

.link-text a {
    text-decoration: none;
    color: rgba(148, 104, 11, 0.99);
    font-weight: bold;
    transition: color 0.3s ease; /* Smooth transition for hover effect */
}

.link-text a:hover {
    color: #f9e79f; /* Change color on hover */
}

.container {
    padding: 30px;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

input[type="text"], input[type="date"] {
    padding: 12px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ddd;
    box-sizing: border-box;
    width: 100%;
}

form .form-header {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    color: #333;
}

footer {
    background-color: #f9e79f;
    color: #515a5a;
    text-align: center;
    padding: 20px;
    font-size: 16px;
}

footer a {
    color: #333;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>

    <div class="container">
        <div class="form-header">
            <h2>Add a Task</h2>
        </div>

        <form action="<?php echo $phpScript; ?>" method="POST">
            <input type="text" name="name" placeholder="Task Name" required>
            <button type="submit">Submit Task</button>
        </form>

        <div class="link-text">
            <p><a href="index.php">Go back to Home</a></p>
        </div>
    </div>

    <footer>
        &copy; <?php echo $curYear; ?> TaskList
    </footer>

</body>
</html>
