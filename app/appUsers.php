<?php


namespace App;

use App\appModel as appModel;
use \PDO;

class appUsers extends appModel
{
    function __construct()
    {
        /*
         *  без параметра можно вызвать только если $conn передан в модель в header.php
         *  иначе self::$connection = $conn или передать параметр $conn
        */
        parent::__construct();
    }

    /**
     * Получаем всех пользователей
     *
     * @return array
     */
    public function getAllUsers()
    {
        $sql = "SELECT u.*, g.name as group_name FROM users u
                LEFT JOIN groups g ON g.id = u.group_id
                ORDER BY u.id";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array());

        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    /**
     * Добавляем нового пользователя gpp, (без пароля)
     *
     * @param $arr
     * @return lastInsertId
     */
    function addUser($arr) {
        $sql = "INSERT INTO users (username,enabled,contact_mail,contact_name,group_id ) 
                VALUES(:username,1,:contact_mail,:contact_name,:group_id)";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":username"      => $arr["username"],
            ":contact_name"  => $arr["contact_name"],
            ":contact_mail"  => $arr["contact_mail"],
            ":group_id"      => (int)$arr["group_id"]
        ));

        return $this->conn->lastInsertId();
    }

    /**
     * Получаем все поля указанного пользователя gpp
     *
     * @param $id
     * @return array
     */
    function getUser($id) {
        $sql = "SELECT * FROM users WHERE id= :id ";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":id" => $id
        ));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Редактирвоание пользователя
     * Передаем массивом все редактируемые поля, кроме пароля
     *
     * @param $arr
     */
    function edit($arr) {
        $sql = "UPDATE users SET 
                username = :username, 
                contact_name = :contact_name, 
                group_id = :group_id
                WHERE id = :id ";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":username"      => $arr["username"],
            ":contact_name"  =>$arr["contact_name"],
            ":group_id"      => (int)$arr["group_id"],
            ":id"            => (int)$arr["id"]
        ));
    }

    /**
     * Удаление пользователя
     *
     * @param $arr
     */
    function delete($arr) {
        $sql = "DELETE FROM users WHERE id=:id;";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":id" => (int)$arr["id"]
        ));
    }

    /**
     * Валидация пользователя на дублирование
     *
     * @param $username
     * @return string
     */

    function is_double($username) {
        $sql = "SELECT CASE WHEN (SELECT EXISTS(SELECT 1 FROM users WHERE username = :username)) THEN 'double' ELSE 'unic' END is_double";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(
            ":username" => $username
        ));

        return $stmt->fetch(PDO::FETCH_ASSOC)['is_double'];
    }
}
