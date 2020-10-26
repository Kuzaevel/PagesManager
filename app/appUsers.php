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
        $sql = "SELECT * from users";

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array());

        $arr = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $arr;
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
}
