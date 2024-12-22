<?php
// File: successfulLogin.php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
$curYear = date('Y');

// Function to get the last user ID
function getNewUserID(PDO $pdo): string {
    $sql = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
    $stm = $pdo->query($sql, PDO::FETCH_ASSOC);
    return $stm->rowCount() == 1 ? (string)$stm->fetch()['id'] : '';
}

// Function to get the last username
function getNewUsername(PDO $pdo): string {
    $sql = "SELECT username FROM users ORDER BY id DESC LIMIT 1";
    $stm = $pdo->query($sql, PDO::FETCH_ASSOC);
    return $stm->rowCount() == 1 ? $stm->fetch()['username'] : '';
}

// Greet the new user
function greeting(PDO $pdo): void {
    $newUser = getNewUsername($pdo);
    $id = getNewUserID($pdo);

    echo "<h2>Welcome to TaskList, <span class='highlight'>$newUser</span>!</h2>";
    echo "<p>You are user number <span class='highlight'>$id</span> to sign up.</p>";
}

try {
    require_once 'inc.db.php';
    $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
    $pdo = new PDO($dsn, USER, PWD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    ob_start(); // Start output buffering
    greeting($pdo);
    $greetingContent = ob_get_clean(); // Get the buffered content

} catch (PDOException $e) {
    die($e->getMessage());
}
$pdo = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Successful Login</title>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body {
            background-color: #fcf3cf;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 85vh;
        }
        .container {
            text-align: center;
            margin: auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
        }
        .highlight {
            color:rgb(218, 172, 99);
            font-weight: bold;
            font-family: "Times New Roman", sans-serif;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
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
    <div class="container">
        <?php echo $greetingContent; ?>
        <p><a href="loginForm.php">Back to login</a></p>
    </div>
    <footer id="footer" class="w3-container w3-center w3-text-gray">&copy; <?php echo date('Y'); ?> TaskList
</footer>

</body>
</html>
