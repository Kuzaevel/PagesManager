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
            case 'getUser':
                try {
                    echo json_encode(["success" => true, "user" => $users->getUser($_POST["id"])]);
                } catch (Exception $ex) {
                    echo json_encode(["success" => false, "error" => $ex->getMessage()]);
                }
                break;
            case 'edit':
                try {
                    $users->edit($_POST);
                    echo json_encode(["success" => true]);
                } catch (Exception $ex) {
                    echo json_encode(["success" => false, "data" => $ex->getMessage()]);
                }
                break;
            case 'delete':
                try {
                    $users->delete($_POST);
                    echo json_encode(["success" => true]);
                } catch (Exception $ex) {
                    echo json_encode(["success" => false, "data" => $ex->getMessage()]);
                }
                break;
            case 'validate':
                try {
                    echo json_encode(["success" => true, "is_double"=>$users->is_double($_POST['username'])]);
                } catch (Exception $ex) {
                    echo json_encode(["success" => false, "data" => $ex->getMessage()]);
                }
                break;
        }
    }
