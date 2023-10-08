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
    <title>GR17-HONEYPOT</title>
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
                <a href="./sign-in.php"><button type="button" class="btn btn-outline-light me-2">Login</button></a>
                <a href="./sign-up.php"><button type="button" class="btn btn-primary">Sign-up</button></a>
            </div>
        </div>
    </div>
</header>

<div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
        <div class="col-10 col-sm-8 col-lg-6">
            <img src="./assets/images/honeypot.png" class="d-block mx-lg-auto img-fluid" alt="Honeypot logo"
                 width="700" height="500" loading="lazy">
        </div>
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold lh-1 mb-3">Sign-up to get started</h1>
            <p class="lead">To start using this site you must first create an account. Once logged in you'll be able to
                access the challenges and find vulnerabilities.</p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="./sign-up.php">
                    <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">Sign-up</button>
                </a>
                <a href="./sign-in.php">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4">Login</button>
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
