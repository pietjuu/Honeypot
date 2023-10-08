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
if (isset($_GET['getData'])) {
    $data = $_GET['getData'];
    if ($data === "./chall2-data.txt") {
        echo file_get_contents($data);
    } else if (str_contains($data, "http://") ||
        str_contains($data, "https://") ||
        str_contains($data, "file:///") ||
        str_contains($data, "dict://") ||
        str_contains($data, "sftp://") ||
        str_contains($data, "tftp://") ||
        str_contains($data, "gopher://")) {
        echo '<script>alert("Challenge Solved!")</script>';
        $id = $account->getID();
        try {
            $account->solvedChallenge(2);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    } else {
        echo '<script>alert("Nice try, but no")</script>';
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
    <title>GR17-CHALL2</title>
</head>
<body>

<button onclick=" window.location.href = '2-c81e728d9d4c2f636f067f89cc14862c.php?getData=./chall2-data.txt';">
    Click me!
</button>


</body>
</html>