<?php

namespace App;

use App\appModel as appModel;
use \PDO;

class appRights extends appModel {

    function __construct($conn=null, $file="") {
        self::$connection = $conn;
        parent::__construct();
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

}