<?php


namespace App;


class appModel
{
    public static $connection;
    public $conn;

    public function setConnection($conn){
        $this->conn = $conn;
        //TODO самостоятельное подключение к базе, если переданный объект пустой
    }

    private function init_session(){
        session_start();
    }

    public function __construct($conn=null)
    {
        if( !isset($_SESSION) ){
            $this->init_session();
        }

        if(!$conn) {
            $this->setConnection(self::$connection);
        } else {
            $this->setConnection($conn);
        }

    }
}