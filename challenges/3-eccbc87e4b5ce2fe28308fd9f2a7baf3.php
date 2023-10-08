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
if (isset($_FILES['image'])) {
    $errors = array();
    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileType = $_FILES['image']['type'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $extensions = array("image/jpeg", "image/jpg", "image/png", "image/svg+xml");

    if (in_array($fileType, $extensions) === false) {
        $errors[] = "extension not allowed, please choose a JPEG, PNG or SVG file.";
    }

    if ($fileSize > 2097152) {
        $errors[] = 'File size must be under 2 MB';
    }


    if (empty($errors)) {
        $n = 20;
        try {
            $newName = bin2hex(random_bytes($n));
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
        $fileLink = "./uploads/$newName.$fileExt";
        move_uploaded_file($fileTmp, $fileLink);
        if ($fileType === "image/svg+xml") {
            if (str_contains(file_get_contents($fileLink), "script")) {
                echo '<script>alert("Challenge Solved!")</script>';
                $id = $account->getID();
                try {
                    $account->solvedChallenge(3);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    die();
                }
            }
        }
        echo '<iframe src="' . $fileLink . '" />';
    } else {
        print_r($errors);
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
    <title>GR17-CHALL3</title>
</head>
<body>

<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="image"/>
    <input type="submit"/>
</form>

</body>
</html>
