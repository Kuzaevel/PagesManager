<?php

ob_start();
session_start();

$passed = -1;

if (isset($_POST['username'])) {
    require_once './db.php';

    $sql = "SELECT count(*) AS passed FROM users 
            WHERE username = :username AND password=:password AND enabled = '1'";

    $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $stmt->execute(array(
        ':username' => $_POST['username'],
        ':password' => md5($_POST['password']."top_secret")
    ));

    $res =  $stmt->fetch(PDO::FETCH_ASSOC);

    $passed = $res['passed'];

    if ($passed) {
        $sql = 'UPDATE users SET lastlogin = NOW() WHERE username = :username';
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ':username' => $_POST['username']
        ));
        $_SESSION['user'] = $_POST['username'];

        header('Location: ./index.php');
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>pagesmanager</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div class="container">
        <a class="navbar-brand" href="#">PagesManager</a>
    </div>
</nav>
  <!-- Page Content -->
  <div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Вход</h3>
                </div>
                <div class="panel-body">
                    <form role="form" method="post">
                        <?php
                        if ($passed == 0) {
                            echo "<div style='color: red; font-weight: bold;'>Неверное имя пользователя или пароль<br /><br /></div>";
                        }
                        ?>
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="Логин" name="username" type="text" autofocus value="<?php echo @$_POST['username'] ?>">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Пароль" name="password" type="password" value="<?php echo @$_POST['password'] ?>">
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <input type="submit" class="btn btn-sm btn-primary btn-block" value="Войти" />
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>

<?php
    require_once './footer.php';
?>
