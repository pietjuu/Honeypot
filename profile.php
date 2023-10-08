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
    echo "This the account of: " . $account->getName();
    echo '<br><a href="./profile.php?logout=1">Logout</a>';
} else {
    header("Location: ./index.php");
}
if (isset($_GET['logout'])) {
    if ($_GET['logout'] == 1) {
        try {
            $account->logout();
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
        header("Location: ./index.php");
    }
}