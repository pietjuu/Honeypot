<?php
session_start();
require './assets/php/config.php';
require './assets/php/account.php';
$account = new Account();
$login = FALSE;
try {
    $login = $account->sessionLogin();
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}
if ($login) {
    echo "You are logged in.";
    header("Location: ./challenges.php");
} else
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $login = $account->login($username, $password);
    } catch (Exception $e) {
        echo $e->getMessage();
        die();
    }
    if ($login) {
        echo 'Authentication successful.';
        header("Location: ./challenges.php");
    } else {
        echo 'Authentication failed.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css"/>
    <title>GR17-LOGIN</title>
</head>
<body>
<header class="p-3 bg-dark border-bottom border-secondary">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <a href="./index.php" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <img src="./assets/images/honeypot.png" alt="Honeypot logo" height="50"
                     role="img" aria-label="Bootstrap">
                <h1>Honeypot Project</h1>
            </a>

            <div>
                <a href="./sign-up.php">
                    <button type="button" class="btn btn-primary">Sign-up</button>
                </a>
            </div>
        </div>
    </div>
</header>

<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <div class="row align-items-center g-lg-5 py-5">
        <div class="col-lg-7 text-center text-lg-start">
            <h1 class="display-4 fw-bold lh-1 mb-3">Login</h1>
        </div>
        <div class="col-md-10 mx-auto col-lg-5">
            <form class="p-4 p-md-5 border rounded-3 bg-light" action="sign-in.php" method="post">
                <div class="form-floating mb-3">
                    <input type="text" name="username" class="form-control" id="floatingInput" placeholder="JohnDoe">
                    <label for="floatingInput">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" name="password" class="form-control" id="floatingPassword"
                           placeholder="Password">
                    <label for="floatingPassword">Password</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
                <div class="mb-3">
                    <p>Don't have an account yet? <a href="./sign-up.php" class="text-primary">Sign-up</a> instead.</p>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
