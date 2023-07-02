<?php
require_once 'database.php';
session_start();

function connectToDatabase()
{
    $database = new Db();
    return $database->getConnection();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'username' and 'password' keys exist in $_POST array
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Retrieve form data
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Validate form data
        if (empty($username) || empty($password)) {
            // Handle validation errors and set the error message
            $errorMessage = "Please fill in both the username and password.";
        } else {
            try {
                // Connect to the database
                $connection = connectToDatabase();

                // Query the database to check if the username exists
                $sql = "SELECT password FROM users WHERE username = ?";
                $stmt = $connection->prepare($sql);
                $stmt->execute([$username]);

                // Fetch the hashed password
                $hashedPassword = $stmt->fetchColumn();

                if ($hashedPassword) {
                    // Verify the password
                    if (password_verify($password, $hashedPassword)) {
                        // Login successful
                        $_SESSION['username'] = $username;
                        header("Location: home_page.php"); // Change it later to redirect to home_page.html
                        exit;
                    } else {
                        // Incorrect password
                        $errorMessage = "Incorrect username or password.";
                    }
                } else {
                    // User does not exist
                    $errorMessage = "Incorrect username or password.";
                }
            } catch (PDOException $e) {
                // Handle database errors
                $errorMessage = "Error: " . $e->getMessage();
            }
        }
    } else {
        $errorMessage = "Invalid form data.";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>
<body>
<div class="container">
    <h1>Login</h1>
    <form action="login.php" method="POST">
        
    <?php if (isset($errorMessage)): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required placeholder="enter your username">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="enter your password">
        <button type="submit">Login</button>

    </form>
    <p>Don't have an account? <a class="link" href="./registration.php">Register</a></p>
</div>
</body>
</html>
