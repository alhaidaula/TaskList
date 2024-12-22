<?php
// File: insert/two-tables/index.php
/* Demonstrates the creation of a DB, two tables and the
 * insertion of a record into each table. One form is used
 * to collect fields for both tables.
 */

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

function sanitizeInput($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

function insertUserRecord(PDO $pdo, string $username, string $password): int {
    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username, 'password' => $password]);
    $id = (int)$pdo->lastInsertId();
    echo "New User account created.<br>";
    return $id;
}

function passwordCheck(string $password, string $verifyPass): bool {
    return $password === $verifyPass;
}

function getUserID(PDO $pdo, string $username): int {
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $result = $stmt->fetch();
    return $result ? (int)$result['id'] : 0;
}

function saveUserRecord(PDO $pdo, string $username, string $password, string $verifyPass) {
    if (passwordCheck($password, $verifyPass)) {
        $hashedPass = password_hash($password, PASSWORD_BCRYPT);
        $userID = insertUserRecord($pdo, $username, $hashedPass);
        echo "Welcome! You are user number $userID.";
        header("Location: successfulLogin.php");
    } else {
        echo "Passwords do not match, try again!";
    }
}

$phpScript = sanitizeInput($_SERVER['PHP_SELF']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once 'inc.db.php';
        $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
        $pdo = new PDO($dsn, USER, PWD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $username = sanitizeInput($_POST['username']);
        $password = sanitizeInput($_POST['password']);
        $verifyPass = sanitizeInput($_POST['verifyPass']);

        saveUserRecord($pdo, $username, $password, $verifyPass);
    } catch (PDOException $e) {
        die($e->getMessage());
    }
    $pdo = null;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full viewport height */
    margin: 0;
    background-color:  #fcf3cf; /* Background color */
}

table {
    border-collapse: collapse;
    width: 150%; /* Adjust width as needed */
    max-width: 800px; /* Optional: restrict max width */
    background-color: #ffffff; /* Table background */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #ddd;
    text-align: center;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 12px;
    font-size: 16px;
    color: #34495e; /* Text color */
}

table th {
    background-color:  #fcf3cf; /* Header background color */
    color: #ffffff;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #f1c40f; /* Highlight on hover */
    color: #ffffff;
}

        .form-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 50px auto;
            max-width: 400px;
        }
        .form-container h1 {
            text-align: center;
            font-weight: bold;
            color: #34495e;
        }
        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Sign Up</h1>
        <form id="signUp" action="<?php echo $phpScript; ?>" method="POST">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="verifyPass" placeholder="Verify Password" required>
            <button type="submit">Create Account</button>
        </form>
    </div>
</body>
</html>
