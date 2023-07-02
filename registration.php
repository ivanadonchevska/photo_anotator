<?php
require_once 'database.php';

function connectToDatabase()
{
    $database = new Db();
    return $database->getConnection();
}

// Retrieve form data
$username = isset($_POST['username']) ? $_POST['username'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$cpassword = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';

// Initialize an array to store validation errors
$errors = [];

// Validate form data (add more validation as needed)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($username) || empty($password) || empty($email)) {
        // Add validation error to the errors array
        $errors[] = "Please fill in all the fields.";
    }

    if ($password !== $cpassword) {
        // Add validation error to the errors array
        $errors[] = "Passwords do not match.";
    }

    // If there are no validation errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Connect to the database
            $connection = connectToDatabase();

            // Check if the user already exists
            $sql = "SELECT COUNT(*) FROM users WHERE username = ? OR email = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$username, $email]);
            $userExists = $stmt->fetchColumn();

            if ($userExists) {
                // User already exists
                $errors[] = "Username or email already taken. Please choose different credentials.";
            }

            // If there are no errors, proceed with user registration
            if (empty($errors)) {
                // Prepare and execute the SQL query to insert the user
                $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
                $stmt = $connection->prepare($sql);
                $stmt->execute([$username, $hashedPassword, $email]);

                // Registration successful, redirect to login.php
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            // Registration failed
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Page</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>
<body>
    <div class="container">
        <h1>Registration</h1>
        <form action="registration.php" method="POST">
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required placeholder="enter your username" value="<?php echo $username; ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="enter your email" value="<?php echo $email; ?>">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required placeholder="enter your password">

            <label for="password">Confirm Password:</label>
            <input type="password" id="password" name="cpassword" required placeholder="confirm your password">

            <button type="submit">Register</button>

            <p>Already have an account? <a class="link" href="./login.php">Login</a></p>
        </form>
    </div>
</body>
</html>