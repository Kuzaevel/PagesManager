<?php

require('../vendor/autoload.php');
Mustache_Autoloader::register();
$loader = new Mustache_Loader_FilesystemLoader("../templates",array('extension'=>'.html'));
$m = new Mustache_Engine;

use App\appRights;

if($_SERVER['REQUEST_METHOD']=='POST') {

    if ($_POST && !empty($_POST['action'])) {

        require_once '../db.php';
        //$conn defined in db
        $rights = new appRights($conn);

        switch ($_POST['action']) {

            case 'addPage':
                $arr = array(
                    "success" => true,
                    "id" => $rights->addPage($_POST['object-name'])
                );
                header('Content-Type: application/json');
                echo json_encode($arr);
           break;

            case 'removeObject':
                try{
                    if(isset($_POST['object-id']) && $_POST['object-id']) {
                        $rights->removeObject($_POST['object-id']);
                        header('Content-Type: application/json');
                        echo json_encode(array("success" => true));

                    }
                } catch (Exception $ex) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $ex->getMessage()]);
                }
            break ;

            case 'getGroupAccess':
                try{
                    $id = $rights->getGroupAccess($_POST['object-id'], $_POST['group-id']);
                    $arr = array(
                        "success" => true,
                        "id" => $id
                    );
                    header('Content-Type: application/json');
                    echo json_encode($arr);

                } catch (Exception $ex) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $ex->getMessage()]);
                }
            break;

            case 'addPermission':
                try {
                    $rights->addPermision($_POST['object-id'], $_POST['group-id'],$_POST['access-id']);
                    $arr = array(
                        "success" => true
                    );
                    header('Content-Type: application/json');
                    echo json_encode(['success' =>true ]);

                } catch (Exception $ex) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $ex->getMessage()]);
                }
            break;

            case 'removePermission':
                try {
                    $rights->removePermission($_POST['object-id'], $_POST['group-id']);
                    $arr = array(
                        "success" => true,
                    );
                    header('Content-Type: application/json');
                    echo json_encode(['success' =>true ]);

                } catch (Exception $ex) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $ex->getMessage()]);
                }
                break;

            case 'getObjectPermission':
                try {
                    $html = "<table style='border: 1px solid grey;margin-top:5px; margin-left:10px;'><tbody>";
                    $permissions = $rights->getObjectPermission($_POST['object-id']);

                    foreach ($permissions as $out) {
                        $html .= "<tr><td style='border: 1px solid grey; padding: 2px 5px 2px 5px;'>".$out["name"] . "</td><td style='border: 1px solid grey; padding: 2px 5px 2px 5px;'>" . $out["description"] . "</td></tr>";
                    }
                    $html .= "</tbody></table>";

                    header('Content-Type: application/json');
                    echo json_encode(['success' =>true, 'html' => $html ]);

                } catch (Exception $ex) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $ex->getMessage()]);
                }
            break;

        }
    }
} else {
    die();
}