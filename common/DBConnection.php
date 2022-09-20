<?php

namespace common;
use mysqli;

class DBConnection{
    protected static $db;
    protected static $mysqli_db;

    private function __construct() {
        try {
        self::$mysqli_db=new mysqli('localhost','ivanovr6_db', '020&VhUM','ivanovr6_db') or die(self::$mysqli_db->error);

         return self::$mysqli_db;
        }
        catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
    }

    public function query($query)  
     {
    return self::$mysqli_db->query($query);
     }

    public static function getInstance() {
        if (!self::$mysqli_db) {
            new DBConnection();
        }
        return self::$mysqli_db;
    }

    public function real_escape_string($str)
     {
        return self::$mysqli_db->real_escape_string();
     }


    public function getLink()
    {
        return self::$mysqli_db;
    }


    public function __destruct() {
        //Close the Connection
        //self::$mysqli_db->close();
    }
}