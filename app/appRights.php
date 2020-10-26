<?php

namespace App;

use App\appModel as appModel;
use \PDO;

class appRights extends appModel {

    function __construct($conn=null, $file="") {
        self::$connection = $conn;
        parent::__construct();
    }

}