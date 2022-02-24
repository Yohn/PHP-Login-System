<?php

require 'env.php';


$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if (!$conn)
{
    die("Connection failed: ". mysqli_connect_error());
}

try {
     $dbh = new PDO("mysql:host=".DB_HOST."; dbname = ".DB_DATABASE, DB_USERNAME, DB_PASSWORD);
     $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
     # $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
     # $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
}
catch(PDOException $e) {
     echo $e->getMessage();
     file_put_contents(APP_PRIVATE_PATH.'/PDOErrors.txt', $e->getMessage(), FILE_APPEND);
}
