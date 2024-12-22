<?php

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

$curYear = date('Y');
session_start();

$userName = $_SESSION['username'] ?? null;
if ($userName) {
    $welcomeMessage = "<h3>Add a Record | <a href='index.php'>Home</a></h3>";
} else {
    die('User not logged in. Please log in first.');
}

function sanitizeInput($value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

function insertProjectRecord(PDO $pdo, string $name, string $date, int $userID): int {
    if (!$userID) {
        die("Error: User ID not found. Cannot insert project.");
    }

    $sql = "
    INSERT INTO project (user\$id, name, dueDate)
    VALUES (:userID, :name, :dueDate)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':dueDate', $date, PDO::PARAM_STR);

    $stmt->execute();
    return (int)$pdo->lastInsertId();
}

function saveProjectRecord(PDO $pdo, string $name, string $date, string $userName) {
    $userID = getUserID($pdo, $userName);

    if ($userID) {
        $projectID = insertProjectRecord($pdo, $name, $date, $userID);
        if ($projectID) {
            echo "Project successfully added with ID: $projectID.<br>";
            header("Location: addConfirm.php?success=1");
            exit;
        } else {
            die("Failed to add project. Please try again.");
        }
    } else {
        die("Error: User ID not found for username: $userName.");
    }
}

function getUserID(PDO $pdo, string $userName): int {
    $sql = "
    SELECT id
    FROM users
    WHERE username = :userName
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->rowCount() === 1 ? (int)$stmt->fetch(PDO::FETCH_ASSOC)['id'] : 0;
}

$phpScript = sanitizeInput($_SERVER['PHP_SELF']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once 'inc.db.php';
        $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
        $pdo = new PDO($dsn, USER, PWD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $name = sanitizeInput($_POST['name']);
        $date = sanitizeInput($_POST['date']);

        saveProjectRecord($pdo, $name, $date, $userName);
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
    <title>Add Project</title>
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

        h3 {
            margin: 0;
            font-size: 24px;
        
        }

        .container {
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 650px;
            margin: 50px auto;

          
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
        }

        input[type="text"]:focus, input[type="date"]:focus {
            border-color: #f9e79f;
            outline: none;
        }

        button {
            background-color: #f9e79f;
            color: 0000;
            padding: 14px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color:  0000;
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
        font-family: Arial, sans-serif; /* Optional: Define a font family */

        }

        .form-header {
            font-weight: bold;
            text-align: center;
            color: #000;
            text-align: center;
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
            color: 0000;
            font-size: 16px;
        }

        .link-text a {
            text-decoration: none;
            color: rgba(148, 104, 11, 0.99) ;
            font-weight: bold;
        }

        .link-text a:hover {
            color: 0000;
        }

    </style>
</head>
<body>

   

    <div class="container form-container">
        <div class="form-header">
            <h2>Add a Project</h2>
        </div>

        <form action="<?php echo $phpScript; ?>" method="POST">
            <input type="text" name="name" placeholder="Project Name" required>
            <input type="date" name="date" min="1997-01-01" max="2030-12-31" required>
            <button type="submit">Submit Project</button>
        </form>

        <div class="form-footer">
            <p class="form-text">Once you submit, the project will be added to the system.</p>
        </div>

        <div class="link-text">
            <p><a href="index.php">Go back to Home</a></p>
        </div>
    </div>

    <footer>
        &copy; <?php echo $curYear; ?> TaskList
    </footer>

</body>
</html>
