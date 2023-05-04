<?php

    function getDataBaseConnection(){
        $db = new PDO('sqlite:admin.db');
        return $db;
    }

    
?>