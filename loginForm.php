<?php
// File: loginForm.php
declare(strict_types=1);

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

$curYear = date('Y');
$username = $password = $errorMessage = "";
$phpScript = htmlspecialchars($_SERVER['PHP_SELF']);

function sanitizeValue($value)
{
    return htmlspecialchars(stripslashes(trim($value)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'inc.db.php';

    $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
    try {
        $pdo = new PDO($dsn, USER, PWD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $loginUsername = sanitizeValue($_POST['username']);
        $loginPassword = sanitizeValue($_POST['password']);

        // Use a prepared statement to securely fetch the user record
        $sql = "SELECT username, password FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $loginUsername, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($loginPassword, $userRecord['password'])) {
                // User authenticated successfully
                $_SESSION['username'] = $loginUsername;

                // Redirect to the welcome page
                header('Location: index.php');
                exit;
            } else {
                $errorMessage = "Invalid username or password.";
            }
        } else {
            $errorMessage = "Account not found.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Database error: " . $e->getMessage();
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
    <title>Login</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fcf3cf;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            background-color: #ffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background-color: #f9e79f;
            padding: 20px;
            text-align: center;
        }

        .login-header h1 {
            color: #000;
            font-weight: bold;
            margin: 0;
            font-size: 1.8rem;
        }

        .login-form {
            padding: 20px;
        }

        .login-form label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: #f9e79f;
            color: #000;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-form button:hover {
            background-color: #f9e79f;
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-top: -10px;
            margin-bottom: 15px;
        }

        .signup-link {
            text-align: center;
            margin-top: 10px;
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
    <div class="login-card">
        <div class="login-header">
            <h1>Login</h1>
        </div>
        <form action="<?php echo $phpScript; ?>" method="POST" class="login-form">
            <?php if (!empty($errorMessage)): ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>

            <label for="username">Username <span class="w3-text-red">*</span></label>
            <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo $username; ?>" required>

            <label for="password">Password <span class="w3-text-red">*</span></label>
            <input type="password" id="password" name="password" placeholder="Enter your password" value="<?php echo $password; ?>" required>

            <button type="submit">Login</button>
        </form>

        <div class="signup-link">
            <p>No account yet? <a href="signUp.php">Sign Up!</a></p>
        </div>
    </div>

    <footer id="footer" class="w3-container w3-center w3-text-gray">&copy; <?php echo date('Y'); ?> TaskList
</footer>
</body>
</html>