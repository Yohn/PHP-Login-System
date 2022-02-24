<?php

require 'env.php';


$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if (!$conn)
{
    die("Connection failed: ". mysqli_connect_error());
}

try {
     $dbh = new PDO("mysql:host=localhost;dbname=klik_loginsystem", "root", "1234");
     $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
     # $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
     # $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
}
catch(PDOException $e) {
     echo $e->getMessage();
     file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
}
