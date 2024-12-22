<?php
// File: welcome.php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

$curYear = date('Y');
$projectStatus = '';

session_start();
$userName = $_SESSION['username'] ?? null;

if (!$userName) {
    header('Location: loginForm.php');
    exit;
}

$welcomeMessage = "&emsp; Welcome to your Task List, $userName.";

// Disable browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

function getUserID(PDO $pdo, string $userName): ?int {
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $userName, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['id'] ?? null;
}

function listProjects(PDO $pdo, int $userID): void {
    $sql = "SELECT id, name, dueDate FROM project WHERE user\$id = :userID ORDER BY id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($projects)) {
        echo "<h3 style='text-align:center; color:black;'>You currently have no projects due.</h3>";
        return;
    }

    echo "<h3 style='
    text-align: center;
    color: #333;
    font-family: \"Times New Roman\", sans-serif;
    font-weight: bold;
    font-size: 24px;
    text-transform: sentence-case;
    letter-spacing: 1px;
    width: 50%; /* Adjust this value as needed */
    height: 10%;
    margin: 0 auto; /* Centers the element horizontally */
    min-height: 100px; 
    line-height: 130px
'>Your everyday TODO:</h3>";

    echo "<table border=1 cellspacing=0 cellpadding=0>
          <tr>
              <td style='height:40px;width:160px;text-align:center'><font color=blue>Project Name</font></td>
              <td style='height:40px;width:160px;text-align:center'><font color=red>Due Date</font></td>
          </tr>";

    foreach ($projects as $project) {
        echo "<tr>
                  <td style='height:40px;width:160px;text-align:center'>{$project['name']}</td>
                  <td style='height:40px;width:160px;text-align:center'>{$project['dueDate']}</td>
              </tr>";
    }
    echo "</table>";

    foreach ($projects as $project) {
        listTasks($pdo, $project['id']);
    }
}

function listTasks(PDO $pdo, int $projectID): void {
    $sql = "SELECT name FROM task WHERE project\$id = :projectID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($tasks)) {
        echo "<h4 style='text-align:center; color:black;'>Tasks for Project ID $projectID:</h4>";
        echo "<table border=1 cellspacing=0 cellpadding=0>
              <tr>
                  <td style='height:40px;width:160px;text-align:center'><font color=blue>Task</font></td>
              </tr>";

        foreach ($tasks as $task) {
            echo "<tr>
                      <td style='height:40px;width:160px;text-align:center'>{$task['name']}</td>
                  </tr>";
        }
        echo "</table>";
    }
}

try {
    require_once 'inc.db.php';
    $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
    $pdo = new PDO($dsn, USER, PWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false
    ]);

    $userID = getUserID($pdo, $userName);

    if (!$userID) {
        die("Error: Unable to fetch user ID for username $userName.");
    }

    echo "<div class='w3-panel' id='tables'>";
    
    listProjects($pdo, $userID);
    echo "</div>";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
$pdo = null;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>

<body id="body" class="w3-container w3-margin-left">

    <!-- Welcome message -->
    <div class="w3-panel">
        <header id="header" class="w3-container w3-center w3-text-gray">
            <h2>
                <?php 
                    echo $welcomeMessage;
                    echo $projectStatus;
                ?>
            </h2>
            <h5>
                <a href='addRecord.php'>Add Project</a>&emsp;
                <a href='addTask.php'>Add Task</a>&emsp;
                <a href='updateRecord.php'>Update</a>&emsp;
                <a href='deleteRecord.php'>Delete Record</a>&emsp;
                <a style='color:red' href='loginForm.php'>Logout</a>
            </h5>
        </header>
    </div>
</body>
<footer id="footer" class="w3-container w3-center w3-text-gray">&copy; <?php echo $curYear; ?> TaskList </footer>

<style>
        /* General body styles */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #fcf3cf;
    display: flex;
    flex-direction: column;
    min-height: 200px;
}

/* Header styles */
header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%; /* Changed from 1000% to 100% */
    height: 40px; /* Increased height to accommodate content */
    background-color: #f9e79f;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

header h2 {
    margin: 0;
    font-size: 20px;
    color: #333;
}

header nav {
    display: flex;
    gap: 15px;
}

header nav a {
    text-decoration: none;
    color: black;
    font-size: 16px;
}

header nav a:hover {
    color: red;
}


/* Main content */
.content {
    margin: 80px auto;
    flex: 1;
    width: 90%;
    max-width: 1200px;
}

.content h3 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

table th, table td {
    border: 1px solid #ddd;
    text-align: center;
    padding: 10px;
    font-size: 17px;
}

table th {
    background-color: #f9e79f;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Footer styles */
footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 70px;
    background-color: #f9e79f;
    text-align: center;
    line-height: 70px;
    color: #515a5a;
    font-size: 16px;
    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
}

/* Button styles */
button, a.w3-button {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 15px;
    font-size: 14px;
    text-align: center;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    text-decoration: none;
}

button:hover, a.w3-button:hover {
    background-color: #0056b3;
}

/* Success message styling */
.success-message {
    color: green;
    background-color: #d4edda;
    padding: 10px;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}

    </style>
    
    </html> 