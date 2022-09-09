<?php
 // SQL Database Connection
 
 // local mac
 $host = "localhost";
 $user = "root";
 $pw = "root";
 $db = "RPiSmartGardenDB";

  // local RPi
  //$host = "localhost";
  //$user = "40176844";
  //$pw = "banana12";
  //$db = "RPiSmartGardenDB";

 // database connection check
 $conn = new mysqli($host, $user, $pw, $db);

 if ($conn->connect_error) {
    echo  "not connected to database".$conn->connect_error;
    exit();
 } else {
    //echo "connected to database";
 }

 ?>