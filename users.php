<?php
    ob_start();
    require_once 'header.php';

    use App\appUsers;
    $users = new appUsers();

    if($_SERVER['REQUEST_METHOD']=='GET') {
        $data["users"] = $users->getAllUsers();
        $data['groups'] = $rights->getAllGroups();
    }

    echo $m->render($loader->load('users'), $data);
    require_once './footer.php';
?>
<script src='js/users.js'></script>
<?php
    if($_SERVER['REQUEST_METHOD']=='POST') {
        ob_clean();
        switch ($_POST['action']) {
            case 'addUser':
                try {
                    $id = $users->addUser($_POST);
                    if(isset($id)) echo json_encode(["success" => true]);
                } catch (Exception $ex) {
                    echo json_encode(["success" => false, "data" => $ex->getMessage()]);
                }
                break;
        }
    }
