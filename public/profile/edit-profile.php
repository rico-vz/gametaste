<?php
include_once './dbh.inc.php';
session_start();

$servername = "localhost";
$dbname = "UR_DB_NAME";
$charset = "utf8mb4";

$id = $_SESSION['loggedInUser'];

// Form is submitted
if (!isset($id)) {
    header("Location: ./me");
    die();
}

try {
    if (!$id) {
        echo ("No ID has been specified.");
        die();
    } else {
        $mediaID = $id;
        $object = new Dbh();
        $pdoConn = $object->connect();
        $mediaList = $pdoConn->query("SELECT * FROM users WHERE id = $id");
    }
} catch (Exception $e) {
    echo "Something went wrong:" . $e->getMessage();
    die();
}

if (isset($_POST['email'])) {
    $sql = "UPDATE users SET email=?, bio=?, avatar=?, twitch=?, youtube=?, twitter=?, instagram=?, discord=? WHERE id=$id";
    $updateSQL = $pdoConn->prepare($sql);
    $updateSQL->execute([
        $_POST['email'], $_POST['bio'], $_POST['avatar'], $_POST['twitch'], $_POST['youtube'],
        $_POST['twitter'], $_POST['instagram'], $_POST['discord']
    ]);
    header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>edit profile</title>
</head>

<body>
    <?php foreach ($mediaList as $data) ?>

    <form action="" method="POST" style="display: flex; flex-direction: column; align-items: flex-start;">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <br>
        <table class="table2">
            <tr>
                <th>
                    <h1>edit profile</h1>
                </th>
            </tr>
            <tr>
                <td>
                    <h3>email</h3>
                </td>
                <td><input type="text" name="email" placeholder="<?php echo $data['email'] ?>"
                        value="<?php echo $data['email']; ?>"></td>
            </tr>
            <tr>
                <td>
                    <h3>bio</h3>
                </td>
                <td><input type="text" name="bio" placeholder="<?php echo $data['bio'] ?>"
                        value="<?php echo $data['bio']; ?>"></td>
            </tr>
            <tr>
                <td>
                    <h3>link naar avatar</h3>
                </td>
                <td><input type="text" name="avatar" placeholder="<?php echo $data['avatar'] ?>"
                        value="<?php echo $data['avatar']; ?>"></td>
            </tr>
            <tr>
                <td>
                    <h3>twitch username</h3>
                </td>
                <td><input type="text" name="twitch" placeholder="<?php echo $data['twitch'] ?>"
                        value="<?php echo $data['twitch']; ?>"></td>
            </tr>
            <tr>
                <td>
                    <h3>youtube</h3>
                </td>
                <td><input type="text" name="youtube" placeholder="<?php $data['youtube'] ?>"
                        value="<?php echo $data['youtube']; ?>"></td>
            </tr>
            <tr>
                <td>
                    <h3>twitter</h3>
                </td>
                <td><input type="text" name="twitter" placeholder="<?php echo $data['twitter'] ?>"
                        value="<?php echo $data['twitter']; ?>"></td>
            </tr>
            <tr>
                <td>
                    <h3>instagram</h3>
                </td>
                <td><input type="text" name="instagram" placeholder="<?php echo $data['instagram'] ?>"
                        value="<?php echo $data['instagram']; ?>"></td>
            </tr>
            <tr>
                <td>
                    <h3>discord</h3>
                </td>
                <td><input type="text" name="discord" placeholder="<?php echo $data['discord'] ?>"
                        value="<?php echo $data['discord']; ?>"></td>
            </tr>
        </table>
        <br>

        <button class="button">
            <span class="text">submit changes</span>
            <i class="ri-check-line icon"></i>
        </button>

        <br>
        <a href="me">Terug</a>
    </form>
    <form action="/user/changePassword">
        <input type="submit" value="Change password" />
    </form>
    <form action="/user/profileDelete" onclick="return confirm('Are you sure you want to delete your account?');">
        <input type="submit" value="Delete profile" />
    </form>

    <br>
    <a href="index">Terug naar beginpagina</a>
</body>

</html>