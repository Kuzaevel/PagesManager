<?php
    ob_start();
    require_once 'header.php';

    use App\appUsers;
    $users = new appUsers();

    if($_SERVER['REQUEST_METHOD']=='GET') {
        $data["users"] = $users->getAllUsers();
    }

    echo $m->render($loader->load('users'), $data);
?>

<?php
    require_once './footer.php';
?>
