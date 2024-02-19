<?php
    session_start();
    if(isset($_SESSION["user"])){
        header("Location: index.html");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syntax Saga Registration</title>
    <link rel="stylesheet" href="regandlog.css">
</head>
<body>
    <div class="wrapper">
        <?php
        if(isset($_SESSION["user"])){
            header("Location: index.html");
            exit(); // Added exit after header redirect to prevent further execution
        }

        require_once "database.php";

        $errors = [];

        if(isset($_POST["submit"])) {
            $lastName = $_POST["LastName"];
            $firstName = $_POST["FirstName"];
            $email = $_POST["Email"];
            $username = $_POST["username"];
            $password = $_POST["password"];
            $repeatPassword = $_POST["repeat_password"];
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Validation
            if (empty($lastName) || empty($firstName) || empty($email) || empty($username) || empty($password) || empty($repeatPassword)) {
                $errors[] = "All fields are required";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors[] = "Email is not valid";
            }

            if (!isValidUsername($username)) {
                $errors[] = "Username is not valid. It should contain only letters, numbers, underscores, and hyphens.";
            }

            if(strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long";
            }

            if ($password != $repeatPassword){
                $errors[] = "Passwords do not match";
            }

            // Check if email already exists
            $sql = "SELECT * FROM syntaxsaga_users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount > 0) {
                $errors[] = "Email already exists";
            }

            // If no errors, insert user into database
            if (empty($errors)) {
                $sql = "INSERT INTO syntaxsaga_users (Last_Name, First_Name, email, username, password) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                if($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $lastName, $firstName, $email, $username, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You are Registered Successfully!</div>";
                } else {
                    die("Something went wrong.");
                }
            } else {
                // Display errors
                foreach($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }
        }

        function isValidUsername($username) {
            // Username should contain only letters, numbers, underscores, and hyphens.
            return preg_match('/^[a-zA-Z0-9_-]+$/', $username);
        }
        ?>
        <form action="registration.php" method="post">
            <h1>Register</h1>
            <div class="input-box">
                <input type="text" name="LastName" placeholder="Last Name:" required>
            </div>
            <div class="input-box">
                <input type="text" name="FirstName" placeholder="First Name:" required>
            </div>
            <div class="input-box">
                <input type="email" name="Email" placeholder="Email:" required>
            </div>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username:" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password:" required>
            </div>
            <div class="input-box">
                <input type="password" name="repeat_password" placeholder="Confirm Password:" required>
            </div>

            <button type="submit" name="submit" class="btn">Register</button>

            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
