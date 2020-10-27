<?php

namespace App;

use App\appModel as appModel;
use \PDO;

class appRights extends appModel {

    /**
     * @var string $userName current user name
     */
    private $userName;

    /**
     * @var string $pageName current page name
     */
    private $pageName;

    public $userId;

    function __construct($conn=null, $file="") {
        self::$connection = $conn;
        parent::__construct();

        if (!isset($_SESSION['user'])) {
            header("Location: login.php");
        } else {
            $this->setCurrentFileName($file);
            $this->userName = $_SESSION['user'];
            $this->userId = $this->getUserId();
        }
    }

    /**
     * Получаем все группы пользователей
     *
     * @return array
     */
    public function getAllGroups() {
        $sql = "SELECT id, `name` AS group_name FROM groups;";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array());

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserId() {
        $sql = "SELECT id FROM users WHERE username = :username;";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":username" => $_SESSION['users']
        ));

        $arr = $stmt->fetch(PDO::FETCH_ASSOC);
        return $arr['id'];
    }

    public  function getAllUsers() {
        $sql = "SELECT id,username from users;";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array());

        $arr = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $arr;
    }

    /**
     * get current name
     *
     * @return string
     */
    public function getPageName() {
        echo $this->pageName;
        return $this->pageName;
    }

    /**
     * задает имя переданного файла как $pageName
     *
     * @param string $file
     */
    public function setCurrentFileName($file) {
        $file = str_replace("\\", "/", $file);
        $this->pageName = $file;
    }

    /**
     * получаем access_id доступа для конкретного объекта и группы
     *
     * @param $object_id
     * @param $group_id
     * @return string
     */
    public function getGroupAccess($object_id, $group_id) {
        /*$sql = "SELECT ra.id, ra.description AS access_description "
	            ."FROM rights_permissions rp "
                ."LEFT JOIN rights_access ra ON rp.access_id = ra.id "
                ."WHERE object_id = :object_id AND group_id = :group_id	LIMIT 1";*/

        $sql = "SELECT access_id "
            ."FROM rights_permissions "
            ."WHERE object_id= :object_id AND group_id = :group_id ";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":object_id" => (int) $object_id,
            ":group_id"  => (int) $group_id
        ));
        $arr = $stmt->fetch(PDO::FETCH_ASSOC);

        return $arr['access_id'];
    }

    /**
     * Получаем наименование доступа для текущей страницы и текущего пользователя
     *
     * @return string
     */
    public function getPermission() {
        $sql = "SELECT ra.id, ra.description
                FROM rights_objects ro
                         LEFT JOIN rights_permissions rp ON ro.id = rp.object_id
                         LEFT JOIN rights_access ra ON rp.access_id = ra.id
                WHERE ro.name = :pagename AND rp.group_id = (SELECT group_id FROM users WHERE username = :username)";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

        $param = array(
            ":username"  => $this->userName,
            ":pagename"  => $this->pageName
        );

        $stmt->execute($param);
        $arr = $stmt->fetch(PDO::FETCH_ASSOC);
        return $arr['description'];
    }


    /**
     * Получаем группу текущего пользователя
     *
     * @return string
     */
    public function getUserGroup() {
        $sql = "SELECT rg.name AS usergroup
                FROM users u
                  LEFT JOIN rights_groups rg ON rg.id = u.group_id
                WHERE username =:username";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":username" => $this->userName
        ));
        $arr = $stmt->fetch(PDO::FETCH_ASSOC);

        return $arr['usergroup'];
    }

    /**
     * Проверям права на текущую страницу для текущего пользователя
     * Если доступ запрещен то перенаправляем либо на предыдущую страницу либо на index.php
     *
     * @return string
     */
    public function checkPermission(){
        switch ($this->getPermission()) {
            case "denied":
                if(isset($_SERVER['HTTP_REFERER'])) {
                    $ref = $_SERVER['HTTP_REFERER'];
                    echo "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"utf-8\">";
                    echo "<script> window.location.href ='".$ref."'
                            alert('Пользователю ".$this->userName." ограничен доступ к этой странице.');
                        </script>";
                    echo "</head></html>";
                    die();
                } else {
                    echo "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"utf-8\">";
                    echo '<script>alert(\'Пользователю '.$this->userName.' огранчен доступ к этой странице.\r\nВы будете перенаправлены на основную страницу.\')</script>';
                    echo "<script> window.location.href ='index.php';</script>";
                    echo "</head></html>";
                    die();
                }
                break;
            case "read":
                return 'read';
                break;
            default:
                return $this->getPermission();
                break;
        }
    }

    /**
     * Получаем id страницы по ее названию
     *
     * @param string $pageName
     * @return string
     */
    public function getPageIdByName($pageName) {
        $sql = "SELECT id FROM rights_objects WHERE name=:name limit 1";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":name" => $pageName
        ));
        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    /**
     * Получаем все права на указанный объект
     *
     * @param $id
     * @return array
     */
    public function getObjectPermission($id) {
        $sql = "SELECT rg.`name`, ra.description 
                FROM rights_permissions rp 
                LEFT JOIN rights_groups rg ON rp.group_id = rg.id 
                LEFT JOIN rights_access ra ON rp.access_id = ra.id 
                WHERE object_id = :id 
                ORDER BY group_id";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":id" => $id
        ));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получаем все права на указанную страницу
     *
     * @param $pageName
     * @return array
     */
    public function getPagePermission($pageName) {
        $sql = "SELECT rg.id AS group_id, rg.name AS group_name, ra.description AS access_description, ro.id AS object_id, ro.name
                    FROM rights_groups rg
                      LEFT JOIN rights_permissions rp ON rp.group_id = rg.id AND rp.object_id = (SELECT id FROM rights_objects WHERE name =:name)
                      LEFT JOIN rights_access ra ON rp.access_id = ra.id
                      LEFT JOIN rights_objects ro ON ro.id = (SELECT id FROM rights_objects WHERE name=:name)
                WHERE rg.name!='superadmin' ORDER BY group_id";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":name" => $pageName
        ));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получаем все типы доступов
     *
     * @return array
     */
    public function getAllAccess() {
        $sql = "SELECT id, description as access_desc FROM rights_access;";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array());

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Добавляем указанную страницу как объект доступа
     *
     * @param $name
     * @return int
     */
    public function addPage($name) {

        $sql = "INSERT INTO rights_objects (name, type) VALUES (:name, 1);";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":name" => $name
        ));

        return $this->conn->lastInsertId();
    }


    /**
     * Удаляем блок с указаным идентификатором
     *
     * @param $id
     */
    public function removeObject($id) {
        $sql = "DELETE FROM rights_permissions WHERE object_id = :id";
        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":id" => $id
        ));

        $sql = "DELETE FROM rights_objects WHERE id = :id";
        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":id" => $id
        ));

    }

    /**
     * Добавляем право для указанного объекта доступа
     *
     * @param $object
     * @param $group
     * @param $access
     */
    public function addPermision($object, $group, $access){
        $sql = "INSERT INTO rights_permissions (object_id, group_id, access_id) "
            ."VALUES (:object_id, :group_id, :access_id ) "
            ."ON DUPLICATE KEY UPDATE access_id = :access_id ";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":object_id" => $object,
            ":group_id"  => $group,
            ":access_id" => ($access != 'null'? $access : NULL)
        ));
    }

    /**
     * Удаляем право для указанного объекта доступа и страницы
     *
     * @param $object
     * @param $group
     */
    public function removePermission($object, $group){
        $sql = "DELETE FROM rights_permissions WHERE object_id = :object_id AND group_id = :group_id  ";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":object_id" => $object,
            ":group_id"  => $group
        ));
    }

    /**
     * Проверка существования страницы как объекта доступа с прописанными правами доступа
     *
     * @return bool
     **/
    public function isExist($name) {
        $sql ="SELECT EXISTS (SELECT 1 FROM rights_objects WHERE `name`= :name) AS is_exist";
        //TODO дописать запрос на существование у объекта хоть одного доступа

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":name" => $name
        ));
        return $stmt->fetch(PDO::FETCH_ASSOC)['is_exist'];
    }


}