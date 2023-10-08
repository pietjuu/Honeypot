<?php
session_start();
require '../assets/php/config.php';
require '../assets/php/account.php';
$account = new Account();
$login = FALSE;
try {
    $login = $account->sessionLogin();
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}
if (!$login) {
    header("Location: ../index.php");
}
$password = "0e540853622400160407992788832284";
if (isset($_POST['pwd'])) {
    $a = $_POST['pwd'];
    if (md5($a) == $password) {
        echo '<script>alert("Challenge Solved!")</script>';
        $id = $account->getID();
        try {
            $account->solvedChallenge(1);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    } else {
        echo '<script>alert("Incorrect, try again.")</script>';
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
    <title>GR17-CHALL1</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css"/>
</head>
<body class="text-center">

<main class="form-signin w-25 m-auto">
    <form class="p-4 p-md-5 border rounded-3 bg-light position-absolute top-50 start-50 translate-middle"
          action="1-c4ca4238a0b923820dcc509a6f75849b.php" method="post">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInput" value="admin" disabled>
            <label for="floatingInput">Username</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" name="pwd" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Password</label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Sign up</button>
    </form>
</main>
</body>
</html>