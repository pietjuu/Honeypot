<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Please enter your username and password";
    exit;
} else {
    if ($_SERVER['PHP_AUTH_USER'] === "admin" && $_SERVER['PHP_AUTH_PW'] === "44OzfvdT%5L@OWbPP7qT$10@1^S4R6@Q3tEwB^XS@R#L#LFuELsb") {
        require './assets/php/config.php';
        require './assets/php/account.php';
        global $pdo;
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $deleteUser = "DELETE FROM honeypot.users WHERE (UserID = :id)";
            $stmt = $pdo->prepare($deleteUser);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $deleteUserSession = "DELETE FROM honeypot.user_sessions WHERE (UserID = :id)";
            $stmt = $pdo->prepare($deleteUserSession);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        if (isset($_GET['enable'])) {
            $id = $_GET['enable'];
            $enableUser = "UPDATE honeypot.users SET enabled = 1 WHERE UserID = :id";
            $stmt = $pdo->prepare($enableUser);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        if (isset($_GET['disable'])) {
            $id = $_GET['disable'];
            $disableUser = "UPDATE honeypot.users SET enabled = 0 WHERE UserID = :id";
            $stmt = $pdo->prepare($disableUser);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        $getAllUsers = 'SELECT * from honeypot.users t1 left join (SELECT UserID as UID, LoginTime from honeypot.user_sessions) t2 on t1.UserID=t2.UID order by t1.UserID';
        $stmt = $pdo->prepare($getAllUsers);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        echo "Invalid credentials";
        die();
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
    <title>Admin page</title>
    <style>
        #users {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #users td, #users th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #users tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #users tr:hover {
            background-color: #ddd;
        }

        #users th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: royalblue;
            color: white;
        }
    </style>
</head>
<body>
<script>
    function deleteUser(id) {
        if (confirm("Are you sure you want to delete this user?")) {
            window.location.href = "admin.php?delete=" + id;
        }
    }

    function toggleState(id, state) {
        if (state === 1) {
            if (confirm("Are you sure you want to disable this user?")) {
                window.location.href = "admin.php?disable=" + id;
            }
        } else {
            if (confirm("Are you sure you want to enable this user?")) {
                window.location.href = "admin.php?enable=" + id;
            }
        }
    }
</script>
<h1>Admin Panel</h1>
<table id="users">
    <tr>
        <th>Delete User</th>
        <th>Is Enabled</th>
        <th>User ID</th>
        <th>Username</th>
        <th>Time of Registration</th>
        <th>Time of Last Logon</th>
        <th>Challenge1</th>
        <th>Challenge2</th>
        <th>Challenge3</th>
        <th>Challenge4</th>
        <th>Challenge5</th>
    </tr>
    <?php
    foreach ($result as $row) {
        $delete = "deleteUser(" . $row['UserID'] . ")";
        $toggleState = "toggleState(" . $row['UserID'] . ", " . $row['enabled'] . ")";
        echo <<<END
        <tr>
            <td><button onclick="$delete">Delete user</button></td>
            <td>{$row['enabled']}<button onclick="$toggleState">Change</button></td>
            <td>{$row['UserID']}</td>
            <td>{$row['Username']}</td>
            <td>{$row['reg_time']}</td>
            <td>{$row['LoginTime']}</td>
            <td>{$row['challenge1']}</td>
            <td>{$row['challenge2']}</td>
            <td>{$row['challenge3']}</td>
            <td>{$row['challenge4']}</td>
            <td>{$row['challenge5']}</td>
        </tr>
        END;
    }
    ?>
</table>
</body>
</html>

