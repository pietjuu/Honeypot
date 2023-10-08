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
if (!$login) {
    header("Location: ./index.php");
}
$id = $account->getId();
try {
    $solvedCh = $account->getSolvedChallenges();
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}
?>
<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css"/>
    <script src="https://kit.fontawesome.com/da2d1bb890.js" crossorigin="anonymous"></script>
    <title>GR17-CHALLS</title>
</head>
<body>
<header class="p-3 bg-dark">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <a href="./challenges.php" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <img src="./assets/images/honeypot.png" alt="Honeypot logo" height="50"
                     role="img" aria-label="Bootstrap">
                <h1>Honeypot Project</h1>
            </a>
            <a href="./profile.php">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>
    </div>
</header>

<div class="d-flex flex-wrap align-items-center">

    <div class="card m-4" style="width: 18rem; height: 18rem;">
        <div class="card-body">
            <a href="../WebEnvPersonalGit/challenges/1-c4ca4238a0b923820dcc509a6f75849b.php"><h5 class="card-title">Challenge 1</h5></a>
            <h6 class="card-subtitle mb-2 text-muted">PHP</h6>
            <p>Exploit this vulnerability in PHP.</p><br>
            <p class="card-text">
                <?php
                if ($solvedCh[0] == 1) {
                    echo '<p class="text-success"><i class="fa-solid fa-check"></i>Solved</p>';
                } else {
                    echo '<p class="text-danger"><i class="fa-solid fa-times"></i>Not solved</p>';
                }
                ?>
        </div>
    </div>

    <div class="card m-4" style="width: 18rem; height: 18rem;">
        <div class="card-body">
            <a href="../WebEnvPersonalGit/challenges/2-c81e728d9d4c2f636f067f89cc14862c.php"><h5 class="card-title">Challenge 2</h5></a>
            <h6 class="card-subtitle mb-2 text-muted">SSRF</h6>
            <p>Get SSRF to work on this page.</p><br>
            <p class="card-text">
                <?php
                if ($solvedCh[1] == 1) {
                    echo '<p class="text-success"><i class="fa-solid fa-check"></i>Solved</p>';
                } else {
                    echo '<p class="text-danger"><i class="fa-solid fa-times"></i>Not solved</p>';
                }
                ?>
        </div>
    </div>

    <div class="card m-4" style="width: 18rem; height: 18rem;">
        <div class="card-body">
            <a href="../WebEnvPersonalGit/challenges/3-eccbc87e4b5ce2fe28308fd9f2a7baf3.php"><h5 class="card-title">Challenge 3</h5></a>
            <h6 class="card-subtitle mb-2 text-muted">XSS</h6>
            <p>Get a script to execute on this page.</p><br>
            <p class="card-text">
                <?php
                if ($solvedCh[2] == 1) {
                    echo '<p class="text-success"><i class="fa-solid fa-check"></i>Solved</p>';
                } else {
                    echo '<p class="text-danger"><i class="fa-solid fa-times"></i>Not solved</p>';
                }
                ?>
        </div>
    </div>


    <div class="card m-4" style="width: 18rem; height: 18rem;">
        <div class="card-body">
            <a href="../WebEnvPersonalGit/challenges/4-a87ff679a2f3e71d9181a67b7542122c.php"><h5 class="card-title">Challenge 4</h5></a>
            <h6 class="card-subtitle mb-2 text-muted">Broken Authentication</h6>
            <p>Get access to the page contents even though you are not authorised</p>
            <p class="card-text">
                <?php
                if ($solvedCh[3] == 1) {
                    echo '<p class="text-success"><i class="fa-solid fa-check"></i>Solved</p>';
                } else {
                    echo '<p class="text-danger"><i class="fa-solid fa-times"></i>Not solved</p>';
                }
                ?>
        </div>
    </div>
    <div class="card m-4" style="width: 18rem; height: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Challenge 5</h5>
            <h6 class="card-subtitle mb-2 text-muted">IDOR</h6>
            <p>Try finding this page yourself!</p><br>
            <p class="card-text">
                <?php
                if ($solvedCh[4] == 1) {
                    echo '<p class="text-success"><i class="fa-solid fa-check"></i>Solved</p>';
                } else {
                    echo '<p class="text-danger"><i class="fa-solid fa-times"></i>Not solved</p>';
                }
                ?>
        </div>
    </div>
</div>
</body>
</html>

