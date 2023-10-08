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
if (!isset($_COOKIE['isAdmin'])) {
    setcookie('isAdmin', 'false');
    header("Location: /");
    exit;
}
if ($_COOKIE["isAdmin"] === 'true') {
    echo "You are an admin!";
    echo '<script>alert("Challenge Solved!")</script>';
    $id = $account->getID();
    try {
        $account->solvedChallenge(4);
    } catch (Exception $e) {
        echo $e->getMessage();
        die();
    }
} else {
    echo "You are not an admin!";
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GR17-CHALL4</title>
</head>
<body>
</body>
</html>


