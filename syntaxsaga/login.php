<?php
session_start();

// Check if the user is already logged in, redirect to index.php
if(isset($_SESSION["syntaxsaga_users"])) {
    header("Location: index.html");
    exit(); // Ensure to exit after redirection
}

$errors = [];

if(isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    require_once "database.php";
    
    $sql = "SELECT * FROM syntaxsaga_users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user["password"])) {
            $_SESSION["syntaxsaga_users"] = "yes";
            header("Location: index.html");
            exit(); // Always exit after a header redirect
        } else {
            $errors[] = "Password does not match";
        }
    } else {
        $errors[] = "Username does not exist";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syntax Saga Login</title>
    <link rel="stylesheet" href="regandlog.css">
</head>
<body>
    <div class="wrapper">
        <form action="login.php" method="post">
            <h1>Login</h1>
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username:" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password:" required>
            </div>

            <button type="submit" name="login" value="Login" class="btn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="registration.php">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>