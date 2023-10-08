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
echo '<script>alert("Challenge Solved!")</script>';
$id = $account->getID();
try {
    $account->solvedChallenge(5);
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GR17-CHALL5</title>
</head>
<body>
<h1>You found Challenge 5!</h1>
</body>
</html>

